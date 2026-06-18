-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 18, 2026 at 09:16 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_waf`
--

-- --------------------------------------------------------

--
-- Table structure for table `ataques`
--

CREATE TABLE `ataques` (
  `id_ataque` int(11) NOT NULL,
  `fecha_ataque` datetime NOT NULL,
  `tipo_ataque` varchar(50) NOT NULL,
  `ip_ataque` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo`
--

CREATE TABLE `bloqueo` (
  `id_bloqueo` int(11) NOT NULL COMMENT 'primary key',
  `fecha_bloqueo` datetime NOT NULL,
  `ip` varchar(200) NOT NULL,
  `server` varchar(200) NOT NULL,
  `url` varchar(800) NOT NULL,
  `learning` varchar(50) DEFAULT NULL,
  `vers` varchar(50) DEFAULT NULL,
  `total_processed` varchar(11) DEFAULT NULL,
  `total_blocked` varchar(11) DEFAULT NULL,
  `zoneN` varchar(11) DEFAULT NULL,
  `idN` int(11) NOT NULL,
  `tipo_ataque` varchar(255) NOT NULL,
  `var_nameN` text NOT NULL,
  `cscoreN` varchar(200) DEFAULT NULL,
  `scoreN` varchar(255) DEFAULT NULL,
  `metodo` varchar(200) NOT NULL,
  `activo_bloqueo` int(11) NOT NULL,
  `fecha_r` date NOT NULL,
  `log_bloqueo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_ddos`
--

CREATE TABLE `bloqueo_ddos` (
  `id_bloqueo_ddos` int(11) NOT NULL,
  `ip_ddos` varchar(50) NOT NULL,
  `codigo_pais` varchar(50) NOT NULL,
  `fecha_ddos` datetime NOT NULL,
  `total_coneccion` int(11) NOT NULL,
  `lista_blanca` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_ddos_pais`
--

CREATE TABLE `bloqueo_ddos_pais` (
  `id_bloqueo_ddos_pais` int(11) NOT NULL,
  `id_pais` int(11) NOT NULL,
  `total_bloqueo_ddos_pais` int(11) NOT NULL,
  `iso3` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bloqueo_ddos_pais`
--

INSERT INTO `bloqueo_ddos_pais` (`id_bloqueo_ddos_pais`, `id_pais`, `total_bloqueo_ddos_pais`, `iso3`) VALUES
(1, 1, 0, ''),
(2, 2, 0, ''),
(3, 3, 0, ''),
(4, 4, 0, ''),
(5, 5, 0, ''),
(6, 6, 0, ''),
(7, 7, 0, ''),
(8, 8, 0, ''),
(9, 9, 0, ''),
(10, 10, 0, ''),
(11, 11, 0, ''),
(12, 12, 0, ''),
(13, 13, 0, ''),
(14, 14, 0, ''),
(15, 15, 0, ''),
(16, 16, 0, ''),
(17, 17, 0, ''),
(18, 18, 0, ''),
(19, 19, 0, ''),
(20, 20, 0, ''),
(21, 21, 0, ''),
(22, 22, 0, ''),
(23, 23, 0, ''),
(24, 24, 0, ''),
(25, 25, 0, ''),
(26, 26, 0, ''),
(27, 27, 0, ''),
(28, 28, 0, ''),
(29, 29, 0, ''),
(30, 30, 0, ''),
(31, 31, 0, ''),
(32, 32, 0, ''),
(33, 33, 0, ''),
(34, 34, 0, ''),
(35, 35, 0, ''),
(36, 36, 0, ''),
(37, 37, 0, ''),
(38, 38, 0, ''),
(39, 39, 0, ''),
(40, 40, 0, ''),
(41, 41, 0, ''),
(42, 42, 0, ''),
(43, 43, 0, ''),
(44, 44, 0, ''),
(45, 45, 0, ''),
(46, 46, 0, ''),
(47, 47, 0, ''),
(48, 48, 0, ''),
(49, 49, 0, ''),
(50, 50, 0, ''),
(51, 51, 0, ''),
(52, 52, 0, ''),
(53, 53, 0, ''),
(54, 54, 0, ''),
(55, 55, 0, ''),
(56, 56, 0, ''),
(57, 57, 0, ''),
(58, 58, 0, ''),
(59, 59, 0, ''),
(60, 60, 0, ''),
(61, 61, 0, ''),
(62, 62, 0, ''),
(63, 63, 0, ''),
(64, 64, 0, ''),
(65, 65, 0, ''),
(66, 66, 0, ''),
(67, 67, 0, ''),
(68, 68, 0, ''),
(69, 69, 0, ''),
(70, 70, 0, ''),
(71, 71, 0, ''),
(72, 72, 0, ''),
(73, 73, 0, ''),
(74, 74, 0, ''),
(75, 75, 0, ''),
(76, 76, 0, ''),
(77, 77, 0, ''),
(78, 78, 0, ''),
(79, 79, 0, ''),
(80, 80, 0, ''),
(81, 81, 0, ''),
(82, 82, 0, ''),
(83, 83, 0, ''),
(84, 84, 0, ''),
(85, 85, 0, ''),
(86, 86, 0, ''),
(87, 87, 0, ''),
(88, 88, 0, ''),
(89, 89, 0, ''),
(90, 90, 0, ''),
(91, 91, 0, ''),
(92, 92, 0, ''),
(93, 93, 0, ''),
(94, 94, 0, ''),
(95, 95, 0, ''),
(96, 96, 0, ''),
(97, 97, 0, ''),
(98, 98, 0, ''),
(99, 99, 0, ''),
(100, 100, 0, ''),
(101, 101, 0, ''),
(102, 102, 0, ''),
(103, 103, 0, ''),
(104, 104, 0, ''),
(105, 105, 0, ''),
(106, 106, 0, ''),
(107, 107, 0, ''),
(108, 108, 0, ''),
(109, 109, 0, ''),
(110, 110, 0, ''),
(111, 111, 0, ''),
(112, 112, 0, ''),
(113, 113, 0, ''),
(114, 114, 0, ''),
(115, 115, 0, ''),
(116, 116, 0, ''),
(117, 117, 0, ''),
(118, 118, 0, ''),
(119, 119, 0, ''),
(120, 120, 0, ''),
(121, 121, 0, ''),
(122, 122, 0, ''),
(123, 123, 0, ''),
(124, 124, 0, ''),
(125, 125, 0, ''),
(126, 126, 0, ''),
(127, 127, 0, ''),
(128, 128, 0, ''),
(129, 129, 0, ''),
(130, 130, 0, ''),
(131, 131, 0, ''),
(132, 132, 0, ''),
(133, 133, 0, ''),
(134, 134, 0, ''),
(135, 135, 0, ''),
(136, 136, 0, ''),
(137, 137, 0, ''),
(138, 138, 0, ''),
(139, 139, 0, ''),
(140, 140, 0, ''),
(141, 141, 0, ''),
(142, 142, 0, ''),
(143, 143, 0, ''),
(144, 144, 0, ''),
(145, 145, 0, ''),
(146, 146, 0, ''),
(147, 147, 0, ''),
(148, 148, 0, ''),
(149, 149, 0, ''),
(150, 150, 0, ''),
(151, 151, 0, ''),
(152, 152, 0, ''),
(153, 153, 0, ''),
(154, 154, 0, ''),
(155, 155, 0, ''),
(156, 156, 0, ''),
(157, 157, 0, ''),
(158, 158, 0, ''),
(159, 159, 0, ''),
(160, 160, 0, ''),
(161, 161, 0, ''),
(162, 162, 0, ''),
(163, 163, 0, ''),
(164, 164, 0, ''),
(165, 165, 0, ''),
(166, 166, 0, ''),
(167, 167, 0, ''),
(168, 168, 0, ''),
(169, 169, 0, ''),
(170, 170, 0, ''),
(171, 171, 0, ''),
(172, 172, 0, ''),
(173, 173, 0, ''),
(174, 174, 0, ''),
(175, 175, 0, ''),
(176, 176, 0, ''),
(177, 177, 0, ''),
(178, 178, 0, ''),
(179, 179, 0, ''),
(180, 180, 0, ''),
(181, 181, 0, ''),
(182, 182, 0, ''),
(183, 183, 0, ''),
(184, 184, 0, ''),
(185, 185, 0, ''),
(186, 186, 0, ''),
(187, 187, 0, ''),
(188, 188, 0, ''),
(189, 189, 0, ''),
(190, 190, 0, ''),
(191, 191, 0, ''),
(192, 192, 0, ''),
(193, 193, 0, ''),
(194, 194, 0, ''),
(195, 195, 0, ''),
(196, 196, 0, ''),
(197, 197, 0, ''),
(198, 198, 0, ''),
(199, 199, 0, ''),
(200, 200, 0, ''),
(201, 201, 0, ''),
(202, 202, 0, ''),
(203, 203, 0, ''),
(204, 204, 0, ''),
(205, 205, 0, ''),
(206, 206, 0, ''),
(207, 207, 0, ''),
(208, 208, 0, ''),
(209, 209, 0, ''),
(210, 210, 0, ''),
(211, 211, 0, ''),
(212, 212, 0, ''),
(213, 213, 0, ''),
(214, 214, 0, ''),
(215, 215, 0, ''),
(216, 216, 0, ''),
(217, 217, 0, ''),
(218, 218, 0, ''),
(219, 219, 0, ''),
(220, 220, 0, ''),
(221, 221, 0, ''),
(222, 222, 0, ''),
(223, 223, 0, ''),
(224, 224, 0, ''),
(225, 225, 0, ''),
(226, 226, 0, ''),
(227, 227, 0, ''),
(228, 228, 0, ''),
(229, 229, 0, ''),
(230, 230, 0, ''),
(231, 231, 0, ''),
(232, 232, 0, ''),
(233, 233, 0, ''),
(234, 234, 0, ''),
(235, 235, 0, ''),
(236, 236, 0, ''),
(237, 237, 0, ''),
(238, 238, 0, ''),
(239, 239, 0, ''),
(240, 240, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_ddos_pais_rango`
--

CREATE TABLE `bloqueo_ddos_pais_rango` (
  `id_bloqueo_ddos_pais_rango` int(11) NOT NULL,
  `id_pais` int(11) NOT NULL,
  `nombre_pais` varchar(200) NOT NULL,
  `iso` varchar(200) NOT NULL,
  `iso3` varchar(200) NOT NULL,
  `fecha_bloqueo_pais` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_ip`
--

CREATE TABLE `bloqueo_ip` (
  `id_bloqueo_ip` int(11) NOT NULL,
  `ip_bloqueada` varchar(100) DEFAULT NULL,
  `fecha_bloqueo_ip` datetime DEFAULT NULL,
  `tipo_ataque_ip` varchar(200) NOT NULL,
  `fecha_bloqueo_ip2` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_ip_pais`
--

CREATE TABLE `bloqueo_ip_pais` (
  `id_bloqueo_ip_pais` int(11) NOT NULL,
  `id_pais` int(11) NOT NULL,
  `total_bloqueo_ip_pais` int(11) NOT NULL,
  `iso3` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bloqueo_ip_pais`
--

INSERT INTO `bloqueo_ip_pais` (`id_bloqueo_ip_pais`, `id_pais`, `total_bloqueo_ip_pais`, `iso3`) VALUES
(1, 1, 0, ''),
(2, 2, 0, ''),
(3, 3, 0, ''),
(4, 4, 0, ''),
(5, 5, 0, ''),
(6, 6, 0, ''),
(7, 7, 0, ''),
(8, 8, 0, ''),
(9, 9, 0, ''),
(10, 10, 0, ''),
(11, 11, 0, ''),
(12, 12, 0, ''),
(13, 13, 0, ''),
(14, 14, 0, ''),
(15, 15, 0, ''),
(16, 16, 0, ''),
(17, 17, 0, ''),
(18, 18, 0, ''),
(19, 19, 0, ''),
(20, 20, 0, ''),
(21, 21, 0, ''),
(22, 22, 0, ''),
(23, 23, 0, ''),
(24, 24, 0, ''),
(25, 25, 0, ''),
(26, 26, 0, ''),
(27, 27, 0, ''),
(28, 28, 0, ''),
(29, 29, 0, ''),
(30, 30, 0, ''),
(31, 31, 0, ''),
(32, 32, 0, ''),
(33, 33, 0, ''),
(34, 34, 0, ''),
(35, 35, 0, ''),
(36, 36, 0, ''),
(37, 37, 0, ''),
(38, 38, 0, ''),
(39, 39, 0, ''),
(40, 40, 0, ''),
(41, 41, 0, ''),
(42, 42, 0, ''),
(43, 43, 0, ''),
(44, 44, 0, ''),
(45, 45, 0, ''),
(46, 46, 0, ''),
(47, 47, 0, ''),
(48, 48, 0, ''),
(49, 49, 0, ''),
(50, 50, 0, ''),
(51, 51, 0, ''),
(52, 52, 0, ''),
(53, 53, 0, ''),
(54, 54, 0, ''),
(55, 55, 0, ''),
(56, 56, 0, ''),
(57, 57, 0, ''),
(58, 58, 0, ''),
(59, 59, 0, ''),
(60, 60, 0, ''),
(61, 61, 0, ''),
(62, 62, 0, ''),
(63, 63, 0, ''),
(64, 64, 0, ''),
(65, 65, 0, ''),
(66, 66, 0, ''),
(67, 67, 0, ''),
(68, 68, 0, ''),
(69, 69, 0, ''),
(70, 70, 0, ''),
(71, 71, 0, ''),
(72, 72, 0, ''),
(73, 73, 0, ''),
(74, 74, 0, ''),
(75, 75, 0, ''),
(76, 76, 0, ''),
(77, 77, 0, ''),
(78, 78, 0, ''),
(79, 79, 0, ''),
(80, 80, 0, ''),
(81, 81, 0, ''),
(82, 82, 0, ''),
(83, 83, 0, ''),
(84, 84, 0, ''),
(85, 85, 0, ''),
(86, 86, 0, ''),
(87, 87, 0, ''),
(88, 88, 0, ''),
(89, 89, 0, ''),
(90, 90, 0, ''),
(91, 91, 0, ''),
(92, 92, 0, ''),
(93, 93, 0, ''),
(94, 94, 0, ''),
(95, 95, 0, ''),
(96, 96, 0, ''),
(97, 97, 0, ''),
(98, 98, 0, ''),
(99, 99, 0, ''),
(100, 100, 0, ''),
(101, 101, 0, ''),
(102, 102, 0, ''),
(103, 103, 0, ''),
(104, 104, 0, ''),
(105, 105, 0, ''),
(106, 106, 0, ''),
(107, 107, 0, ''),
(108, 108, 0, ''),
(109, 109, 0, ''),
(110, 110, 0, ''),
(111, 111, 0, ''),
(112, 112, 0, ''),
(113, 113, 0, ''),
(114, 114, 0, ''),
(115, 115, 0, ''),
(116, 116, 0, ''),
(117, 117, 0, ''),
(118, 118, 0, ''),
(119, 119, 0, ''),
(120, 120, 0, ''),
(121, 121, 0, ''),
(122, 122, 0, ''),
(123, 123, 0, ''),
(124, 124, 0, ''),
(125, 125, 0, ''),
(126, 126, 0, ''),
(127, 127, 0, ''),
(128, 128, 0, ''),
(129, 129, 0, ''),
(130, 130, 0, ''),
(131, 131, 0, ''),
(132, 132, 0, ''),
(133, 133, 0, ''),
(134, 134, 0, ''),
(135, 135, 0, ''),
(136, 136, 0, ''),
(137, 137, 0, ''),
(138, 138, 0, ''),
(139, 139, 0, ''),
(140, 140, 0, ''),
(141, 141, 0, ''),
(142, 142, 0, ''),
(143, 143, 0, ''),
(144, 144, 0, ''),
(145, 145, 0, ''),
(146, 146, 0, ''),
(147, 147, 0, ''),
(148, 148, 0, ''),
(149, 149, 0, ''),
(150, 150, 0, ''),
(151, 151, 0, ''),
(152, 152, 0, ''),
(153, 153, 0, ''),
(154, 154, 0, ''),
(155, 155, 0, ''),
(156, 156, 0, ''),
(157, 157, 0, ''),
(158, 158, 0, ''),
(159, 159, 0, ''),
(160, 160, 0, ''),
(161, 161, 0, ''),
(162, 162, 0, ''),
(163, 163, 0, ''),
(164, 164, 0, ''),
(165, 165, 0, ''),
(166, 166, 0, ''),
(167, 167, 0, ''),
(168, 168, 0, ''),
(169, 169, 0, ''),
(170, 170, 0, ''),
(171, 171, 0, ''),
(172, 172, 0, ''),
(173, 173, 0, ''),
(174, 174, 0, ''),
(175, 175, 0, ''),
(176, 176, 0, ''),
(177, 177, 0, ''),
(178, 178, 0, ''),
(179, 179, 0, ''),
(180, 180, 0, ''),
(181, 181, 0, ''),
(182, 182, 0, ''),
(183, 183, 0, ''),
(184, 184, 0, ''),
(185, 185, 0, ''),
(186, 186, 0, ''),
(187, 187, 0, ''),
(188, 188, 0, ''),
(189, 189, 0, ''),
(190, 190, 0, ''),
(191, 191, 0, ''),
(192, 192, 0, ''),
(193, 193, 0, ''),
(194, 194, 0, ''),
(195, 195, 0, ''),
(196, 196, 0, ''),
(197, 197, 0, ''),
(198, 198, 0, ''),
(199, 199, 0, ''),
(200, 200, 0, ''),
(201, 201, 0, ''),
(202, 202, 0, ''),
(203, 203, 0, ''),
(204, 204, 0, ''),
(205, 205, 0, ''),
(206, 206, 0, ''),
(207, 207, 0, ''),
(208, 208, 0, ''),
(209, 209, 0, ''),
(210, 210, 0, ''),
(211, 211, 0, ''),
(212, 212, 0, ''),
(213, 213, 0, ''),
(214, 214, 0, ''),
(215, 215, 0, ''),
(216, 216, 0, ''),
(217, 217, 0, ''),
(218, 218, 0, ''),
(219, 219, 0, ''),
(220, 220, 0, ''),
(221, 221, 0, ''),
(222, 222, 0, ''),
(223, 223, 0, ''),
(224, 224, 0, ''),
(225, 225, 0, ''),
(226, 226, 0, ''),
(227, 227, 0, ''),
(228, 228, 0, ''),
(229, 229, 0, ''),
(230, 230, 0, ''),
(231, 231, 0, ''),
(232, 232, 0, ''),
(233, 233, 0, ''),
(234, 234, 0, ''),
(235, 235, 0, ''),
(236, 236, 0, ''),
(237, 237, 0, ''),
(238, 238, 0, ''),
(239, 239, 0, ''),
(240, 240, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_master`
--

CREATE TABLE `bloqueo_master` (
  `id_bloqueo_master` int(11) NOT NULL,
  `id_bloqueo_referencia` int(11) NOT NULL,
  `tabla` varchar(100) NOT NULL,
  `ip_bloqueda` varchar(200) NOT NULL,
  `fecha_bloqueo` datetime NOT NULL,
  `tipo_ataque` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_pais`
--

CREATE TABLE `bloqueo_pais` (
  `id_bloqueo_pais` int(11) NOT NULL,
  `id_pais` int(11) NOT NULL,
  `total_bloqueo` int(11) NOT NULL,
  `iso3` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bloqueo_pais`
--

INSERT INTO `bloqueo_pais` (`id_bloqueo_pais`, `id_pais`, `total_bloqueo`, `iso3`) VALUES
(1, 1, 0, ''),
(2, 2, 0, ''),
(3, 3, 0, ''),
(4, 4, 0, ''),
(5, 5, 0, ''),
(6, 6, 0, ''),
(7, 7, 0, ''),
(8, 8, 0, ''),
(9, 9, 0, ''),
(10, 10, 0, ''),
(11, 11, 0, ''),
(12, 12, 0, ''),
(13, 13, 0, ''),
(14, 14, 0, ''),
(15, 15, 0, ''),
(16, 16, 0, ''),
(17, 17, 0, ''),
(18, 18, 0, ''),
(19, 19, 0, ''),
(20, 20, 0, ''),
(21, 21, 0, ''),
(22, 22, 0, ''),
(23, 23, 0, ''),
(24, 24, 0, ''),
(25, 25, 0, ''),
(26, 26, 0, ''),
(27, 27, 0, ''),
(28, 28, 0, ''),
(29, 29, 0, ''),
(30, 30, 0, ''),
(31, 31, 0, ''),
(32, 32, 0, ''),
(33, 33, 0, ''),
(34, 34, 0, ''),
(35, 35, 0, ''),
(36, 36, 0, ''),
(37, 37, 0, ''),
(38, 38, 0, ''),
(39, 39, 0, ''),
(40, 40, 0, ''),
(41, 41, 0, ''),
(42, 42, 0, ''),
(43, 43, 0, ''),
(44, 44, 0, ''),
(45, 45, 0, ''),
(46, 46, 0, ''),
(47, 47, 0, ''),
(48, 48, 0, ''),
(49, 49, 0, ''),
(50, 50, 0, ''),
(51, 51, 0, ''),
(52, 52, 0, ''),
(53, 53, 0, ''),
(54, 54, 0, ''),
(55, 55, 0, ''),
(56, 56, 0, ''),
(57, 57, 0, ''),
(58, 58, 0, ''),
(59, 59, 0, ''),
(60, 60, 0, ''),
(61, 61, 0, ''),
(62, 62, 0, ''),
(63, 63, 0, ''),
(64, 64, 0, ''),
(65, 65, 0, ''),
(66, 66, 0, ''),
(67, 67, 0, ''),
(68, 68, 0, ''),
(69, 69, 0, ''),
(70, 70, 0, ''),
(71, 71, 0, ''),
(72, 72, 0, ''),
(73, 73, 0, ''),
(74, 74, 0, ''),
(75, 75, 0, ''),
(76, 76, 0, ''),
(77, 77, 0, ''),
(78, 78, 0, ''),
(79, 79, 0, ''),
(80, 80, 0, ''),
(81, 81, 0, ''),
(82, 82, 0, ''),
(83, 83, 0, ''),
(84, 84, 0, ''),
(85, 85, 0, ''),
(86, 86, 0, ''),
(87, 87, 0, ''),
(88, 88, 0, ''),
(89, 89, 0, ''),
(90, 90, 0, ''),
(91, 91, 0, ''),
(92, 92, 0, ''),
(93, 93, 0, ''),
(94, 94, 0, ''),
(95, 95, 0, ''),
(96, 96, 0, ''),
(97, 97, 0, ''),
(98, 98, 0, ''),
(99, 99, 0, ''),
(100, 100, 0, ''),
(101, 101, 0, ''),
(102, 102, 0, ''),
(103, 103, 0, ''),
(104, 104, 0, ''),
(105, 105, 0, ''),
(106, 106, 0, ''),
(107, 107, 0, ''),
(108, 108, 0, ''),
(109, 109, 0, ''),
(110, 110, 0, ''),
(111, 111, 0, ''),
(112, 112, 0, ''),
(113, 113, 0, ''),
(114, 114, 0, ''),
(115, 115, 0, ''),
(116, 116, 0, ''),
(117, 117, 0, ''),
(118, 118, 0, ''),
(119, 119, 0, ''),
(120, 120, 0, ''),
(121, 121, 0, ''),
(122, 122, 0, ''),
(123, 123, 0, ''),
(124, 124, 0, ''),
(125, 125, 0, ''),
(126, 126, 0, ''),
(127, 127, 0, ''),
(128, 128, 0, ''),
(129, 129, 0, ''),
(130, 130, 0, ''),
(131, 131, 0, ''),
(132, 132, 0, ''),
(133, 133, 0, ''),
(134, 134, 0, ''),
(135, 135, 0, ''),
(136, 136, 0, ''),
(137, 137, 0, ''),
(138, 138, 0, ''),
(139, 139, 0, ''),
(140, 140, 0, ''),
(141, 141, 0, ''),
(142, 142, 0, ''),
(143, 143, 0, ''),
(144, 144, 0, ''),
(145, 145, 0, ''),
(146, 146, 0, ''),
(147, 147, 0, ''),
(148, 148, 0, ''),
(149, 149, 0, ''),
(150, 150, 0, ''),
(151, 151, 0, ''),
(152, 152, 0, ''),
(153, 153, 0, ''),
(154, 154, 0, ''),
(155, 155, 0, ''),
(156, 156, 0, ''),
(157, 157, 0, ''),
(158, 158, 0, ''),
(159, 159, 0, ''),
(160, 160, 0, ''),
(161, 161, 0, ''),
(162, 162, 0, ''),
(163, 163, 0, ''),
(164, 164, 0, ''),
(165, 165, 0, ''),
(166, 166, 0, ''),
(167, 167, 0, ''),
(168, 168, 0, ''),
(169, 169, 0, ''),
(170, 170, 0, ''),
(171, 171, 0, ''),
(172, 172, 0, ''),
(173, 173, 0, ''),
(174, 174, 0, ''),
(175, 175, 0, ''),
(176, 176, 0, ''),
(177, 177, 0, ''),
(178, 178, 0, ''),
(179, 179, 0, ''),
(180, 180, 0, ''),
(181, 181, 0, ''),
(182, 182, 0, ''),
(183, 183, 0, ''),
(184, 184, 0, ''),
(185, 185, 0, ''),
(186, 186, 0, ''),
(187, 187, 0, ''),
(188, 188, 0, ''),
(189, 189, 0, ''),
(190, 190, 0, ''),
(191, 191, 0, ''),
(192, 192, 0, ''),
(193, 193, 0, ''),
(194, 194, 0, ''),
(195, 195, 0, ''),
(196, 196, 0, ''),
(197, 197, 0, ''),
(198, 198, 0, ''),
(199, 199, 0, ''),
(200, 200, 0, ''),
(201, 201, 0, ''),
(202, 202, 0, ''),
(203, 203, 0, ''),
(204, 204, 0, ''),
(205, 205, 0, ''),
(206, 206, 0, ''),
(207, 207, 0, ''),
(208, 208, 0, ''),
(209, 209, 0, ''),
(210, 210, 0, ''),
(211, 211, 0, ''),
(212, 212, 0, ''),
(213, 213, 0, ''),
(214, 214, 0, ''),
(215, 215, 0, ''),
(216, 216, 0, ''),
(217, 217, 0, ''),
(218, 218, 0, ''),
(219, 219, 0, ''),
(220, 220, 0, ''),
(221, 221, 0, ''),
(222, 222, 0, ''),
(223, 223, 0, ''),
(224, 224, 0, ''),
(225, 225, 0, ''),
(226, 226, 0, ''),
(227, 227, 0, ''),
(228, 228, 0, ''),
(229, 229, 0, ''),
(230, 230, 0, ''),
(231, 231, 0, ''),
(232, 232, 0, ''),
(233, 233, 0, ''),
(234, 234, 0, ''),
(235, 235, 0, ''),
(236, 236, 0, ''),
(237, 237, 0, ''),
(238, 238, 0, ''),
(239, 239, 0, ''),
(240, 240, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `bloqueo_pais_rango`
--

CREATE TABLE `bloqueo_pais_rango` (
  `id_bloqueo_rango` int(11) UNSIGNED NOT NULL,
  `id_pais` int(11) NOT NULL,
  `nombre_pais` varchar(200) NOT NULL,
  `iso` varchar(200) NOT NULL,
  `iso3` varchar(200) NOT NULL,
  `idN` int(11) NOT NULL,
  `fecha_bloqueo_pais` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conexiones`
--

CREATE TABLE `conexiones` (
  `id_conexion` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `conexiones`
--

INSERT INTO `conexiones` (`id_conexion`, `cantidad`, `fecha_registro`) VALUES
(1, 0, '2022-12-01');

-- --------------------------------------------------------

--
-- Table structure for table `credentials_email`
--

CREATE TABLE `credentials_email` (
  `id_credential` int(11) NOT NULL,
  `dominio_mail` varchar(50) NOT NULL,
  `host_mail` varchar(200) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `password_mail` varchar(200) NOT NULL,
  `smtp_secure` varchar(30) NOT NULL,
  `port` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `credentials_email`
--

INSERT INTO `credentials_email` (`id_credential`, `dominio_mail`, `host_mail`, `user_email`, `password_mail`, `smtp_secure`, `port`) VALUES
(1, '192.185.131.136', 'mail.netsoluciones.com', 'info@netsoluciones.com', 'wazup3UThl4L51', 'ssl', 465);

-- --------------------------------------------------------

--
-- Table structure for table `datos_server`
--

CREATE TABLE `datos_server` (
  `id_server` int(11) NOT NULL,
  `memoria` varchar(255) NOT NULL,
  `disco` varchar(255) NOT NULL,
  `cpu` varchar(255) NOT NULL,
  `fecha_reg` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detalle_rule`
--

CREATE TABLE `detalle_rule` (
  `id_detalle_r` int(11) NOT NULL,
  `id_rule` int(11) NOT NULL,
  `nombre_d_r` varchar(255) NOT NULL,
  `activo_r` int(11) NOT NULL,
  `numero_rule_detalle` int(11) NOT NULL,
  `fecha_c_d_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `detalle_rule`
--

INSERT INTO `detalle_rule` (`id_detalle_r`, `id_rule`, `nombre_d_r`, `activo_r`, `numero_rule_detalle`, `fecha_c_d_r`) VALUES
(1, 1, 'weird request, unable to parse', 1, 1, '2018-08-09'),
(2, 1, 'request too big, stored on disk and not parsed', 1, 2, '2018-08-09'),
(3, 1, 'invalid hex encoding, null bytes', 1, 10, '2018-08-09'),
(4, 1, 'unknown content-type', 1, 11, '2018-08-09'),
(5, 1, 'invalid formatted urls', 1, 12, '2018-08-09'),
(6, 1, 'invalid POST formats', 1, 13, '2018-08-09'),
(7, 1, 'invalid POST boundary', 1, 14, '2018-08-09'),
(8, 1, 'invalid JSON', 1, 15, '2018-08-09'),
(9, 1, 'empty POST', 1, 16, '2018-08-09'),
(10, 1, 'libinjection_sql', 1, 17, '2018-08-09'),
(11, 1, 'libinjection_xss', 1, 18, '2018-08-09'),
(12, 2, 'sql keywords', 1, 1000, '2018-08-09'),
(13, 2, 'double quote', 1, 1001, '2018-08-09'),
(14, 2, 'possible hex encoding', 1, 1002, '2018-08-09'),
(15, 2, 'mysql comment (/*)', 1, 1003, '2018-08-09'),
(16, 2, 'mysql comment (*/)', 1, 1004, '2018-08-09'),
(17, 2, 'mysql keyword (|)', 1, 1005, '2018-08-09'),
(18, 2, 'mysql keyword (&&)', 1, 1006, '2018-08-09'),
(19, 2, 'mysql comment (--)', 1, 1007, '2018-08-09'),
(20, 2, 'semicolon', 1, 1008, '2018-08-09'),
(21, 2, 'equal sign in var, probable sql/xss', 1, 1009, '2018-08-09'),
(22, 2, 'open parenthesis, probable sql/xss', 1, 1010, '2018-08-09'),
(23, 2, 'close parenthesis, probable sql/xss', 1, 1011, '2018-08-09'),
(24, 2, 'simple quote', 1, 1013, '2018-08-09'),
(25, 2, 'comma', 1, 1015, '2018-08-09'),
(26, 2, 'mysql comment (#)', 1, 1016, '2018-08-09'),
(27, 2, 'double arobase (@@)', 1, 1017, '2018-08-09'),
(28, 3, 'http:// scheme', 1, 1100, '2018-08-09'),
(29, 3, 'https:// scheme', 1, 1101, '2018-08-09'),
(30, 3, 'ftp:// scheme', 1, 1102, '2018-08-09'),
(31, 3, 'php:// scheme', 1, 1103, '2018-08-09'),
(32, 3, 'sftp:// scheme', 1, 1104, '2018-08-09'),
(33, 3, 'zlib:// scheme', 1, 1105, '2018-08-09'),
(34, 3, 'data:// scheme', 1, 1106, '2018-08-09'),
(35, 3, 'glob:// scheme', 1, 1107, '2018-08-09'),
(36, 3, 'phar:// scheme', 1, 1108, '2018-08-09'),
(37, 3, 'file:// scheme', 1, 1109, '2018-08-09'),
(38, 3, 'gopher:// scheme', 1, 1110, '2018-08-09'),
(39, 4, 'double dot', 1, 1200, '2018-08-09'),
(40, 4, 'obvious probe', 1, 1202, '2018-08-09'),
(41, 4, 'obvious windows path', 1, 1203, '2018-08-09'),
(42, 4, 'obvious probe', 1, 1204, '2018-08-09'),
(43, 4, 'backslash', 1, 1205, '2018-08-09'),
(44, 4, 'slash in args', 1, 1206, '2018-08-09'),
(45, 5, 'html open tag', 1, 1302, '2018-08-09'),
(46, 5, 'html close tag', 1, 1303, '2018-08-09'),
(47, 5, 'open square backet ([), possible js', 1, 1310, '2018-08-09'),
(48, 5, 'close square bracket (]), possible js', 1, 1311, '2018-08-09'),
(49, 5, 'tilde (~) character', 1, 1312, '2018-08-09'),
(50, 5, 'grave accent (`)', 1, 1314, '2018-08-09'),
(51, 5, 'double encoding', 1, 1315, '2018-08-09'),
(52, 6, 'utf7/8 encoding', 1, 1400, '2018-08-09'),
(53, 6, 'M$ encoding', 1, 1401, '2018-08-09'),
(54, 7, 'asp/php/jsp/htaccess file upload', 1, 1500, '2018-08-09'),
(55, 3, 'zip:// scheme', 1, 1111, '2020-10-28'),
(56, 3, 'expect:// scheme', 1, 1112, '2020-10-28'),
(57, 3, 'input:// scheme', 1, 1113, '2020-10-28'),
(58, 4, 'dir traversal bypass', 1, 1207, '2020-10-28'),
(94, 1, 'no generic rules', 1, 19, '2022-03-05'),
(95, 1, 'bad utf8', 1, 20, '2022-03-05'),
(731, 1, 'illegal host header', 1, 21, '2023-01-15'),
(732, 2, 'json functions and operators', 1, 1018, '2023-01-15'),
(733, 4, 'dir traversal bypass', 1, 1208, '2023-01-15'),
(734, 4, 'dir traversal bypass', 1, 1209, '2023-01-15'),
(735, 4, 'dir traversal bypass', 1, 1210, '2023-01-15'),
(736, 7, 'uploaded filename contains non-printable ascii chars', 1, 1501, '2023-01-15'),
(737, 62, 'Havij-SQL_scanner', 1, 42000312, '2023-01-20'),
(738, 62, 'Abnormal double http:// in HTTP header,', 1, 42000310, '2023-01-20'),
(739, 62, 'Wordpress-UA, probably Botnet-Attack', 1, 42000317, '2023-01-20'),
(740, 62, 'MASSCAN - UA Detected', 1, 42000326, '2023-01-20'),
(741, 62, 'SensePost Wikto-Scanner', 1, 42000452, '2023-01-20'),
(742, 62, 'acunetix scan nginx buffer size ', 1, 42001326, '2023-01-20'),
(743, 62, 'acunetix scan website ', 1, 42001327, '2023-01-20'),
(744, 62, 'acunetix scan website ', 1, 42001328, '2023-01-20'),
(745, 62, 'Scanner webmole', 1, 42000159, '2023-01-20'),
(746, 62, 'Some Scanner  nlpproject.info', 1, 42000454, '2023-01-20'),
(747, 62, 'Cloud-Mapping-Scanner', 1, 42000453, '2023-01-20'),
(748, 62, 'Sucuri Vulnerability Scaner', 1, 42000364, '2023-01-20'),
(749, 62, 'Brutus - Scanner', 1, 42000258, '2023-01-20'),
(750, 62, 'PHPMyAdmin - Scanner (2) ', 1, 42000244, '2023-01-20'),
(751, 62, 'PHPMyAdmin - Scanner', 1, 42000243, '2023-01-20'),
(752, 62, 'PHPPgAdmin - Scanner', 1, 42000242, '2023-01-20'),
(753, 62, 'MysqlDumper - Scanner ', 1, 42000241, '2023-01-20'),
(754, 62, 'AB - ApacheBenchmark-Tool detected', 1, 42000240, '2023-01-20'),
(755, 62, 'Netsparker-Scan in Progress', 1, 42000202, '2023-01-20'),
(756, 62, 'Scanner sqlmap sql injection', 1, 42000203, '2023-01-20'),
(757, 62, 'Scanner Mysqloit  - Mysql Injection Takover Tool', 1, 42000200, '2023-01-20'),
(758, 62, 'Scanner IBM NSA User Agent', 1, 42000198, '2023-01-20'),
(759, 62, 'Scanner DavTest WebDav Vulnerability Scanner', 1, 42000194, '2023-01-20'),
(760, 62, 'Scanner w3af', 1, 42000178, '2023-01-20'),
(761, 62, 'PHP-Injetion on UA', 1, 42000174, '2023-01-20'),
(762, 62, 'Scanner whisker', 1, 42000171, '2023-01-20'),
(763, 62, 'Scanner whatweb', 1, 42000151, '2023-01-20'),
(764, 62, 'DirBuster Web App Scan in Progress', 1, 42000036, '2023-01-20'),
(765, 62, 'gzinflate in URI', 1, 42000259, '2023-01-20'),
(766, 62, '/bin/sh in URI', 1, 42000257, '2023-01-20'),
(767, 62, 'possible CONF-File - Access', 1, 42000252, '2023-01-20'),
(768, 62, 'possible INI - File - Access', 1, 42000254, '2023-01-20'),
(769, 62, 'SFTP-config-file access', 1, 42000084, '2023-01-20'),
(770, 62, 'php supply chain attack ', 1, 42000085, '2023-01-20'),
(771, 62, 'log4j attack detection ', 1, 42000086, '2023-01-20'),
(772, 63, 'HOST-Header Injection', 1, 42000465, '2023-01-20'),
(773, 63, ' possible XML/XXE-Exploitation atempt (Doctype)', 1, 42000455, '2023-01-20'),
(774, 63, 'Meterpreter-UA detected', 1, 42000381, '2023-01-20'),
(775, 63, 'GIT-Homedir-Access', 1, 42000329, '2023-01-20'),
(776, 63, 'PHP_SYSTEM_CMD', 1, 42000049, '2023-01-20'),
(777, 63, 'HTTP - Smuggling-Attempt (NewLine in URI)', 1, 42000278, '2023-01-20'),
(778, 64, 'Wordpress XMLRPC possible Password Brute Force', 1, 42000442, '2023-01-20'),
(779, 64, 'WordPress XMLRPC Enumeration system.listMethods', 1, 42000443, '2023-01-20'),
(780, 64, 'WordPress XMLRPC Enumeration system.getCapabilities', 1, 42000444, '2023-01-20'),
(781, 64, 'WordPress TotalCache-DBCache-Access', 1, 42000125, '2023-01-20'),
(782, 64, 'WordPress Uploadify-Access', 1, 42000126, '2023-01-20'),
(783, 64, 'Access To mm-forms-community upload dir', 1, 42000060, '2023-01-20'),
(3089, 55, 'Wordpress in user-agent', 1, 10000000, '2026-05-28'),
(3090, 55, 'masscan in user-agent', 1, 10000001, '2026-05-28'),
(3091, 55, 'acunetix scan nginx buffer size', 1, 10000002, '2026-05-28'),
(3092, 55, 'acunetix scan website', 1, 10000003, '2026-05-28'),
(3093, 55, 'acunetix scan website', 1, 10000004, '2026-05-28'),
(3094, 55, 'Havij in user-agent', 1, 10000005, '2026-05-28'),
(3095, 55, 'webmole in user-agent', 1, 10000006, '2026-05-28'),
(3096, 55, 'nlpproject.info in user-agent', 1, 10000007, '2026-05-28'),
(3097, 55, 'cloudmapping in user-agent', 1, 10000008, '2026-05-28'),
(3098, 55, 'Sucuri in user-agent', 1, 10000009, '2026-05-28'),
(3099, 55, 'Brutus in user-agent', 1, 10000010, '2026-05-28'),
(3100, 55, 'apachebench in user-agent', 1, 10000011, '2026-05-28'),
(3101, 55, 'netsparker in user-agent', 1, 10000012, '2026-05-28'),
(3102, 55, 'Mysqloit in user-agent', 1, 10000013, '2026-05-28'),
(3103, 55, 'network-services-auditor in user-agent', 1, 10000014, '2026-05-28'),
(3104, 55, 'dav.pm in user-agent', 1, 10000015, '2026-05-28'),
(3105, 55, 'w3af in user-agent', 1, 10000016, '2026-05-28'),
(3106, 55, 'PHP-Injetion on UA', 1, 10000017, '2026-05-28'),
(3107, 55, 'whisker in user-agent', 1, 10000018, '2026-05-28'),
(3108, 55, 'whatweb in user-agent', 1, 10000019, '2026-05-28'),
(3109, 55, 'DirBuster in user-agent', 1, 10000020, '2026-05-28'),
(3110, 55, 'zerodium in user-agent', 1, 10000021, '2026-05-28'),
(3111, 55, 'log4j attack detection', 1, 10000022, '2026-05-28'),
(3112, 55, 'python in user-agent', 1, 10000023, '2026-05-28'),
(3113, 55, 'meterpreter in user-agent', 1, 10000024, '2026-05-28'),
(3114, 55, 'zgrab in user-agent', 1, 10000025, '2026-05-28'),
(3115, 55, 'nmap in user-agent', 1, 10000026, '2026-05-28'),
(3116, 55, 'curl in user-agent', 1, 10000027, '2026-05-28'),
(3117, 55, 'wget in user-agent', 1, 10000028, '2026-05-28'),
(3118, 55, 'slqmap in user-agent', 1, 10000029, '2026-05-28'),
(3119, 55, 'paloaltonetworks in user-agent', 1, 10000030, '2026-05-28'),
(3120, 55, 'palo alto network in user-agent', 1, 10000031, '2026-05-28'),
(3121, 55, 'Expense in user-agent', 1, 10000032, '2026-05-28'),
(3122, 55, 'NetSystemsResearch in user-agent', 1, 10000033, '2026-05-28'),
(3123, 55, 'Golang in user-agent', 1, 10000034, '2026-05-28'),
(3124, 55, 'libwww-perl in user-agent', 1, 10000035, '2026-05-28'),
(3125, 55, 'l9tcpid in user-agent', 1, 10000036, '2026-05-28'),
(3126, 55, 'l9explore in user-agent', 1, 10000037, '2026-05-28'),
(3127, 55, 'WPScan in user-agent', 1, 10000038, '2026-05-28'),
(3128, 55, 'WinHttpReq in user-agent', 1, 10000039, '2026-05-28'),
(3129, 55, 'EgyScan security scanner', 1, 10000040, '2026-05-28'),
(3130, 55, 'GuzzleHttp in user-agent', 1, 10000041, '2026-05-28'),
(3131, 55, 'AsyncHttpClient in user-agent', 1, 10000042, '2026-05-28'),
(3132, 59, 'gzinflate in URI', 1, 40000000, '2026-05-28'),
(3133, 59, 'php system called', 1, 40000001, '2026-05-28'),
(3134, 59, 'php base64_decode called', 1, 40000002, '2026-05-28'),
(3135, 59, 'php eval called', 1, 40000003, '2026-05-28'),
(3136, 59, 'php eval called', 1, 40000004, '2026-05-28'),
(3137, 59, 'SQL Admin Interface', 1, 40000005, '2026-05-28'),
(3138, 59, 'SQL Admin Interface', 1, 40000006, '2026-05-28'),
(3139, 59, 'SQL Admin Interface', 1, 40000007, '2026-05-28'),
(3140, 59, 'MysqlDumper', 1, 40000008, '2026-05-28'),
(3141, 59, 'SQL Admin Interface', 1, 40000009, '2026-05-28'),
(3142, 59, 'SQL Admin Interface', 1, 40000010, '2026-05-28'),
(3143, 59, 'SQL Admin Interface', 1, 40000011, '2026-05-28'),
(3144, 59, 'SQL Admin Interface', 1, 40000012, '2026-05-28'),
(3145, 59, 'SQL Admin Interface', 1, 40000013, '2026-05-28'),
(3146, 59, 'SQL Admin Interface', 1, 40000014, '2026-05-28'),
(3147, 59, 'SQL Admin Interface', 1, 40000015, '2026-05-28'),
(3148, 59, 'SQL Admin Interface', 1, 40000016, '2026-05-28'),
(3149, 59, 'SQL Admin Interface', 1, 40000017, '2026-05-28'),
(3150, 59, 'SQL Admin Interface', 1, 40000018, '2026-05-28'),
(3151, 59, 'SQL Admin Interface', 1, 40000019, '2026-05-28'),
(3152, 59, 'SQL Admin Interface', 1, 40000020, '2026-05-28'),
(3153, 59, 'SQL Admin Interface', 1, 40000021, '2026-05-28'),
(3154, 59, 'SQL Admin Interface', 1, 40000022, '2026-05-28'),
(3155, 59, 'SQL Admin Interface', 1, 40000023, '2026-05-28'),
(3156, 59, 'SQL Admin Interface', 1, 40000024, '2026-05-28'),
(3157, 59, 'SQL Admin Interface', 1, 40000025, '2026-05-28'),
(3158, 59, 'CVE-2017-9841', 1, 40000026, '2026-05-28'),
(3159, 59, 'PHP easter egg credits', 1, 40000027, '2026-05-28'),
(3160, 59, 'Block PHP Xdebug', 1, 40000028, '2026-05-28'),
(3161, 59, 'PHPinfo access', 1, 40000029, '2026-05-28'),
(3162, 59, 'Access to php install', 1, 40000030, '2026-05-28'),
(3163, 59, 'SQL Admin Interface', 1, 40000031, '2026-05-28'),
(3164, 59, 'SQL Admin Interface', 1, 40000032, '2026-05-28'),
(3165, 59, 'SQL Admin Interface', 1, 40000033, '2026-05-28'),
(3166, 59, 'SQL Admin Interface', 1, 40000034, '2026-05-28'),
(3167, 59, 'Access to Lavarel telescope', 1, 40000035, '2026-05-28'),
(3168, 59, 'SQL Admin Interface', 1, 40000036, '2026-05-28'),
(3169, 59, 'Symfony Web Framework dev mode', 1, 40000037, '2026-05-28'),
(3170, 59, 'phpstorm in request', 1, 40000038, '2026-05-28'),
(3171, 57, 'Wordpress XMLRPC possible Password Brute Force', 1, 30000000, '2026-05-28'),
(3172, 57, 'WordPress XMLRPC Enumeration system.listMethods', 1, 30000001, '2026-05-28'),
(3173, 57, 'WordPress XMLRPC Enumeration system.getCapabilities', 1, 30000002, '2026-05-28'),
(3174, 57, 'WordPress TotalCache-DBCache-Access', 1, 30000003, '2026-05-28'),
(3175, 57, 'WordPress Uploadify-Access', 1, 30000004, '2026-05-28'),
(3176, 57, 'Access To mm-forms-community upload dir', 1, 30000005, '2026-05-28'),
(3177, 57, 'WordPress access to wp-config.php', 1, 30000006, '2026-05-28'),
(3178, 57, 'WordPress malicious access to ALFA_DATA path', 1, 30000007, '2026-05-28'),
(3179, 57, 'WordPress malicious access to alfacgiapi path', 1, 30000008, '2026-05-28'),
(3180, 57, 'WordPress malicious access to cgialfa path', 1, 30000009, '2026-05-28'),
(3181, 56, 'file access to .conf', 1, 20000000, '2026-05-28'),
(3182, 56, 'file access to .ini', 1, 20000001, '2026-05-28'),
(3183, 56, 'file access to .sql', 1, 20000002, '2026-05-28'),
(3184, 56, 'file access to .txt', 1, 20000003, '2026-05-28'),
(3185, 56, 'file access to sftp-config.json', 1, 20000004, '2026-05-28'),
(3186, 56, 'bazaar version control folder access', 1, 20000005, '2026-05-28'),
(3187, 56, 'git version control folder access', 1, 20000006, '2026-05-28'),
(3188, 56, 'mercurial version control folder access', 1, 20000007, '2026-05-28'),
(3189, 56, 'svn version control folder access', 1, 20000008, '2026-05-28'),
(3190, 56, 'bazaar version control folder access', 1, 20000009, '2026-05-28'),
(3191, 56, 'git version control folder access', 1, 20000010, '2026-05-28'),
(3192, 56, 'mercurial version control folder access', 1, 20000011, '2026-05-28'),
(3193, 56, 'svn version control folder access', 1, 20000012, '2026-05-28'),
(3194, 56, 'file access to .htpasswd', 1, 20000013, '2026-05-28'),
(3195, 56, 'file access to .htaccess', 1, 20000014, '2026-05-28'),
(3196, 56, 'file access to .ds_store', 1, 20000015, '2026-05-28'),
(3197, 56, 'file access to changelog', 1, 20000016, '2026-05-28'),
(3198, 56, 'file access to core dumps', 1, 20000017, '2026-05-28'),
(3199, 56, 'file access to .module (drupal)', 1, 20000018, '2026-05-28'),
(3200, 56, 'file access to web.config (drupal)', 1, 20000019, '2026-05-28'),
(3201, 56, 'file access to release notes', 1, 20000020, '2026-05-28'),
(3202, 56, 'file access to cache files', 1, 20000021, '2026-05-28'),
(3203, 56, 'folder access to WEB-INF', 1, 20000022, '2026-05-28'),
(3204, 56, 'Exposed OpenWRT', 1, 20000023, '2026-05-28'),
(3205, 56, 'Exposed cgi-bin', 1, 20000024, '2026-05-28'),
(3206, 56, 'Exposed Jenkins', 1, 20000025, '2026-05-28'),
(3207, 56, 'Exposed Oracle WebLogic Server Administration Console', 1, 20000026, '2026-05-28'),
(3208, 56, 'Exposed Nuxeo Enterprise Platform', 1, 20000027, '2026-05-28'),
(3209, 56, 'Exposed Zabbix', 1, 20000028, '2026-05-28'),
(3210, 56, 'burp collaborator', 1, 20000029, '2026-05-28'),
(3211, 56, 'Netsparker', 1, 20000030, '2026-05-28'),
(3212, 56, 'HTTP - Smuggling-Attempt (NewLine in URI)', 1, 20000031, '2026-05-28'),
(3213, 56, 'HOST-Header Injection', 1, 20000032, '2026-05-28'),
(3214, 56, '/bin/sh in URI', 1, 20000033, '2026-05-28'),
(3215, 56, '/etc/passwd in URI', 1, 20000034, '2026-05-28'),
(3216, 56, '/etc/shadow in URI', 1, 20000035, '2026-05-28'),
(3217, 56, '/etc/hosts in URI', 1, 20000036, '2026-05-28'),
(3218, 56, '/Windows/system.ini in URI', 1, 20000037, '2026-05-28'),
(3219, 56, ' possible XML/XXE-Exploitation atempt (Doctype)', 1, 20000038, '2026-05-28'),
(3220, 56, 'Abnormal double http:// in HTTP header', 1, 20000039, '2026-05-28'),
(3221, 56, 'Abnormal double http:// in HTTP header', 1, 20000040, '2026-05-28'),
(3222, 56, 'Abnormal double http:// in HTTP header', 1, 20000041, '2026-05-28'),
(3223, 56, 'Abnormal double http:// in HTTP header', 1, 20000042, '2026-05-28'),
(3224, 56, 'CVE-2018-20062', 1, 20000043, '2026-05-28'),
(3225, 56, 'AWS Credential Stealer', 1, 20000044, '2026-05-28'),
(3226, 56, 'Access to dot folder or file', 1, 20000045, '2026-05-28'),
(3227, 56, 'Exposed Microsoft Exchange', 1, 20000046, '2026-05-28'),
(3228, 56, 'Exposed Microsoft Exchange', 1, 20000047, '2026-05-28'),
(3229, 56, 'Exposed Microsoft Exchange', 1, 20000048, '2026-05-28'),
(3230, 56, 'CVE-2021-3129', 1, 20000049, '2026-05-28'),
(3231, 56, 'CVE-2018-13379', 1, 20000050, '2026-05-28'),
(3232, 56, 'Exposed Apache Tomcat Administration Panel', 1, 20000051, '2026-05-28'),
(3233, 56, 'NMAP enumeration attempt', 1, 20000052, '2026-05-28'),
(3234, 56, 'NMAP enumeration attempt', 1, 20000053, '2026-05-28'),
(3235, 56, 'NMAP enumeration attempt', 1, 20000054, '2026-05-28'),
(3236, 56, 'NMAP enumeration attempt', 1, 20000055, '2026-05-28'),
(3237, 56, 'NMAP enumeration attempt', 1, 20000056, '2026-05-28'),
(3238, 56, 'NMAP enumeration attempt', 1, 20000057, '2026-05-28'),
(3239, 56, 'Siemens PLC scan', 1, 20000058, '2026-05-28'),
(3240, 56, 'Siemens PLC scan', 1, 20000059, '2026-05-28'),
(3241, 56, 'Siemens PLC scan', 1, 20000060, '2026-05-28'),
(3242, 56, 'Citrix XenApp', 1, 20000061, '2026-05-28'),
(3243, 56, 'CVE-2018-1000861', 1, 20000062, '2026-05-28'),
(3244, 56, 'CVE-2019-1003029, CVE-2019-1003030', 1, 20000063, '2026-05-28'),
(3245, 56, 'Attempted Log4J Bypass', 1, 20000064, '2026-05-28'),
(3246, 56, 'Windowssystem.ini in URI', 1, 20000065, '2026-05-28'),
(3247, 56, '/Windows/win.ini in URI', 1, 20000066, '2026-05-28'),
(3248, 56, 'Windowswin.ini in URI', 1, 20000067, '2026-05-28'),
(3249, 56, 'Exposed Apache Host Manager App', 1, 20000068, '2026-05-28'),
(3250, 56, 'CVE-2022-22947', 1, 20000069, '2026-05-28'),
(3251, 56, 'CVE-2022-22965', 1, 20000070, '2026-05-28'),
(3252, 56, 'CVE-2022-22965', 1, 20000071, '2026-05-28'),
(3253, 56, 'CVE-2021-28481', 1, 20000072, '2026-05-28'),
(3254, 56, 'Prevent IndoXploit/IDX Shell dump access', 1, 20000073, '2026-05-28'),
(3255, 56, 'Access all grafana folders', 1, 20000074, '2026-05-28'),
(3256, 56, 'file access to .yml', 1, 20000075, '2026-05-28'),
(3257, 56, 'file access to .yaml', 1, 20000076, '2026-05-28'),
(3258, 56, 'file access to .ctmpl', 1, 20000077, '2026-05-28'),
(3259, 56, 'file access to .hcl', 1, 20000078, '2026-05-28'),
(3260, 56, 'file access to .md', 1, 20000079, '2026-05-28'),
(3261, 56, 'file access to readme', 1, 20000080, '2026-05-28'),
(3262, 56, 'file access to .toml', 1, 20000081, '2026-05-28'),
(3263, 56, 'Exposed AWS Elastic Beanstalk configuration', 1, 20000082, '2026-05-28'),
(3264, 56, 'file access to temporary backup files', 1, 20000083, '2026-05-28'),
(3265, 56, 'path traversal in nuxt framework', 1, 20000084, '2026-05-28'),
(3266, 56, 'Exposed AWS config files', 1, 20000085, '2026-05-28'),
(3267, 56, 'Exposed AWS config files', 1, 20000086, '2026-05-28'),
(3268, 56, 'Exposed AWS config files', 1, 20000087, '2026-05-28'),
(3269, 56, 'Exposed AWS config files', 1, 20000088, '2026-05-28'),
(3270, 56, 'Exposed temp copy', 1, 20000089, '2026-05-28'),
(3271, 56, 'Exposed temp copy', 1, 20000090, '2026-05-28'),
(3272, 56, 'Exposed temp copy', 1, 20000091, '2026-05-28'),
(3273, 56, 'Exposed vscode directory', 1, 20000092, '2026-05-28'),
(3274, 56, 'access to .env file or dir', 1, 20000093, '2026-05-28'),
(3275, 56, 'androxgh0st exploit', 1, 20000094, '2026-05-28'),
(3276, 58, 'Transact-SQL GESP', 1, 50000000, '2026-05-28'),
(3277, 58, 'SQL Injection', 1, 50000001, '2026-05-28'),
(3278, 58, 'SQL Injection', 1, 50000002, '2026-05-28'),
(3279, 58, 'SQL Injection', 1, 50000003, '2026-05-28'),
(3280, 58, 'SQL Injection', 1, 50000004, '2026-05-28'),
(3281, 58, 'SQL Injection', 1, 50000005, '2026-05-28'),
(3282, 58, 'SQL Injection', 1, 50000006, '2026-05-28');

-- --------------------------------------------------------

--
-- Table structure for table `geoippais`
--

CREATE TABLE `geoippais` (
  `id_geoippais` int(11) NOT NULL,
  `iso_pais` varchar(50) NOT NULL,
  `activo_geoip` int(11) NOT NULL,
  `fecha_geoip` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_bloqueo`
--

CREATE TABLE `grafica_bloqueo` (
  `id_grafica_bloqueo` int(11) NOT NULL,
  `fecha_bloqueo` date NOT NULL,
  `total_bloqueo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_bloqueo_bots`
--

CREATE TABLE `grafica_bloqueo_bots` (
  `id_grafica_bot` int(11) NOT NULL,
  `fecha_bloqueo_bot` date NOT NULL,
  `total_bloqueo_bot` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_bloqueo_ip`
--

CREATE TABLE `grafica_bloqueo_ip` (
  `id_grafica_ip` int(11) NOT NULL,
  `fecha_bloqueo_ip` date NOT NULL,
  `total_bloqueo_ip` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_bloqueo_rango`
--

CREATE TABLE `grafica_bloqueo_rango` (
  `id_grafica_bloqueo_rango` int(11) UNSIGNED NOT NULL,
  `fecha_bloqueo_rango` datetime NOT NULL,
  `total_bloqueo_rango` int(11) NOT NULL,
  `rango_bloqueo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_bloqueo_rango_ip`
--

CREATE TABLE `grafica_bloqueo_rango_ip` (
  `id_grafica_bloqueo_rango_ip` int(11) NOT NULL,
  `fecha_bloqueo_rango_ip` datetime NOT NULL,
  `total_bloqueo_rango_ip` int(11) NOT NULL,
  `rango_bloqueo_ip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_consulta`
--

CREATE TABLE `grafica_consulta` (
  `id_grafica` int(11) NOT NULL,
  `fecha_bloqueo` date NOT NULL,
  `total_waf` int(11) NOT NULL,
  `total_fuerza` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_ddos`
--

CREATE TABLE `grafica_ddos` (
  `id_grafica_ddos` int(11) NOT NULL,
  `ip_ddos` varchar(100) NOT NULL,
  `fecha_ddos` date NOT NULL,
  `total_ddos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_principal`
--

CREATE TABLE `grafica_principal` (
  `id_grafica` int(11) NOT NULL,
  `fecha_bloqueo` date NOT NULL,
  `total_waf` int(11) NOT NULL,
  `total_fuerza` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_visitas`
--

CREATE TABLE `grafica_visitas` (
  `id_grafica_visitas` int(11) UNSIGNED NOT NULL,
  `dominio` varchar(255) NOT NULL,
  `fecha_visita` date NOT NULL,
  `total_visita` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grafica_visitas_dominio`
--

CREATE TABLE `grafica_visitas_dominio` (
  `id_grafica_visita` int(11) NOT NULL,
  `fecha_visita` datetime NOT NULL,
  `total_visita` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_vulnerability`
--

CREATE TABLE `history_vulnerability` (
  `id_history` int(11) NOT NULL,
  `host` varchar(100) NOT NULL,
  `resultado` varchar(200) NOT NULL,
  `fecha_escaneo` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `host`
--

CREATE TABLE `host` (
  `id_host` int(11) NOT NULL,
  `nombre_host` varchar(200) NOT NULL,
  `fecha_r_host` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `host_borrados`
--

CREATE TABLE `host_borrados` (
  `id_host_borrado` int(11) UNSIGNED NOT NULL,
  `nombre_host` varchar(255) NOT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `host_visita_borrados`
--

CREATE TABLE `host_visita_borrados` (
  `id_host_visita_borrados` int(11) UNSIGNED NOT NULL,
  `nombre_host` varchar(255) NOT NULL,
  `fecha_r` date NOT NULL,
  `visita_limpia` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notificacion_regla`
--

CREATE TABLE `notificacion_regla` (
  `id_notificacion` int(11) NOT NULL,
  `id_regla` int(11) NOT NULL,
  `activa_notificacion` int(11) NOT NULL,
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notificacion_regla`
--

INSERT INTO `notificacion_regla` (`id_notificacion`, `id_regla`, `activa_notificacion`, `fecha_registro`) VALUES
(1, 55, 1, '2025-12-28'),
(2, 59, 1, '2025-12-28'),
(3, 57, 1, '2025-12-28'),
(4, 56, 1, '2025-12-28'),
(5, 58, 0, '2025-12-28'),
(6, 55, 1, '2026-01-15'),
(7, 59, 1, '2026-01-15'),
(8, 57, 1, '2026-01-15'),
(9, 56, 1, '2026-01-15'),
(10, 58, 1, '2026-01-15'),
(11, 55, 1, '2026-01-28'),
(12, 59, 1, '2026-01-28'),
(13, 57, 1, '2026-01-28'),
(14, 56, 0, '2026-01-28'),
(15, 58, 0, '2026-01-28'),
(16, 55, 1, '2026-02-15'),
(17, 59, 1, '2026-02-15'),
(18, 57, 1, '2026-02-15'),
(19, 56, 1, '2026-02-15'),
(20, 58, 1, '2026-02-15'),
(21, 55, 1, '2026-02-28'),
(22, 59, 1, '2026-02-28'),
(23, 57, 1, '2026-02-28'),
(24, 56, 0, '2026-02-28'),
(25, 58, 0, '2026-02-28'),
(26, 55, 1, '2026-03-15'),
(27, 59, 1, '2026-03-15'),
(28, 57, 1, '2026-03-15'),
(29, 56, 1, '2026-03-15'),
(30, 58, 1, '2026-03-15'),
(31, 55, 1, '2026-03-28'),
(32, 59, 1, '2026-03-28'),
(33, 57, 1, '2026-03-28'),
(34, 56, 1, '2026-03-28'),
(35, 58, 1, '2026-03-28'),
(36, 55, 1, '2026-04-15'),
(37, 59, 1, '2026-04-15'),
(38, 57, 1, '2026-04-15'),
(39, 56, 1, '2026-04-15'),
(40, 58, 1, '2026-04-15'),
(41, 55, 1, '2026-04-28'),
(42, 59, 1, '2026-04-28'),
(43, 57, 1, '2026-04-28'),
(44, 56, 1, '2026-04-28'),
(45, 58, 1, '2026-04-28'),
(46, 55, 1, '2026-05-15'),
(47, 59, 1, '2026-05-15'),
(48, 57, 1, '2026-05-15'),
(49, 56, 1, '2026-05-15'),
(50, 58, 1, '2026-05-15'),
(51, 55, 1, '2026-05-28'),
(52, 59, 1, '2026-05-28'),
(53, 57, 1, '2026-05-28'),
(54, 56, 1, '2026-05-28'),
(55, 58, 1, '2026-05-28');

-- --------------------------------------------------------

--
-- Table structure for table `openai_api`
--

CREATE TABLE `openai_api` (
  `id_openai_api` int(11) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `activado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `openai_api`
--

INSERT INTO `openai_api` (`id_openai_api`, `api_key`, `activado`) VALUES
(1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `paises`
--

CREATE TABLE `paises` (
  `id_pais` int(11) NOT NULL,
  `iso` varchar(20) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `paises`
--

INSERT INTO `paises` (`id_pais`, `iso`, `nombre`) VALUES
(1, 'AF', 'Afganistán'),
(2, 'AX', 'Islas Gland'),
(3, 'AL', 'Albania'),
(4, 'DE', 'Alemania'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antártida'),
(9, 'AG', 'Antigua y Barbuda'),
(10, 'AN', 'Antillas Holandesas'),
(11, 'SA', 'Arabia Saudí'),
(12, 'DZ', 'Argelia'),
(13, 'AR', 'Argentina'),
(14, 'AM', 'Armenia'),
(15, 'AW', 'Aruba'),
(16, 'AU', 'Australia'),
(17, 'AT', 'Austria'),
(18, 'AZ', 'Azerbaiyán'),
(19, 'BS', 'Bahamas'),
(20, 'BH', 'Bahréin'),
(21, 'BD', 'Bangladesh'),
(22, 'BB', 'Barbados'),
(23, 'BY', 'Bielorrusia'),
(24, 'BE', 'Bélgica'),
(25, 'BZ', 'Belice'),
(26, 'BJ', 'Benin'),
(27, 'BM', 'Bermudas'),
(28, 'BT', 'Bhután'),
(29, 'BO', 'Bolivia'),
(30, 'BA', 'Bosnia y Herzegovina'),
(31, 'BW', 'Botsuana'),
(32, 'BV', 'Isla Bouvet'),
(33, 'BR', 'Brasil'),
(34, 'BN', 'Brunéi'),
(35, 'BG', 'Bulgaria'),
(36, 'BF', 'Burkina Faso'),
(37, 'BI', 'Burundi'),
(38, 'CV', 'Cabo Verde'),
(39, 'KY', 'Islas Caimán'),
(40, 'KH', 'Camboya'),
(41, 'CM', 'Camerún'),
(42, 'CA', 'Canadá'),
(43, 'CF', 'República Centroafricana'),
(44, 'TD', 'Chad'),
(45, 'CZ', 'República Checa'),
(46, 'CL', 'Chile'),
(47, 'CN', 'China'),
(48, 'CY', 'Chipre'),
(49, 'CX', 'Isla de Navidad'),
(50, 'VA', 'Ciudad del Vaticano'),
(51, 'CC', 'Islas Cocos'),
(52, 'CO', 'Colombia'),
(53, 'KM', 'Comoras'),
(54, 'CD', 'República Democrática del Congo'),
(55, 'CG', 'Congo'),
(56, 'CK', 'Islas Cook'),
(57, 'KP', 'Corea del Norte'),
(58, 'KR', 'Corea del Sur'),
(59, 'CI', 'Costa de Marfil'),
(60, 'CR', 'Costa Rica'),
(61, 'HR', 'Croacia'),
(62, 'CU', 'Cuba'),
(63, 'DK', 'Dinamarca'),
(64, 'DM', 'Dominica'),
(65, 'DO', 'República Dominicana'),
(66, 'EC', 'Ecuador'),
(67, 'EG', 'Egipto'),
(68, 'SV', 'El Salvador'),
(69, 'AE', 'Emiratos Árabes Unidos'),
(70, 'ER', 'Eritrea'),
(71, 'SK', 'Eslovaquia'),
(72, 'SI', 'Eslovenia'),
(73, 'ES', 'España'),
(74, 'UM', 'Islas ultramarinas de Estados Unidos'),
(75, 'US', 'Estados Unidos'),
(76, 'EE', 'Estonia'),
(77, 'ET', 'Etiopía'),
(78, 'FO', 'Islas Feroe'),
(79, 'PH', 'Filipinas'),
(80, 'FI', 'Finlandia'),
(81, 'FJ', 'Fiyi'),
(82, 'FR', 'Francia'),
(83, 'GA', 'Gabón'),
(84, 'GM', 'Gambia'),
(85, 'GE', 'Georgia'),
(86, 'GS', 'Islas Georgias del Sur y Sandwich del Sur'),
(87, 'GH', 'Ghana'),
(88, 'GI', 'Gibraltar'),
(89, 'GD', 'Granada'),
(90, 'GR', 'Grecia'),
(91, 'GL', 'Groenlandia'),
(92, 'GP', 'Guadalupe'),
(93, 'GU', 'Guam'),
(94, 'GT', 'Guatemala'),
(95, 'GF', 'Guayana Francesa'),
(96, 'GN', 'Guinea'),
(97, 'GQ', 'Guinea Ecuatorial'),
(98, 'GW', 'Guinea-Bissau'),
(99, 'GY', 'Guyana'),
(100, 'HT', 'Haití'),
(101, 'HM', 'Islas Heard y McDonald'),
(102, 'HN', 'Honduras'),
(103, 'HK', 'Hong Kong'),
(104, 'HU', 'Hungría'),
(105, 'IN', 'India'),
(106, 'ID', 'Indonesia'),
(107, 'IR', 'Irán'),
(108, 'IQ', 'Iraq'),
(109, 'IE', 'Irlanda'),
(110, 'IS', 'Islandia'),
(111, 'IL', 'Israel'),
(112, 'IT', 'Italia'),
(113, 'JM', 'Jamaica'),
(114, 'JP', 'Japón'),
(115, 'JO', 'Jordania'),
(116, 'KZ', 'Kazajstán'),
(117, 'KE', 'Kenia'),
(118, 'KG', 'Kirguistán'),
(119, 'KI', 'Kiribati'),
(120, 'KW', 'Kuwait'),
(121, 'LA', 'Laos'),
(122, 'LS', 'Lesotho'),
(123, 'LV', 'Letonia'),
(124, 'LB', 'Líbano'),
(125, 'LR', 'Liberia'),
(126, 'LY', 'Libia'),
(127, 'LI', 'Liechtenstein'),
(128, 'LT', 'Lituania'),
(129, 'LU', 'Luxemburgo'),
(130, 'MO', 'Macao'),
(131, 'MK', 'ARY Macedonia'),
(132, 'MG', 'Madagascar'),
(133, 'MY', 'Malasia'),
(134, 'MW', 'Malawi'),
(135, 'MV', 'Maldivas'),
(136, 'ML', 'Malí'),
(137, 'MT', 'Malta'),
(138, 'FK', 'Islas Malvinas'),
(139, 'MP', 'Islas Marianas del Norte'),
(140, 'MA', 'Marruecos'),
(141, 'MH', 'Islas Marshall'),
(142, 'MQ', 'Martinica'),
(143, 'MU', 'Mauricio'),
(144, 'MR', 'Mauritania'),
(145, 'YT', 'Mayotte'),
(146, 'MX', 'México'),
(147, 'FM', 'Micronesia'),
(148, 'MD', 'Moldavia'),
(149, 'MC', 'Mónaco'),
(150, 'MN', 'Mongolia'),
(151, 'MS', 'Montserrat'),
(152, 'MZ', 'Mozambique'),
(153, 'MM', 'Myanmar'),
(154, 'NA', 'Namibia'),
(155, 'NR', 'Nauru'),
(156, 'NP', 'Nepal'),
(157, 'NI', 'Nicaragua'),
(158, 'NE', 'Níger'),
(159, 'NG', 'Nigeria'),
(160, 'NU', 'Niue'),
(161, 'NF', 'Isla Norfolk'),
(162, 'NO', 'Noruega'),
(163, 'NC', 'Nueva Caledonia'),
(164, 'NZ', 'Nueva Zelanda'),
(165, 'OM', 'Omán'),
(166, 'NL', 'Países Bajos'),
(167, 'PK', 'Pakistán'),
(168, 'PW', 'Palau'),
(169, 'PS', 'Palestina'),
(170, 'PA', 'Panamá'),
(171, 'PG', 'Papúa Nueva Guinea'),
(172, 'PY', 'Paraguay'),
(173, 'PE', 'Perú'),
(174, 'PN', 'Islas Pitcairn'),
(175, 'PF', 'Polinesia Francesa'),
(176, 'PL', 'Polonia'),
(177, 'PT', 'Portugal'),
(178, 'PR', 'Puerto Rico'),
(179, 'QA', 'Qatar'),
(180, 'GB', 'Reino Unido'),
(181, 'RE', 'Reunión'),
(182, 'RW', 'Ruanda'),
(183, 'RO', 'Rumania'),
(184, 'RU', 'Rusia'),
(185, 'EH', 'Sahara Occidental'),
(186, 'SB', 'Islas Salomón'),
(187, 'WS', 'Samoa'),
(188, 'AS', 'Samoa Americana'),
(189, 'KN', 'San Cristóbal y Nevis'),
(190, 'SM', 'San Marino'),
(191, 'PM', 'San Pedro y Miquelón'),
(192, 'VC', 'San Vicente y las Granadinas'),
(193, 'SH', 'Santa Helena'),
(194, 'LC', 'Santa Lucía'),
(195, 'ST', 'Santo Tomé y Príncipe'),
(196, 'SN', 'Senegal'),
(197, 'CS', 'Serbia y Montenegro'),
(198, 'SC', 'Seychelles'),
(199, 'SL', 'Sierra Leona'),
(200, 'SG', 'Singapur'),
(201, 'SY', 'Siria'),
(202, 'SO', 'Somalia'),
(203, 'LK', 'Sri Lanka'),
(204, 'SZ', 'Suazilandia'),
(205, 'ZA', 'Sudáfrica'),
(206, 'SD', 'Sudán'),
(207, 'SE', 'Suecia'),
(208, 'CH', 'Suiza'),
(209, 'SR', 'Surinam'),
(210, 'SJ', 'Svalbard y Jan Mayen'),
(211, 'TH', 'Tailandia'),
(212, 'TW', 'Taiwán'),
(213, 'TZ', 'Tanzania'),
(214, 'TJ', 'Tayikistán'),
(215, 'IO', 'Territorio Británico del Océano Índico'),
(216, 'TF', 'Territorios Australes Franceses'),
(217, 'TL', 'Timor Oriental'),
(218, 'TG', 'Togo'),
(219, 'TK', 'Tokelau'),
(220, 'TO', 'Tonga'),
(221, 'TT', 'Trinidad y Tobago'),
(222, 'TN', 'Túnez'),
(223, 'TC', 'Islas Turcas y Caicos'),
(224, 'TM', 'Turkmenistán'),
(225, 'TR', 'Turquía'),
(226, 'TV', 'Tuvalu'),
(227, 'UA', 'Ucrania'),
(228, 'UG', 'Uganda'),
(229, 'UY', 'Uruguay'),
(230, 'UZ', 'Uzbekistán'),
(231, 'VU', 'Vanuatu'),
(232, 'VE', 'Venezuela'),
(233, 'VN', 'Vietnam'),
(234, 'VG', 'Islas Vírgenes Británicas'),
(235, 'VI', 'Islas Vírgenes de los Estados Unidos'),
(236, 'WF', 'Wallis y Futuna'),
(237, 'YE', 'Yemen'),
(238, 'DJ', 'Yibuti'),
(239, 'ZM', 'Zambia'),
(240, 'ZW', 'Zimbabue');

-- --------------------------------------------------------

--
-- Table structure for table `reporte`
--

CREATE TABLE `reporte` (
  `id_reporte` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `rango` int(11) NOT NULL,
  `tipo_periodo` varchar(90) NOT NULL,
  `activo_periodo` int(11) NOT NULL,
  `fecha_periodo` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respaldos`
--

CREATE TABLE `respaldos` (
  `id_respaldo` int(11) NOT NULL,
  `tipo_respaldo` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `ruta_respaldo` varchar(200) NOT NULL,
  `fecha_r` date NOT NULL,
  `activo_respaldo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resumen_datos`
--

CREATE TABLE `resumen_datos` (
  `id_resumen` int(11) NOT NULL,
  `paquetes` int(11) NOT NULL,
  `virus` int(11) NOT NULL,
  `bloqueo_ip` int(11) NOT NULL,
  `bloqueo_waf` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `resumen_datos`
--

INSERT INTO `resumen_datos` (`id_resumen`, `paquetes`, `virus`, `bloqueo_ip`, `bloqueo_waf`) VALUES
(1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id_rule` int(11) NOT NULL,
  `nombre_rule` varchar(255) NOT NULL,
  `inicio_rule` int(11) NOT NULL,
  `fin_rule` int(11) NOT NULL,
  `activo_rule` int(11) NOT NULL,
  `fecha_c_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `rules`
--

INSERT INTO `rules` (`id_rule`, `nombre_rule`, `inicio_rule`, `fin_rule`, `activo_rule`, `fecha_c_r`) VALUES
(1, 'INTERNAL RULES', 1, 999, 1, '2018-08-09'),
(2, 'SQL Injections', 1000, 1099, 1, '2018-08-09'),
(3, 'OBVIOUS RFI', 1100, 1199, 1, '2018-08-09'),
(4, 'Directory traversal', 1200, 1299, 1, '2018-08-09'),
(5, 'XSS', 1300, 1399, 1, '2018-08-09'),
(6, 'Evading tricks', 1400, 1499, 1, '2018-08-09'),
(7, 'File uploads', 1500, 1600, 1, '2018-08-09'),
(8, 'Havij-SQL_scanner', 0, 0, 1, '2018-08-10'),
(9, 'Abnormal double http:// in HTTP header', 0, 0, 1, '2018-08-10'),
(10, 'Wordpress-UA, probably Botnet-Attack', 0, 0, 1, '2018-08-10'),
(11, 'MASSCAN - UA Detected', 0, 0, 1, '2018-08-10'),
(12, 'SensePost Wikto-Scanner', 0, 0, 1, '2018-08-21'),
(13, 'acunetix scan nginx buffer size ', 0, 0, 1, '2020-03-14'),
(14, 'acunetix scan website', 0, 0, 1, '2022-03-03'),
(15, 'acunetix scan website', 0, 0, 1, '2022-03-03'),
(16, 'Scanner webmole', 0, 0, 1, '2022-03-03'),
(17, 'Some Scanner  nlpproject.info', 0, 0, 1, '2022-03-03'),
(18, 'Cloud-Mapping-Scanner', 0, 0, 1, '2022-03-03'),
(19, 'Sucuri Vulnerability Scaner', 0, 0, 1, '2022-03-03'),
(20, 'Brutus - Scanner', 0, 0, 1, '2022-03-03'),
(21, 'PHPMyAdmin - Scanner (2)', 0, 0, 1, '2022-03-03'),
(22, 'PHPMyAdmin - Scanner', 0, 0, 1, '2022-03-03'),
(23, 'PHPPgAdmin - Scanner', 0, 0, 1, '2022-03-03'),
(24, 'MysqlDumper - Scanner', 0, 0, 1, '2022-03-03'),
(25, 'AB - ApacheBenchmark-Tool detected', 0, 0, 1, '2022-03-03'),
(26, 'Netsparker-Scan in Progress', 0, 0, 1, '2022-03-03'),
(27, 'Scanner sqlmap sql injection', 0, 0, 1, '2022-03-03'),
(28, 'Scanner Mysqloit  - Mysql Injection Takover Tool', 0, 0, 1, '2022-03-03'),
(29, 'Scanner IBM NSA User Agent', 0, 0, 1, '2022-03-03'),
(30, 'Scanner DavTest WebDav Vulnerability Scanner', 0, 0, 1, '2022-03-03'),
(31, 'Scanner w3af', 0, 0, 1, '2022-03-03'),
(32, 'PHP-Injetion on UA', 0, 0, 1, '2022-03-03'),
(33, 'Scanner whisker', 0, 0, 1, '2022-03-03'),
(34, 'Scanner whatweb', 0, 0, 1, '2022-03-03'),
(35, 'DirBuster Web App Scan in Progress', 0, 0, 1, '2022-03-03'),
(36, 'gzinflate in URI', 0, 0, 1, '2022-03-03'),
(37, '/bin/sh in URI', 0, 0, 1, '2022-03-03'),
(38, 'possible CONF-File - Access', 0, 0, 1, '2022-03-03'),
(39, 'possible INI - File - Access', 0, 0, 1, '2022-03-03'),
(40, 'SFTP-config-file access', 0, 0, 1, '2022-03-03'),
(41, 'php supply chain attack', 0, 0, 1, '2022-03-03'),
(42, 'log4j attack detection', 0, 0, 1, '2022-03-03'),
(43, 'HOST-Header Injection', 0, 0, 1, '2022-03-03'),
(44, 'possible XML/XXE-Exploitation atempt (Doctype)', 0, 0, 1, '2022-03-03'),
(45, 'Meterpreter-UA detected', 0, 0, 1, '2022-03-03'),
(46, 'GIT-Homedir-Access', 0, 0, 1, '2022-03-03'),
(47, 'PHP_SYSTEM_CMD', 0, 0, 1, '2022-03-03'),
(48, 'HTTP - Smuggling-Attempt (NewLine in URI)', 0, 0, 1, '2022-03-03'),
(49, 'Wordpress XMLRPC possible Password Brute Force', 0, 0, 1, '2022-03-03'),
(50, 'WordPress XMLRPC Enumeration system.listMethods', 0, 0, 1, '2022-03-03'),
(51, 'WordPress XMLRPC Enumeration system.getCapabilities', 0, 0, 1, '2022-03-03'),
(52, 'WordPress TotalCache-DBCache-Access', 0, 0, 1, '2022-03-03'),
(53, 'WordPress Uploadify-Access', 0, 0, 1, '2022-03-03'),
(54, 'Access To mm-forms-community upload dir', 0, 0, 1, '2022-03-03'),
(55, 'Scanners', 0, 0, 1, '2022-11-14'),
(56, 'Webserver Security', 0, 0, 1, '2022-11-16'),
(57, 'Wordpress', 0, 0, 1, '2022-11-16'),
(58, 'SQL Injection', 0, 0, 1, '2022-11-16'),
(59, 'PHP Security', 0, 0, 1, '2022-11-30'),
(62, 'scanner', 0, 0, 1, '2023-01-20'),
(63, 'web server', 0, 0, 1, '2023-01-20'),
(64, 'wordpress block', 0, 0, 1, '2023-01-20');

-- --------------------------------------------------------

--
-- Table structure for table `sitio`
--

CREATE TABLE `sitio` (
  `id_sitio` int(11) NOT NULL,
  `tipo_sitio` varchar(50) NOT NULL,
  `tipo_config` varchar(255) NOT NULL,
  `tipo_zimbra` varchar(255) NOT NULL,
  `nombre_sitio` varchar(200) NOT NULL,
  `ip_sitio` varchar(100) NOT NULL,
  `puerto_sitio` varchar(50) NOT NULL,
  `tipo_certificado` varchar(20) NOT NULL,
  `activo_sitio` int(11) NOT NULL,
  `fecha_r_sitio` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `activo_geo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sitio_balancer`
--

CREATE TABLE `sitio_balancer` (
  `id_balancer` int(11) NOT NULL,
  `nombre_sitio` varchar(255) NOT NULL,
  `activo_sitio` int(11) NOT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sitio_ssl`
--

CREATE TABLE `sitio_ssl` (
  `id_ssl` int(11) NOT NULL,
  `nombre_sitio` varchar(255) NOT NULL,
  `activo_sitio` int(11) NOT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sitio_zimbra`
--

CREATE TABLE `sitio_zimbra` (
  `id_zimbra` int(11) NOT NULL,
  `nombre_zimbra` varchar(200) NOT NULL,
  `tipo_zimbra` varchar(200) NOT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `threat_ip_list`
--

CREATE TABLE `threat_ip_list` (
  `id_threat` int(11) NOT NULL,
  `id_bloqueo_referencia` int(11) NOT NULL,
  `ip` varchar(200) NOT NULL,
  `source_file` varchar(100) NOT NULL,
  `hits` int(11) NOT NULL,
  `first_seen` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  `codigo_pais` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `threat_ip_list_master_date`
--

CREATE TABLE `threat_ip_list_master_date` (
  `id` int(11) NOT NULL,
  `fecha_master` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `threat_ip_list_master_date`
--

INSERT INTO `threat_ip_list_master_date` (`id`, `fecha_master`) VALUES
(1, '2025-06-01 10:10:05');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre_u` varchar(255) NOT NULL,
  `apellido_u` varchar(255) NOT NULL,
  `email_u` varchar(255) NOT NULL,
  `password_encrip` varchar(500) NOT NULL,
  `tipo_usuario` int(11) NOT NULL,
  `activo_u` int(11) NOT NULL,
  `fecha_c_u` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre_u`, `apellido_u`, `email_u`, `password_encrip`, `tipo_usuario`, `activo_u`, `fecha_c_u`) VALUES
(1, 'admin', 'admin', 'admin@hotmail.es', '$2y$12$P/dlpQfYVEhZeP7jE9IEauGb3/mCI2tzA8y./Gz9R3r/p56bxBK.i', 1, 1, '2019-05-01');

-- --------------------------------------------------------

--
-- Table structure for table `usuario_host`
--

CREATE TABLE `usuario_host` (
  `id_usuario_h` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_host` int(11) NOT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `virus_archivo`
--

CREATE TABLE `virus_archivo` (
  `id_archivo` int(11) NOT NULL,
  `sin_virus` int(11) NOT NULL,
  `con_virus` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visita_dominio`
--

CREATE TABLE `visita_dominio` (
  `id_visita` int(11) NOT NULL,
  `fecha_visita` datetime NOT NULL,
  `ip_visita` varchar(100) NOT NULL,
  `dominio` varchar(255) NOT NULL,
  `activo_visita` int(11) NOT NULL,
  `fecha_visita_date` date GENERATED ALWAYS AS (cast(`fecha_visita` as date)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visita_dominio_group`
--

CREATE TABLE `visita_dominio_group` (
  `id_visita_group` int(11) UNSIGNED NOT NULL,
  `id_visita` int(11) NOT NULL,
  `fecha_visita` datetime NOT NULL,
  `ip_visita` varchar(100) NOT NULL,
  `dominio` varchar(250) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visita_pais`
--

CREATE TABLE `visita_pais` (
  `id_visita_pais` int(11) NOT NULL,
  `id_pais` int(11) NOT NULL,
  `total_visita` int(11) NOT NULL,
  `iso3` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `visita_pais`
--

INSERT INTO `visita_pais` (`id_visita_pais`, `id_pais`, `total_visita`, `iso3`) VALUES
(1, 1, 0, ''),
(2, 2, 0, ''),
(3, 3, 0, ''),
(4, 4, 0, ''),
(5, 5, 0, ''),
(6, 6, 0, ''),
(7, 7, 0, ''),
(8, 8, 0, ''),
(9, 9, 0, ''),
(10, 10, 0, ''),
(11, 11, 0, ''),
(12, 12, 0, ''),
(13, 13, 0, ''),
(14, 14, 0, ''),
(15, 15, 0, ''),
(16, 16, 0, ''),
(17, 17, 0, ''),
(18, 18, 0, ''),
(19, 19, 0, ''),
(20, 20, 0, ''),
(21, 21, 0, ''),
(22, 22, 0, ''),
(23, 23, 0, ''),
(24, 24, 0, ''),
(25, 25, 0, ''),
(26, 26, 0, ''),
(27, 27, 0, ''),
(28, 28, 0, ''),
(29, 29, 0, ''),
(30, 30, 0, ''),
(31, 31, 0, ''),
(32, 32, 0, ''),
(33, 33, 0, ''),
(34, 34, 0, ''),
(35, 35, 0, ''),
(36, 36, 0, ''),
(37, 37, 0, ''),
(38, 38, 0, ''),
(39, 39, 0, ''),
(40, 40, 0, ''),
(41, 41, 0, ''),
(42, 42, 0, ''),
(43, 43, 0, ''),
(44, 44, 0, ''),
(45, 45, 0, ''),
(46, 46, 0, ''),
(47, 47, 0, ''),
(48, 48, 0, ''),
(49, 49, 0, ''),
(50, 50, 0, ''),
(51, 51, 0, ''),
(52, 52, 0, ''),
(53, 53, 0, ''),
(54, 54, 0, ''),
(55, 55, 0, ''),
(56, 56, 0, ''),
(57, 57, 0, ''),
(58, 58, 0, ''),
(59, 59, 0, ''),
(60, 60, 0, ''),
(61, 61, 0, ''),
(62, 62, 0, ''),
(63, 63, 0, ''),
(64, 64, 0, ''),
(65, 65, 0, ''),
(66, 66, 0, ''),
(67, 67, 0, ''),
(68, 68, 0, ''),
(69, 69, 0, ''),
(70, 70, 0, ''),
(71, 71, 0, ''),
(72, 72, 0, ''),
(73, 73, 0, ''),
(74, 74, 0, ''),
(75, 75, 0, ''),
(76, 76, 0, ''),
(77, 77, 0, ''),
(78, 78, 0, ''),
(79, 79, 0, ''),
(80, 80, 0, ''),
(81, 81, 0, ''),
(82, 82, 0, ''),
(83, 83, 0, ''),
(84, 84, 0, ''),
(85, 85, 0, ''),
(86, 86, 0, ''),
(87, 87, 0, ''),
(88, 88, 0, ''),
(89, 89, 0, ''),
(90, 90, 0, ''),
(91, 91, 0, ''),
(92, 92, 0, ''),
(93, 93, 0, ''),
(94, 94, 0, ''),
(95, 95, 0, ''),
(96, 96, 0, ''),
(97, 97, 0, ''),
(98, 98, 0, ''),
(99, 99, 0, ''),
(100, 100, 0, ''),
(101, 101, 0, ''),
(102, 102, 0, ''),
(103, 103, 0, ''),
(104, 104, 0, ''),
(105, 105, 0, ''),
(106, 106, 0, ''),
(107, 107, 0, ''),
(108, 108, 0, ''),
(109, 109, 0, ''),
(110, 110, 0, ''),
(111, 111, 0, ''),
(112, 112, 0, ''),
(113, 113, 0, ''),
(114, 114, 0, ''),
(115, 115, 0, ''),
(116, 116, 0, ''),
(117, 117, 0, ''),
(118, 118, 0, ''),
(119, 119, 0, ''),
(120, 120, 0, ''),
(121, 121, 0, ''),
(122, 122, 0, ''),
(123, 123, 0, ''),
(124, 124, 0, ''),
(125, 125, 0, ''),
(126, 126, 0, ''),
(127, 127, 0, ''),
(128, 128, 0, ''),
(129, 129, 0, ''),
(130, 130, 0, ''),
(131, 131, 0, ''),
(132, 132, 0, ''),
(133, 133, 0, ''),
(134, 134, 0, ''),
(135, 135, 0, ''),
(136, 136, 0, ''),
(137, 137, 0, ''),
(138, 138, 0, ''),
(139, 139, 0, ''),
(140, 140, 0, ''),
(141, 141, 0, ''),
(142, 142, 0, ''),
(143, 143, 0, ''),
(144, 144, 0, ''),
(145, 145, 0, ''),
(146, 146, 0, ''),
(147, 147, 0, ''),
(148, 148, 0, ''),
(149, 149, 0, ''),
(150, 150, 0, ''),
(151, 151, 0, ''),
(152, 152, 0, ''),
(153, 153, 0, ''),
(154, 154, 0, ''),
(155, 155, 0, ''),
(156, 156, 0, ''),
(157, 157, 0, ''),
(158, 158, 0, ''),
(159, 159, 0, ''),
(160, 160, 0, ''),
(161, 161, 0, ''),
(162, 162, 0, ''),
(163, 163, 0, ''),
(164, 164, 0, ''),
(165, 165, 0, ''),
(166, 166, 0, ''),
(167, 167, 0, ''),
(168, 168, 0, ''),
(169, 169, 0, ''),
(170, 170, 0, ''),
(171, 171, 0, ''),
(172, 172, 0, ''),
(173, 173, 0, ''),
(174, 174, 0, ''),
(175, 175, 0, ''),
(176, 176, 0, ''),
(177, 177, 0, ''),
(178, 178, 0, ''),
(179, 179, 0, ''),
(180, 180, 0, ''),
(181, 181, 0, ''),
(182, 182, 0, ''),
(183, 183, 0, ''),
(184, 184, 0, ''),
(185, 185, 0, ''),
(186, 186, 0, ''),
(187, 187, 0, ''),
(188, 188, 0, ''),
(189, 189, 0, ''),
(190, 190, 0, ''),
(191, 191, 0, ''),
(192, 192, 0, ''),
(193, 193, 0, ''),
(194, 194, 0, ''),
(195, 195, 0, ''),
(196, 196, 0, ''),
(197, 197, 0, ''),
(198, 198, 0, ''),
(199, 199, 0, ''),
(200, 200, 0, ''),
(201, 201, 0, ''),
(202, 202, 0, ''),
(203, 203, 0, ''),
(204, 204, 0, ''),
(205, 205, 0, ''),
(206, 206, 0, ''),
(207, 207, 0, ''),
(208, 208, 0, ''),
(209, 209, 0, ''),
(210, 210, 0, ''),
(211, 211, 0, ''),
(212, 212, 0, ''),
(213, 213, 0, ''),
(214, 214, 0, ''),
(215, 215, 0, ''),
(216, 216, 0, ''),
(217, 217, 0, ''),
(218, 218, 0, ''),
(219, 219, 0, ''),
(220, 220, 0, ''),
(221, 221, 0, ''),
(222, 222, 0, ''),
(223, 223, 0, ''),
(224, 224, 0, ''),
(225, 225, 0, ''),
(226, 226, 0, ''),
(227, 227, 0, ''),
(228, 228, 0, ''),
(229, 229, 0, ''),
(230, 230, 0, ''),
(231, 231, 0, ''),
(232, 232, 0, ''),
(233, 233, 0, ''),
(234, 234, 0, ''),
(235, 235, 0, ''),
(236, 236, 0, ''),
(237, 237, 0, ''),
(238, 238, 0, ''),
(239, 239, 0, ''),
(240, 240, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `vulnerability`
--

CREATE TABLE `vulnerability` (
  `id_vulnerability` int(11) NOT NULL,
  `host` varchar(100) NOT NULL,
  `port` varchar(100) NOT NULL,
  `vulnerability` text NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_analisis` datetime NOT NULL,
  `severidad` varchar(250) NOT NULL,
  `more_information` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whitelist`
--

CREATE TABLE `whitelist` (
  `id_white` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `ip_white` varchar(60) NOT NULL,
  `activo_ip` int(11) NOT NULL,
  `fecha_r_ip` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whitelist_ddos`
--

CREATE TABLE `whitelist_ddos` (
  `id_whitelist_ddos` int(11) NOT NULL,
  `ip_whitelist_ddos` varchar(50) NOT NULL,
  `activo` int(11) NOT NULL,
  `fecha_r` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ataques`
--
ALTER TABLE `ataques`
  ADD PRIMARY KEY (`id_ataque`);

--
-- Indexes for table `bloqueo`
--
ALTER TABLE `bloqueo`
  ADD PRIMARY KEY (`id_bloqueo`),
  ADD KEY `idx_bloqueo_fecha_server` (`fecha_bloqueo`,`server`),
  ADD KEY `idx_bloqueo_activo_fecha` (`activo_bloqueo`,`fecha_bloqueo`),
  ADD KEY `idx_bloqueo_server` (`server`),
  ADD KEY `idx_bloqueo_activo_fecha_idn` (`activo_bloqueo`,`fecha_bloqueo`,`idN`);

--
-- Indexes for table `bloqueo_ddos`
--
ALTER TABLE `bloqueo_ddos`
  ADD PRIMARY KEY (`id_bloqueo_ddos`),
  ADD KEY `idx_bloqueo_ddos_fecha_ip` (`fecha_ddos`,`ip_ddos`),
  ADD KEY `idx_bloqueo_ddos_ip_white` (`ip_ddos`,`lista_blanca`),
  ADD KEY `idx_bloqueo_ddos_pais_fecha` (`codigo_pais`,`fecha_ddos`);

--
-- Indexes for table `bloqueo_ddos_pais`
--
ALTER TABLE `bloqueo_ddos_pais`
  ADD PRIMARY KEY (`id_bloqueo_ddos_pais`);

--
-- Indexes for table `bloqueo_ddos_pais_rango`
--
ALTER TABLE `bloqueo_ddos_pais_rango`
  ADD PRIMARY KEY (`id_bloqueo_ddos_pais_rango`),
  ADD KEY `idx_bloqueo_ddos_pais_rango_fecha` (`fecha_bloqueo_pais`);

--
-- Indexes for table `bloqueo_ip`
--
ALTER TABLE `bloqueo_ip`
  ADD PRIMARY KEY (`id_bloqueo_ip`),
  ADD KEY `idx_bloqueo_ip_fecha` (`fecha_bloqueo_ip`),
  ADD KEY `idx_bloqueo_ip_tipo_fecha` (`tipo_ataque_ip`,`fecha_bloqueo_ip`),
  ADD KEY `idx_bloqueo_ip_fecha2` (`fecha_bloqueo_ip2`),
  ADD KEY `idx_bloqueo_ip_fecha_ip` (`fecha_bloqueo_ip`,`ip_bloqueada`(50));

--
-- Indexes for table `bloqueo_ip_pais`
--
ALTER TABLE `bloqueo_ip_pais`
  ADD PRIMARY KEY (`id_bloqueo_ip_pais`);

--
-- Indexes for table `bloqueo_master`
--
ALTER TABLE `bloqueo_master`
  ADD PRIMARY KEY (`id_bloqueo_master`),
  ADD KEY `idx_bloqueo_master_fecha` (`fecha_bloqueo`),
  ADD KEY `idx_bloqueo_master_tabla_fecha` (`tabla`,`fecha_bloqueo`),
  ADD KEY `idx_bloqueo_master_tabla_ref` (`tabla`,`id_bloqueo_referencia`);

--
-- Indexes for table `bloqueo_pais`
--
ALTER TABLE `bloqueo_pais`
  ADD PRIMARY KEY (`id_bloqueo_pais`);

--
-- Indexes for table `bloqueo_pais_rango`
--
ALTER TABLE `bloqueo_pais_rango`
  ADD PRIMARY KEY (`id_bloqueo_rango`),
  ADD KEY `idx_bloqueo_pais_rango_fecha` (`fecha_bloqueo_pais`);

--
-- Indexes for table `conexiones`
--
ALTER TABLE `conexiones`
  ADD PRIMARY KEY (`id_conexion`);

--
-- Indexes for table `credentials_email`
--
ALTER TABLE `credentials_email`
  ADD PRIMARY KEY (`id_credential`);

--
-- Indexes for table `datos_server`
--
ALTER TABLE `datos_server`
  ADD PRIMARY KEY (`id_server`);

--
-- Indexes for table `detalle_rule`
--
ALTER TABLE `detalle_rule`
  ADD PRIMARY KEY (`id_detalle_r`),
  ADD KEY `idx_detalle_rule_activo_numero` (`activo_r`,`numero_rule_detalle`),
  ADD KEY `idx_detalle_rule_id_rule_numero` (`id_rule`,`numero_rule_detalle`);

--
-- Indexes for table `geoippais`
--
ALTER TABLE `geoippais`
  ADD PRIMARY KEY (`id_geoippais`);

--
-- Indexes for table `grafica_bloqueo`
--
ALTER TABLE `grafica_bloqueo`
  ADD PRIMARY KEY (`id_grafica_bloqueo`),
  ADD KEY `idx_grafica_bloqueo_fecha` (`fecha_bloqueo`);

--
-- Indexes for table `grafica_bloqueo_bots`
--
ALTER TABLE `grafica_bloqueo_bots`
  ADD PRIMARY KEY (`id_grafica_bot`),
  ADD KEY `idx_grafica_bloqueo_bots_fecha` (`fecha_bloqueo_bot`);

--
-- Indexes for table `grafica_bloqueo_ip`
--
ALTER TABLE `grafica_bloqueo_ip`
  ADD PRIMARY KEY (`id_grafica_ip`),
  ADD KEY `idx_grafica_bloqueo_ip_fecha` (`fecha_bloqueo_ip`);

--
-- Indexes for table `grafica_bloqueo_rango`
--
ALTER TABLE `grafica_bloqueo_rango`
  ADD PRIMARY KEY (`id_grafica_bloqueo_rango`),
  ADD KEY `idx_rango_fecha` (`rango_bloqueo`,`fecha_bloqueo_rango`);

--
-- Indexes for table `grafica_bloqueo_rango_ip`
--
ALTER TABLE `grafica_bloqueo_rango_ip`
  ADD PRIMARY KEY (`id_grafica_bloqueo_rango_ip`),
  ADD KEY `idx_rango_fecha_ip` (`rango_bloqueo_ip`,`fecha_bloqueo_rango_ip`);

--
-- Indexes for table `grafica_consulta`
--
ALTER TABLE `grafica_consulta`
  ADD PRIMARY KEY (`id_grafica`),
  ADD KEY `idx_grafica_consulta_fecha` (`fecha_bloqueo`);

--
-- Indexes for table `grafica_ddos`
--
ALTER TABLE `grafica_ddos`
  ADD PRIMARY KEY (`id_grafica_ddos`);

--
-- Indexes for table `grafica_principal`
--
ALTER TABLE `grafica_principal`
  ADD PRIMARY KEY (`id_grafica`),
  ADD KEY `idx_grafica_principal_fecha` (`fecha_bloqueo`);

--
-- Indexes for table `grafica_visitas`
--
ALTER TABLE `grafica_visitas`
  ADD PRIMARY KEY (`id_grafica_visitas`),
  ADD UNIQUE KEY `uniq_grafica_visitas_dominio_fecha` (`dominio`,`fecha_visita`),
  ADD KEY `idx_grafica_visitas_fecha` (`fecha_visita`);

--
-- Indexes for table `grafica_visitas_dominio`
--
ALTER TABLE `grafica_visitas_dominio`
  ADD PRIMARY KEY (`id_grafica_visita`),
  ADD UNIQUE KEY `uniq_grafica_visitas_dominio_fecha` (`fecha_visita`);

--
-- Indexes for table `history_vulnerability`
--
ALTER TABLE `history_vulnerability`
  ADD PRIMARY KEY (`id_history`);

--
-- Indexes for table `host`
--
ALTER TABLE `host`
  ADD PRIMARY KEY (`id_host`),
  ADD KEY `idx_host_nombre` (`nombre_host`);

--
-- Indexes for table `host_borrados`
--
ALTER TABLE `host_borrados`
  ADD PRIMARY KEY (`id_host_borrado`),
  ADD KEY `idx_host_borrados_nombre` (`nombre_host`);

--
-- Indexes for table `host_visita_borrados`
--
ALTER TABLE `host_visita_borrados`
  ADD PRIMARY KEY (`id_host_visita_borrados`),
  ADD KEY `idx_host_visita_borrados_nombre` (`nombre_host`),
  ADD KEY `idx_hvb_limpia` (`visita_limpia`);

--
-- Indexes for table `notificacion_regla`
--
ALTER TABLE `notificacion_regla`
  ADD PRIMARY KEY (`id_notificacion`);

--
-- Indexes for table `openai_api`
--
ALTER TABLE `openai_api`
  ADD PRIMARY KEY (`id_openai_api`);

--
-- Indexes for table `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id_pais`),
  ADD KEY `idx_paises_iso` (`iso`);

--
-- Indexes for table `reporte`
--
ALTER TABLE `reporte`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indexes for table `respaldos`
--
ALTER TABLE `respaldos`
  ADD PRIMARY KEY (`id_respaldo`);

--
-- Indexes for table `resumen_datos`
--
ALTER TABLE `resumen_datos`
  ADD PRIMARY KEY (`id_resumen`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id_rule`);

--
-- Indexes for table `sitio`
--
ALTER TABLE `sitio`
  ADD PRIMARY KEY (`id_sitio`);

--
-- Indexes for table `sitio_balancer`
--
ALTER TABLE `sitio_balancer`
  ADD PRIMARY KEY (`id_balancer`);

--
-- Indexes for table `sitio_ssl`
--
ALTER TABLE `sitio_ssl`
  ADD PRIMARY KEY (`id_ssl`);

--
-- Indexes for table `sitio_zimbra`
--
ALTER TABLE `sitio_zimbra`
  ADD PRIMARY KEY (`id_zimbra`);

--
-- Indexes for table `threat_ip_list`
--
ALTER TABLE `threat_ip_list`
  ADD PRIMARY KEY (`id_threat`),
  ADD KEY `idx_threat_ip` (`ip`),
  ADD KEY `idx_threat_ref` (`id_bloqueo_referencia`);

--
-- Indexes for table `threat_ip_list_master_date`
--
ALTER TABLE `threat_ip_list_master_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indexes for table `usuario_host`
--
ALTER TABLE `usuario_host`
  ADD PRIMARY KEY (`id_usuario_h`);

--
-- Indexes for table `virus_archivo`
--
ALTER TABLE `virus_archivo`
  ADD PRIMARY KEY (`id_archivo`);

--
-- Indexes for table `visita_dominio`
--
ALTER TABLE `visita_dominio`
  ADD PRIMARY KEY (`id_visita`),
  ADD KEY `idx_visita_dominio_activo_fecha_dominio` (`activo_visita`,`fecha_visita`,`dominio`),
  ADD KEY `idx_visita_dominio_dominio` (`dominio`),
  ADD KEY `idx_visita_dominio_activo_ip` (`activo_visita`,`ip_visita`),
  ADD KEY `idx_visita_dominio_fecha_ip` (`fecha_visita`,`ip_visita`),
  ADD KEY `idx_visita_dominio_grafica` (`activo_visita`,`fecha_visita_date`,`dominio`);

--
-- Indexes for table `visita_dominio_group`
--
ALTER TABLE `visita_dominio_group`
  ADD PRIMARY KEY (`id_visita_group`),
  ADD UNIQUE KEY `uk_group_ip_visita` (`ip_visita`),
  ADD KEY `idx_visita_dominio_group_dominio` (`dominio`);

--
-- Indexes for table `visita_pais`
--
ALTER TABLE `visita_pais`
  ADD PRIMARY KEY (`id_visita_pais`);

--
-- Indexes for table `vulnerability`
--
ALTER TABLE `vulnerability`
  ADD PRIMARY KEY (`id_vulnerability`),
  ADD UNIQUE KEY `uniq_host_port_vuln_fecha` (`host`,`port`,`vulnerability`,`fecha_analisis`) USING HASH;

--
-- Indexes for table `whitelist`
--
ALTER TABLE `whitelist`
  ADD PRIMARY KEY (`id_white`),
  ADD KEY `idx_whitelist_ip` (`ip_white`);

--
-- Indexes for table `whitelist_ddos`
--
ALTER TABLE `whitelist_ddos`
  ADD PRIMARY KEY (`id_whitelist_ddos`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ataques`
--
ALTER TABLE `ataques`
  MODIFY `id_ataque` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bloqueo`
--
ALTER TABLE `bloqueo`
  MODIFY `id_bloqueo` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key';

--
-- AUTO_INCREMENT for table `bloqueo_ddos`
--
ALTER TABLE `bloqueo_ddos`
  MODIFY `id_bloqueo_ddos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bloqueo_ddos_pais`
--
ALTER TABLE `bloqueo_ddos_pais`
  MODIFY `id_bloqueo_ddos_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `bloqueo_ddos_pais_rango`
--
ALTER TABLE `bloqueo_ddos_pais_rango`
  MODIFY `id_bloqueo_ddos_pais_rango` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bloqueo_ip`
--
ALTER TABLE `bloqueo_ip`
  MODIFY `id_bloqueo_ip` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bloqueo_ip_pais`
--
ALTER TABLE `bloqueo_ip_pais`
  MODIFY `id_bloqueo_ip_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `bloqueo_master`
--
ALTER TABLE `bloqueo_master`
  MODIFY `id_bloqueo_master` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bloqueo_pais`
--
ALTER TABLE `bloqueo_pais`
  MODIFY `id_bloqueo_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `bloqueo_pais_rango`
--
ALTER TABLE `bloqueo_pais_rango`
  MODIFY `id_bloqueo_rango` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conexiones`
--
ALTER TABLE `conexiones`
  MODIFY `id_conexion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `credentials_email`
--
ALTER TABLE `credentials_email`
  MODIFY `id_credential` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `datos_server`
--
ALTER TABLE `datos_server`
  MODIFY `id_server` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detalle_rule`
--
ALTER TABLE `detalle_rule`
  MODIFY `id_detalle_r` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3283;

--
-- AUTO_INCREMENT for table `geoippais`
--
ALTER TABLE `geoippais`
  MODIFY `id_geoippais` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_bloqueo`
--
ALTER TABLE `grafica_bloqueo`
  MODIFY `id_grafica_bloqueo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_bloqueo_bots`
--
ALTER TABLE `grafica_bloqueo_bots`
  MODIFY `id_grafica_bot` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_bloqueo_ip`
--
ALTER TABLE `grafica_bloqueo_ip`
  MODIFY `id_grafica_ip` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_bloqueo_rango`
--
ALTER TABLE `grafica_bloqueo_rango`
  MODIFY `id_grafica_bloqueo_rango` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_bloqueo_rango_ip`
--
ALTER TABLE `grafica_bloqueo_rango_ip`
  MODIFY `id_grafica_bloqueo_rango_ip` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_consulta`
--
ALTER TABLE `grafica_consulta`
  MODIFY `id_grafica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_ddos`
--
ALTER TABLE `grafica_ddos`
  MODIFY `id_grafica_ddos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_principal`
--
ALTER TABLE `grafica_principal`
  MODIFY `id_grafica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_visitas`
--
ALTER TABLE `grafica_visitas`
  MODIFY `id_grafica_visitas` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grafica_visitas_dominio`
--
ALTER TABLE `grafica_visitas_dominio`
  MODIFY `id_grafica_visita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_vulnerability`
--
ALTER TABLE `history_vulnerability`
  MODIFY `id_history` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `host`
--
ALTER TABLE `host`
  MODIFY `id_host` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `host_borrados`
--
ALTER TABLE `host_borrados`
  MODIFY `id_host_borrado` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `host_visita_borrados`
--
ALTER TABLE `host_visita_borrados`
  MODIFY `id_host_visita_borrados` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notificacion_regla`
--
ALTER TABLE `notificacion_regla`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `openai_api`
--
ALTER TABLE `openai_api`
  MODIFY `id_openai_api` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paises`
--
ALTER TABLE `paises`
  MODIFY `id_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `reporte`
--
ALTER TABLE `reporte`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `respaldos`
--
ALTER TABLE `respaldos`
  MODIFY `id_respaldo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resumen_datos`
--
ALTER TABLE `resumen_datos`
  MODIFY `id_resumen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id_rule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `sitio`
--
ALTER TABLE `sitio`
  MODIFY `id_sitio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sitio_balancer`
--
ALTER TABLE `sitio_balancer`
  MODIFY `id_balancer` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sitio_ssl`
--
ALTER TABLE `sitio_ssl`
  MODIFY `id_ssl` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sitio_zimbra`
--
ALTER TABLE `sitio_zimbra`
  MODIFY `id_zimbra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `threat_ip_list`
--
ALTER TABLE `threat_ip_list`
  MODIFY `id_threat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `threat_ip_list_master_date`
--
ALTER TABLE `threat_ip_list_master_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `usuario_host`
--
ALTER TABLE `usuario_host`
  MODIFY `id_usuario_h` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `virus_archivo`
--
ALTER TABLE `virus_archivo`
  MODIFY `id_archivo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visita_dominio`
--
ALTER TABLE `visita_dominio`
  MODIFY `id_visita` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visita_dominio_group`
--
ALTER TABLE `visita_dominio_group`
  MODIFY `id_visita_group` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visita_pais`
--
ALTER TABLE `visita_pais`
  MODIFY `id_visita_pais` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `vulnerability`
--
ALTER TABLE `vulnerability`
  MODIFY `id_vulnerability` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whitelist`
--
ALTER TABLE `whitelist`
  MODIFY `id_white` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `whitelist_ddos`
--
ALTER TABLE `whitelist_ddos`
  MODIFY `id_whitelist_ddos` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
