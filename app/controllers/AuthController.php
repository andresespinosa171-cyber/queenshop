<?php

class AuthController extends Controller {

    public function loginForm(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->view('auth/login', ['title' => 'QueenShop — Iniciar Sesión'], 'auth/layout');
    }

    public function login(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            session_flash('error', 'Completá todos los campos.');
            $this->redirect('/login');
            return;
        }

        $db = getDB();
        $stmt = $db->prepare("SELECT u.*, c.name AS company_name FROM users u JOIN companies c ON u.company_id = c.id WHERE u.username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            session_flash('error', 'Usuario o contraseña incorrectos.');
            $this->redirect('/login');
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['company_id'] = $user['company_id'];
        $_SESSION['company_name'] = $user['company_name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Load full company branding into session
        $db2 = getDB();
        $company = $db2->query("SELECT * FROM companies WHERE id = ?", [$user['company_id']])->fetch();
        $_SESSION['store_name'] = $company['store_name'] ?? 'QueenShop';
        $_SESSION['logo'] = $company['logo'] ?? 'logo.svg';
        $_SESSION['theme'] = $company['theme'] ?? 'queenshop';
        $_SESSION['primary_color'] = $company['primary_color'] ?? '#ffc107';
        $_SESSION['company_description'] = $company['description'] ?? '';

        $this->redirect('/');
    }

    public function registerForm(): void {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->view('auth/register', ['title' => 'QueenShop — Crear Cuenta'], 'auth/layout');
    }

    public function register(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $storeType = $_POST['store_type'] ?? 'pet_shop';

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

        try {
            $db->beginTransaction();

            // Map store type to branding
            if ($storeType === 'shoe_store') {
                $theme = 'wolfstor';
                $primaryColor = '#2563eb';
                $storeName = $username . "'s WolfStor";
                $logo = 'wolfstor-logo.svg';
                $description = 'Tienda de zapatos';
            } else {
                $theme = 'queenshop';
                $primaryColor = '#ffc107';
                $storeName = $username . "'s Shop";
                $logo = 'logo.svg';
                $description = 'Tienda de mascotas';
            }

            // Create company with branding
            $stmt = $db->prepare("INSERT INTO companies (name, store_name, theme, logo, primary_color, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$storeName, $storeName, $theme, $logo, $primaryColor, $description]);
            $companyId = $db->lastInsertId();

            // Create user
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $stmt = $db->prepare("INSERT INTO users (company_id, username, password) VALUES (?, ?, ?)");
            $stmt->execute([$companyId, $username, $hash]);

            $db->commit();

            session_flash('success', 'Cuenta creada correctamente. Ahora iniciá sesión.');
            $this->redirect('/login');
        } catch (Exception $e) {
            $db->rollBack();
            session_flash('error', 'Error al crear la cuenta. Intentalo de nuevo.');
            $this->redirect('/register');
        }
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('/login');
    }
}
