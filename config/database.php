<?php
date_default_timezone_set('America/Bogota');

function getDB(): PDO {
    static $db = null;
    if ($db === null) {
        $mysqlConfig = __DIR__ . '/db.mysql.php';
        if (file_exists($mysqlConfig)) {
            try {
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
            } catch (PDOException $e) {
                // MySQL unreachable — fallback to SQLite
                $db = null;
            }
        }
        if ($db === null) {
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

        // Auto-migrate existing databases
        runMigrations($db);
    }
    return $db;
}

/** Run pending migrations — safe for existing DBs */
function runMigrations(PDO $db): void {
    $isMySQL = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';

    // Crear tabla de control de migraciones
    if ($isMySQL) {
        $db->exec("CREATE TABLE IF NOT EXISTS _migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    } else {
        $db->exec("CREATE TABLE IF NOT EXISTS _migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    $applied = $db->query("SELECT name FROM _migrations")->fetchAll(PDO::FETCH_COLUMN);

    $migrations = [
        '001_clients' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                $db->exec("CREATE TABLE IF NOT EXISTS clients (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    company_id INT NOT NULL DEFAULT 1,
                    name VARCHAR(200) NOT NULL,
                    phone VARCHAR(50) DEFAULT '',
                    email VARCHAR(200) DEFAULT '',
                    address TEXT DEFAULT '',
                    notes TEXT DEFAULT '',
                    total_debt DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
                )");
            } else {
                $db->exec("CREATE TABLE IF NOT EXISTS clients (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    company_id INTEGER NOT NULL DEFAULT 1,
                    name VARCHAR(200) NOT NULL,
                    phone VARCHAR(50) DEFAULT '',
                    email VARCHAR(200) DEFAULT '',
                    address TEXT DEFAULT '',
                    notes TEXT DEFAULT '',
                    total_debt DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
                )");
            }
        },
        '002_debt_payments' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                $db->exec("CREATE TABLE IF NOT EXISTS debt_payments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    client_id INT NOT NULL,
                    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    type VARCHAR(20) NOT NULL DEFAULT 'payment',
                    notes TEXT DEFAULT '',
                    payment_date DATE NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
                )");
            } else {
                $db->exec("CREATE TABLE IF NOT EXISTS debt_payments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    client_id INTEGER NOT NULL,
                    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    type TEXT NOT NULL DEFAULT 'payment',
                    notes TEXT DEFAULT '',
                    payment_date DATE NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
                )");
            }
        },
        '003_add_client_to_sales' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                try { $db->exec("ALTER TABLE sales ADD COLUMN client_id INT DEFAULT NULL"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE sales ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'paid'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE sales ADD COLUMN pending_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00"); } catch (Exception $e) {}
            } else {
                $info = $db->query("PRAGMA table_info(sales)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('client_id', $info)) {
                    $db->exec("ALTER TABLE sales ADD COLUMN client_id INTEGER DEFAULT NULL");
                }
                if (!in_array('payment_status', $info)) {
                    $db->exec("ALTER TABLE sales ADD COLUMN payment_status TEXT NOT NULL DEFAULT 'paid'");
                }
                if (!in_array('pending_amount', $info)) {
                    $db->exec("ALTER TABLE sales ADD COLUMN pending_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00");
                }
            }
        },
        '004_returns' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                $db->exec("CREATE TABLE IF NOT EXISTS returns (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sale_id INT NOT NULL,
                    company_id INT NOT NULL DEFAULT 1,
                    return_type VARCHAR(20) NOT NULL DEFAULT 'refund',
                    reason TEXT NOT NULL,
                    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
                    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
                )");
                $db->exec("CREATE TABLE IF NOT EXISTS return_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    return_id INT NOT NULL,
                    product_id INT NOT NULL,
                    product_name VARCHAR(200) NOT NULL,
                    quantity INT NOT NULL DEFAULT 1,
                    unit_price DECIMAL(10,2) NOT NULL,
                    subtotal DECIMAL(10,2) NOT NULL,
                    action VARCHAR(20) NOT NULL DEFAULT 'restock',
                    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE
                )");
            } else {
                $db->exec("CREATE TABLE IF NOT EXISTS returns (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    sale_id INTEGER NOT NULL,
                    company_id INTEGER NOT NULL DEFAULT 1,
                    return_type TEXT NOT NULL DEFAULT 'refund',
                    reason TEXT NOT NULL,
                    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
                    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
                )");
                $db->exec("CREATE TABLE IF NOT EXISTS return_items (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    return_id INTEGER NOT NULL,
                    product_id INTEGER NOT NULL,
                    product_name TEXT NOT NULL,
                    quantity INTEGER NOT NULL DEFAULT 1,
                    unit_price DECIMAL(10,2) NOT NULL,
                    subtotal DECIMAL(10,2) NOT NULL,
                    action TEXT NOT NULL DEFAULT 'restock',
                    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE
                )");
            }
        },
        '005_add_company_branding' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                try { $db->exec("ALTER TABLE companies ADD COLUMN theme VARCHAR(50) NOT NULL DEFAULT 'queenshop'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN store_name VARCHAR(200) NOT NULL DEFAULT 'QueenShop'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN logo VARCHAR(255) NOT NULL DEFAULT 'logo.svg'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN primary_color VARCHAR(7) NOT NULL DEFAULT '#ffc107'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN description TEXT DEFAULT ''"); } catch (Exception $e) {}
                // Seed WolfStor if not exists
                $cnt = $db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
                if ($cnt < 3) {
                    $db->exec("INSERT IGNORE INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES (3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos')");
                }
            } else {
                $info = $db->query("PRAGMA table_info(companies)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('theme', $info)) $db->exec("ALTER TABLE companies ADD COLUMN theme TEXT NOT NULL DEFAULT 'queenshop'");
                if (!in_array('store_name', $info)) $db->exec("ALTER TABLE companies ADD COLUMN store_name TEXT NOT NULL DEFAULT 'QueenShop'");
                if (!in_array('logo', $info)) $db->exec("ALTER TABLE companies ADD COLUMN logo TEXT NOT NULL DEFAULT 'logo.svg'");
                if (!in_array('primary_color', $info)) $db->exec("ALTER TABLE companies ADD COLUMN primary_color TEXT NOT NULL DEFAULT '#ffc107'");
                if (!in_array('description', $info)) $db->exec("ALTER TABLE companies ADD COLUMN description TEXT DEFAULT ''");
                $cnt = $db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
                if ($cnt < 3) {
                    $db->exec("INSERT INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES (3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos')");
                }
            }
        },
        '006_user_companies' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                try { $db->exec("ALTER TABLE companies ADD COLUMN theme VARCHAR(50) NOT NULL DEFAULT 'queenshop'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN store_name VARCHAR(200) NOT NULL DEFAULT 'QueenShop'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN logo VARCHAR(255) NOT NULL DEFAULT 'logo.svg'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN primary_color VARCHAR(7) NOT NULL DEFAULT '#ffc107'"); } catch (Exception $e) {}
                try { $db->exec("ALTER TABLE companies ADD COLUMN description TEXT DEFAULT ''"); } catch (Exception $e) {}
                // Seed WolfStor if not exists
                $cnt = $db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
                if ($cnt < 3) {
                    $db->exec("INSERT IGNORE INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES (3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos')");
                }
            } else {
                $info = $db->query("PRAGMA table_info(companies)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('theme', $info)) $db->exec("ALTER TABLE companies ADD COLUMN theme TEXT NOT NULL DEFAULT 'queenshop'");
                if (!in_array('store_name', $info)) $db->exec("ALTER TABLE companies ADD COLUMN store_name TEXT NOT NULL DEFAULT 'QueenShop'");
                if (!in_array('logo', $info)) $db->exec("ALTER TABLE companies ADD COLUMN logo TEXT NOT NULL DEFAULT 'logo.svg'");
                if (!in_array('primary_color', $info)) $db->exec("ALTER TABLE companies ADD COLUMN primary_color TEXT NOT NULL DEFAULT '#ffc107'");
                if (!in_array('description', $info)) $db->exec("ALTER TABLE companies ADD COLUMN description TEXT DEFAULT ''");
                $cnt = $db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
                if ($cnt < 3) {
                    $db->exec("INSERT INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES (3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos')");
                }
            }
        },
        '007_category_company_id' => function () use ($db, $isMySQL) {
            if ($isMySQL) {
                try { $db->exec("ALTER TABLE categories ADD COLUMN company_id INT DEFAULT NULL"); } catch (Exception $e) {}
                $db->exec("UPDATE categories SET company_id = 1 WHERE company_id IS NULL");
                $db->exec("INSERT IGNORE INTO categories (name, company_id) VALUES
                    ('Sneakers', 3), ('Botas', 3), ('Zapatillas', 3), ('Sandalias', 3),
                    ('Zapatos de Vestir', 3), ('Deportivos', 3), ('Ojotas', 3), ('Otros', 3)");
            } else {
                $info = $db->query("PRAGMA table_info(categories)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('company_id', $info)) {
                    $db->exec("ALTER TABLE categories ADD COLUMN company_id INTEGER DEFAULT NULL");
                }
                $db->exec("UPDATE categories SET company_id = 1 WHERE company_id IS NULL");
                $db->exec("INSERT OR IGNORE INTO categories (name, company_id) VALUES
                    ('Sneakers', 3), ('Botas', 3), ('Zapatillas', 3), ('Sandalias', 3),
                    ('Zapatos de Vestir', 3), ('Deportivos', 3), ('Ojotas', 3), ('Otros', 3)");
            }
        },
        '005b_seed_wolfstor' => function () use ($db, $isMySQL) {
            // Seed WolfStor for databases where migration 005 already ran without the seed
            $cnt = $db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
            if ($cnt < 3) {
                if ($isMySQL) {
                    $db->exec("INSERT IGNORE INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES (3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos')");
                } else {
                    $db->exec("INSERT OR IGNORE INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES (3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos')");
                }
            }
            // Also ensure user_companies has WolfStor access for existing users
            try {
                $db->exec("INSERT OR IGNORE INTO user_companies (user_id, company_id, role) VALUES (1, 3, 'user'), (3, 3, 'user')");
            } catch (Exception $e) {
                // user_companies table might not exist yet — that's fine
            }
        },
    ];

    foreach ($migrations as $name => $callback) {
        if (!in_array($name, $applied, true)) {
            $callback();
            $db->prepare("INSERT INTO _migrations (name) VALUES (?)")->execute([$name]);
        }
    }
}
