<?php
/**
 * Migration script to add professor_dialogs column to rooms table
 * Run this once to update the database
 */

require_once 'app/Config/database.php';

// Add professor_dialogs column
$sql = "ALTER TABLE `rooms` ADD COLUMN `professor_dialogs` TEXT DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Success: professor_dialogs column added to rooms table!<br>";
    
    // Set default dialogs for existing rooms
    $rooms = $conn->query("SELECT id, name, description FROM rooms");
    
    while ($room = $rooms->fetch_assoc()) {
        $default_dialogs = json_encode([
            "Welcome, young explorer! I am Professor Aldric.",
            "You have entered the " . $room['name'] . ". A magnificent place, isn't it?",
            $room['description'],
            "Look for the glowing markers to find hidden artifacts.",
            "Collect them all to gain knowledge and experience. Good luck!"
        ]);
        
        $stmt = $conn->prepare("UPDATE rooms SET professor_dialogs = ? WHERE id = ?");
        $stmt->bind_param("si", $default_dialogs, $room['id']);
        $stmt->execute();
        
        echo "Set default dialogs for: " . $room['name'] . "<br>";
    }
    
    echo "<br><strong>Migration complete!</strong>";
} else {
    if (strpos($conn->error, "Duplicate column") !== false) {
        echo "Column already exists, skipping...";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
