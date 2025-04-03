-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2025 at 11:16 AM
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
-- Database: `quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `domande`
--

CREATE TABLE `domande` (
  `id` int(11) NOT NULL,
  `id_quiz` int(11) NOT NULL,
  `testo_domanda` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domande`
--

INSERT INTO `domande` (`id`, `id_quiz`, `testo_domanda`) VALUES
(30, 19, 'Cos e un join'),
(31, 19, 'Rispondi alla domanda 1');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `descrizione` text NOT NULL,
  `numero_domande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `nome`, `data_creazione`, `descrizione`, `numero_domande`) VALUES
(19, 'Verifica Info Join Esterno', '2025-01-09 09:35:23', 'no descrrrrrr', 2);

-- --------------------------------------------------------

--
-- Table structure for table `risposte`
--

CREATE TABLE `risposte` (
  `id` int(11) NOT NULL,
  `id_quiz` int(11) NOT NULL,
  `id_studente` int(11) NOT NULL,
  `risposta` text NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risposte`
--

INSERT INTO `risposte` (`id`, `id_quiz`, `id_studente`, `risposta`, `datetime`) VALUES
(67, 19, 27, 'boh', '2025-01-10 22:24:41'),
(68, 19, 27, 'fatto', '2025-01-10 22:24:41'),
(75, 19, 34, 'asdfsd', '2025-01-10 22:25:29'),
(76, 19, 34, 'sdfsa', '2025-01-10 22:25:29'),
(77, 19, 29, 'fasdfsdf', '2025-01-10 22:25:40'),
(78, 19, 29, 'fsafs', '2025-01-10 22:25:40'),
(79, 19, 37, 'fasdfsafasf', '2025-01-10 22:25:52'),
(80, 19, 37, 'asdfasfas', '2025-01-10 22:25:52'),
(81, 19, 38, 'fasdfasf', '2025-01-10 22:26:04'),
(82, 19, 38, 'asdfasf', '2025-01-10 22:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `utente`
--

CREATE TABLE `utente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL DEFAULT 'password',
  `permesso` int(11) NOT NULL DEFAULT 0 COMMENT '0 = utente\r\n1 = professore\r\n2 = admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utente`
--

INSERT INTO `utente` (`id`, `nome`, `cognome`, `login`, `password`, `permesso`) VALUES
(1, 'Admin', 'Master', 'admin', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 2),
(2, 'Giulio', 'Rossi', 'professore', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 1),
(3, 'Marco', 'Bianchi', 'marco', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(4, 'Giulia', 'Verdi', 'giulia_v', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(5, 'Matteo', 'Neri', 'matteo_n', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(6, 'Elena', 'Russo', 'elena_r', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(7, 'Davide', 'Ferrari', 'davide_f', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(8, 'Sara', 'Colombo', 'sara_c', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(9, 'Francesco', 'Gallo', 'francesco_g', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(10, 'Alice', 'Fontana', 'alice_f', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(11, 'Simone', 'Moretti', 'simone_m', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(12, 'Martina', 'De Luca', 'martina_dl', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(13, 'Giorgio', 'Fabbri', 'giorgio_f', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0),
(14, 'Valentina', 'Conti', 'valentina_c', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `domande`
--
ALTER TABLE `domande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_quiz` (`id_quiz`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `risposte`
--
ALTER TABLE `risposte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_domanda` (`id_quiz`);

--
-- Indexes for table `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_login_utente` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `domande`
--
ALTER TABLE `domande`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `risposte`
--
ALTER TABLE `risposte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `utente`
--
ALTER TABLE `utente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `domande`
--
ALTER TABLE `domande`
  ADD CONSTRAINT `domande_ibfk_1` FOREIGN KEY (`id_quiz`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risposte`
--
ALTER TABLE `risposte`
  ADD CONSTRAINT `risposte_ibfk_1` FOREIGN KEY (`id_quiz`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;