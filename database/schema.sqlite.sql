-- Esquema de base de datos para QueenShop MVC (SQLite)
-- Usado para desarrollo local con sqlite

CREATE TABLE IF NOT EXISTS companies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT '',
    purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INTEGER NOT NULL DEFAULT 0,
    category_id INTEGER,
    image VARCHAR(255) DEFAULT NULL,
    company_id INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sales (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    final_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    item_count INTEGER NOT NULL DEFAULT 0,
    client_id INTEGER DEFAULT NULL,
    payment_status TEXT NOT NULL DEFAULT 'paid',
    pending_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    company_id INTEGER NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sale_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS clients (
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
);

CREATE TABLE IF NOT EXISTS debt_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    type TEXT NOT NULL DEFAULT 'payment',
    notes TEXT DEFAULT '',
    payment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS returns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sale_id INTEGER NOT NULL,
    company_id INTEGER NOT NULL DEFAULT 1,
    return_type TEXT NOT NULL DEFAULT 'refund',
    reason TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS return_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    return_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    action TEXT NOT NULL DEFAULT 'restock',
    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    company_id INTEGER NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Categorías por defecto
INSERT OR IGNORE INTO categories (id, name) VALUES (1, 'Alimentos');
INSERT OR IGNORE INTO categories (id, name) VALUES (2, 'Juguetes');
INSERT OR IGNORE INTO categories (id, name) VALUES (3, 'Higiene');
INSERT OR IGNORE INTO categories (id, name) VALUES (4, 'Accesorios');
INSERT OR IGNORE INTO categories (id, name) VALUES (5, 'Medicamentos');
INSERT OR IGNORE INTO categories (id, name) VALUES (6, 'Ropa');
INSERT OR IGNORE INTO categories (id, name) VALUES (7, 'Otros');

-- Seed: Empresas de demostración
INSERT OR IGNORE INTO companies (id, name) VALUES (1, 'QueenShop Norte');
INSERT OR IGNORE INTO companies (id, name) VALUES (2, 'QueenShop Sur');

-- Seed: Usuarios de demostración (contraseña = 123456 con BCrypt)
INSERT OR IGNORE INTO users (id, company_id, username, password, role) VALUES (1, 1, 'norte', '$2y$10$ASymD4N/TIeFjIaAlZ6R8ejsy4Rw84S5MG69r4mCRFMmvIERgpAN2', 'user');
INSERT OR IGNORE INTO users (id, company_id, username, password, role) VALUES (2, 2, 'sur', '$2y$10$ASymD4N/TIeFjIaAlZ6R8ejsy4Rw84S5MG69r4mCRFMmvIERgpAN2', 'user');
INSERT OR IGNORE INTO users (id, company_id, username, password, role) VALUES (3, 1, 'admin', '$2y$10$ASymD4N/TIeFjIaAlZ6R8ejsy4Rw84S5MG69r4mCRFMmvIERgpAN2', 'admin');
