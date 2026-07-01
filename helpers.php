<?php
// �adowanie po��czenia z baz�
require_once 'db_connect.php';

/**
 * Sprawdza, czy istnieje użytkownik o danym loginie lub e-mailu
 */
function userExists($conn, $username, $email) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

/**
 * Dodaje nowego użytkownika (domyślnie nadaje rolę admina przy pierwszej rejestracji)
 */
function registerUser($conn, $username, $email, $password) {
    // Sprawdż, czy istnieje już jakiś użytkownik
    $result = $conn->query("SELECT COUNT(*) AS count FROM users");
    $row = $result->fetch_assoc();
    $isFirstUser = ($row['count'] == 0);

    // Hashowanie hasla
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = $isFirstUser ? 'admin' : 'user';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
    $stmt->execute();
    $stmt->close();

    return $isFirstUser ? 'admin' : 'user';
}

/**
 * Logowanie użytkownika
 */
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

/**
 * Sprawdza, czy zalogowany u�ytkownik ma uprawnienia admina
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Zwraca aktualnie zalogowanego użytkownika
 */
function currentUser() {
    return $_SESSION['username'] ?? null;
}
?>
