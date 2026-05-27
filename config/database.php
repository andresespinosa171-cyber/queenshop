<?php
date_default_timezone_set('America/Bogota');

define('DB_PATH', __DIR__ . '/../database/petshop.db');

function getDB(): PDO {
    static $db = null;
    if ($db === null) {
        $isNew = !file_exists(DB_PATH);
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec('PRAGMA journal_mode=WAL');
        $db->exec('PRAGMA foreign_keys=ON');

        if ($isNew) {
            $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
            $db->exec($schema);
        }
    }
    return $db;
}
