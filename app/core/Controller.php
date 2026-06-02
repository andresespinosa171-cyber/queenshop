<?php

abstract class Controller {
    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/' . $layout . '.php';
    }

    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . $url);
        exit;
    }

    protected function json(mixed $data): void {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function back(): void {
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
