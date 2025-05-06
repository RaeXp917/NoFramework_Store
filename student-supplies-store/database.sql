-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 11:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_store`
--
CREATE DATABASE IF NOT EXISTS `student_store` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `student_store`;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `total_price`, `created_at`) VALUES
(1, 'giannhs', 'bababooey@gmail.com', 26.74, '2025-05-03 11:50:06'),
(2, 'naras', 'onaraseinaisexy@gmail.com', 23.97, '2025-05-03 14:38:37'),
(3, 'whyiwastemytime3hoursserverswas3', 'plzdonthappenagian@gmail.com', 3.98, '2025-05-03 19:44:33'),
(4, 'dodwasindeedhere', 'nikos123@gmail.com', 25.49, '2025-05-03 20:32:46'),
(5, 'testtest123', '1231@gmail.com', 39.98, '2025-05-04 08:35:24'),
(6, 'testest11', 'eeata21@gmail.com', 6.98, '2025-05-04 08:46:26'),
(7, 'giannhs', '1232@gmail.com', 3.98, '2025-05-04 09:45:20'),
(8, 'maria', 'dimitris221@gmail.com', 26.97, '2025-05-04 10:59:23'),
(9, 'klearxosx', 'klearxos123@gmail.com', 6.55, '2025-05-04 12:07:05'),
(10, 'test111', 'idkanemail@gmail.com', 22.59, '2025-05-04 12:27:08');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(1, 1, 17, 1),
(2, 1, 18, 1),
(3, 1, 19, 1),
(4, 1, 21, 1),
(5, 2, 5, 1),
(6, 2, 6, 1),
(7, 2, 18, 1),
(8, 3, 2, 1),
(9, 3, 5, 1),
(10, 4, 18, 1),
(11, 4, 21, 1),
(12, 4, 22, 1),
(13, 5, 18, 2),
(14, 6, 2, 1),
(15, 6, 5, 1),
(16, 6, 22, 1),
(17, 7, 2, 1),
(18, 7, 5, 1),
(19, 8, 2, 1),
(20, 8, 18, 1),
(21, 8, 27, 1),
(22, 9, 15, 1),
(23, 9, 19, 1),
(24, 9, 22, 1),
(25, 10, 12, 1),
(26, 10, 18, 1),
(27, 10, 20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`) VALUES
(1, 'Spiral Notebook', '100 pages, ruled', 2.50, 'images/notebook.jpg', 'Writing'),
(2, 'Ballpoint Pen black', 'Black ink, pack of 3', 1.99, 'images/black_pen.jpg', 'Writing'),
(3, 'Paper Clips', 'A bag of paper clips.\r\nUsed for managing papers!', 1.00, 'images/paper_clip.jpg', 'Office Supplies'),
(5, 'Ballpoint Pen Blue', 'Blue ink, pack of 3', 1.99, 'images/blue_pen.jpg', 'Writing'),
(6, 'Ballpoint Pen Red', 'Red ink, pack of 3', 1.99, 'images/red_pen.jpg', 'Writing'),
(7, 'Gel Pens Assorted Colors', 'pack of 12 vibrant gel pens', 7.50, 'images/gel_pens.jpg', 'Writing'),
(8, 'Highlighters pack of 4', 'A pack of 4, which includes.\r\nYellow, Pink, Green, Orange\r\n', 3.99, 'images/highlighters.jpg', 'Writing'),
(9, 'No. 2 Pencilcs', 'Pack of 10 graphite pencils', 2.99, 'images/pencils.jpg', 'Writing'),
(10, 'Mechanical Penchil Set', '0.7mm  pencil with lead refills and erasers', 4.50, 'images/mech_pencil.jpg', 'Writing'),
(11, 'Permanent Markers Black', 'pack of 2 permanent markers color: Black', 3.20, 'images/markers_black.jpg', 'Writing'),
(12, 'Composition Notebook', 'Wide ruled, 100 sheets', 1.50, 'images/comp_notebook.jpg', 'Office Supplies'),
(13, 'Sticky notes Yellow', '8x8 centimeters, 100 sheets pad', 1.25, 'images/sticky_notes.jpg', 'Office Supplies'),
(14, 'Ruler 30 centimeters', 'A Clear plastik ruler set with inches and cm', 0.99, 'images/ruler.jpg', 'Accessories'),
(15, 'Binder Clips', 'Small and medium clip, pack of 20', 2.75, 'images/binder_clip.jpg', 'Office Supplies'),
(16, 'Pencil Sharpener Manual', 'Compact sharpener for standard pencils', 1.50, 'images/sharpener.jpg', 'Accessories'),
(17, 'Pencil Case Zippered', 'Durable Black farbic case.', 3.50, 'images/pencil_case.jpg', 'Accessories'),
(18, 'Backpack Classic Blue', 'Standar size backpack with multiple compartments', 19.99, 'images/backpack.jpg', 'Accessories'),
(19, 'Eraser Pink', 'Large pink eraser, smudge-free!', 0.80, 'images/eraser.jpg', 'Accessories'),
(20, 'White School Glue', '100 ml bottle, washable!', 1.10, 'images/glue.jpg', 'Art Supplies'),
(21, 'Crayons 24 Count', 'Classic box of 24 assorted colors', 2.50, 'images/crayons.jpg', 'Art Supplies'),
(22, 'Colored Pencils 12 Count', 'Pre-sharpened, assorted colors', 3.00, 'images/colored_pencils.jpg', 'Art Supplies'),
(23, 'Washable Markers', 'Pack of 10 classic colors', 4.50, 'images/markers_washable.jpg', 'Art Supplies'),
(24, 'Sidewalk Chalk', 'White chalk sticks pack of 50', 5.50, 'images/chalk.jpg', 'Writing'),
(26, 'Scissors', 'A kid-friendly scissors', 1.99, 'images/scissors_1746283675.jpg', 'Accessories'),
(27, 'Blank', 'Erases mistakes', 4.99, 'images/blank_1746304672.jpg', 'Accessories'),
(28, 'Fountain Pen', 'A sleek, elegant pen that offers smooth, refined writing experience.', 80.00, 'images/fountain_pen_1746356627.jpg', 'Writing');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
