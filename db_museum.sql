-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Des 2025 pada 06.17
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
  `xp_reward` int(11) DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `artifacts`
--

INSERT INTO `artifacts` (`id`, `room_id`, `name`, `description`, `image_url`, `xp_reward`) VALUES
(1, 1, 'Iron Helm', 'A sturdy helmet from a fallen knight.', 'https://images.unsplash.com/photo-1599739291060-4578e77dac5d', 50),
(2, 1, 'Old Scroll', 'Ancient writings depicting a great battle.', 'https://images.unsplash.com/photo-1620618871900-589547ea8cb9', 30),
(3, 2, 'Marble Bust', 'A sculpture from the height of the Renaissance.', 'https://images.unsplash.com/photo-1576504677631-061820f666f8', 80),
(4, 2, 'Paint Brush', 'Used by a master artist.', 'https://images.unsplash.com/photo-1513364776144-60967b0f800f', 40),
(5, 3, 'Golden Chalice', 'A cup fit for a king.', 'https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2', 100);

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
(4, 2, 'Renaissance means...?', 'Revolution', 'Rebirth', 'Religion', 'b', 25),
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
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `min_level`, `image_url`) VALUES
(1, 'Medieval Hall', 'Dark ages, knights, and castles.', 1, 'https://images.unsplash.com/photo-1599739291060-4578e77dac5d'),
(2, 'Renaissance Gallery', 'The rebirth of art and culture.', 2, 'https://images.unsplash.com/photo-1518998053901-5348d3969105'),
(3, 'Baroque Palace', 'Grandeur, drama, and opulence.', 3, 'https://images.unsplash.com/photo-1565060169194-118831f52d9b'),
(4, 'Royal Archives', 'Secret documents and forbidden history.', 4, 'https://images.unsplash.com/photo-1505664194779-8beaceb93744');

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
(6, 'rifky123', '$2y$10$1HHhRCGeQZj0PVd9L2hTKeUx4BZ43l5B3lTjQgGf6/UEK8C2l0jDi', 'visitor', 245, 2, '2025-12-12 15:04:25');

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
(3, 6, 3, '2025-12-12 15:18:39'),
(4, 6, 4, '2025-12-12 15:18:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_quizzes`
--

CREATE TABLE `user_quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_quizzes`
--

INSERT INTO `user_quizzes` (`id`, `user_id`, `quiz_id`, `answered_at`) VALUES
(1, 6, 2, '2025-12-12 15:08:28'),
(2, 6, 1, '2025-12-12 15:08:33');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `user_collections`
--
ALTER TABLE `user_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `user_quizzes`
--
ALTER TABLE `user_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Ketidakleluasaan untuk tabel `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD CONSTRAINT `user_quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_quizzes_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
