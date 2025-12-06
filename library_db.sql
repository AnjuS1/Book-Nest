-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3308/
-- Generation Time: Dec 01, 2025 at 04:46 PM
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
-- Database: `library_db`
--
CREATE DATABASE IF NOT EXISTS `db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `added_date` date DEFAULT NULL,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `category`, `description`, `publisher`, `isbn`, `quantity`, `image`, `available`, `added_date`) VALUES
(1, 'Harry Potter and the Goblet of Fire', 'J. K. Rowling', '1', 'Harry Potter and the Goblet of Fire is the fourth book in J.K. Rowling\'s Harry Potter series. In this story, Harry competes in the dangerous Triwizard Tournament, faces magical challenges, and uncovers a plot that brings the return of Lord Voldemort, marking a darker turn in the series.', 'Scholastic, Inc', '9781338878929', 10, 'harry_potter.jpg', 8, '2025-11-25'),

(2, 'The Pumpkin Spice Café (Dream Harbor, Book 1)', 'Laurie Gilmore', '4', 'When Jeanie\'s aunt gifts her the beloved Pumpkin Spice Café in the small town of Dream Harbor, Jeanie jumps at the chance for a fresh start away from her very dull desk job.\r\n\r\nLogan is a local farmer who avoids Dream Harbor\'s gossip at all costs. But Jeanie\'s arrival disrupts Logan\'s routine and he wants nothing to do with the irritatingly upbeat new girl, except that he finds himself inexplicably drawn to her.\r\n\r\nWill Jeanie\'s happy-go-lucky attitude win over the grumpy-but-gorgeous Logan, or has this city girl found the one person in town who won’t fall for her charm, or her pumpkin spice lattes…', 'HarperCollins Publishers', '9780008610678', 5, 'pumpkin_spice.jpg', 4, '2025-11-26'),

(3, 'You Shouldn\'t Have Come Here', 'Jeneva Rose', '3', 'Grace Evans, an overworked New Yorker looking for a total escape from her busy life, books an Airbnb on a ranch in the middle of Wyoming. When she arrives at the idyllic getaway, she\'s pleased to find that the owner is a handsome man by the name of Calvin Wells—and he\'s eager to introduce her to his easygoing way of life. But there are things Grace discovers that she\'s not too pleased about: A lack of cell phone service. A missing woman. And a feeling that something isn\'t right with the ranch.', 'Blackstone Publishing', '9798212876827', 5, 'rose.jpg', 5, '2025-11-26'),

(4, 'The Hong Kong Widow', 'Kristen Loesch', '2', 'In 1950s Hong Kong, Mei is a young refugee of the Chinese Communist revolution struggling to put her past in Shanghai behind her. When she receives a shocking invitation—to take part in a competition in one of the city\'s most notorious haunted houses, pitting six spirit mediums against one another in a series of six séances over six nights, until a single winner emerges—she has every reason to refuse.', 'Penguin Publishing Group', '9780593548011', 8, 'widow.jpg', 7, '2025-11-26'),

(5, 'One Piece, Vol. 110', 'Eiichiro Oda', '5', 'As a child, Monkey D. Luffy dreamed of becoming King of the Pirates. But his life changed when he accidentally gained the power to stretch like rubber at the cost of never being able to swim again! Years later, Luffy sets off in search of the One Piece, said to be the greatest treasure in the world.\r\n\r\nWith the help of the Giant Pirates, Luffy and crew may be able to escape Egghead alive, but they\'ll still have to get by the seemingly immortal Five Elders. Meanwhile, Dr. Vegapunk\'s broadcast may reveal a secret that will shock the whole world!', 'VIZ Media LLC', '9781974758968', 20, 'one_peice.jpg', 19, '2025-11-26'),

(6, 'Spy x Family, Vol. 1', 'Tatsuya Endo', '5', 'A spy, an assassin and a telepath walk into a manga, and that\'s Spy x Family. You\'ll laugh, you\'ll cry (from laughter), and you\'ll probably binge the whole series.\r\n\r\nNot one to depend on others, Twilight has his work cut out for him procuring both a wife and a child for his mission to infiltrate an elite private school. What he doesn\'t know is that the wife he\'s chosen is an assassin and the child he\'s adopted is a telepath!', 'VIZ Media LLC', '9781974720286', 10, 'spyxfamily.jpg', 10, '2025-11-26'),

(7, 'Black Panther: T\'Challa Declassified', 'Maurice Broaddus, Marvel Comics', '6', 'He\'s a king, a hero, a loving brother and son, a husband to a goddess. The orphan king—the Haramu-Fal—and the Damisa-Sarki. And a man facing his doubts, his failures, and his destiny.\r\n\r\nRevisit the life of one of Marvel Comics\' most iconic and inspiring characters via in-world interviews, journal entries, newspaper articles, and intelligence briefings from S.H.I.E.L.D. and other agencies.\r\n\r\nBlack Panther: T\'Challa Declassified draws on more than half a century of classic tales to present an insightful, completely unique—and completely personal—take on one of the most talked-about heroes of all time.\r\n\r\nFeaturing the perspectives of T\'Challa\'s most important allies and enemies, including Shuri, Queen-Mother Ramonda, Ororo Munroe aka Storm, Everett K. Ross, Erik Killmonger, and the White Wolf, and accounts of some of Marvel Comics\' most memorable moments, this first-of-its-kind archival collection is a must-read for fans of all ages.', 'BenBella Books', '9781637744185', 5, 'marvel.jpg', 5, '2025-11-26'),

(8, 'Strawberry Shortcake - The Snow Dance', 'Amy Ackelsberg', '1', 'The Snow Dance - The Berry girls hold a winter dance and since Plum is the best dancer she will lead it. But the day before the performance she gets hurt! Will the others still be able to put on a show?', 'Ackelsberg', '1234567890123', 5, 'strawberry.jpg', 2, '2025-11-27'),

(9, 'Water Moon: A Novel', 'Samantha Sotto Yambao', '4', 'In a backstreet in Tokyo lies a pawnshop, but not everyone can find it. Most will see a cozy ramen restaurant. And only the chosen ones—those who are lost—will find a place to pawn their life choices and deepest regrets.\r\n\r\nHana Ishikawa wakes on her first morning as the pawnshop\'s new owner to find it ransacked, the shop\'s most precious acquisition stolen, and her father missing. And then into the shop stumbles a charming stranger, quite unlike its other customers, for he offers help instead of seeking it.\r\n\r\nTogether, they must journey through a mystical world to find Hana\'s father and the stolen choice—by way of rain puddles, rides on paper cranes, the bridge between midnight and morning, and a night market in the clouds.\r\n\r\nBut as they get closer to the truth, Hana must reveal a secret of her own—and risk making a choice that she will never be able to take back.', 'Random House Worlds', '9780593725016', 8, 'watermoon.jpg', 8, '2025-11-30'),

(10, 'Twisted Love', 'Ana Huang', '4', 'He has a heart of ice…but for her, he\'d burn the world!', 'Sourcebooks', '9781728274867', 1, 'twisted_love.jpg', 1, '2025-12-01'),

(19, 'Test', 'Test', '1', '', 'Test', '1111111111111', 1, 'strawberry.jpg', 0, '2025-12-01');


--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Fiction'),
(2, 'Horror'),
(3, 'Mystery & Crime'),
(4, 'Romance'),
(5, 'Manga'),
(6, 'Comics'),
(7, 'Thriller'),
(8, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
CREATE TABLE IF NOT EXISTS `fines` (
  `fine_id` int(11) NOT NULL AUTO_INCREMENT,
  `issue_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `fine_amount` decimal(6,2) DEFAULT NULL,
  `paid_status` enum('Paid','Unpaid') NOT NULL DEFAULT 'Unpaid',
  `fine_date` date DEFAULT NULL,
  `remarks` varchar(255) DEFAULT 'No remarks',
  `paid_method` enum('online','offline') DEFAULT NULL,
  PRIMARY KEY (`fine_id`),
  KEY `issue_id` (`issue_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`fine_id`, `issue_id`, `member_id`, `fine_amount`, `paid_status`, `fine_date`, `remarks`, `paid_method`) VALUES
(27, 15, 3, 4.00, 'Unpaid', '2025-11-30', 'Overdue', NULL),
(28, 16, 3, 10.00, 'Paid', '2025-11-30', 'No remarks', 'offline'),
(29, 14, 2, 2.00, 'Unpaid', '2025-11-30', 'No remarks', NULL),
(30, 19, 10, 4.00, 'Paid', '2025-11-30', 'Overdue', 'offline');

-- --------------------------------------------------------

--
-- Table structure for table `issued_books`
--

DROP TABLE IF EXISTS `issued_books`;
CREATE TABLE IF NOT EXISTS `issued_books` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Issued','Returned') DEFAULT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `member_id` (`member_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issued_books`
--

INSERT INTO `issued_books` (`issue_id`, `member_id`, `book_id`, `issue_date`, `due_date`, `return_date`, `status`) VALUES
(14, 2, 5, '2025-11-30', '2025-12-07', NULL, 'Issued'),
(15, 3, 8, '2025-11-30', '2025-11-29', NULL, 'Issued'),
(16, 3, 19, '2025-11-30', '2025-12-07', NULL, 'Issued'),
(17, 6, 1, '2025-11-30', '2025-12-07', NULL, 'Issued'),
(18, 1, 5, '2025-11-30', '2025-12-07', '2025-11-30', 'Returned'),
(19, 10, 8, '2025-11-24', '2025-11-29', NULL, 'Issued');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`member_id`),
  KEY `fk_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `phone`, `address`, `join_date`, `user_id`) VALUES
(1, '9137107773', '12306 craig st', '2025-11-26', 2),
(2, '8161414141', '8111 craig st', '2025-11-27', 3),
(3, '6575673242', '8100 craig st', '2025-11-27', 4),
(4, '9876543210', '12304 craig st', '2025-11-27', 5),
(5, '9137107773', '12301 w 124th', '2025-11-30', 6),
(6, '7777777777', '12306 craig st', '2025-12-01', 7),
(9, '9137107773', '8888  151st', '2025-12-01', 10),
(10, '9137107773', '1111 111st', '2025-12-01', 11),
(14, '9137107773', '1111 221st', '2025-12-01', 26);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','member','user') NOT NULL DEFAULT 'user',
  `visit_count` int(11) DEFAULT 0,
  `last_visit` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `visit_count`, `last_visit`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$yAVYRC6a1Yz0bDaihrRgsOVd4EySB2FL0Lz5fiUJuxBmWYmhO.BBq', 'admin', 0, '2025-12-01 08:55:57'),
(2, 'User1', 'u1@gmail.com', '$2y$10$NEH8.bik8UceOqCxdE9vRuvh4IWW67HF0xT/QKWrixhzDRyUG/gmm', 'member', 0, '2025-11-30 21:23:58'),
(3, 'User2', 'u2@gmail.com', '$2y$10$MZcLf2yKy.LSu/tI8/76LOZoYIeOkNvn22tgUfP88p1GrXZtErZv.', 'member', 0, '2025-11-30 21:21:29'),
(4, 'User3', 'u3@gmail.com', '$2y$10$MgqzSsSLOcDqRpEK0OJ.0eKA122X2Z0kTCQwU/YOa1DEkLt.xuM3m', 'member', 0, '2025-11-30 21:43:23'),
(5, 'User4', 'u4@gmail.com', '$2y$10$J2Y1T2buPadwC8DAWsgi5eZm7cHFAjq4erysmg6PWvsGePEHVrqae', 'member', 0, NULL),
(6, 'User5', 'u5@gmail.com', '$2y$10$2MO4hqMEaedLfIOm3FH3iOaMDWJWzubBUWcLQYQ9cOIs/8yXe4z5u', 'member', 0, '2025-11-30 20:49:42'),
(7, 'User6', 'u6@gmail.com', '$2y$10$b39tv1HlDkiNrn0kIIKIkOroimEgvHoesIJTr7e8b.AAaJ8F11u4C', 'member', 0, '2025-11-30 21:22:49'),
(8, 'User7', 'u7@gmail.com', '$2y$10$kDm/teroLxqzKAdrqy6ZTO9kXfA07ApVQO0M4fME/chQjD7WctIF6', 'user', 0, NULL),
(9, 'User8', 'u8@gmail.com', '$2y$10$cRy4Z8dBfsPttYFazNI6d.xdFcEJdcCcZjVYgbGZmxkFG8p3BjCJK', 'user', 0, NULL),
(10, 'User9', 'u9@gmail.com', '$2y$10$MOxlsY5sLRui1ghvVw2EYeaJ5zJyEjPSdBdbtt1Bn9Ys2PVk0R4ge', 'member', 0, '2025-11-30 20:41:35'),
(11, 'Test', 'test@gmail.com', '$2y$10$ILwKQcfI.g7nS1BUzD4IIOS/TeZvzo6f8gJUvbifF2bOa4zuMs0h6', 'member', 0, '2025-11-30 23:27:38'),
(26, 'Test1', 'test1@gmail.com', '$2y$10$0mdtom.j9DVClOdmRwB6Ou/o5kdLlomgzmKK09vXvfBRhiUwOeFMe', 'member', 0, '2025-11-30 23:29:25');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issued_books` (`issue_id`),
  ADD CONSTRAINT `fines_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Constraints for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD CONSTRAINT `issued_books_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `issued_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
