CREATE DATABASE IF NOT EXISTS clothstore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE clothstore;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- готовий адмін
-- email: admin@clothstore.com
-- password: admin123

INSERT INTO users (name, email, password, role)
VALUES (
    'Administrator',
    'admin@clothstore.com',
    '$2y$10$7D4r0Gm6k9S2i1Q7xv3D1uXjz0M2.vrV1o8rjv9E6wC4k6sW5Wn5K',
    'admin'
)
ON DUPLICATE KEY UPDATE email = email;