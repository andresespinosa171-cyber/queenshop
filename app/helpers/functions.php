<?php

function old(string $key, mixed $default = ''): mixed {
    return $_SESSION['_old'][$key] ?? $default;
}

function session_flash(string $key, string $message): void {
    $_SESSION['_flash'][$key] = $message;
}

function session_has(string $key): bool {
    return isset($_SESSION['_flash'][$key]);
}

function session_get(string $key): string {
    $val = $_SESSION['_flash'][$key] ?? '';
    unset($_SESSION['_flash'][$key]);
    return $val;
}

function format_currency(float $amount): string {
    return '$' . number_format($amount, 2, ',', '.');
}

function asset(string $path): string {
    return $path;
}

function image_url(?string $path): string {
    if ($path && file_exists(__DIR__ . '/../../' . $path)) {
        return BASE_URL . '/' . ltrim($path, '/');
    }
    return BASE_URL . '/assets/img/no-image.svg';
}

function truncate(string $text, int $length = 50): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

function is_first_fortnight(): bool {
    return (int) date('d') <= 15;
}

function is_second_fortnight(): bool {
    return !is_first_fortnight();
}

function current_fortnight_range(): array {
    $year = date('Y');
    $month = date('m');
    if (is_first_fortnight()) {
        return ['start' => "{$year}-{$month}-01", 'end' => "{$year}-{$month}-15"];
    }
    $lastDay = date('t', strtotime("{$year}-{$month}-01"));
    return ['start' => "{$year}-{$month}-16", 'end' => "{$year}-{$month}-{$lastDay}"];
}

function in_current_fortnight(string $date): bool {
    $range = current_fortnight_range();
    return $date >= $range['start'] && $date <= $range['end'];
}
