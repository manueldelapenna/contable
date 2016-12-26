-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-12-2016 a las 22:02:37
-- Versión del servidor: 10.1.13-MariaDB
-- Versión de PHP: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `contable`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `category`
--

INSERT INTO `category` (`id`, `parent_id`, `name`) VALUES
(5, 4, 'Boxers'),
(7, 1, 'Calzado'),
(9, 2, 'Calzado'),
(3, 2, 'Corpiños'),
(1, NULL, 'Hombre'),
(2, NULL, 'Mujer'),
(4, 1, 'Ropa Interior'),
(11, 2, 'Ropa Interior'),
(6, 4, 'Slips');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `iva_condition_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cuit_dni` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phones` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observations` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `customer`
--

INSERT INTO `customer` (`id`, `iva_condition_id`, `created_at`, `name`, `cuit_dni`, `address`, `city`, `state`, `zipcode`, `phones`, `email`, `observations`) VALUES
(1001, 5, '2016-11-30 00:00:00', 'Consumidor Final', '00000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fos_user`
--

CREATE TABLE `fos_user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `firstname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `fos_user`
--

INSERT INTO `fos_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `firstname`, `lastname`) VALUES
(1, 'manueldelapenna', 'manueldelapenna', 'manueldelapenna@gmail.com', 'manueldelapenna@gmail.com', 1, 'QkedtvdReupikUbtjF5SelPxci.7EuFFA1inkmzWc.0', 'q4dFJXAKE1tRCEz/ksg7fGYuPS7K8DvqegOuELGIL0BPdsU5EcFbdZyirKt/m/Xoap5Nv8BFg2VKGXwMo0eG9w==', '2016-12-26 19:41:19', NULL, NULL, 'a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:16:"ROLE_SUPER_ADMIN";}', 'Manuel', 'De la Penna');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva_condition`
--

CREATE TABLE `iva_condition` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `iva_condition`
--

INSERT INTO `iva_condition` (`id`, `name`) VALUES
(5, 'Consumidor Final'),
(3, 'Exento'),
(2, 'Monotributista'),
(4, 'No Responsable'),
(1, 'Responsable Inscripto');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_comment`
--

CREATE TABLE `order_comment` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product_quantity` double NOT NULL,
  `product_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_state`
--

CREATE TABLE `order_state` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `stock` double NOT NULL,
  `min_stock` double NOT NULL,
  `max_stock` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provider`
--

CREATE TABLE `provider` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cuit` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observations` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_order`
--

CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_state_id` int(11) DEFAULT NULL,
  `sales_point_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `subtotal` double NOT NULL,
  `discount_amount` double NOT NULL,
  `shipping_amount` double NOT NULL,
  `total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_point`
--

CREATE TABLE `sales_point` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tax`
--

CREATE TABLE `tax` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_unique` (`name`,`parent_id`),
  ADD KEY `IDX_64C19C1727ACA70` (`parent_id`);

--
-- Indices de la tabla `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_81398E09E0AD1F90` (`iva_condition_id`);

--
-- Indices de la tabla `fos_user`
--
ALTER TABLE `fos_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479C05FB297` (`confirmation_token`);

--
-- Indices de la tabla `iva_condition`
--
ALTER TABLE `iva_condition`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_CFFFBAF05E237E06` (`name`);

--
-- Indices de la tabla `order_comment`
--
ALTER TABLE `order_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_620EFB278D9F6D38` (`order_id`);

--
-- Indices de la tabla `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_52EA1F098D9F6D38` (`order_id`);

--
-- Indices de la tabla `order_state`
--
ALTER TABLE `order_state`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_200DA6065E237E06` (`name`);

--
-- Indices de la tabla `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D34A04AD77153098` (`code`),
  ADD UNIQUE KEY `UNIQ_D34A04AD5E237E06` (`name`),
  ADD KEY `IDX_D34A04AD12469DE2` (`category_id`),
  ADD KEY `IDX_D34A04ADA53A8AA` (`provider_id`),
  ADD KEY `IDX_D34A04ADB2A824D8` (`tax_id`);

--
-- Indices de la tabla `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_92C4739C5E237E06` (`name`);

--
-- Indices de la tabla `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_21E210B29395C3F3` (`customer_id`),
  ADD KEY `IDX_21E210B2E420DE70` (`order_state_id`),
  ADD KEY `IDX_21E210B28D945686` (`sales_point_id`);

--
-- Indices de la tabla `sales_point`
--
ALTER TABLE `sales_point`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_41E142925E237E06` (`name`);

--
-- Indices de la tabla `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8E81BA765E237E06` (`name`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1002;
--
-- AUTO_INCREMENT de la tabla `fos_user`
--
ALTER TABLE `fos_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `iva_condition`
--
ALTER TABLE `iva_condition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `order_comment`
--
ALTER TABLE `order_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `order_state`
--
ALTER TABLE `order_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `provider`
--
ALTER TABLE `provider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sales_point`
--
ALTER TABLE `sales_point`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tax`
--
ALTER TABLE `tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `FK_64C19C1727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`);

--
-- Filtros para la tabla `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `FK_81398E09E0AD1F90` FOREIGN KEY (`iva_condition_id`) REFERENCES `iva_condition` (`id`);

--
-- Filtros para la tabla `order_comment`
--
ALTER TABLE `order_comment`
  ADD CONSTRAINT `FK_620EFB278D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`);

--
-- Filtros para la tabla `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`);

--
-- Filtros para la tabla `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_D34A04ADA53A8AA` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`id`),
  ADD CONSTRAINT `FK_D34A04ADB2A824D8` FOREIGN KEY (`tax_id`) REFERENCES `tax` (`id`);

--
-- Filtros para la tabla `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD CONSTRAINT `FK_21E210B28D945686` FOREIGN KEY (`sales_point_id`) REFERENCES `sales_point` (`id`),
  ADD CONSTRAINT `FK_21E210B29395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `FK_21E210B2E420DE70` FOREIGN KEY (`order_state_id`) REFERENCES `order_state` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
