Documentation Plan: Classic Old Europe Museum
Project Name: Classic Old Europe Museum
Type: Interactive Virtual Museum (Gamified)
Tech Stack: PHP Native, MySQL, JavaScript, Tailwind CSS
Goal: Membuat website museum interaktif di mana user bisa eksplorasi, mengumpulkan artefak, dan naik level (bukan sekadar website informasi statis).
1. System Architecture (Arsitektur Sistem)
Karena menggunakan PHP Native, kita akan menggunakan struktur folder modular agar kode tidak berantakan (Separation of Concerns).

2. Database Schema (Desain Database)
Kita membutuhkan 5 tabel utama untuk menangani core mechanic (Leveling & Collecting).
Database Name: db_museum
A. Table users
Menyimpan data pengunjung dan progress level mereka.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('visitor', 'admin') DEFAULT 'visitor',
    xp INT DEFAULT 0,           -- Total Experience Point
    level INT DEFAULT 1,        -- Level saat ini (1-4)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


B. Table rooms
Menyimpan data ruangan (Medieval, Renaissance, Baroque, Archive).
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    min_level INT DEFAULT 1,    -- Level minimal untuk masuk (Lock system)
    image_url VARCHAR(255)      -- Background image ruangan
);


C. Table artifacts
Barang-barang yang ada di dalam museum.
CREATE TABLE artifacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    xp_reward INT DEFAULT 50,   -- XP yang didapat saat collect
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);


D. Table user_collections (Pivot Table)
Mencatat artefak apa saja yang sudah diambil user (agar tidak bisa diambil 2x).
CREATE TABLE user_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    artifact_id INT,
    collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (artifact_id) REFERENCES artifacts(id) ON DELETE CASCADE
);


E. Table quizzes
Pertanyaan untuk mini-game di setiap ruangan.
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    question TEXT NOT NULL,
    option_a VARCHAR(255),
    option_b VARCHAR(255),
    option_c VARCHAR(255),
    correct_option CHAR(1),     -- 'a', 'b', atau 'c'
    xp_reward INT DEFAULT 20,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);


3. Game Mechanics & Logic (Alur Logika)
Ini adalah bagian terpenting agar website terasa "hidup" sesuai konsep PDF.
A. Leveling System (Sistem Level)
Logika kenaikan level berdasarkan XP. Ini bisa ditaruh di helper function PHP.
Level 1 (Visitor): 0 - 100 XP
Level 2 (Explorer): 101 - 300 XP (Unlock Renaissance Hall)
Level 3 (Historian): 301 - 600 XP (Unlock Baroque Hall)
Level 4 (Royal Curator): > 600 XP (Unlock Royal Archive)
B. The Loop (Alur Bermain)
User Login -> Masuk lobby.php.
Pilih Ruangan -> Cek min_level ruangan vs user_level.
Jika Level Cukup -> Redirect ke room.php?id=1.
Jika Kurang -> Tampilkan Alert "Access Denied".
Di Dalam Ruangan:
User klik gambar artefak -> Muncul Modal Detail.
Klik tombol "Collect" -> AJAX request ke collect.php.
Backend: Tambah data ke user_collections & Tambah XP user.
Frontend: Ubah tombol jadi "Collected" & Update XP Bar.
Kuis:
User jawab benar -> +XP.
4. Page Specifications (Spesifikasi Halaman)
Detail teknis per halaman untuk memandu coding.

1. Lobby (lobby.php)
UI: Grid layout menampilkan 4 kartu ruangan.
Logic:
Query data rooms dari database.
Looping card ruangan.
Tambahkan class CSS .locked (abu-abu/gembok) jika level user < level ruangan.

2. Room Page (room.php?id=X)
Penting: Gunakan satu file ini untuk semua ruangan. Konten berubah berdasarkan $_GET['id'].
Features:
Background image sesuai database ruangan.
Artifacts: Tampilkan icon/gambar artefak secara absolute positioning (biar tersebar di layar) atau Grid.
Cek Koleksi: Saat render artefak, cek tabel user_collections. Jika sudah punya, beri visual effect (misal: glowing atau opacity turun).

3. My Collection (my_collection.php)
UI: Seperti rak lemari (Cabinet of Curiosities).
Logic:
SELECT * FROM user_collections JOIN artifacts ... WHERE user_id = Session User.
Tampilkan Badge:
Bronze (Total collect > 5)
Silver (Total collect > 10)
Gold (Total collect > 20)
4. Admin Panel (admin/)
Security: Cek di paling atas file:
if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); }


CRUD: Fokus pada tabel artifacts dulu (MVP), agar admin bisa menambah benda koleksi tanpa coding ulang.
5. Development Roadmap (Rencana Pengerjaan)
Urutan pengerjaan agar efisien (mulai dari Backend ke Frontend).
Phase 1: Foundation
[ ] Setup Database MySQL (Create Tables).
[ ] Buat config/database.php.
[ ] Buat Auth System (Login, Register, Logout, Session Management).
Phase 2: Core Mechanics (Backend Focus)
[ ] Buat halaman lobby.php (Tampil ruangan & status lock).
[ ] Buat halaman room.php (Tampil list artefak dari DB).
[ ] Buat handlers/collect.php (Logic tambah XP & masuk koleksi).
Phase 3: User Interface (Frontend Focus)
[ ] Styling CSS (Tema Klasik/Vintage, Font Serif).
[ ] Modal Pop-up untuk detail artefak (JS).
[ ] Halaman my_collection.php.
Phase 4: Gamification & Admin
[ ] Implementasi Kuis sederhana.
[ ] Buat Admin Panel (Add Artifact).
[ ] Polishing (Suara, Animasi Fade-in).
6. MVP Checkpoints
Sesuai dokumen PDF (Halaman 12), pastikan fitur ini jalan sebelum deadline:
Login & Register.
Lobby dengan minimal 3 ruangan.
Bisa masuk ruangan dan klik artefak.
Tombol "Collect" berfungsi (Nambah XP).
Halaman My Collection menampilkan barang yang diambil.
