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

            // Create company
            $companyName = $username . "'s Shop";
            $stmt = $db->prepare("INSERT INTO companies (name) VALUES (?)");
            $stmt->execute([$companyName]);
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
