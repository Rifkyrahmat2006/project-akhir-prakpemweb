<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
<script>
    // Clear sessionStorage (browser storage for congrats modals, etc.)
    sessionStorage.clear();
    // Redirect to login
    window.location.href = 'index.php';
</script>
</body>
</html>
