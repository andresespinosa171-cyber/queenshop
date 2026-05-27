<?php

abstract class Model {
    protected string $table = '';
    protected ?PDO $db = null;

    public function __construct() {
        $this->db = getDB();
    }

    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function all(): array {
        return $this->query("SELECT * FROM {$this->table} ORDER BY id DESC")->fetchAll();
    }

    public function find(int|string $id): array|false {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id])->fetch();
    }

    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->query(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int|string $id, array $data): void {
        $sets = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $this->query(
            "UPDATE {$this->table} SET {$sets} WHERE id = ?",
            [...array_values($data), $id]
        );
    }

    public function delete(int|string $id): void {
        $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}
