# VesperaVeloria - Classic Old Europe Museum

![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.0-cyan)

Pengalaman museum interaktif dengan sistem koleksi artefak, kuis, dan level progression.

## 🎮 Demo Akun

### 👤 Visitor Account

```
Username: visitor
Password: visitor123
```

### 🔐 Admin Account

```
Username: admin
Password: admin123
```

## 🌐 Demo URL

**Production:** [https://vesperaveloria.befreetechnology.my.id](https://vesperaveloria.befreetechnology.my.id)

| Halaman     | URL                |
| ----------- | ------------------ |
| Home        | `/index.php`       |
| Login       | `/login.php`       |
| Register    | `/register.php`    |
| Lobby       | `/lobby/index.php` |
| Admin Panel | `/admin/`          |

## ✨ Fitur

### Visitor

- 🚪 Jelajahi ruangan museum bertema (Medieval, Renaissance, Baroque, Royal Archives)
- 💎 Kumpulkan artefak tersembunyi
- 📝 Jawab kuis dari Professor Aldric
- 🏆 Naik level dan buka ruangan baru
- 🎵 Background musik per ruangan

### Admin

- 📊 Dashboard statistik
- 🖼️ Kelola artefak (CRUD)
- ❓ Kelola kuis (CRUD)
- 🗺️ Room Editor - drag & drop posisi artefak
- 💬 Edit dialog Professor
- 👥 Monitor pengunjung

## 🛠️ Teknologi

- **Backend:** PHP 8.0+
- **Database:** MySQL
- **Frontend:** TailwindCSS, Vanilla JS
- **Icons:** Font Awesome

## 📁 Struktur Folder

```
project-akhir/
├── app/                  # Backend logic
│   ├── Config/          # Environment & database config
│   ├── Controllers/     # Page controllers
│   ├── Handlers/        # API handlers
│   ├── Middleware/      # Auth middleware
│   └── Models/          # Data models
├── public/              # Web root
│   ├── admin/           # Admin panel
│   ├── assets/          # CSS, JS, images, music
│   └── lobby/           # Game pages
├── views/               # View templates
│   ├── components/      # Reusable components
│   ├── layouts/         # Header, footer
│   └── pages/           # Page views
└── database/            # SQL migrations
```

## 🚀 Instalasi Lokal

1. Clone repository
2. Import `db_museum.sql` ke MySQL
3. Copy `app/Config/env.example.php` ke `env.php`
4. Sesuaikan kredensial database di `env.php`
5. Akses via `http://localhost/project-akhir/public/`

## 📝 License

MIT License © 2024
