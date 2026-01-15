-- 1. Criação da Base de Dados
CREATE DATABASE IF NOT EXISTS `saltflow_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `saltflow_db`;

-- --------------------------------------------------------
-- 2. Criação das Tabelas (Ordem lógica para respeitar dependências)
-- --------------------------------------------------------

-- Tabela de Categorias
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Ingredientes
CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Utilizadores
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('cliente','staff','cozinha','admin','configurador') DEFAULT 'cliente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Produtos
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Relação Produto-Ingrediente (Muitos para Muitos)
CREATE TABLE `product_ingredients` (
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`ingredient_id`),
  KEY `ingredient_id` (`ingredient_id`),
  CONSTRAINT `product_ingredients_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `product_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Encomendas
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `table_number` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('aguardando_confirmacao','pendente','em_preparacao','entregue','pago') DEFAULT 'aguardando_confirmacao',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `daily_order_number` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Itens da Encomenda
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `custom_ingredients` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Logs (Auditoria)
CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Inserção de Dados
-- --------------------------------------------------------

-- Categorias
INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Tostas', 'tostas'), (2, 'Burgers', 'burgers'), (3, 'Saladas', 'saladas'),
(4, 'Wraps', 'wraps'), (5, 'Petiscos', 'petiscos'), (6, 'Sangria', 'sangria'),
(7, 'Beer', 'beer'), (8, 'Cocktails', 'coktail'), (9, 'Sumo', 'sumo'),
(10, 'Água', 'agua'), (11, 'Café', 'cafe'), (12, 'Vinho', 'vinho');

-- Ingredientes
INSERT INTO `ingredients` (`id`, `name`, `is_deleted`) VALUES
(1, 'Rúcula', 0), (2, 'Mozzarella', 0), (3, 'Tomate', 0), (4, 'Pesto', 0),
(5, 'Presunto', 0), (6, 'Brie', 0), (7, 'Balsâmico Morango', 0), (8, 'Bife de Frango grelhado', 0),
(9, 'Bacon', 0), (10, 'Alface', 0), (11, 'Maionese especial', 0), (12, 'Hambúrguer de frango', 0),
(13, 'Hambúrguer Angus', 0), (14, 'Cheddar', 0), (15, 'Molho especial', 0), (16, 'Hummus', 0),
(17, 'Pepino', 0), (18, 'Falafel', 0), (19, 'Alcaparras', 0), (20, 'Paté de feijão', 0),
(21, 'Croutons', 0), (22, 'Guacamole', 0), (23, 'Gambas', 0), (24, 'Alho', 0),
(25, 'Tremoços', 0), (26, 'Amendoins', 0), (27, 'Tapenade', 0), (28, 'Couve Coração', 0);

-- Produtos
INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`) VALUES
(1, 1, 'Italiana', 'Rúcula, mozzarella, tomate, pesto', 8.00),
(2, 1, 'Portuguesa', 'Presunto, rúcula, brie, balsâmico morango', 9.00),
(5, 2, 'Angus', 'Hambúrguer Angus, cheddar, alface iceberg, molho especial', 13.50),
(6, 3, 'Libanesa', 'Hummus, mix de saladas, pepino, couve coração,tomate ,falafel ,alcaparras', 12.00);


-- Relação Produtos e Ingredientes
INSERT INTO `product_ingredients` (`product_id`, `ingredient_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),
(2, 1), (2, 5), (2, 6), (2, 7);

-- Encomendas e Itens
INSERT INTO `orders` (`id`, `user_id`, `table_number`, `total_price`, `status`, `created_at`, `daily_order_number`) VALUES
(1, 3, 5, 8.00, 'entregue', '2026-01-13 17:22:32', 0),
(6, NULL, 2, 9.00, 'em_preparacao', '2026-01-14 12:35:22', 1);

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`, `custom_ingredients`) VALUES
(1, 1, 1, 1, 8.00, 'Rúcula, Mozzarella, Tomate, Pesto'),
(6, 6, 2, 1, 9.00, 'Rúcula, Presunto, Brie, Balsâmico Morango');

-- Logs
INSERT INTO `logs` (`user_id`, `action`, `details`, `created_at`) VALUES
(3, 'Nova Encomenda', 'Encomenda #1 criada para a mesa 5', '2026-01-13 17:22:32');

COMMIT;