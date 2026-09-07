CREATE DATABASE IF NOT EXISTS nsbm_marketplace;

USE nsbm_marketplace;

DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;



-- USERS TABLE

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIES TABLE

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- PRODUCTS TABLE

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    category_id INT NOT NULL,

    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    image VARCHAR(255),

    location VARCHAR(150),

    status ENUM('pending', 'approved', 'rejected', 'sold')
        DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT
);

-- ORDERS TABLE


CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,

    buyer_id INT NOT NULL,

    total_amount DECIMAL(10,2) NOT NULL,

    status ENUM('pending', 'completed', 'cancelled')
        DEFAULT 'completed',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (buyer_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


-- ORDER ITEMS

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    order_id INT NOT NULL,
    product_id INT NOT NULL,
    seller_id INT NOT NULL,

    price DECIMAL(10,2) NOT NULL,
    quantity INT DEFAULT 1 ,

    FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE RESTRICT,

    FOREIGN KEY (seller_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- CART ITEMS

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,

    UNIQUE (user_id, product_id),

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
);


-- ADDED SUBTOTAL COLUMN
ALTER TABLE order_items
ADD COLUMN subtotal DECIMAL(10,2) NOT NULL DEFAULT 1;

-- ADDED QUANTITY COLUMN
ALTER TABLE products
ADD COLUMN quantity INT NOT NULL DEFAULT 1;

-- ADDED CATEGORIES TO CATEGORIES TABLE
INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic devices and accessories'),
('Books', 'Textbooks, novels and other books'),
('Clothing', 'Clothes, shoes and fashion items'),
('Food', 'Food and homemade products'),
('Other', 'Other products');

-- ADDED SAMPLE USERS TO TEST THE SYSTEM
INSERT INTO users (name, email, password, phone, role, status) VALUES
(
    'Test Admin',
    'admin@test.local',
    '$2y$12$wZMfG0fmZ6yjWamL32EVrurNoHknqv3G6QgZtstQ9To9R2S85cWVW',
    '0700000001',
    'admin',
    'active'
),
(
    'Test Student One',
    'student1@test.local',
    '$2y$12$wZMfG0fmZ6yjWamL32EVrurNoHknqv3G6QgZtstQ9To9R2S85cWVW',
    '0700000002',
    'user',
    'active'
),
(
    'Test Student Two',
    'student2@test.local',
    '$2y$12$wZMfG0fmZ6yjWamL32EVrurNoHknqv3G6QgZtstQ9To9R2S85cWVW',
    '0700000003',
    'user',
    'active'
),
(
    'Test Student Three',
    'student3@test.local',
    '$2y$12$wZMfG0fmZ6yjWamL32EVrurNoHknqv3G6QgZtstQ9To9R2S85cWVW',
    '0700000004',
    'user',
    'active'
);

-- ADDED SAMPLE PRODUCTS
INSERT INTO products (user_id, category_id, title, description, price, quantity, image, location, status) VALUES
(2, 1, 'Scientific Calculator', 'Casio calculator suitable for students', 3500.00, 5, 'null', 'NSBM Green University', 'approved'),
(2, 2, 'Programming Fundamentals Book', 'A book for programming students', 2500.00, 3, 'null', 'NSBM Green University', 'approved'),
(3, 1, 'Wireless Mouse', 'Logitech wireless mouse', 1800.00, 10, 'null', 'NSBM Green Universitiy', 'approved'),
(
    3,
    3,
    'University Hoodie',
    'Comfortable university hoodie in good condition.',
    4500.00,
    2,
    'hoodie.jpg',
    'Maharagama',
    'pending'
),

(
    4,
    2,
    'A4 Notebook Pack',
    'Pack of high-quality A4 notebooks suitable for university studies.',
    1200.00,
    10,
    'notebooks.jpg',
    'Kottawa',
    'approved'
),

(
    4,
    4,
    'Homemade Brownies',
    'Fresh homemade brownies prepared for university students.',
    1500.00,
    6,
    'brownies.jpg',
    'NSBM Green University',
    'pending'
);


