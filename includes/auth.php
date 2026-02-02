<?php
// Authentication functions

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

function logout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
