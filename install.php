<?php
/**
 * QueenShop — Instalador de base de datos para InfinityFree.
 * 
 * ABRIR EN EL NAVEGADOR UNA SOLA VEZ para crear las tablas.
 * Después del import exitoso, BORRAR este archivo del server.
 */

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
<title>QueenShop — Instalación</title>
<style>body{font-family:sans-serif;max-width:600px;margin:40px auto;padding:0 20px}
.success{color:#28a745;font-weight:bold}.error{color:#dc3545;font-weight:bold}
pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow-x:auto}
</style></head><body>';
echo '<h1>🛠️ QueenShop — Instalación de base de datos</h1>';

try {
    // Cargar config de MySQL
    $config = require __DIR__ . '/config/db.mysql.php';
    
    echo '<p>Conectando a MySQL...</p>';
    $db = new PDO(
        "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4",
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo '<p class="success">✅ Conexión exitosa</p>';
    
    // Verificar si ya hay tablas
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo '<p class="error">⚠️ Ya existen tablas en la base de datos:</p>';
        echo '<ul>';
        foreach ($tables as $t) {
            echo "<li>$t</li>";
        }
        echo '</ul>';
        echo '<p>Si querés reinstalar, primero borrá las tablas desde phpMyAdmin.</p>';
    } else {
        // Importar schema
        $schemaFile = __DIR__ . '/database/schema.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception("No se encontró database/schema.sql");
        }
        
        $sql = file_get_contents($schemaFile);
        echo '<p>Ejecutando schema.sql...</p>';
        
        // Ejecutar sentencias una por una
        $statements = explode(';', $sql);
        $count = 0;
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (!empty($stmt) && !str_starts_with($stmt, '--')) {
                $db->exec($stmt);
                $count++;
            }
        }
        
        echo "<p class=\"success\">✅ Schema importado correctamente — $count sentencias ejecutadas</p>";
        
        // Verificar tablas creadas
        $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        echo '<p>Tablas creadas:</p><ul>';
        foreach ($tables as $t) {
            $row = $db->query("SELECT COUNT(*) FROM `$t`")->fetch(PDO::FETCH_COLUMN);
            echo "<li><strong>$t</strong> — $row registros</li>";
        }
        echo '</ul>';
    }
    
    echo '<hr>';
    echo '<p>✅ Instalación completada.</p>';
    echo '<p class="error"><strong>⚠️ IMPORTANTE: borrá este archivo (install.php) del servidor</strong></p>';
    echo '<p><a href="index.php">→ Ir a QueenShop</a></p>';
    
} catch (Exception $e) {
    echo '<p class="error">❌ ERROR: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}

echo '</body></html>';
