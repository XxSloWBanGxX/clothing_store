-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Чрв 09 2026 р., 21:39
-- Версія сервера: 8.0.43
-- Версія PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `clothstore`
--

-- --------------------------------------------------------

--
-- Структура таблиці `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `selected_size` varchar(50) DEFAULT NULL,
  `selected_color_name` varchar(100) DEFAULT NULL,
  `selected_color_hex` varchar(20) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `session_id` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `selected_size`, `selected_color_name`, `selected_color_hex`, `quantity`, `session_id`, `created_at`) VALUES
(14, 3, 10, NULL, NULL, NULL, 1, NULL, '2026-04-08 08:02:21'),
(15, 3, 9, NULL, NULL, NULL, 1, NULL, '2026-04-08 08:02:23'),
(16, 2, 12, 'L', 'Білий', '#ffffff', 1, NULL, '2026-05-18 07:05:51');

-- --------------------------------------------------------

--
-- Структура таблиці `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Худі', 'hoodies', '2026-03-27 16:36:49'),
(2, 'Футболки', 't-shirts', '2026-03-27 16:36:49'),
(3, 'Куртки', 'jackets', '2026-03-27 16:36:49'),
(4, 'Штани', 'pants', '2026-03-27 16:36:49'),
(5, 'Світшоти', 'sweatshirts', '2026-03-27 16:36:49');

-- --------------------------------------------------------

--
-- Структура таблиці `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `product_id` int NOT NULL,
  `session_id` varchar(120) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `delivery_method` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `comment` text,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(50) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `full_name`, `phone`, `email`, `city`, `address_line`, `delivery_method`, `payment_method`, `comment`, `total_amount`, `status`, `created_at`) VALUES
(1, 2, 'Богдан', '0989056229', 'huawi7ua@gmail.com', 'Коломия', 'Лесі Українки №5', 'nova_poshta', 'cash_on_delivery', 'мені дуже подобається ваш сайт :)', 1299.00, 'cancelled', '2026-04-06 22:41:58'),
(2, 3, 'Administrator', '+380000000000', 'admin@clothstore.com', 'коломия', 'вул. довбуша 251', 'nova_poshta', 'card', 'мені будь-ласка шаурму сирну', 4097.00, 'cancelled', '2026-04-06 22:45:12'),
(3, 3, 'Administrator', '+380000000000', 'admin@clothstore.com', 'коломия', 'вул. довбуша 321', 'courier', 'cash_on_delivery', '423', 2598.00, 'new', '2026-04-08 07:21:33'),
(4, 3, 'Administrator', '+380000000000', 'admin@clothstore.com', 'коломия', 'вул. довбуша 341', 'nova_poshta', 'cash_on_delivery', '', 1299.00, 'new', '2026-04-08 08:01:59'),
(5, 2, 'Богдан', '0989056229', 'huawi7ua@gmail.com', 'коломия', 'вул. довбуша 341', 'nova_poshta', 'cash_on_delivery', 'кпкп', 1549.00, 'new', '2026-05-18 07:06:58');

-- --------------------------------------------------------

--
-- Структура таблиці `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `selected_size` varchar(50) DEFAULT NULL,
  `selected_color_name` varchar(100) DEFAULT NULL,
  `selected_color_hex` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `selected_size`, `selected_color_name`, `selected_color_hex`, `created_at`) VALUES
(1, 1, 10, 'Soft Gray Sweatshirt', 1299.00, 1, NULL, NULL, NULL, '2026-04-06 22:41:59'),
(2, 2, 10, 'Soft Gray Sweatshirt', 1299.00, 1, 'M', 'Чорний', '#111111', '2026-04-06 22:45:12'),
(3, 2, 9, 'Straight Fit Pants', 1499.00, 1, NULL, NULL, NULL, '2026-04-06 22:45:12'),
(4, 2, 10, 'Soft Gray Sweatshirt', 1299.00, 1, NULL, NULL, NULL, '2026-04-06 22:45:12'),
(5, 3, 10, 'Soft Gray Sweatshirt', 1299.00, 1, 'XS', 'Чорний', '#111111', '2026-04-08 07:21:33'),
(6, 3, 10, 'Soft Gray Sweatshirt', 1299.00, 1, NULL, NULL, NULL, '2026-04-08 07:21:33'),
(7, 4, 10, 'Soft Gray Sweatshirt', 1299.00, 1, NULL, NULL, NULL, '2026-04-08 08:01:59'),
(8, 5, 20, 'Штани Light Beige', 1549.00, 1, 'S', 'Бежевий', '#d6c1a3', '2026-05-18 07:06:58');

-- --------------------------------------------------------

--
-- Структура таблиці `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `image`, `stock`, `is_featured`, `created_at`) VALUES
(1, 5, 'Світшот Rose Touch', 'svitshot-rose-touch', 'Модний світшот із м’якою тканиною та сучасним дизайном. Підійде для легких і стильних образів.', 1499.00, 'hoodie-black.jpg', 9, 1, '2026-03-27 16:36:49'),
(2, 5, 'Світшот Minimal Sand', 'svitshot-minimal-sand', 'Мінімалістичний світшот у спокійних відтінках. Виглядає стильно та пасує до різних варіантів одягу.', 1449.00, 'classic-white-t-shirt.jpg', 9, 1, '2026-03-27 16:36:49'),
(3, 5, 'Світшот Blue Mood', 'svitshot-blue-mood', 'Комфортний світшот для створення стильного повсякденного образу. Має приємну тканину та вільний крій.', 1349.00, 'urban-jacket-gray.jpg', 0, 0, '2026-03-27 16:36:49'),
(4, 5, 'Світшот Urban Grey', 'svitshot-urban-grey', 'Практичний світшот у сучасному стилі. Добре поєднується з джинсами, штанами та кросівками.', 1399.00, 'wide-leg-pants.jpg', 10, 1, '2026-03-27 16:36:49'),
(5, 5, 'Світшот Basic White', 'svitshot-basic-white', 'Базовий світшот із м’якого матеріалу. Зручний, легкий та чудово підходить для щоденного носіння.', 1299.00, 'minimal-sweatshirt.jpg', 14, 0, '2026-03-27 16:36:49'),
(6, 3, 'Куртка Denim Line', 'kurtka-denim-line', 'Сучасна куртка в casual-стилі. Ідеальний вибір для повсякденних образів та активного міського життя.', 2399.00, 'basic-beige-hoodie.jpg', 9, 0, '2026-03-27 16:36:49'),
(7, 3, 'Куртка Winter Motion', 'kurtka-winter-motion', 'Тепла куртка для холодної погоди. Забезпечує комфорт, добре зберігає тепло та має практичні кишені.', 3499.00, 'black-essential-t-shirt.jpg', 6, 0, '2026-03-27 16:36:49'),
(8, 3, 'Куртка Classic Beige', 'kurtka-classic-beige', 'Елегантна куртка у класичному стилі. Добре поєднується з джинсами, штанами та базовими светрами.', 2999.00, 'minimal-bomber-jacket.jpg', 8, 1, '2026-03-27 16:36:49'),
(9, 3, 'Куртка Street Wind', 'kurtka-street-wind', 'Легка вітрозахисна куртка, яка чудово підходить для прохолодної погоди. Практична, комфортна та універсальна.', 2699.00, 'straight-fit-pants.jpg', 10, 0, '2026-03-27 16:36:49'),
(10, 3, 'Куртка Urban Black', 'kurtka-urban-black', 'Стильна демісезонна куртка для щоденного носіння. Має зручний крій, якісну застібку та сучасний мінімалістичний дизайн.', 2899.00, '05a.jpeg', 12, 0, '2026-03-27 16:36:49'),
(11, 2, 'Футболка Essential Black', 'futbolka-essential-black', 'Базова футболка для щоденного використання. Має зручний крій, приємну тканину та універсальний дизайн.', 699.00, '331.jpeg', 18, 0, '2026-04-09 18:01:59'),
(12, 2, 'Футболка Pure White', 'futbolka-pure-white', 'Класична біла футболка, яка підходить до будь-якого стилю. Ідеальний базовий елемент гардероба.', 649.00, '310.jpeg', 0, 0, '2026-04-09 18:02:37'),
(13, 2, 'Футболка Street Print', 'futbolka-street-print', 'Стильна футболка з яскравим характером. Підходить для молодіжних та міських образів.', 799.00, '113.jpeg', 14, 0, '2026-04-09 18:03:16'),
(14, 2, 'Футболка Summer Sky', 'futbolka-summer-sky', 'Легка футболка у свіжих кольорах. Ідеально підходить для теплої погоди та комфортного щоденного носіння.', 749.00, 'a47.jpeg', 14, 0, '2026-04-09 18:03:52'),
(15, 2, 'Футболка Fresh Lime', 'futbolka-fresh-lime', 'Яскрава футболка для тих, хто хоче додати кольору до свого образу. Комфортна та сучасна.', 729.00, 'ba1.jpeg', 11, 0, '2026-04-09 18:04:32'),
(16, 1, 'Худі Oversize Black', 'khudi-oversize-black', 'Тепле худі oversize-крою для комфортного повсякденного носіння. Добре підходить для прохолодної погоди.', 1599.00, 'c08.jpeg', 13, 0, '2026-04-09 18:05:14'),
(17, 1, 'Худі Urban Blue', 'khudi-urban-blue', 'Зручне та стильне худі для міського стилю. Має м’який матеріал і практичний капюшон.', 1649.00, 'ef2.jpeg', 11, 0, '2026-04-09 18:05:57'),
(18, 4, 'Штани Casual Fit', 'shtany-casual-fit', 'Зручні штани для щоденного носіння. Поєднують комфорт, сучасний крій та практичність.', 1499.00, 'c45.jpeg', 11, 0, '2026-04-09 18:06:40'),
(19, 4, 'Штани Street Motion', 'shtany-street-motion', 'Стильні штани для міського образу. Добре сидять, зручні в носінні та підходять для різних сезонів.', 1599.00, '5283b5c5de62a79de3ad0c166d2166b0.jpeg', 10, 0, '2026-04-09 18:07:22'),
(20, 4, 'Штани Light Beige', 'shtany-light-beige', 'Легкі штани у спокійних відтінках. Добре поєднуються зі світшотами, футболками та сорочками.', 1549.00, '05d.jpeg', 9, 0, '2026-04-09 18:08:04'),
(21, 4, 'Штани Sport Line', 'shtany-sport-line', 'Спортивні штани для активного дня та комфортного відпочинку. Мають сучасний вигляд і зручний фасон.', 1399.00, '494.jpeg', 0, 0, '2026-04-09 18:08:45');

-- --------------------------------------------------------

--
-- Структура таблиці `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_hex` varchar(20) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `color_name`, `color_hex`, `sort_order`) VALUES
(22, 9, 'Чорний', '#111111', 1),
(23, 9, 'Хакі', '#6b7a3a', 2),
(24, 8, 'Бежевий', '#d6c1a3', 1),
(25, 8, 'Коричневий', '#6b4423', 2),
(26, 7, 'Чорний', '#111111', 1),
(27, 7, 'Синій', '#2563eb', 2),
(28, 6, 'Синій', '#2563eb', 1),
(29, 6, 'Блакитний', '#38bdf8', 2),
(30, 5, 'Білий', '#ffffff', 1),
(31, 5, 'Сірий', '#808080', 2),
(32, 4, 'Чорний', '#111111', 1),
(33, 4, 'Сірий', '#808080', 2),
(36, 2, 'Бежевий', '#d6c1a3', 1),
(37, 2, 'Коричневий', '#6b4423', 2),
(38, 1, 'Бежевий', '#d6c1a3', 1),
(39, 1, 'Рожевий', '#ec4899', 2),
(67, 3, 'Синій', '#2563eb', 1),
(68, 3, 'Блакитний', '#38bdf8', 2),
(69, 21, 'Сірий', '#808080', 1),
(70, 21, 'Синій', '#2563eb', 2),
(71, 20, 'Бежевий', '#d6c1a3', 1),
(72, 20, 'Коричневий', '#6b4423', 2),
(73, 19, 'Чорний', '#111111', 1),
(74, 19, 'Хакі', '#6b7a3a', 2),
(75, 18, 'Чорний', '#111111', 1),
(76, 18, 'Сірий', '#808080', 2),
(77, 17, 'Синій', '#2563eb', 1),
(78, 17, 'Блакитний', '#38bdf8', 2),
(79, 16, 'Чорний', '#111111', 1),
(80, 16, 'Сірий', '#808080', 2),
(81, 15, 'Зелений', '#16a34a', 1),
(82, 15, 'Жовтий', '#facc15', 2),
(83, 14, 'Синій', '#2563eb', 1),
(84, 14, 'Блакитний', '#38bdf8', 2),
(85, 13, 'Чорний', '#111111', 1),
(86, 13, 'Червоний', '#dc2626', 2),
(87, 12, 'Білий', '#ffffff', 1),
(88, 10, 'Чорний', '#111111', 1),
(89, 10, 'Сірий', '#808080', 2),
(90, 11, 'Чорний', '#111111', 1),
(91, 11, 'Білий', '#ffffff', 2);

-- --------------------------------------------------------

--
-- Структура таблиці `product_images`
--

CREATE TABLE `product_images` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `sort_order`, `created_at`) VALUES
(1, 1, 'hoodie-black-1.jpg', 1, '2026-03-27 18:19:01'),
(2, 1, 'hoodie-black-2.jpg', 2, '2026-03-27 18:19:01'),
(3, 1, 'hoodie-black-3.jpg', 3, '2026-03-27 18:19:01'),
(4, 2, 'classic-white-t-shirt-1.jpg', 1, '2026-03-27 18:19:01'),
(5, 2, 'classic-white-t-shirt-2.jpg', 2, '2026-03-27 18:19:01'),
(6, 3, 'urban-jacket-gray-1.jpg', 1, '2026-03-27 18:19:01'),
(7, 3, 'urban-jacket-gray-2.jpg', 2, '2026-03-27 18:19:01'),
(8, 4, 'wide-leg-pants-1.jpg', 1, '2026-03-27 18:19:01'),
(9, 4, 'wide-leg-pants-2.jpg', 2, '2026-03-27 18:19:01'),
(19, 10, 'images.jpg', 2, '2026-04-06 17:44:40'),
(22, 21, '2cf.jpeg', 1, '2026-04-09 18:12:58'),
(23, 21, 'db7.jpeg', 2, '2026-04-09 18:12:58'),
(24, 20, '05d_1.jpeg', 1, '2026-04-09 18:14:45'),
(25, 20, 'bcf.jpeg', 2, '2026-04-09 18:14:45'),
(26, 19, '7b4117f70b7f0024cf91cad0b0f21f55.jpeg', 1, '2026-04-09 18:15:59'),
(27, 18, '3b8.jpeg', 1, '2026-04-09 18:17:19'),
(28, 10, '037.jpeg', 3, '2026-04-09 18:23:09'),
(29, 10, '51d.jpeg', 4, '2026-04-09 18:23:09');

-- --------------------------------------------------------

--
-- Структура таблиці `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `size_label` varchar(50) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size_label`, `sort_order`) VALUES
(78, 9, 'M', 1),
(79, 9, 'L', 2),
(80, 9, 'XL', 3),
(81, 9, 'XXL', 4),
(82, 8, 'S', 1),
(83, 8, 'M', 2),
(84, 8, 'L', 3),
(85, 7, 'M', 1),
(86, 7, 'L', 2),
(87, 7, 'XL', 3),
(88, 7, 'XXL', 4),
(89, 6, 'S', 1),
(90, 6, 'M', 2),
(91, 6, 'L', 3),
(92, 6, 'XL', 4),
(93, 5, 'S', 1),
(94, 5, 'M', 2),
(95, 5, 'L', 3),
(96, 5, 'XL', 4),
(97, 4, 'M', 1),
(98, 4, 'L', 2),
(99, 4, 'XL', 3),
(103, 2, 'M', 1),
(104, 2, 'L', 2),
(105, 2, 'XL', 3),
(106, 2, 'XXL', 4),
(107, 1, 'S', 1),
(108, 1, 'M', 2),
(109, 1, 'L', 3),
(172, 3, 'S', 1),
(173, 3, 'M', 2),
(174, 3, 'L', 3),
(175, 21, 'S', 1),
(176, 21, 'M', 2),
(177, 21, 'L', 3),
(178, 21, 'XL', 4),
(179, 20, 'S', 1),
(180, 20, 'M', 2),
(181, 20, 'L', 3),
(182, 19, 'M', 1),
(183, 19, 'L', 2),
(184, 19, 'XL', 3),
(185, 19, 'XXL', 4),
(186, 18, 'S', 1),
(187, 18, 'M', 2),
(188, 18, 'L', 3),
(189, 18, 'XL', 4),
(190, 17, 'XS', 1),
(191, 17, 'S', 2),
(192, 17, 'M', 3),
(193, 17, 'L', 4),
(194, 16, 'M', 1),
(195, 16, 'L', 2),
(196, 16, 'XL', 3),
(197, 16, 'XXL', 4),
(198, 15, 'S', 1),
(199, 15, 'M', 2),
(200, 15, 'L', 3),
(201, 15, 'XL', 4),
(202, 15, 'XXL', 5),
(203, 14, 'S', 1),
(204, 14, 'M', 2),
(205, 14, 'L', 3),
(206, 13, 'S', 1),
(207, 13, 'M', 2),
(208, 13, 'L', 3),
(209, 13, 'XL', 4),
(210, 12, 'S', 1),
(211, 12, 'M', 2),
(212, 12, 'L', 3),
(213, 12, 'XL', 4),
(214, 12, 'XXL', 5),
(215, 10, 'S', 1),
(216, 10, 'M', 2),
(217, 10, 'L', 3),
(218, 10, 'XL', 4),
(219, 11, 'S', 1),
(220, 11, 'M', 2),
(221, 11, 'L', 3),
(222, 11, 'XL', 4);

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','support') NOT NULL DEFAULT 'user',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verification_code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `password`, `role`, `is_verified`, `verification_code`, `created_at`) VALUES
(2, 'Богдан', 'Bogdan', 'huawi7ua@gmail.com', '0989056229', '$2y$10$8sjrJ4xwBrnR/EoKy9bXiuNRFwNmnk9bFlPOoZ3CZOll2pV8YgO6.', 'user', 0, '124254', '2026-03-27 17:53:04'),
(3, 'Administrator', 'admin', 'admin@clothstore.com', '+380000000000', '$2y$10$WN0i3S3iLBXPBEo4ChIpfuG9LYX7TRoZErinIFwv2xv0y5LRvaare', 'admin', 1, NULL, '2026-03-27 17:59:07');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product_variant` (`user_id`,`product_id`,`selected_size`,`selected_color_name`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Індекси таблиці `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Індекси таблиці `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_favorites_product` (`product_id`);

--
-- Індекси таблиці `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Індекси таблиці `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_product` (`product_id`);

--
-- Індекси таблиці `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Індекси таблиці `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_colors_product` (`product_id`);

--
-- Індекси таблиці `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Індекси таблиці `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_sizes_product` (`product_id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблиці `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблиці `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблиці `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT для таблиці `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблиці `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `product_colors`
--
ALTER TABLE `product_colors`
  ADD CONSTRAINT `fk_product_colors_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `fk_product_sizes_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
