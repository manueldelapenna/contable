-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-03-2017 a las 01:27:20
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
-- Estructura de tabla para la tabla `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `balance` decimal(12,4) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `account`
--

INSERT INTO `account` (`id`, `balance`, `customer_id`) VALUES
(1, '0.0000', 1004),
(2, '-9287.8900', 1003),
(3, '-14400.0000', 1002);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `account_movement`
--

CREATE TABLE `account_movement` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `debit_note_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `detail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(12,4) NOT NULL,
  `discr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `credit_note_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `account_movement`
--

INSERT INTO `account_movement` (`id`, `account_id`, `invoice_id`, `debit_note_id`, `date`, `detail`, `amount`, `discr`, `credit_note_id`) VALUES
(4, 2, 33, NULL, '2017-03-21 23:32:57', 'Factura', '1443.0600', 'invoiceAccountMovement', NULL),
(5, 3, NULL, NULL, '2017-03-28 00:11:00', 'Nota de Crédito', '14400.0000', 'creditNoteAccountMovement', 3),
(6, 2, NULL, NULL, '2017-03-28 00:45:12', 'Nota de Crédito ', '240.0000', 'creditNoteAccountMovement', 4),
(7, 2, 34, NULL, '2017-03-29 00:53:20', 'Factura', '284.0000', 'invoiceAccountMovement', NULL),
(8, 2, 35, NULL, '2017-03-29 00:53:54', 'Factura', '1225.0500', 'invoiceAccountMovement', NULL),
(9, 2, NULL, NULL, '2017-03-29 01:05:53', 'Nota de Crédito', '12000.0000', 'creditNoteAccountMovement', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `budget`
--

CREATE TABLE `budget` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `budget_state_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `subtotal` decimal(12,4) NOT NULL,
  `discount_amount` decimal(12,4) NOT NULL,
  `shipping_amount` decimal(12,4) NOT NULL,
  `total` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `budget_item`
--

CREATE TABLE `budget_item` (
  `id` int(11) NOT NULL,
  `budget_id` int(11) DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product_quantity` double NOT NULL,
  `product_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit_price` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `budget_state`
--

CREATE TABLE `budget_state` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- Estructura de tabla para la tabla `credit_note`
--

CREATE TABLE `credit_note` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sales_point_id` int(11) NOT NULL,
  `sales_condition_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `subtotal` decimal(12,4) NOT NULL,
  `discount_amount` decimal(12,4) NOT NULL,
  `shipping_amount` decimal(12,4) NOT NULL,
  `total` decimal(12,4) NOT NULL,
  `total_discounted` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `credit_note`
--

INSERT INTO `credit_note` (`id`, `customer_id`, `sales_point_id`, `sales_condition_id`, `date`, `subtotal`, `discount_amount`, `shipping_amount`, `total`, `total_discounted`) VALUES
(1, 1005, 2, 1, '2017-03-27 23:22:53', '12000.0000', '1220.0000', '0.0000', '10780.0000', '0.0000'),
(2, 1005, 2, 1, '2017-03-27 23:24:06', '12000.0000', '1220.0000', '0.0000', '10780.0000', '0.0000'),
(3, 1002, 2, 2, '2017-03-28 00:10:37', '14400.0000', '0.0000', '0.0000', '14400.0000', '0.0000'),
(4, 1003, 2, 2, '2017-03-28 00:45:12', '240.0000', '0.0000', '0.0000', '240.0000', '0.0000'),
(5, 1003, 2, 1, '2017-03-29 01:04:59', '300.0000', '0.0000', '0.0000', '300.0000', '300.0000'),
(6, 1003, 1, 2, '2017-03-29 01:05:53', '12000.0000', '0.0000', '0.0000', '12000.0000', '0.0000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credit_note_item`
--

CREATE TABLE `credit_note_item` (
  `id` int(11) NOT NULL,
  `product_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product_quantity` double NOT NULL,
  `product_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit_price` decimal(12,4) NOT NULL,
  `creditNote_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `credit_note_item`
--

INSERT INTO `credit_note_item` (`id`, `product_code`, `product_quantity`, `product_description`, `unit_price`, `creditNote_id`) VALUES
(1, '1234', 1, 'notebook dell', '12000.0000', 1),
(2, '1234', 1, 'notebook dell', '12000.0000', 2),
(3, '1122', 20, 'teclado genius', '120.0000', 3),
(4, '1234', 1, 'notebook dell', '12000.0000', 3),
(5, '1122', 2, 'teclado genius', '120.0000', 4),
(6, '1', 1, 'descuento por pago contado', '300.0000', 5),
(7, '1234', 1, 'notebook dell', '12000.0000', 6);

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
(1001, 5, '2016-11-30 00:00:00', 'Consumidor Final', '00000000', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1002, 5, '2016-12-29 00:00:00', 'Manuel De la Penna', '31042332', 'Calle 12 1091', 'La Plata', 'Buenos Airtes', '1900', '2983565189', 'manueldelapenna@gmail.com', NULL),
(1003, 5, '2016-12-29 00:00:00', 'Leo Dela', '34138678', 'Calle 527', 'Tolosa', 'Buenos Aires', '1900', '2983565190', 'leodelapenna@gmail.com', NULL),
(1004, 5, '2017-02-15 00:00:00', 'Simon', '35246807', NULL, 'La Plata', 'Buenos Aires', '1900', '2983565191', 'simondelapenna@gmail.com', NULL),
(1005, 5, '2017-02-15 00:00:00', 'Pedro De la Penna', '12345789', NULL, 'La Plata', 'Buenos Aires', '1900', '2983565191', 'pedrito', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `debit_note`
--

CREATE TABLE `debit_note` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sales_point_id` int(11) NOT NULL,
  `sales_condition_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `total` decimal(12,4) NOT NULL,
  `concept` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_payed` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `debit_note`
--

INSERT INTO `debit_note` (`id`, `customer_id`, `sales_point_id`, `sales_condition_id`, `date`, `total`, `concept`, `total_payed`) VALUES
(1, 1002, 1, 2, '2017-03-21 00:00:00', '100.0000', 'ajuste de precio de mercaderia', '0.0000');

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
(1, 'manueldelapenna', 'manueldelapenna', 'manueldelapenna@gmail.com', 'manueldelapenna@gmail.com', 1, 'QkedtvdReupikUbtjF5SelPxci.7EuFFA1inkmzWc.0', 'q4dFJXAKE1tRCEz/ksg7fGYuPS7K8DvqegOuELGIL0BPdsU5EcFbdZyirKt/m/Xoap5Nv8BFg2VKGXwMo0eG9w==', '2017-03-20 22:18:14', NULL, NULL, 'a:2:{i:0;s:10:"ROLE_ADMIN";i:1;s:16:"ROLE_SUPER_ADMIN";}', 'Manuel', 'De la Penna');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sales_point_id` int(11) NOT NULL,
  `sales_condition_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `subtotal` decimal(12,4) NOT NULL,
  `discount_amount` decimal(12,4) NOT NULL,
  `shipping_amount` decimal(12,4) NOT NULL,
  `total` decimal(12,4) NOT NULL,
  `total_payed` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `invoice`
--

INSERT INTO `invoice` (`id`, `customer_id`, `sales_point_id`, `sales_condition_id`, `order_id`, `date`, `subtotal`, `discount_amount`, `shipping_amount`, `total`, `total_payed`) VALUES
(33, 1003, 2, 2, 43, '2017-03-21 23:32:57', '1338.0600', '5.0000', '110.0000', '1443.0600', '0.0000'),
(34, 1003, 2, 2, 45, '2017-03-29 00:53:20', '265.0000', '1.0000', '20.0000', '284.0000', '0.0000'),
(35, 1003, 2, 2, 46, '2017-03-29 00:53:54', '1225.0500', '0.0000', '0.0000', '1225.0500', '0.0000'),
(36, 1002, 2, 1, 47, '2017-03-29 00:54:24', '12070.0000', '0.0000', '0.0000', '12070.0000', '12070.0000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice_item`
--

CREATE TABLE `invoice_item` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `product_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `product_quantity` double NOT NULL,
  `product_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit_price` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `invoice_item`
--

INSERT INTO `invoice_item` (`id`, `invoice_id`, `product_code`, `product_quantity`, `product_description`, `unit_price`) VALUES
(31, 33, '1234', 6, 'sdfsdf', '23.0101'),
(32, 33, '222', 50, 'dsfsdf', '24.0000'),
(33, 34, '123', 2, 'sdfsf', '100.0000'),
(34, 34, '23', 5, 'sfsdf', '13.0000'),
(35, 35, '123', 15, 'svsdf', '1.6700'),
(36, 35, 'lij', 300, 'ijoij', '4.0000'),
(37, 36, '1130', 1, 'mouse genius', '70.0000'),
(38, 36, '1234', 1, 'notebook dell', '12000.0000');

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
  `unit_price` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `order_item`
--

INSERT INTO `order_item` (`id`, `order_id`, `product_code`, `product_quantity`, `product_description`, `unit_price`) VALUES
(77, 43, '1234', 6, 'sdfsdf', '23.0101'),
(79, 45, '123', 2, 'sdfsf', '100.0000'),
(80, 45, '23', 5, 'sfsdf', '13.0000'),
(81, 46, '123', 15, 'svsdf', '1.6700'),
(82, 46, 'lij', 300, 'ijoij', '4.0000'),
(83, 43, '222', 50, 'dsfsdf', '24.0000'),
(84, 47, '1130', 1, 'mouse genius', '70.0000'),
(85, 47, '1234', 1, 'notebook dell', '12000.0000'),
(86, 48, '1234', 1, 'notebook dell', '12000.0000'),
(87, 48, '1130', 1, 'mouse genius', '70.0000'),
(88, 48, '000', 1, 'tarifa plana', '50.0000'),
(89, 49, '1234', 2, 'notebook dell', '12000.0000'),
(90, 49, '1131', 3, 'mouse genius wireless', '170.0000'),
(91, 49, '1122', 1, 'teclado genius', '120.0000'),
(96, 52, '1234', 1, 'notebook dell', '12000.0000'),
(97, 52, '1122', 1, 'teclado genius', '120.0000'),
(98, 53, '1234', 2, 'notebook dell', '12000.0000'),
(99, 53, '1131', 5, 'mouse genius wireless', '170.0000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_state`
--

CREATE TABLE `order_state` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `order_state`
--

INSERT INTO `order_state` (`id`, `name`) VALUES
(1, 'Abierto'),
(3, 'Cancelado'),
(2, 'Cerrado');

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
  `price` decimal(12,4) NOT NULL,
  `stock` double NOT NULL,
  `min_stock` double NOT NULL,
  `max_stock` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `product`
--

INSERT INTO `product` (`id`, `category_id`, `provider_id`, `tax_id`, `code`, `provider_code`, `name`, `description`, `price`, `stock`, `min_stock`, `max_stock`) VALUES
(1, 1, 1, 1, '1234', '12345', 'notebook dell', 'notebook dell i7', '12000.0000', 23, 5, 50),
(2, 1, 1, 1, '1122', '15432', 'teclado genius', 'teclado genius ergonomico', '120.0000', 45, 5, 50),
(3, 1, 1, 1, '1130', '15444', 'mouse genius', 'mouse genius ergonomico', '70.0000', 14, 5, 50),
(5, 1, 1, 1, '1131', '15445', 'mouse genius wireless', 'mouse genius compatible con todos los estandares', '170.0000', 9, 5, 40);

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

--
-- Volcado de datos para la tabla `provider`
--

INSERT INTO `provider` (`id`, `name`, `email`, `cuit`, `phone`, `address`, `zipcode`, `city`, `state`, `observations`) VALUES
(1, 'Proveedor 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_order`
--

CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_state_id` int(11) NOT NULL,
  `sales_point_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `subtotal` decimal(12,4) NOT NULL,
  `discount_amount` decimal(12,4) NOT NULL,
  `shipping_amount` decimal(12,4) NOT NULL,
  `total` decimal(12,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_order`
--

INSERT INTO `purchase_order` (`id`, `customer_id`, `order_state_id`, `sales_point_id`, `date`, `subtotal`, `discount_amount`, `shipping_amount`, `total`) VALUES
(43, 1003, 2, 2, '2017-01-17 09:03:25', '1338.0600', '5.0000', '110.0000', '1443.0600'),
(45, 1003, 2, 2, '2017-01-17 09:07:58', '265.0000', '1.0000', '20.0000', '284.0000'),
(46, 1003, 2, 2, '2017-01-17 09:42:24', '1225.0500', '0.0000', '0.0000', '1225.0500'),
(47, 1002, 2, 2, '2017-02-04 09:00:47', '12070.0000', '0.0000', '0.0000', '12070.0000'),
(48, 1002, 1, 2, '2017-02-04 09:03:29', '12120.0000', '0.0000', '0.0000', '12120.0000'),
(49, 1002, 1, 2, '2017-02-04 09:15:15', '24630.0000', '0.0000', '0.0000', '24630.0000'),
(52, 1002, 1, 2, '2017-02-04 09:22:05', '12120.0000', '0.0000', '0.0000', '12120.0000'),
(53, 1002, 1, 2, '2017-03-27 07:37:28', '24850.0000', '2458.0000', '0.0000', '22392.0000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_condition`
--

CREATE TABLE `sales_condition` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `sales_condition`
--

INSERT INTO `sales_condition` (`id`, `name`) VALUES
(1, 'Contado'),
(2, 'Cuenta Corriente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_point`
--

CREATE TABLE `sales_point` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `sales_point`
--

INSERT INTO `sales_point` (`id`, `name`) VALUES
(2, 'Fabrica'),
(1, 'Local');

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
-- Volcado de datos para la tabla `tax`
--

INSERT INTO `tax` (`id`, `name`, `amount`) VALUES
(1, '21.00 %', 0.21),
(2, '10.50 %', 0.105);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7D3656A49395C3F3` (`customer_id`);

--
-- Indices de la tabla `account_movement`
--
ALTER TABLE `account_movement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8C1377419B6B5FBA` (`account_id`),
  ADD KEY `IDX_8C1377412989F1FD` (`invoice_id`),
  ADD KEY `IDX_8C137741CE4D4989` (`debit_note_id`),
  ADD KEY `IDX_8C1377411C696F7A` (`credit_note_id`);

--
-- Indices de la tabla `budget`
--
ALTER TABLE `budget`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_73F2F77B8D9F6D38` (`order_id`),
  ADD KEY `IDX_73F2F77B9395C3F3` (`customer_id`),
  ADD KEY `IDX_73F2F77BD2176D65` (`budget_state_id`);

--
-- Indices de la tabla `budget_item`
--
ALTER TABLE `budget_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_65DF274E36ABA6B8` (`budget_id`);

--
-- Indices de la tabla `budget_state`
--
ALTER TABLE `budget_state`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C882470D5E237E06` (`name`);

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_unique` (`name`,`parent_id`),
  ADD KEY `IDX_64C19C1727ACA70` (`parent_id`);

--
-- Indices de la tabla `credit_note`
--
ALTER TABLE `credit_note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C87F45299395C3F3` (`customer_id`),
  ADD KEY `IDX_C87F45298D945686` (`sales_point_id`),
  ADD KEY `IDX_C87F4529143A9E0E` (`sales_condition_id`);

--
-- Indices de la tabla `credit_note_item`
--
ALTER TABLE `credit_note_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7EABC2542155372B` (`creditNote_id`);

--
-- Indices de la tabla `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_81398E09E0AD1F90` (`iva_condition_id`);

--
-- Indices de la tabla `debit_note`
--
ALTER TABLE `debit_note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_659B94A29395C3F3` (`customer_id`),
  ADD KEY `IDX_659B94A28D945686` (`sales_point_id`),
  ADD KEY `IDX_659B94A2143A9E0E` (`sales_condition_id`);

--
-- Indices de la tabla `fos_user`
--
ALTER TABLE `fos_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479C05FB297` (`confirmation_token`);

--
-- Indices de la tabla `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_906517448D9F6D38` (`order_id`),
  ADD KEY `IDX_906517449395C3F3` (`customer_id`),
  ADD KEY `IDX_906517448D945686` (`sales_point_id`),
  ADD KEY `IDX_90651744143A9E0E` (`sales_condition_id`);

--
-- Indices de la tabla `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1DDE477B2989F1FD` (`invoice_id`);

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
  ADD KEY `IDX_21E210B2E420DE70` (`order_state_id`),
  ADD KEY `IDX_21E210B28D945686` (`sales_point_id`),
  ADD KEY `IDX_21E210B29395C3F3` (`customer_id`);

--
-- Indices de la tabla `sales_condition`
--
ALTER TABLE `sales_condition`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8198D00A5E237E06` (`name`);

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
-- AUTO_INCREMENT de la tabla `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `account_movement`
--
ALTER TABLE `account_movement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `budget`
--
ALTER TABLE `budget`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `budget_item`
--
ALTER TABLE `budget_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `budget_state`
--
ALTER TABLE `budget_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `credit_note`
--
ALTER TABLE `credit_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `credit_note_item`
--
ALTER TABLE `credit_note_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;
--
-- AUTO_INCREMENT de la tabla `debit_note`
--
ALTER TABLE `debit_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `fos_user`
--
ALTER TABLE `fos_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT de la tabla `invoice_item`
--
ALTER TABLE `invoice_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
--
-- AUTO_INCREMENT de la tabla `order_state`
--
ALTER TABLE `order_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `provider`
--
ALTER TABLE `provider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT de la tabla `sales_condition`
--
ALTER TABLE `sales_condition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `sales_point`
--
ALTER TABLE `sales_point`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `tax`
--
ALTER TABLE `tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `FK_7D3656A49395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Filtros para la tabla `account_movement`
--
ALTER TABLE `account_movement`
  ADD CONSTRAINT `FK_8C1377411C696F7A` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_note` (`id`),
  ADD CONSTRAINT `FK_8C1377412989F1FD` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`),
  ADD CONSTRAINT `FK_8C1377419B6B5FBA` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `FK_8C137741CE4D4989` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_note` (`id`);

--
-- Filtros para la tabla `budget`
--
ALTER TABLE `budget`
  ADD CONSTRAINT `FK_73F2F77B8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`),
  ADD CONSTRAINT `FK_73F2F77B9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `FK_73F2F77BD2176D65` FOREIGN KEY (`budget_state_id`) REFERENCES `budget_state` (`id`);

--
-- Filtros para la tabla `budget_item`
--
ALTER TABLE `budget_item`
  ADD CONSTRAINT `FK_65DF274E36ABA6B8` FOREIGN KEY (`budget_id`) REFERENCES `budget` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `FK_64C19C1727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`);

--
-- Filtros para la tabla `credit_note`
--
ALTER TABLE `credit_note`
  ADD CONSTRAINT `FK_C87F4529143A9E0E` FOREIGN KEY (`sales_condition_id`) REFERENCES `sales_condition` (`id`),
  ADD CONSTRAINT `FK_C87F45298D945686` FOREIGN KEY (`sales_point_id`) REFERENCES `sales_point` (`id`),
  ADD CONSTRAINT `FK_C87F45299395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Filtros para la tabla `credit_note_item`
--
ALTER TABLE `credit_note_item`
  ADD CONSTRAINT `FK_7EABC2542155372B` FOREIGN KEY (`creditNote_id`) REFERENCES `credit_note` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `FK_81398E09E0AD1F90` FOREIGN KEY (`iva_condition_id`) REFERENCES `iva_condition` (`id`);

--
-- Filtros para la tabla `debit_note`
--
ALTER TABLE `debit_note`
  ADD CONSTRAINT `FK_659B94A2143A9E0E` FOREIGN KEY (`sales_condition_id`) REFERENCES `sales_condition` (`id`),
  ADD CONSTRAINT `FK_659B94A28D945686` FOREIGN KEY (`sales_point_id`) REFERENCES `sales_point` (`id`),
  ADD CONSTRAINT `FK_659B94A29395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Filtros para la tabla `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK_90651744143A9E0E` FOREIGN KEY (`sales_condition_id`) REFERENCES `sales_condition` (`id`),
  ADD CONSTRAINT `FK_906517448D945686` FOREIGN KEY (`sales_point_id`) REFERENCES `sales_point` (`id`),
  ADD CONSTRAINT `FK_906517448D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`),
  ADD CONSTRAINT `FK_906517449395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`);

--
-- Filtros para la tabla `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD CONSTRAINT `FK_1DDE477B2989F1FD` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `order_comment`
--
ALTER TABLE `order_comment`
  ADD CONSTRAINT `FK_620EFB278D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`);

--
-- Filtros para la tabla `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`) ON DELETE CASCADE;

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
