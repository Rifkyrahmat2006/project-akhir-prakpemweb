<?php
$host = 'localhost';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create Database
$sql = "CREATE DATABASE IF NOT EXISTS db_museum";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->select_db("db_museum");

// Table Users
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('visitor', 'admin') DEFAULT 'visitor',
    xp INT DEFAULT 0,
    level INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Table Rooms
$sql = "CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    min_level INT DEFAULT 1,
    image_url VARCHAR(255)
)";
$conn->query($sql);

// Table Artifacts
$sql = "CREATE TABLE IF NOT EXISTS artifacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    xp_reward INT DEFAULT 50,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Table User Collections
$sql = "CREATE TABLE IF NOT EXISTS user_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    artifact_id INT,
    collected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (artifact_id) REFERENCES artifacts(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Seed Rooms
$rooms_check = $conn->query("SELECT * FROM rooms");
if ($rooms_check->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO rooms (name, description, min_level, image_url) VALUES (?, ?, ?, ?)");
    
    $rooms_data = [
        ['Medieval Hall', 'Dark ages, knights, and castles.', 1, 'https://images.unsplash.com/photo-1599739291060-4578e77dac5d'],
        ['Renaissance Gallery', 'The rebirth of art and culture.', 2, 'https://images.unsplash.com/photo-1518998053901-5348d3969105'],
        ['Baroque Palace', 'Grandeur, drama, and opulence.', 3, 'https://images.unsplash.com/photo-1565060169194-118831f52d9b'],
        ['Royal Archives', 'Secret documents and forbidden history.', 4, 'https://images.unsplash.com/photo-1505664194779-8beaceb93744']
    ];

    foreach ($rooms_data as $room) {
        $stmt->bind_param("ssis", $room[0], $room[1], $room[2], $room[3]);
        $stmt->execute();
    }
    echo "Rooms seeded.\n";
}

// Seed Artifacts
$artifacts_check = $conn->query("SELECT * FROM artifacts");
if ($artifacts_check->num_rows == 0) {
    // Get Room IDs
    $rooms = $conn->query("SELECT id, name FROM rooms");
    $room_ids = [];
    while ($r = $rooms->fetch_assoc()) {
        $room_ids[$r['name']] = $r['id'];
    }

    $stmt = $conn->prepare("INSERT INTO artifacts (room_id, name, description, image_url, xp_reward) VALUES (?, ?, ?, ?, ?)");

    $artifacts_data = [
        // Medieval Hall
        [$room_ids['Medieval Hall'], 'Iron Helm', 'A sturdy helmet from a fallen knight.', 'https://images.unsplash.com/photo-1599739291060-4578e77dac5d', 50],
        [$room_ids['Medieval Hall'], 'Old Scroll', 'Ancient writings depicting a great battle.', 'https://images.unsplash.com/photo-1620618871900-589547ea8cb9', 30],
        // Renaissance Gallery
        [$room_ids['Renaissance Gallery'], 'Marble Bust', 'A sculpture from the height of the Renaissance.', 'https://images.unsplash.com/photo-1576504677631-061820f666f8', 80],
        [$room_ids['Renaissance Gallery'], 'Paint Brush', 'Used by a master artist.', 'https://images.unsplash.com/photo-1513364776144-60967b0f800f', 40],
        // Baroque Palace
        [$room_ids['Baroque Palace'], 'Golden Chalice', 'A cup fit for a king.', 'https://images.unsplash.com/photo-1601004890684-d8cbf643f5f2', 100],
    ];

    foreach ($artifacts_data as $a) {
        $stmt->bind_param("isssi", $a[0], $a[1], $a[2], $a[3], $a[4]);
        $stmt->execute();
    }
    echo "Artifacts seeded.\n";
}

// Table Quizzes
$sql = "CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    question TEXT NOT NULL,
    option_a VARCHAR(255),
    option_b VARCHAR(255),
    option_c VARCHAR(255),
    correct_option CHAR(1),
    xp_reward INT DEFAULT 20,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Table to track answered quizzes (prevent re-answering)
$sql = "CREATE TABLE IF NOT EXISTS user_quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_id INT,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Seed Quizzes
$quiz_check = $conn->query("SELECT * FROM quizzes");
if ($quiz_check->num_rows == 0) {
    $rooms = $conn->query("SELECT id, name FROM rooms");
    $room_ids = [];
    while ($r = $rooms->fetch_assoc()) {
        $room_ids[$r['name']] = $r['id'];
    }

    $stmt = $conn->prepare("INSERT INTO quizzes (room_id, question, option_a, option_b, option_c, correct_option, xp_reward) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $quizzes_data = [
        // Medieval Hall
        [$room_ids['Medieval Hall'], 'What period is known as the Dark Ages?', 'Renaissance', 'Medieval', 'Baroque', 'b', 25],
        [$room_ids['Medieval Hall'], 'What type of armor did knights typically wear?', 'Leather', 'Chainmail', 'Plastic', 'b', 20],
        // Renaissance Gallery
        [$room_ids['Renaissance Gallery'], 'Who painted the Mona Lisa?', 'Michelangelo', 'Leonardo da Vinci', 'Raphael', 'b', 30],
        [$room_ids['Renaissance Gallery'], 'Renaissance means...?', 'Revolution', 'Rebirth', 'Religion', 'b', 25],
        // Baroque Palace
        [$room_ids['Baroque Palace'], 'Baroque art originated in which country?', 'France', 'Italy', 'Spain', 'b', 30],
        [$room_ids['Baroque Palace'], 'Which artist is famous for Baroque paintings?', 'Caravaggio', 'Picasso', 'Van Gogh', 'a', 35],
        // Royal Archives
        [$room_ids['Royal Archives'], 'What document limited the power of English kings?', 'Declaration of Independence', 'Magna Carta', 'Constitution', 'b', 40],
    ];

    foreach ($quizzes_data as $q) {
        $stmt->bind_param("isssssi", $q[0], $q[1], $q[2], $q[3], $q[4], $q[5], $q[6]);
        $stmt->execute();
    }
    echo "Quizzes seeded.\n";
}

echo "Setup complete!";
$conn->close();
?>
