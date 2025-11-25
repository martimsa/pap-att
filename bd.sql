-- 1. COMEÇAR DO ZERO (Apaga a base antiga se existir)
DROP DATABASE IF EXISTS saltflow_db;
CREATE DATABASE saltflow_db;
USE saltflow_db;

-- 2. CRIAR AS TABELAS

-- Tabela de Utilizadores
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('cliente', 'admin', 'staff') DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Categorias
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL
);

-- Tabela de Produtos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Tabela de Ingredientes
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Tabela Pivot (Liga Produtos a Ingredientes)
CREATE TABLE product_ingredients (
    product_id INT,
    ingredient_id INT,
    PRIMARY KEY (product_id, ingredient_id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
);

-- Tabela de Pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pendente', 'em_preparacao', 'entregue', 'pago') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Itens do Pedido
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    price_at_purchase DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- 3. INSERIR DADOS (Com IDs forçados para evitar erros)

-- Inserir Categorias
INSERT INTO categories (id, name, slug) VALUES
(1, 'Toasts', 'tostas'),
(2, 'Burgers', 'burgers'),
(3, 'Salads', 'saladas'),
(4, 'Wraps', 'wraps'),
(5, 'Snacks', 'petiscos'),
(6, 'Bloodletting', 'bloodletting'),
(7, 'Beer', 'beer'),
(8, 'Cocktails', 'coktail'),
(9, 'Juice', 'juice'),
(10, 'Water', 'water'),
(11, 'Coffee', 'coffee'),
(12, 'Wine', 'wine');

-- Inserir Produtos (IDs explícitos de 1 a 40+)
INSERT INTO products (id, category_id, name, description, price) VALUES
-- Toasts (Cat 1)
(1, 1, 'Italiana', 'Rocket, mozzarella, tomato, pesto', 8.00),
(2, 1, 'Portuguesa', 'Cured ham, rocket, cheese, balsamic', 9.00),
(3, 1, 'Chicken & Bacon', 'Grilled Chicken, bacon, lettuce, special mayonnaise', 9.00),

-- Burgers (Cat 2)
(4, 2, 'Crispy Chicken', 'Chicken burger, lettuce, tomato, cheese', 11.00),
(5, 2, 'Angus', 'Angus burger, cheddar, iceberg lettuce, special sauce', 13.50),

-- Salads (Cat 3)
(6, 3, 'Lebanesa', 'Hummus, salad mix, cucumber, tomato, falafel, capers', 12.00),
(7, 3, 'Chicken Chic', 'Bean paté, salad mix, chicken, mozzarella, croutons', 12.00),

-- Wraps (Cat 4)
(8, 4, 'Hummus & Falafel', 'Wrap with hummus and falafel', 8.00),
(9, 4, 'Guacamole & Chicken', 'Wrap with guacamole and chicken', 8.00),

-- Snacks (Cat 5)
(10, 5, 'Gambas al Ajillo', 'Shrimps sautéed with garlic', 16.00),
(11, 5, 'Lupin / Peanuts', 'Salted lupin beans / Salted peanuts', 2.00),
(12, 5, 'Dips', 'Hummus / Tapenade / Guacamole', 3.00),

-- Bloodletting (Cat 6)
(13, 6, 'Bloodletting 1L', 'Sangria 1 Litro', 25.00),
(14, 6, 'Bloodletting 2L', 'Sangria 2 Litros', 37.00),
(15, 6, 'Bloodletting Cup', 'Copo de Sangria', 7.00),

-- Beer (Cat 7)
(16, 7, 'Draft 20cl', '20cl', 2.20),
(17, 7, 'Draft 33cl', '33cl', 3.30),
(18, 7, 'Draft 50cl', '50cl', 5.00),
(19, 7, 'Bottled Gluten free', 'Gluten free', 3.30),

-- Cocktails (Cat 8 - Exemplo)
(20, 8, 'Frozen Pinacolada', NULL, 10.00),
(21, 8, 'Margarita', NULL, 8.00),
(22, 8, 'Mojito', NULL, 8.00),

-- Juice (Cat 9)
(23, 9, 'Daily Juice', NULL, 4.50),
(24, 9, 'Coca Cola', NULL, 2.50),

-- Water (Cat 10)
(25, 10, 'Bottle 0.5L', '0.5L', 1.80),

-- Coffee (Cat 11)
(26, 11, 'Coffee', NULL, 1.20),

-- Wine (Cat 12)
(27, 12, 'Glass of wine', NULL, 5.00);

-- Inserir Ingredientes (IDs explícitos)
INSERT INTO ingredients (id, name) VALUES 
(1, 'Rocket'), (2, 'Mozzarella'), (3, 'Tomato'), (4, 'Pesto'), -- 1-4
(5, 'Cured Ham'), (6, 'Cheese'), (7, 'Balsamic'), -- 5-7
(8, 'Grilled Chicken'), (9, 'Bacon'), (10, 'Lettuce'), (11, 'Mayonnaise'), -- 8-11
(12, 'Chicken Burger'), (13, 'Angus Burger'), (14, 'Cheddar'), (15, 'Special Sauce'), -- 12-15
(16, 'Hummus'), (17, 'Cucumber'), (18, 'Falafel'), (19, 'Capers'), -- 16-19
(20, 'Bean Paté'), (21, 'Croutons'), (22, 'Guacamole'), -- 20-22
(23, 'Shrimps'), (24, 'Garlic'); -- 23-24

-- Ligar Produtos aos Ingredientes
-- Agora é seguro porque definimos os IDs manualmente acima
INSERT INTO product_ingredients (product_id, ingredient_id) VALUES 
-- Italiana (ID 1)
(1, 1), (1, 2), (1, 3), (1, 4),
-- Portuguesa (ID 2)
(2, 5), (2, 1), (2, 6), (2, 7),
-- Chicken & Bacon (ID 3)
(3, 8), (3, 9), (3, 10), (3, 11),
-- Crispy Chicken (ID 4)
(4, 12), (4, 10), (4, 3), (4, 6),
-- Angus (ID 5)
(5, 13), (5, 14), (5, 10), (5, 15),
-- Lebanesa (ID 6)
(6, 16), (6, 10), (6, 17), (6, 3), (6, 18), (6, 19),
-- Hummus Wrap (ID 8)
(8, 16), (8, 18),
-- Gambas (ID 10)
(10, 23), (10, 24);

-- 4. CRIAR UM UTILIZADOR DE TESTE (Opcional)
-- Password é '12345'
INSERT INTO users (full_name, email, phone_number, username, password_hash, role) 
VALUES ('Admin Teste', 'admin@saltflow.com', '910000000', 'admin', '$2y$10$8.uX/5g/eHz.Z2q.W.u/..p/..p/..p/..p/..p/..p/..p', 'admin');