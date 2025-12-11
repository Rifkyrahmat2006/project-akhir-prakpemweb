<?php
require_once 'app/Config/database.php';

echo "<h2>User List Debug</h2>";
$result = $conn->query("SELECT id, username, password, level FROM users");

if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Password (Hash)</th><th>Level</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . substr($row['password'], 0, 20) . "...</td>";
        echo "<td>" . $row['level'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users found in database.";
}
?>
