SET FOREIGN_KEY_CHECKS = 0;

-- 1. LIMPEZA TOTAL
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_ingredients;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS ingredients;
DROP TABLE IF EXISTS users;

-- 2. TABELAS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('cliente', 'admin', 'staff') DEFAULT 'cliente',
    verification_code VARCHAR(6) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE product_ingredients (
    product_id INT,
    ingredient_id INT,
    PRIMARY KEY (product_id, ingredient_id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pendente', 'em_preparacao', 'entregue', 'pago') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    price_at_purchase DECIMAL(10, 2) NOT NULL,
    custom_ingredients TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 3. DADOS INICIAIS (UTILIZADORES)
-- Password para Admin e Staff é: 12345
INSERT INTO users (full_name, email, phone_number, username, password_hash, role, is_verified) VALUES 
('Administrador', 'admin@saltflow.com', '910000000', 'admin', '$2y$10$8.uX/5g/eHz.Z2q.W.u/..p/..p/..p/..p/..p/..p/..p', 'admin', TRUE),
('Staff Member', 'staff@saltflow.com', '920000000', 'staff', '$2y$10$8.uX/5g/eHz.Z2q.W.u/..p/..p/..p/..p/..p/..p/..p', 'staff', TRUE);

-- 4. CATEGORIAS
INSERT INTO categories (id, name, slug) VALUES
(1, 'Toasts', 'tostas'), (2, 'Burgers', 'burgers'), (3, 'Salads', 'saladas'),
(4, 'Wraps', 'wraps'), (5, 'Snacks', 'petiscos'), (6, 'Bloodletting', 'bloodletting'),
(7, 'Beer', 'beer'), (8, 'Cocktails', 'coktail'), (9, 'Juice', 'juice'),
(10, 'Water', 'water'), (11, 'Coffee', 'coffee'), (12, 'Wine', 'wine');

-- 5. INGREDIENTES
INSERT INTO ingredients (id, name) VALUES 
(1, 'Rocket'), (2, 'Mozzarella'), (3, 'Tomato'), (4, 'Pesto'), 
(5, 'Cured Ham'), (6, 'Cheese'), (7, 'Balsamic'), 
(8, 'Grilled Chicken'), (9, 'Bacon'), (10, 'Lettuce'), (11, 'Special Mayo'),
(12, 'Chicken Burger'), (13, 'Angus Burger'), (14, 'Cheddar'), (15, 'Special Sauce'),
(16, 'Hummus'), (17, 'Cucumber'), (18, 'Falafel'), (19, 'Capers'),
(20, 'Bean Paté'), (21, 'Croutons'), (22, 'Guacamole'),
(23, 'Shrimps'), (24, 'Garlic'), (25, 'Lupin Beans'), (26, 'Peanuts'), (27, 'Tapenade');

-- 6. PRODUTOS COMPLETOS
INSERT INTO products (id, category_id, name, description, price, is_active) VALUES
(1, 1, 'Italiana', 'Rocket, mozzarella, tomato, pesto', 8.00, 1),
(2, 1, 'Portuguesa', 'Cured ham, rocket, cheese, balsamic', 9.00, 1),
(3, 1, 'Chicken & Bacon', 'Grilled Chicken, bacon, lettuce, special mayonnaise', 9.00, 1),
(4, 2, 'Crispy Chicken', 'Chicken burger, lettuce, tomato, cheese', 11.00, 1),
(5, 2, 'Angus', 'Angus burger, cheddar, iceberg lettuce, special sauce', 13.50, 1),
(6, 3, 'Lebanesa', 'Hummus, salad mix, cucumber, tomato, falafel, capers', 12.00, 1),
(7, 3, 'Chicken Chic', 'Bean paté, salad mix, chicken, mozzarella, croutons', 12.00, 1),
(8, 4, 'Hummus & Falafel', 'Wrap with hummus and falafel', 8.00, 1),
(9, 4, 'Guacamole & Chicken', 'Wrap with guacamole and chicken', 8.00, 1),
(10, 5, 'Gambas al Ajillo', 'Shrimps sautéed with garlic', 16.00, 1),
(11, 5, 'Lupin / Peanuts', 'Salted lupin beans / Salted peanuts', 2.00, 1),
(12, 5, 'Dips', 'Hummus / Tapenade / Guacamole', 3.00, 1),
(13, 6, 'Bloodletting 1L', 'Sangria 1 Litro', 25.00, 1),
(14, 6, 'Bloodletting 2L', 'Sangria 2 Litros', 37.00, 1),
(15, 6, 'Bloodletting Cup', 'Copo de Sangria', 7.00, 1),
(16, 7, 'Draft 20cl', '20cl', 2.20, 1),
(17, 7, 'Draft 33cl', '33cl', 3.30, 1),
(18, 7, 'Draft 50cl', '50cl', 5.00, 1),
(19, 7, 'Bottled Gluten free', 'Gluten free', 3.30, 1),
(20, 7, 'Bottled Stout', 'Stout', 3.30, 1),
(21, 7, 'Bottled 0% Alcohol', '0% Alcohol', 3.30, 1),
(22, 7, 'Bottled Inedit', '75cl', 10.00, 1),
(23, 7, 'Bottled Original', 'Original', 3.30, 1),
(24, 7, 'Bottled Lemon', 'Lemon', 2.50, 1),
(25, 8, 'Frozen Pinacolada', '', 10.00, 1),
(26, 8, 'Margarita', '', 8.00, 1),
(27, 8, 'Aperol spritz', '', 7.50, 1),
(28, 8, 'Mojito', '', 8.00, 1),
(29, 8, 'Flavoured Mojito', '', 8.50, 1),
(30, 8, 'House Gin', '', 7.50, 1),
(31, 8, 'Premium Gin', '', 11.00, 1),
(32, 8, 'Caipirinha', '', 7.50, 1),
(33, 8, 'Caipirosca', '', 7.50, 1),
(34, 8, 'Moscow Mule', '', 8.00, 1),
(35, 8, 'Tonic Porto', '', 7.00, 1),
(36, 8, 'Shots', 'Unit', 3.00, 1),
(37, 9, 'Daily Juice', '', 4.50, 1),
(38, 9, 'Oranje juice', '', 4.00, 1),
(39, 9, 'Lemonade', '', 4.00, 1),
(40, 9, 'Guaraná', '', 2.50, 1),
(41, 9, '7up', '', 2.50, 1),
(42, 9, 'Coca Cola', '', 2.50, 1),
(43, 10, 'Bottle 0.5L', '0.5L', 1.80, 1),
(44, 10, 'Pedras', '', 2.00, 1),
(45, 11, 'Coffee', '', 1.20, 1),
(46, 12, 'Glass of wine', '', 5.00, 1);

-- 7. LIGAÇÕES INGREDIENTES
INSERT INTO product_ingredients (product_id, ingredient_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),
(2, 5), (2, 1), (2, 6), (2, 7),
(3, 8), (3, 9), (3, 10), (3, 11),
(4, 12), (4, 10), (4, 3), (4, 6),
(5, 13), (5, 14), (5, 10), (5, 15),
(6, 16), (6, 10), (6, 17), (6, 3), (6, 18), (6, 19),
(7, 20), (7, 10), (7, 8), (7, 2), (7, 21),
(8, 16), (8, 18),
(9, 22), (9, 8),
(10, 23), (10, 24),
(11, 25), (11, 26),
(12, 16), (12, 27), (12, 22);

SET FOREIGN_KEY_CHECKS = 1;