-- Esquema de base de datos para QueenShop MVC (MySQL)
-- Usado en InfinityFree — importar desde phpMyAdmin

CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    theme VARCHAR(50) NOT NULL DEFAULT 'queenshop',
    store_name VARCHAR(200) NOT NULL DEFAULT 'QueenShop',
    logo VARCHAR(255) NOT NULL DEFAULT 'logo.svg',
    primary_color VARCHAR(7) NOT NULL DEFAULT '#ffc107',
    description TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT '',
    purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    category_id INT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    company_id INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    final_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    item_count INT NOT NULL DEFAULT 0,
    client_id INT DEFAULT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'paid',
    pending_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    company_id INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clients (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS debt_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    type VARCHAR(20) NOT NULL DEFAULT 'payment',
    notes TEXT DEFAULT '',
    payment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    company_id INT NOT NULL DEFAULT 1,
    return_type VARCHAR(20) NOT NULL DEFAULT 'refund',
    reason TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS return_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    return_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    action VARCHAR(20) NOT NULL DEFAULT 'restock',
    FOREIGN KEY (return_id) REFERENCES returns(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categorías por defecto
INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Alimentos'),
(2, 'Juguetes'),
(3, 'Higiene'),
(4, 'Accesorios'),
(5, 'Medicamentos'),
(6, 'Ropa'),
(7, 'Otros');

-- Seed: Empresas de demostración
INSERT IGNORE INTO companies (id, name, store_name, theme, logo, primary_color, description) VALUES
(1, 'QueenShop Norte', 'QueenShop Norte', 'queenshop', 'logo.svg', '#ffc107', 'Tienda de mascotas'),
(2, 'QueenShop Sur', 'QueenShop Sur', 'queenshop', 'logo.svg', '#ffc107', 'Tienda de mascotas'),
(3, 'WolfStor', 'WolfStor', 'wolfstor', 'wolfstor-logo.svg', '#2563eb', 'Tienda de zapatos');

-- Seed: Usuarios de demostración (contraseña = 123456 con BCrypt)
INSERT IGNORE INTO users (id, company_id, username, password, role) VALUES
(1, 1, 'norte', '$2y$10$ASymD4N/TIeFjIaAlZ6R8ejsy4Rw84S5MG69r4mCRFMmvIERgpAN2', 'user'),
(2, 2, 'sur', '$2y$10$ASymD4N/TIeFjIaAlZ6R8ejsy4Rw84S5MG69r4mCRFMmvIERgpAN2', 'user'),
(3, 1, 'admin', '$2y$10$ASymD4N/TIeFjIaAlZ6R8ejsy4Rw84S5MG69r4mCRFMmvIERgpAN2', 'admin');
