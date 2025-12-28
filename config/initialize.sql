
CREATE DATABASE IF NOT EXISTS ecommerce_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE ecommerce_db;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    phone VARCHAR(20),
    photo VARCHAR(255),
    country VARCHAR(100),
    city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    short_description VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    discount DECIMAL(5,2) DEFAULT 0.00,
    image VARCHAR(255),
    category_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
);


CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    CONSTRAINT fk_cart_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_cart_items_cart
        FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_items_product
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending','approved','delivered','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);


CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending','paid') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    amount DECIMAL(15,2) NOT NULL,
    provider VARCHAR(100) NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    CONSTRAINT fk_payments_order
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_user
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_product
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

INSERT INTO users (first_name, last_name, email, password, role, country, city) VALUES
('Waad', 'Nayef', 'waad@gmail.com', 'pass123', 'admin', 'Jordan', 'Amman'),
('Motaz', 'Alnaser', 'motaz@gmail.com', 'pass123', 'admin', 'Jordan', 'Amman'),
('Lina', 'Haddad', 'lina@gmail.com', 'pass123', 'user', 'Jordan', 'Irbid'),
('Omar', 'Khaled', 'omar@gmail.com', 'pass123', 'user', 'Jordan', 'Aqaba'),
('Admin', 'User', 'admin@gmail.com', 'pass123', 'user', 'Jordan', 'Amman');

INSERT INTO categories (name, description) VALUES
('Electronics', 'Phones and gadgets'),
('Clothing', 'Men and women clothing'),
('Books', 'Books and novels'),
('Home Appliances', 'Home appliances');

INSERT INTO products (name, description, short_description, price, stock, category_id) VALUES
('iPhone 15', 'The latest Apple iPhone 15 with advanced camera features, high performance, and long battery life.', 'Apple iPhone 15 full-featured smartphone', 1200, 30, 1),
('Samsung S24', 'Samsung Galaxy S24 offers cutting-edge technology, a stunning display, and powerful performance.', 'Samsung Galaxy S24 advanced smartphone', 1000, 25, 1),
('Laptop HP', 'HP Laptop with high-speed processor, large storage, and exceptional graphics for work and play.', 'High-performance HP laptop for all tasks', 900, 20, 1),
('Earbuds', 'Wireless earbuds with crystal clear sound, long-lasting battery, and comfortable fit for daily use.', 'Comfortable wireless earbuds with long battery life', 80, 50, 1),
('Smart Watch', 'Fitness-focused smart watch tracking health, workouts, notifications, and stylish enough for daily wear.', 'Smart watch with fitness tracking and notifications', 150, 40, 1),

('T-Shirt', 'Comfortable cotton t-shirt suitable for casual wear, available in multiple colors and sizes.', 'Stylish cotton t-shirt for daily casual wear', 20, 100, 2),
('Jeans', 'Durable blue jeans made from high-quality denim offering comfort, style, and long-lasting wear.', 'Comfortable and stylish blue denim jeans', 40, 80, 2),
('Jacket', 'Warm winter jacket designed for extreme weather conditions with stylish design and functional pockets.', 'Warm winter jacket with functional pockets', 70, 50, 2),
('Dress', 'Elegant women’s dress perfect for special occasions, parties, or casual outings with a modern touch.', 'Elegant women dress suitable for parties and events', 60, 40, 2),
('Sneakers', 'Lightweight running sneakers providing comfort, durability, and modern design for sports or casual wear.', 'Lightweight sneakers for running and casual wear', 90, 60, 2),

('Harry Potter', 'A magical fantasy novel from the Harry Potter series filled with adventure, mystery, and friendship.', 'Magical fantasy novel full of adventure and mystery', 25, 70, 3),
('Clean Code', 'Comprehensive programming guide teaching best practices, coding standards, and software craftsmanship.', 'Programming guide teaching best coding practices', 45, 30, 3),
('SQL Guide', 'Detailed SQL guide covering database queries, optimization techniques, and practical examples for developers.', 'Complete SQL guide for database developers', 35, 40, 3),
('Novel X', 'A gripping drama novel with unexpected twists, deep characters, and an engaging storyline.', 'Drama novel with deep characters and engaging plot', 20, 60, 3),
('Kids Story', 'A delightful collection of children’s stories filled with fun, morals, and imagination.', 'Fun and educational stories for children', 15, 80, 3),

('Microwave', 'High-performance kitchen microwave with multiple cooking modes, easy-to-use controls, and safety features.', 'Kitchen microwave with multiple cooking modes', 200, 15, 4),
('Blender', 'Powerful food blender capable of handling smoothies, sauces, and other recipes efficiently.', 'High-power blender for smoothies and sauces', 80, 25, 4),
('Vacuum Cleaner', 'Efficient vacuum cleaner designed to clean carpets, floors, and hard-to-reach areas effortlessly.', 'Vacuum cleaner for thorough home cleaning', 180, 20, 4),
('Electric Kettle', 'Fast-boiling electric kettle made of stainless steel with safety shut-off and convenient design.', 'Electric kettle with fast boiling and safety features', 50, 30, 4),
('Air Fryer', 'Healthy air fryer allowing oil-free cooking of your favorite meals with ease and excellent results.', 'Oil-free air fryer for healthy cooking', 220, 10, 4);

INSERT INTO cart (user_id) VALUES (1),(2),(3),(4),(5);

INSERT INTO cart_items (cart_id, product_id, quantity, total_price) VALUES
(1, 1, 1, 1200),
(1, 4, 1, 80),
(2, 6, 2, 40),
(3, 11, 1, 25),
(4, 16, 1, 200);

INSERT INTO orders (user_id, total_price, status) VALUES
(1, 1200, 'approved'),
(2, 80, 'delivered'),
(3, 45, 'pending'),
(4, 150, 'approved'),
(1, 90, 'delivered'),
(2, 200, 'pending'),
(3, 60, 'approved'),
(4, 25, 'delivered'),
(1, 220, 'approved'),
(2, 40, 'pending');

INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES
(1, 1, 1, 1200),
(2, 4, 1, 80),
(3, 12, 1, 45),
(4, 5, 1, 150),
(5, 10, 1, 90),
(6, 16, 1, 200),
(7, 9, 1, 60),
(8, 11, 1, 25),
(9, 20, 1, 220),
(10, 6, 2, 40);

INSERT INTO payments (order_id, user_id, status, paid_at, amount, provider, payment_method) VALUES
(1, 1, 'paid', NOW(), 1200, 'Visa', 'Credit Card'),
(2, 2, 'paid', NOW(), 80, 'Cash', 'Cash on Delivery'),
(4, 4, 'paid', NOW(), 150, 'Visa', 'Credit Card'),
(5, 1, 'paid', NOW(), 90, 'Mastercard', 'Credit Card'),
(9, 1, 'paid', NOW(), 220, 'Visa', 'Credit Card');

INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(1, 1, 5, 'Excellent product'),
(2, 6, 4, 'Good quality'),
(3, 11, 5, 'Loved this book'),
(4, 16, 4, 'Very useful');

