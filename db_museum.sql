-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Des 2025 pada 07.00
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `artifacts`
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
-- Dumping data untuk tabel `artifacts`
--

INSERT INTO `artifacts` (`id`, `room_id`, `name`, `description`, `image_url`, `xp_reward`, `position_top`, `position_left`) VALUES
(1, 1, 'Veloria Royal Coin (12th Century)', 'An ancient gold coin engraved with the royal crest of Veloria. Once used in trade between kingdoms, this coin reflects the economic power and influence of medieval Europe.', '/project-akhir/public/assets/img/artifacts/739c0ed0e5355e2335c56951e61db6a8.png', 8, '75.7%', '93.74%'),
(2, 1, 'Helm of the Vesper Knight', ' A closed-visor knight helmet worn by elite guardians of the realm. The helmet’s heavy steel structure represents the discipline and resilience of medieval cavalry forces.', '/project-akhir/public/assets/img/artifacts/5efab202cc4e6317d7b92c5c9fee8150.png', 12, '65.92%', '28.76%'),
(3, 2, 'Marble Bust', 'A sculpture from the height of the Renaissance.', 'https://images.unsplash.com/photo-1576504677631-061820f666f8', 80, NULL, NULL),
(4, 2, 'Paint Brush', 'Used by a master artist.', 'https://images.unsplash.com/photo-1513364776144-60967b0f800f', 40, NULL, NULL),
(5, 3, 'Golden Chalice', 'A cup fit for a king.', 'https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2', 100, NULL, NULL),
(6, 1, 'Crusader’s Ironblade', 'A double-edged sword forged for crusader knights during the early holy campaigns. Its worn blade carries marks of countless battles, symbolizing devotion, honor, and sacrifice in medieval warfare.', '/project-akhir/public/assets/img/artifacts/0ed097e08adc01f2cea3027f97c46c18.png', 15, '65.23%', '78.4%');

-- --------------------------------------------------------

--
-- Struktur dari tabel `quizzes`
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
-- Dumping data untuk tabel `quizzes`
--

INSERT INTO `quizzes` (`id`, `room_id`, `question`, `option_a`, `option_b`, `option_c`, `correct_option`, `xp_reward`) VALUES
(1, 1, 'What period is known as the Dark Ages?', 'Renaissance', 'Medieval', 'Baroque', 'b', 25),
(2, 1, 'What type of armor did knights typically wear?', 'Leather', 'Chainmail', 'Plastic', 'b', 20),
(3, 2, 'Who painted the Mona Lisa?', 'Michelangelo', 'Leonardo da Vinci', 'Raphael', 'b', 30),
(5, 3, 'Baroque art originated in which country?', 'France', 'Italy', 'Spain', 'b', 30),
(6, 3, 'Which artist is famous for Baroque paintings?', 'Caravaggio', 'Picasso', 'Van Gogh', 'a', 35),
(7, 4, 'What document limited the power of English kings?', 'Declaration of Independence', 'Magna Carta', 'Constitution', 'b', 40);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rooms`
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
  `professor_dialogs` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `min_level`, `image_url`, `hidden_artifact_name`, `hidden_artifact_desc`, `hidden_artifact_image`, `hidden_artifact_xp`, `professor_dialogs`) VALUES
(1, 'Medieval Hall', 'Dark ages, knights, and castles.', 1, '/project-akhir/public/assets/img/room/medievall_hall.jpeg', 'Ancient Battle Map', 'A secret map showing ancient battle formations.', '', 100, '[\"\\\"Welcome, young explorer! I am Professor Aldric.\\\"\",\"\\\"Before art, before crowns, there was survival.\\\"\",\"\\\"Can you imagine a world where faith and steel decided one’s fate?\\\"\",\"\\\"These relics were not created for admiration, but for necessity.\\\"\",\"\\\"Every blade, every shield once answered a call to fight or to protect.\\\"\",\"\\\"This is where Veloria learned the cost of loyalty.\\\"\",\"\\\"And from this hardship, something greater began to rise.\\\"\"]'),
(2, 'Renaissance Gallery', 'The rebirth of art and culture.', 2, 'https://images.unsplash.com/photo-1518998053901-5348d3969105', 'Master\'s Palette', 'The original color palette of a Renaissance master.', NULL, 120, '[\"“After centuries of conflict, the world began to ask different questions.”\",\"“What if the mind could shape destiny, not just the sword?”\",\"“Here, creation became an act of curiosity.”\",\"“Art spoke, science listened, and knowledge finally dared to grow.”\",\"“Can you feel the shift—from survival to understanding?”\",\"“Veloria was no longer looking inward, but outward.\\\"\"]'),
(3, 'Baroque Palace', 'Grandeur, drama, and opulence.', 3, 'https://images.unsplash.com/photo-1565060169194-118831f52d9b', 'Royal Seal', 'A hidden royal seal of authentication.', NULL, 150, '[\"“With knowledge came ambition… and with ambition, control.”\",\"“Have you noticed how power prefers spectacle over silence?”\",\"“These chambers were built not only to rule, but to be seen ruling.”\",\"“Gold and ceremony became tools of obedience.”\",\"“Behind every symbol of grandeur lies the weight of command.”\",\"“This is where authority learned to dress itself in splendor.”\"]'),
(4, 'Royal Archives', 'Secret documents and forbidden history.', 4, 'https://images.unsplash.com/photo-1505664194779-8beaceb93744', 'Forbidden Manuscript', 'Pages from a forbidden historical text.', NULL, 200, '[\"“But power never reveals everything.”\",\"“Some truths were deemed too dangerous to stand in the open.”\",\"“Here, history was edited… carefully, deliberately.”\",\"“These records do not celebrate—they confess.”\",\"“Would the throne still endure if every secret were known?”\",\"“Now that you stand here… the choice to look away is yours.”\"]');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `xp`, `level`, `created_at`) VALUES
(1, 'rifkyrahmat2006@gmail.com', '$2y$12$Q/Af3hzMA47a63.CQihcQuzZr.5mbkGuhS/H7o.WYi/y07DuENP86', 'visitor', 0, 1, '2025-12-11 06:40:58'),
(2, 'admin', '$2y$12$YSZUvEk3aZYD6LXhfqbIzuP9JZVdPLmE1BC8pYPChyDRSCr6dPHMm', 'admin', 1000, 5, '2025-12-11 06:45:38'),
(3, 'admin123', '$2y$12$ONhQzm3jF1rVICRQtmBFSe9ONX2lZehJerSR2eKdxAdU.snj8WGK6', 'visitor', 0, 1, '2025-12-11 07:05:52'),
(4, 'visitor1', '$2y$12$jFm13CdsxOXdLZtM/dVR/.dZRvhdZ3pa/n0jjh6rAhZiRXM8xHgHS', 'visitor', 0, 1, '2025-12-11 07:41:15'),
(5, 'superadmin@gmail.com', '$2y$12$aCu7i0tXAytQ9X.ZrrwMmOwMrpzeiUC5cKbBjTnPVVQSA.Uchuah2', 'visitor', 0, 1, '2025-12-11 07:56:55'),
(6, 'rifky123', '$2y$10$1HHhRCGeQZj0PVd9L2hTKeUx4BZ43l5B3lTjQgGf6/UEK8C2l0jDi', 'admin', 275, 2, '2025-12-12 15:04:25'),
(7, 'rifky456', '$2y$10$QFw.cpbwGrDWJ4hl2cpP5.ng75K/Pmj5xX8KqALrkUTPmM5OFA9vG', 'visitor', 510, 3, '2025-12-13 05:40:17'),
(8, 'rifky789', '$2y$10$UK0TB0uV2gEn/tY3/JNIYuZEfXkcDR/L1fCTvzxX68BV0Ed5CdeTu', 'visitor', 35, 1, '2025-12-13 12:15:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_collections`
--

CREATE TABLE `user_collections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `artifact_id` int(11) DEFAULT NULL,
  `collected_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_collections`
--

INSERT INTO `user_collections` (`id`, `user_id`, `artifact_id`, `collected_at`) VALUES
(1, 6, 2, '2025-12-12 15:18:25'),
(2, 6, 1, '2025-12-12 15:18:28'),
(9, 7, 6, '2025-12-13 11:12:48'),
(10, 7, 1, '2025-12-13 11:12:52'),
(11, 7, 2, '2025-12-13 11:12:55'),
(12, 7, 4, '2025-12-13 11:24:51'),
(13, 7, 3, '2025-12-13 11:24:55'),
(14, 7, 5, '2025-12-13 11:25:33'),
(15, 8, 2, '2025-12-13 12:16:05'),
(16, 8, 6, '2025-12-13 12:16:29'),
(17, 8, 1, '2025-12-13 12:16:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_hidden_artifacts`
--

CREATE TABLE `user_hidden_artifacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_hidden_artifacts`
--

INSERT INTO `user_hidden_artifacts` (`id`, `user_id`, `room_id`, `unlocked_at`) VALUES
(1, 7, 1, '2025-12-13 10:32:09'),
(2, 7, 2, '2025-12-13 10:36:18'),
(3, 7, 3, '2025-12-13 11:26:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_quizzes`
--

CREATE TABLE `user_quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_quizzes`
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
-- Indeks untuk tabel `artifacts`
--
ALTER TABLE `artifacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `user_collections`
--
ALTER TABLE `user_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artifact_id` (`artifact_id`);

--
-- Indeks untuk tabel `user_hidden_artifacts`
--
ALTER TABLE `user_hidden_artifacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_room` (`user_id`,`room_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `artifacts`
--
ALTER TABLE `artifacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `user_collections`
--
ALTER TABLE `user_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `user_hidden_artifacts`
--
ALTER TABLE `user_hidden_artifacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `user_quizzes`
--
ALTER TABLE `user_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `artifacts`
--
ALTER TABLE `artifacts`
  ADD CONSTRAINT `artifacts_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_collections`
--
ALTER TABLE `user_collections`
  ADD CONSTRAINT `user_collections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_collections_ibfk_2` FOREIGN KEY (`artifact_id`) REFERENCES `artifacts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_hidden_artifacts`
--
ALTER TABLE `user_hidden_artifacts`
  ADD CONSTRAINT `user_hidden_artifacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_hidden_artifacts_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD CONSTRAINT `user_quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_quizzes_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
