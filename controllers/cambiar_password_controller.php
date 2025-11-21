<?php
session_start();
require_once '../models/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    $user_id = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        header("Location: ../views/perfil.php?error=campos_vacios");
        exit();
    }
    
    // Check if new passwords match
    if ($password_nueva !== $password_confirmar) {
        header("Location: ../views/perfil.php?error=passwords_no_coinciden");
        exit();
    }
    
    // Get database connection
    $conn = getConnection();
    
    // Get current password from database
    $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify current password
    if (!password_verify($password_actual, $user['contrasena'])) {
        $conn->close();
        header("Location: ../views/perfil.php?error=password_actual_incorrecto");
        exit();
    }
    
    // Hash new password
    $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?");
    $stmt->bind_param("si", $password_hash, $user_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../views/perfil.php?success=password_actualizado");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: ../views/perfil.php?error=error_actualizar");
        exit();
    }
} else {
    header("Location: ../views/perfil.php");
    exit();
}
?>

