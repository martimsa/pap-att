-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08-Jan-2026 às 22:53
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `saltflow_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Tostas', 'tostas'),
(2, 'Burgers', 'burgers'),
(3, 'Saladas', 'saladas'),
(4, 'Wraps', 'wraps'),
(5, 'Petiscos', 'petiscos'),
(6, 'Sangria', 'sangria'),
(7, 'Beer', 'beer'),
(8, 'Cocktails', 'coktail'),
(9, 'Sumo', 'sumo'),
(10, 'Água', 'agua'),
(11, 'Café', 'cafe'),
(12, 'Vinho', 'vinho');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `is_deleted`) VALUES
(1, 'Rúcula', 0),
(2, 'Mozzarella', 0),
(3, 'Tomate', 0),
(4, 'Pesto', 0),
(5, 'Presunto', 0),
(6, 'Brie', 0),
(7, 'Balsâmico Morango', 0),
(8, 'Bife de Frango grelhado', 0),
(9, 'Bacon', 0),
(10, 'Alface', 0),
(11, 'Maionese especial', 0),
(12, 'Hambúrguer de frango', 0),
(13, 'Hambúrguer Angus', 0),
(14, 'Cheddar', 0),
(15, 'Molho especial', 0),
(16, 'Hummus', 0),
(17, 'Pepino', 0),
(18, 'Falafel', 0),
(19, 'Alcaparras', 0),
(20, 'Paté de feijão', 0),
(21, 'Croutons', 0),
(22, 'Guacamole', 0),
(23, 'Gambas', 0),
(24, 'Alho', 0),
(25, 'Tremoços', 0),
(26, 'Amendoins', 0),
(27, 'Tapenade', 0),
(28, 'Couve Coração', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_number` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('aguardando_confirmacao','pendente','em_preparacao','entregue','pago') DEFAULT 'aguardando_confirmacao',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `custom_ingredients` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `is_active`, `is_deleted`) VALUES
(1, 1, 'Italiana', 'Rúcula, mozzarella, tomate, pesto', 8.00, 1, 0),
(2, 1, 'Portuguesa', 'Presunto, rúcula, brie, balsâmico morango', 9.00, 1, 0),
(3, 1, 'Frango & Bacon', 'Bife de frango grelhado, bacon, alface, maionese especial', 9.00, 1, 0),
(4, 2, 'Crispy Chicken', 'Hambúrguer de frango, alface, tomate, queijo', 11.00, 1, 0),
(5, 2, 'Angus', 'Hambúrguer Angus, cheddar, alface iceberg, molho especial', 13.50, 1, 0),
(6, 3, 'Libanesa', 'Hummus, mix de saladas, pepino, couve coração,tomate ,falafel ,alcaparras', 12.00, 1, 0),
(7, 3, 'Chicken Chic', 'Paté de feijão, salad mix, frango, mozzarella, croutons', 12.00, 1, 0),
(8, 4, 'Hummus & Falafel', 'Wrap com hummus e falafel', 8.00, 1, 0),
(9, 4, 'Guacamole & Frango', 'Wrap com guacamole e frango', 8.00, 1, 0),
(10, 5, 'Gambas al Ajillo', '', 16.00, 1, 0),
(11, 5, 'Tremoços / Amendoins', '', 2.00, 1, 0),
(12, 5, 'Molhos', 'Hummus / Tapenade / Guacamole', 3.00, 1, 0),
(13, 6, '1 Litro', '', 25.00, 1, 0),
(14, 6, '2 Litros', '', 37.00, 1, 0),
(15, 6, 'Copo', '', 7.00, 1, 0),
(16, 7, 'Draft 20cl', '', 2.20, 1, 0),
(17, 7, 'Draft 33cl', '', 3.30, 1, 0),
(18, 7, 'Draft 50cl', '', 5.00, 1, 0),
(19, 7, 'Bottled Gluten free', '', 3.30, 1, 0),
(20, 7, 'Bottled Stout', '', 3.30, 1, 0),
(21, 7, 'Bottled 0% Alcohol', '', 3.30, 1, 0),
(22, 7, 'Bottled Inedit', '', 10.00, 1, 0),
(23, 7, 'Bottled Original', '', 3.30, 1, 0),
(24, 7, 'Bottled Lemon', '', 2.50, 1, 0),
(25, 8, 'Frozen Pinacolada', '', 10.00, 1, 0),
(26, 8, 'Margarita', '', 8.00, 1, 0),
(27, 8, 'Aperol spritz', '', 7.50, 1, 0),
(28, 8, 'Mojito', '', 8.00, 1, 0),
(29, 8, 'Mojito com Sabor', '', 8.50, 1, 0),
(30, 8, 'Gin da Casa', '', 7.50, 1, 0),
(31, 8, 'Gin Premium ', '', 11.00, 1, 0),
(32, 8, 'Caipirinha', '', 7.50, 1, 0),
(33, 8, 'Caipirosca', '', 7.50, 1, 0),
(34, 8, 'Moscow Mule', '', 8.00, 1, 0),
(35, 8, 'Porto Tónico ', '', 7.00, 1, 0),
(36, 8, 'Shots', 'Unit', 3.00, 1, 0),
(37, 9, 'Sumo do Dia', '', 4.50, 1, 0),
(38, 9, 'Sumo de Laranja', '', 4.00, 1, 0),
(39, 9, 'Lemonada', '', 4.00, 1, 0),
(40, 9, 'Guaraná', '', 2.50, 1, 0),
(41, 9, '7up', '', 2.50, 1, 0),
(42, 9, 'Coca Cola', '', 2.50, 1, 0),
(43, 10, 'Garrafa 0.5L', '', 1.80, 1, 0),
(44, 10, 'Pedras', '', 2.00, 1, 0),
(45, 11, 'Café', '', 1.20, 1, 0),
(46, 12, 'Copo de vinho', '', 5.00, 1, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_ingredients`
--

CREATE TABLE `product_ingredients` (
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `product_ingredients`
--

INSERT INTO `product_ingredients` (`product_id`, `ingredient_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 5),
(2, 6),
(2, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(4, 3),
(4, 6),
(4, 10),
(4, 12),
(5, 10),
(5, 13),
(5, 14),
(5, 15),
(6, 3),
(6, 10),
(6, 16),
(6, 17),
(6, 18),
(6, 19),
(7, 2),
(7, 8),
(7, 10),
(7, 20),
(7, 21),
(8, 16),
(8, 18),
(9, 8),
(9, 22),
(10, 23),
(10, 24),
(11, 25),
(11, 26),
(12, 16),
(12, 22),
(12, 27);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('cliente','staff','cozinha','admin','configurador') DEFAULT 'cliente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--


--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices para tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índices para tabela `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD PRIMARY KEY (`product_id`,`ingredient_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limitadores para a tabela `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Limitadores para a tabela `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Limitadores para a tabela `product_ingredients`
--
ALTER TABLE `product_ingredients`
  ADD CONSTRAINT `product_ingredients_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
