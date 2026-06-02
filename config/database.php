<?php
date_default_timezone_set('America/Bogota');

function getDB(): PDO {
    static $db = null;
    if ($db === null) {
        $mysqlConfig = __DIR__ . '/db.mysql.php';
        if (file_exists($mysqlConfig)) {
            $config = require $mysqlConfig;
            $db = new PDO(
                "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4",
                $config['user'],
                $config['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } else {
            $dbPath = __DIR__ . '/../database/petshop.db';
            $isNew = !file_exists($dbPath);
            $db = new PDO('sqlite:' . $dbPath);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->exec('PRAGMA journal_mode=WAL');
            $db->exec('PRAGMA foreign_keys=ON');
            if ($isNew) {
                $schema = file_get_contents(__DIR__ . '/../database/schema.sqlite.sql');
                $db->exec($schema);
            }
        }
    }
    return $db;
}
