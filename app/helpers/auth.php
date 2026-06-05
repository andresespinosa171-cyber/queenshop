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

function current_store_name(): string {
    return $_SESSION['store_name'] ?? 'QueenShop';
}

function current_company_logo(): string {
    return $_SESSION['logo'] ?? 'logo.svg';
}

function current_theme_class(): string {
    return $_SESSION['theme'] ?? 'queenshop';
}

function current_primary_color(): string {
    return $_SESSION['primary_color'] ?? '#ffc107';
}

function current_user_companies(): array {
    return $_SESSION['user_companies'] ?? [];
}
