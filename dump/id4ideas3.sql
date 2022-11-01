-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 01, 2022 at 01:44 AM
-- Server version: 8.0.30
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id4ideas3`
--

-- --------------------------------------------------------

--
-- Table structure for table `Categorias`
--

CREATE TABLE `Categorias` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` smallint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Categorias`
--

INSERT INTO `Categorias` (`id`, `nombre`, `imagen`, `orden`) VALUES
(1, 'Pizzas', 'catPizza.jpg', 10),
(2, 'Empanadas', 'catEmpanadas.jpg', 20),
(3, 'Bebidas', 'catBebidas.jpg', 40),
(4, 'Postre', 'catPostres.jpg', 30);

-- --------------------------------------------------------

--
-- Table structure for table `Productos`
--

CREATE TABLE `Productos` (
  `id` int UNSIGNED NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoriaId` int UNSIGNED NOT NULL,
  `precio` decimal(10,2) UNSIGNED NOT NULL,
  `imagen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` smallint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Productos`
--

INSERT INTO `Productos` (`id`, `nombre`, `categoriaId`, `precio`, `imagen`, `orden`) VALUES
(1, 'Muzzarella', 1, '1450.00', 'muzzarella.jpg', 20),
(2, 'Napolitana', 1, '1550.00', 'napolitana.jpg', 10),
(3, 'Jamon y Morron', 1, '1350.00', 'jamonymorron.jpg', 40),
(4, 'Atún', 2, '1000.00', 'empanada_atun.jpg', 20),
(5, 'Carne', 2, '1350.00', 'empanada_carne.jpg', 10),
(6, 'Jamon y Queso', 2, '950.00', 'empanada_jamonyqueso.jpg', 40),
(7, 'Agua', 3, '250.00', 'agua.jpg', 20),
(8, 'Gaseosa', 3, '350.00', 'gaseosa.jpg', 10),
(9, 'Cerveza', 3, '399.99', 'cerveza.jpg', 40),
(10, 'Almendrado', 4, '550.00', 'almendrado.jpg', 20),
(11, 'Bombones', 4, '600.00', 'bombones.jpg', 10),
(12, 'Helado', 4, '570.75', 'helado.jpg', 40);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'password'),
(2, 'Alice', 'this is my password'),
(3, 'Job', '12345678'),
(4, 'Pedro', '12345678!'),
(5, 'Pedro II', '12345678!!');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Categorias`
--
ALTER TABLE `Categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombre` (`nombre`),
  ADD KEY `orden` (`orden`);

--
-- Indexes for table `Productos`
--
ALTER TABLE `Productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombre` (`nombre`),
  ADD KEY `orden` (`orden`),
  ADD KEY `categoriaId` (`categoriaId`),
  ADD KEY `precio` (`precio`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Categorias`
--
ALTER TABLE `Categorias`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Productos`
--
ALTER TABLE `Productos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
