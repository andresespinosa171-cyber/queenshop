<?php

function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['_flash']['error'] = 'Debés iniciar sesión primero.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

function current_company_id(): int {
    return (int) ($_SESSION['company_id'] ?? 0);
}

function current_company_name(): string {
    return $_SESSION['company_name'] ?? '';
}

function is_admin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}
