-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 04:01 PM
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
-- Database: `db_museum`
--

-- --------------------------------------------------------

--
-- Table structure for table `artifacts`
--

CREATE TABLE `artifacts` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `xp_reward` int(11) DEFAULT 50,
  `position_top` varchar(10) DEFAULT NULL,
  `position_left` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artifacts`
--

INSERT INTO `artifacts` (`id`, `room_id`, `name`, `description`, `image_url`, `xp_reward`, `position_top`, `position_left`) VALUES
(1, 1, 'Veloria Royal Coin (12th Century)', 'An ancient gold coin engraved with the royal crest of Veloria. Once used in trade between kingdoms, this coin reflects the economic power and influence of medieval Europe.', '/project-akhir/public/assets/img/artifacts/739c0ed0e5355e2335c56951e61db6a8.png', 8, '75.7%', '93.74%'),
(2, 1, 'Helm of the Vesper Knight', ' A closed-visor knight helmet worn by elite guardians of the realm. The helmet’s heavy steel structure represents the discipline and resilience of medieval cavalry forces.', '/project-akhir/public/assets/img/artifacts/5efab202cc4e6317d7b92c5c9fee8150.png', 8, '65.92%', '28.76%'),
(6, 1, 'Crusader’s Ironblade', 'A double-edged sword forged for crusader knights during the early holy campaigns. Its worn blade carries marks of countless battles, symbolizing devotion, honor, and sacrifice in medieval warfare.', '/project-akhir/public/assets/img/artifacts/0ed097e08adc01f2cea3027f97c46c18.png', 10, '65.23%', '78.4%'),
(7, 1, 'Seal of the Old Watchtower', 'A wax seal emblem once used to authenticate military dispatches. The tower symbol ensured messages were trusted and carried authority across distant outposts.', '/project-akhir/public/assets/img/artifacts/29d03165e4e8ea0694502db845969bed.png', 7, '64.15%', '40.86%'),
(8, 1, 'Ashen Arrow of the Northern War', 'A blackened arrow used during the Northern War. Its dark shaft symbolizes scorched lands and silent battles fought far from royal courts.', '/project-akhir/public/assets/img/artifacts/9a3e7eac956dfb186d8c3f2d760c50d3.png', 7, '80.98%', '13.28%'),
(9, 1, 'Broken Shield of Arcturus', 'A shattered shield recovered from the ruins of an ancient fortress. Though broken, it stands as a symbol of last resistance and the fall of a once-mighty stronghold.', '/project-akhir/public/assets/img/artifacts/bdb343a36c0cd1fddd256c5c387ec074.png', 9, '63.15%', '72.73%'),
(10, 1, 'Manuscript of the Lost Crusade', 'A fragile parchment manuscript chronicling a forgotten crusade erased from official records. Its faded ink preserves secrets of a failed expedition and the cost of blind faith.', '/project-akhir/public/assets/img/artifacts/591675091556598b1ab0b5124a22c9c5.png', 8, '8.49%', '34.53%'),
(11, 2, 'The Veloria Portrait (1503)', 'A masterfully painted portrait symbolizing the rebirth of classical beauty and human expression. This artwork reflects the Renaissance belief in balance, proportion, and intellectual awakening.', '/project-akhir/public/assets/img/artifacts/abe0b389b8a3373d1c78e8ad05b4499d.png', 8, '71.63%', '51.17%'),
(12, 2, 'Marble Bust of Queen Aurelia', 'A finely carved marble bust depicting Queen Aurelia, a patron of arts and sciences. The sculpture represents royal support for artistic freedom and scholarly progress.', '/project-akhir/public/assets/img/artifacts/ba30c01101dfd0f0e76a6b98bf6da566.png', 16, '67.75%', '90.16%'),
(13, 2, 'Daelorian Painter’s Toolkit', ' A collection of brushes, pigments, and tools used by Renaissance painters. These instruments played a vital role in advancing realism and perspective in European art.', '/project-akhir/public/assets/img/artifacts/c2d3e6e84e98f1db59c23bbf708acd9b.png', 10, '66.17%', '73.36%'),
(14, 2, 'Navigator’s Star Map', 'A celestial map used by explorers during the Age of Discovery. Marked with constellations and sea routes, it symbolizes Europe’s expanding worldview and scientific curiosity.', '/project-akhir/public/assets/img/artifacts/d337f24fd240fbc451cf1ea3d3211342.png', 18, '63.15%', '63.83%'),
(15, 2, 'Golden Quill of the Scholar’s Guild', 'A golden writing quill awarded to esteemed scholars. It represents the power of knowledge, literature, and the written word during the Renaissance era.', '/project-akhir/public/assets/img/artifacts/f4d0623a4bd0ff998b3757cf6e76efb7.png', 12, '63.43%', '9.3%'),
(16, 2, 'The First Velorian Codex', 'One of the earliest compiled books combining art, philosophy, and science. The codex marks the transition from medieval manuscripts to structured scholarly documentation.', '/project-akhir/public/assets/img/artifacts/c3209a5746f7b6508f4e5c9ebe7040cf.png', 22, '73.79%', '9.22%'),
(17, 3, 'Crown of the Sovereign Dawn', 'A heavy royal crown forged during the rise of absolute monarchy. Its darkened gold surface reflects the burden of rule and the unyielding authority of sovereign power. Only those born to command ever wore its weight.', '/project-akhir/public/assets/img/artifacts/9faf414692b11215ef7b8db1b70605d0.png', 30, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `correct_option` char(1) DEFAULT NULL,
  `xp_reward` int(11) DEFAULT 20
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `room_id`, `question`, `option_a`, `option_b`, `option_c`, `correct_option`, `xp_reward`) VALUES
(1, 1, 'What period is known as the Dark Ages?', 'Renaissance', 'Medieval', 'Baroque', 'b', 25),
(2, 1, 'What type of armor did knights typically wear?', 'Leather', 'Chainmail', 'Plastic', 'b', 20),
(3, 2, 'Who painted the Mona Lisa?', 'Michelangelo', 'Leonardo da Vinci', 'Raphael', 'b', 30),
(5, 3, 'Baroque art originated in which country?', 'France', 'Italy', 'Spain', 'b', 30),
(6, 3, 'Which artist is famous for Baroque paintings?', 'Caravaggio', 'Picasso', 'Van Gogh', 'a', 35),
(7, 4, 'What document limited the power of English kings?', 'Declaration of Independence', 'Magna Carta', 'Constitution', 'b', 40),
(41, 1, 'What value defined the life of a medieval knight?', 'Honor and loyalty', 'Artistic creativity', 'Scientific progress', 'a', 25),
(42, 1, 'Which object best represents medieval combat?', 'Knight helmet', 'Writing quill', 'Opera mask', 'a', 20),
(43, 1, 'Why were manuscripts important in medieval times?', 'They preserved knowledge and belief', 'They replaced coins', 'They were used as weapons', 'a', 25),
(44, 1, 'What does a broken shield most commonly symbolize?', 'Celebration', 'Final defense and sacrifice', 'Royal luxury', 'b', 30),
(45, 1, 'Medieval authority relied mostly on what?', 'Faith and force', 'Entertainment', 'Trade routes', 'a', 25),
(46, 2, 'What idea defines the Renaissance period?', 'Rebirth of learning', 'Total rejection of art', 'Military domination', 'a', 25),
(47, 2, 'What theme is central to Renaissance art?', 'Human balance and proportion', 'Dark rituals', 'Royal secrecy', 'a', 25),
(48, 2, 'Why were maps significant during this era?', 'They supported exploration', 'They replaced religion', 'They were purely decorative', 'a', 30),
(49, 2, 'What role did scholars play in society?', 'Spreading knowledge', 'Leading armies', 'Collecting taxes', 'a', 25),
(50, 2, 'The Renaissance is often called the age of what?', 'Rebirth', 'Absolute rule', 'Hidden power', 'a', 30),
(51, 3, 'What emotion did Baroque art aim to evoke?', 'Drama and grandeur', 'Simplicity', 'Playfulness', 'a', 25),
(52, 3, 'Why was gold commonly used in Baroque design?', 'To display power and authority', 'Because it was cheap', 'For personal comfort', 'a', 30),
(53, 3, 'What did ceremonial objects mainly represent?', 'Symbolic control', 'Daily utility', 'Personal wealth', 'a', 25),
(54, 3, 'Why was appearance important in royal courts?', 'It reinforced hierarchy', 'It replaced law', 'It encouraged equality', 'a', 30),
(55, 3, 'Baroque society emphasized which concept?', 'Absolute rule', 'Scientific freedom', 'Cultural neutrality', 'a', 35),
(56, 4, 'Why were certain historical records hidden?', 'They contained dangerous truths', 'They lacked artistic value', 'They were unfinished', 'a', 40),
(57, 4, 'What gives forbidden knowledge its power?', 'It influences decisions in secrecy', 'It entertains the public', 'It replaces authority', 'a', 45),
(58, 4, 'Why is secrecy linked to authority?', 'It limits access to truth', 'It creates equality', 'It removes responsibility', 'a', 40),
(59, 4, 'What risk follows uncovering hidden records?', 'Moral and political consequences', 'Loss of property', 'Physical weakness', 'a', 45),
(60, 4, 'The Royal Archive represents what idea?', 'Control over history', 'Artistic expression', 'Public education', 'a', 50);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `min_level` int(11) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `hidden_artifact_name` varchar(100) DEFAULT NULL,
  `hidden_artifact_desc` text DEFAULT NULL,
  `hidden_artifact_image` varchar(255) DEFAULT NULL,
  `hidden_artifact_xp` int(11) DEFAULT 100,
  `professor_dialogs` text DEFAULT NULL,
  `chest_position_top` varchar(10) DEFAULT '70%',
  `chest_position_left` varchar(10) DEFAULT '85%'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `min_level`, `image_url`, `hidden_artifact_name`, `hidden_artifact_desc`, `hidden_artifact_image`, `hidden_artifact_xp`, `professor_dialogs`, `chest_position_top`, `chest_position_left`) VALUES
(1, 'Medieval Hall', 'Dark ages, knights, and castles.', 1, '/project-akhir/public/assets/img/room/medievall_hall.jpg', 'Ancient Battle Map', 'A secret map showing ancient battle formations.', '', 30, '[\"\\\"Welcome, young explorer! I am Professor Aldric.\\\"\",\"\\\"Before art, before crowns, there was survival.\\\"\",\"\\\"Can you imagine a world where faith and steel decided one’s fate?\\\"\",\"\\\"These relics were not created for admiration, but for necessity.\\\"\",\"\\\"Every blade, every shield once answered a call to fight or to protect.\\\"\",\"\\\"This is where Veloria learned the cost of loyalty.\\\"\",\"\\\"And from this hardship, something greater began to rise.\\\"\"]', '5%', '6%'),
(2, 'Renaissance Gallery', 'The rebirth of art and culture.', 2, '/project-akhir/public/assets/img/room/renaissance_hall.jpg', 'Master\'s Palette', 'The original color palette of a Renaissance master.', NULL, 120, '[\"“After centuries of conflict, the world began to ask different questions.”\",\"“What if the mind could shape destiny, not just the sword?”\",\"“Here, creation became an act of curiosity.”\",\"“Art spoke, science listened, and knowledge finally dared to grow.”\",\"“Can you feel the shift—from survival to understanding?”\",\"“Veloria was no longer looking inward, but outward.\\\"\"]', '70%', '85%'),
(3, 'Baroque Palace', 'Grandeur, drama, and opulence.', 3, '/project-akhir/public/assets/img/room/baroque_hall.jpg', 'Royal Seal', 'A hidden royal seal of authentication.', NULL, 150, '[\"“With knowledge came ambition… and with ambition, control.”\",\"“Have you noticed how power prefers spectacle over silence?”\",\"“These chambers were built not only to rule, but to be seen ruling.”\",\"“Gold and ceremony became tools of obedience.”\",\"“Behind every symbol of grandeur lies the weight of command.”\",\"“This is where authority learned to dress itself in splendor.”\"]', '70%', '85%'),
(4, 'Royal Archives', 'Secret documents and forbidden history.', 4, '/project-akhir/public/assets/img/room/archive_hall.jpg', 'Forbidden Manuscript', 'Pages from a forbidden historical text.', NULL, 200, '[\"“But power never reveals everything.”\",\"“Some truths were deemed too dangerous to stand in the open.”\",\"“Here, history was edited… carefully, deliberately.”\",\"“These records do not celebrate—they confess.”\",\"“Would the throne still endure if every secret were known?”\",\"“Now that you stand here… the choice to look away is yours.”\"]', '70%', '85%');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('visitor','admin') DEFAULT 'visitor',
  `xp` int(11) DEFAULT 0,
  `level` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `xp`, `level`, `created_at`) VALUES
(1, 'rifkyrahmat2006@gmail.com', '$2y$12$Q/Af3hzMA47a63.CQihcQuzZr.5mbkGuhS/H7o.WYi/y07DuENP86', 'visitor', 0, 1, '2025-12-11 06:40:58'),
(2, 'admin', '$2y$12$YSZUvEk3aZYD6LXhfqbIzuP9JZVdPLmE1BC8pYPChyDRSCr6dPHMm', 'admin', 1000, 5, '2025-12-11 06:45:38'),
(3, 'admin123', '$2y$12$ONhQzm3jF1rVICRQtmBFSe9ONX2lZehJerSR2eKdxAdU.snj8WGK6', 'visitor', 0, 1, '2025-12-11 07:05:52'),
(4, 'visitor1', '$2y$12$jFm13CdsxOXdLZtM/dVR/.dZRvhdZ3pa/n0jjh6rAhZiRXM8xHgHS', 'visitor', 0, 1, '2025-12-11 07:41:15'),
(5, 'superadmin@gmail.com', '$2y$12$aCu7i0tXAytQ9X.ZrrwMmOwMrpzeiUC5cKbBjTnPVVQSA.Uchuah2', 'visitor', 0, 1, '2025-12-11 07:56:55'),
(6, 'rifky123', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhCa', 'admin', 275, 2, '2025-12-12 15:04:25'),
(7, 'rifky456', '$2y$10$QFw.cpbwGrDWJ4hl2cpP5.ng75K/Pmj5xX8KqALrkUTPmM5OFA9vG', 'visitor', 510, 3, '2025-12-13 05:40:17'),
(8, 'rifky789', '$2y$10$UK0TB0uV2gEn/tY3/JNIYuZEfXkcDR/L1fCTvzxX68BV0Ed5CdeTu', 'visitor', 35, 1, '2025-12-13 12:15:44'),
(10, 'fahmiadmin', '$2y$10$YsMgXiswWBjFlSjGk2riK.Vf6I.szLIMh3puFhIGFV2zF7m208DLK', 'admin', 2000, 4, '2025-12-14 06:23:23'),
(13, 'fahmi', '$2y$10$QV2jL2Mh1sfhUP4nN6VXE.ns6XRPJR6SnBeMgc8.QFBCk4fbLUF3C', 'visitor', 363, 1, '2025-12-14 12:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_collections`
--

CREATE TABLE `user_collections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `artifact_id` int(11) DEFAULT NULL,
  `collected_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_collections`
--

INSERT INTO `user_collections` (`id`, `user_id`, `artifact_id`, `collected_at`) VALUES
(1, 6, 2, '2025-12-12 15:18:25'),
(2, 6, 1, '2025-12-12 15:18:28'),
(9, 7, 6, '2025-12-13 11:12:48'),
(10, 7, 1, '2025-12-13 11:12:52'),
(11, 7, 2, '2025-12-13 11:12:55'),
(15, 8, 2, '2025-12-13 12:16:05'),
(16, 8, 6, '2025-12-13 12:16:29'),
(17, 8, 1, '2025-12-13 12:16:34'),
(41, 13, 1, '2025-12-14 12:25:54'),
(42, 13, 6, '2025-12-14 12:25:58'),
(43, 13, 9, '2025-12-14 12:26:02'),
(44, 13, 8, '2025-12-14 12:26:08'),
(45, 13, 7, '2025-12-14 12:26:10'),
(46, 13, 2, '2025-12-14 12:26:14'),
(47, 13, 10, '2025-12-14 12:26:18'),
(48, 13, 15, '2025-12-14 13:51:15'),
(49, 13, 16, '2025-12-14 13:51:19'),
(50, 13, 11, '2025-12-14 13:51:21'),
(51, 13, 14, '2025-12-14 13:51:25'),
(52, 13, 13, '2025-12-14 13:51:30'),
(53, 13, 12, '2025-12-14 13:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_hidden_artifacts`
--

CREATE TABLE `user_hidden_artifacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_hidden_artifacts`
--

INSERT INTO `user_hidden_artifacts` (`id`, `user_id`, `room_id`, `unlocked_at`) VALUES
(1, 7, 1, '2025-12-13 10:32:09'),
(2, 7, 2, '2025-12-13 10:36:18'),
(3, 7, 3, '2025-12-13 11:26:27'),
(6, 13, 1, '2025-12-14 12:31:13'),
(7, 13, 2, '2025-12-14 13:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_quizzes`
--

CREATE TABLE `user_quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_quizzes`
--

INSERT INTO `user_quizzes` (`id`, `user_id`, `quiz_id`, `answered_at`, `is_correct`) VALUES
(1, 6, 2, '2025-12-12 15:08:28', 0),
(2, 6, 1, '2025-12-12 15:08:33', 0),
(4, 6, 3, '2025-12-13 05:24:22', 0),
(8, 7, 2, '2025-12-13 11:14:33', 1),
(9, 7, 1, '2025-12-13 11:14:42', 1),
(10, 7, 3, '2025-12-13 11:25:11', 1),
(11, 7, 5, '2025-12-13 11:26:20', 1),
(12, 7, 6, '2025-12-13 11:26:27', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artifacts`
--
ALTER TABLE `artifacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_collections`
--
ALTER TABLE `user_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artifact_id` (`artifact_id`);

--
-- Indexes for table `user_hidden_artifacts`
--
ALTER TABLE `user_hidden_artifacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_room` (`user_id`,`room_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artifacts`
--
ALTER TABLE `artifacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_collections`
--
ALTER TABLE `user_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `user_hidden_artifacts`
--
ALTER TABLE `user_hidden_artifacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_quizzes`
--
ALTER TABLE `user_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artifacts`
--
ALTER TABLE `artifacts`
  ADD CONSTRAINT `artifacts_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_collections`
--
ALTER TABLE `user_collections`
  ADD CONSTRAINT `user_collections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_collections_ibfk_2` FOREIGN KEY (`artifact_id`) REFERENCES `artifacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_hidden_artifacts`
--
ALTER TABLE `user_hidden_artifacts`
  ADD CONSTRAINT `user_hidden_artifacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_hidden_artifacts_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD CONSTRAINT `user_quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_quizzes_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
