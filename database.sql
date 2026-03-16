CREATE DATABASE IF NOT EXISTS clothing_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clothing_store;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@store.com', '$2y$10$e0NRM8Dq0n1l4yZ2aXxYdOeT0j2w0M3GcT4mWlQ7Bml7L9Ygk1Bum', 'admin');
-- пароль: admin123

INSERT INTO products (title, description, price, image, category) VALUES
('Чоловіче худі Urban Black', 'Стильне худі для щоденного носіння. Якісний матеріал, сучасний крій.', 1499.00, 'hoodie.jpg', 'Худі'),
('Базова футболка White Minimal', 'Біла універсальна футболка, підходить для будь-якого стилю.', 599.00, 'tshirt.jpg', 'Футболки'),
('Куртка Street Winter', 'Тепла куртка для осені та зими. Має сучасний молодіжний дизайн.', 2499.00, 'jacket.jpg', 'Куртки');