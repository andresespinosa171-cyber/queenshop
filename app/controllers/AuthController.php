<?php

class AuthController extends Controller {
    private Company $company;

    public function __construct() {
        $this->company = new Company();
    }

    /**
     * Handle store preselection from landing page.
     * Accepts POST (from landing form) or GET (direct link).
     */
    private function preselectedStore(): ?array {
        // POST from landing page
        $storeId = $_POST['store_id'] ?? ($_GET['store'] ?? null);
        if ($storeId) {
            $company = $this->company->find((int) $storeId);
            if ($company) {
                $_SESSION['store_preselected'] = [
                    'company_id'    => (int) $company['id'],
                    'name'          => $company['store_name'] ?? $company['name'],
                    'theme'         => $company['theme'] ?? 'queenshop',
                    'primary_color' => $company['primary_color'] ?? '#ffc107',
                    'logo'          => $company['logo'] ?? 'logo.svg',
                    'description'   => $company['description'] ?? '',
                ];
            }
        }

        return $_SESSION['store_preselected'] ?? null;
    }

    /**
     * Redirect back to store selection landing.
     */
    private function redirectToLanding(string $flash = ''): void {
        unset($_SESSION['store_preselected']);
        if ($flash) {
            session_flash('info', $flash);
        }
        $this->redirect('/');
    }

    public function loginForm(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        $store = $this->preselectedStore();

        if (!$store) {
            // No store selected — go back to landing
            $this->redirectToLanding('Seleccioná una tienda primero.');
            return;
        }

        $this->view('auth/login', [
            'store' => $store,
            'title' => $store['name'] . ' — Iniciar Sesión',
        ], 'auth/layout');
    }

    public function login(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Handle POST from landing page
        $this->preselectedStore();

        $store = $_SESSION['store_preselected'] ?? null;
        if (!$store) {
            $this->redirectToLanding('Seleccioná una tienda primero.');
            return;
        }

        if ($username === '' || $password === '') {
            session_flash('error', 'Completá todos los campos.');
            $this->redirect('/login');
            return;
        }

        $targetCompanyId = $store['company_id'];

        $db = getDB();

        // Find user — join with companies to get company name
        $stmt = $db->prepare("SELECT u.*, c.name AS company_name FROM users u JOIN companies c ON u.company_id = c.id WHERE u.username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            session_flash('error', 'Usuario o contraseña incorrectos.');
            $this->redirect('/login');
            return;
        }

        // Validate user has access to the selected store
        $hasAccess = false;
        $userId = (int) $user['id'];
        if ($userId == 1) {
            $hasAccess = true; // Super-admin can access all
        } else {
            $access = $db->prepare("SELECT 1 FROM user_companies WHERE user_id = ? AND company_id = ?");
            $access->execute([$userId, $targetCompanyId]);
            $hasAccess = (bool) $access->fetch();
        }

        if (!$hasAccess) {
            session_flash('error', 'No tenés acceso a ' . htmlspecialchars($store['name']) . '.');
            $this->redirect('/login');
            return;
        }

        // Clear preselection and set session
        unset($_SESSION['store_preselected']);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['company_id'] = $targetCompanyId;
        $_SESSION['company_name'] = $store['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Load full company branding
        $company = $this->company->find($targetCompanyId);
        $_SESSION['store_name'] = $company['store_name'] ?? $store['name'];
        $_SESSION['logo'] = $company['logo'] ?? $store['logo'];
        $_SESSION['theme'] = $company['theme'] ?? $store['theme'];
        $_SESSION['primary_color'] = $company['primary_color'] ?? $store['primary_color'];
        $_SESSION['company_description'] = $company['description'] ?? $store['description'];

        session_flash('success', 'Bienvenido a ' . htmlspecialchars($_SESSION['store_name']));
        $this->redirect('/dashboard');
    }

    public function registerForm(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
            return;
        }

        $store = $this->preselectedStore();

        if (!$store) {
            $this->redirectToLanding('Seleccioná una tienda primero.');
            return;
        }

        $this->view('auth/register', [
            'store' => $store,
            'title' => $store['name'] . ' — Crear Cuenta',
        ], 'auth/layout');
    }

    public function register(): void {
        $this->preselectedStore();
        $store = $_SESSION['store_preselected'] ?? null;

        if (!$store) {
            $this->redirectToLanding('Seleccioná una tienda primero.');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($username === '' || $password === '') {
            session_flash('error', 'Completá todos los campos.');
            $this->redirect('/register');
            return;
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            session_flash('error', 'El usuario debe tener entre 3 y 50 caracteres.');
            $this->redirect('/register');
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            session_flash('error', 'Solo se permiten letras, números y guión bajo.');
            $this->redirect('/register');
            return;
        }

        if (strlen($password) < 6) {
            session_flash('error', 'La contraseña debe tener al menos 6 caracteres.');
            $this->redirect('/register');
            return;
        }

        if ($password !== $confirm) {
            session_flash('error', 'Las contraseñas no coinciden.');
            $this->redirect('/register');
            return;
        }

        $db = getDB();

        // Check username uniqueness
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            session_flash('error', 'Ese usuario ya existe.');
            $this->redirect('/register');
            return;
        }

        // Use the preselected store's company for new user
        $isWolfStor = $store['theme'] === 'wolfstor';
        $theme = $isWolfStor ? 'wolfstor' : 'queenshop';
        $primaryColor = $isWolfStor ? '#2563eb' : '#ffc107';
        $storeNameSuffix = $isWolfStor ? "'s WolfStor" : "'s Shop";
        $logo = $isWolfStor ? 'wolfstor-logo.svg' : 'logo.svg';
        $description = $isWolfStor ? 'Tienda de zapatos' : 'Tienda de mascotas';

        try {
            $db->beginTransaction();

            // Create company based on preselected store theme
            $fullName = $username . $storeNameSuffix;
            $stmt = $db->prepare("INSERT INTO companies (name, store_name, theme, logo, primary_color, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullName, $fullName, $theme, $logo, $primaryColor, $description]);
            $companyId = $db->lastInsertId();

            // Create user
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $stmt = $db->prepare("INSERT INTO users (company_id, username, password) VALUES (?, ?, ?)");
            $stmt->execute([$companyId, $username, $hash]);
            $userId = $db->lastInsertId();

            // Grant access
            $stmt = $db->prepare("INSERT OR IGNORE INTO user_companies (user_id, company_id, role) VALUES (?, ?, 'admin')");
            $stmt->execute([$userId, $companyId]);

            $db->commit();

            unset($_SESSION['store_preselected']);
            session_flash('success', 'Cuenta creada correctamente para ' . htmlspecialchars($fullName) . '. Ahora iniciá sesión.');
            $this->redirect('/login');
        } catch (Exception $e) {
            $db->rollBack();
            session_flash('error', 'Error al crear la cuenta. Intentalo de nuevo.');
            $this->redirect('/register');
        }
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('/');
    }
}
