-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Jun 2021 um 14:08
-- Server-Version: 10.4.17-MariaDB
-- PHP-Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kwm226_ue05_eder`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `todo`
--

CREATE TABLE `todo` (
  `todoId` int(9) NOT NULL,
  `userId` int(9) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `todoTitle` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `todoText` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdOn` datetime NOT NULL DEFAULT current_timestamp(),
  `lastEditedOn` datetime DEFAULT NULL,
  `userIdOfLastEdit` int(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `todo`
--

INSERT INTO `todo` (`todoId`, `userId`, `status`, `todoTitle`, `todoText`, `createdOn`, `lastEditedOn`, `userIdOfLastEdit`) VALUES
(38, 12, 1, 'Katzenfutter kaufen', 'Sheba', '2021-06-15 14:01:52', '2021-06-15 14:02:22', 12),
(39, 12, 0, 'Programmieren machen', 'PHP', '2021-06-15 14:02:10', NULL, NULL),
(40, 13, 1, 'Hund Gassi gehen', 'im Wald', '2021-06-15 14:03:17', '2021-06-15 14:03:25', 13),
(41, 13, 1, 'lernen', 'viel lernen', '2021-06-15 14:03:35', '2021-06-15 14:05:46', 14),
(42, 14, 1, 'Admin Todo', 'Lord der TodoListe', '2021-06-15 14:05:33', NULL, NULL),
(43, 15, 1, 'PS5 kaufen', 'Mediamarkt vorbestellen, wenn wieder verfügbar', '2021-06-15 14:06:37', NULL, NULL),
(44, 15, 0, 'Pringles mitnehmen', 'Sour cream', '2021-06-15 14:06:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `userid` int(9) NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `role` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`userid`, `username`, `password`, `mail`, `role`) VALUES
(12, 'michael', '$2y$10$zM0Xz5j0rmk3AJOFuyIH5OGtSAexojDPq5t2qLvZUAFhU7eEi0HV6', 'michael@mail.com', 1),
(13, 'maria', '$2y$10$Hup.hXh3aUipUyFeEPrfnupgCwDlN3gySmg5uwHuonLiZnu9QnJuu', 'maria@mail.com', 1),
(14, 'tutor', '$2y$10$hFvhf1mC0.p93lHQqB0CNutn9om3aUT75CiHf70zo2PiE.U19O8Ca', 'tutor@mail.com', 2),
(15, 'alex', '$2y$10$O755FYfQBUYGszDVKeT0tOZouGitgDrhGRVXkg1e/jKx1qwTTFBIe', 'alex@mail.com', 1);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`todoId`),
  ADD KEY `todo_userId-user_userId` (`userId`),
  ADD KEY `userIdOfLastEdit` (`userIdOfLastEdit`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `todo`
--
ALTER TABLE `todo`
  MODIFY `todoId` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `userid` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `todo`
--
ALTER TABLE `todo`
  ADD CONSTRAINT `todo_ibfk_1` FOREIGN KEY (`userIdOfLastEdit`) REFERENCES `user` (`userid`) ON DELETE NO ACTION,
  ADD CONSTRAINT `todo_userId-user_userId` FOREIGN KEY (`userId`) REFERENCES `user` (`userid`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
