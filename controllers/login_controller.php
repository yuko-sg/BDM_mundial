<?php
session_start();
require_once '../models/db_connection.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate required fields
    if (empty($email) || empty($password)) {
        header("Location: ../views/login.php?error=campos_vacios");
        exit();
    }
    
    // Get database connection
    $conn = getConnection();
    
    // Get user by email using stored procedure
    $stmt = $conn->prepare("CALL sp_login(?)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['contrasena'])) {
            // Password correct, create session
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['correo'];
            $_SESSION['user_role'] = $user['id_rol'];
            
            $stmt->close();
            
            // Clear stored procedure results
            while ($conn->more_results()) {
                $conn->next_result();
            }
            
            $conn->close();
            
            // Redirect to dashboard
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            // Invalid password
            $stmt->close();
            
            // Clear stored procedure results
            while ($conn->more_results()) {
                $conn->next_result();
            }
            
            $conn->close();
            header("Location: ../views/login.php?error=credenciales_invalidas");
            exit();
        }
    } else {
        // User not found
        $stmt->close();
        
        // Clear stored procedure results
        while ($conn->more_results()) {
            $conn->next_result();
        }
        
        $conn->close();
        header("Location: ../views/login.php?error=credenciales_invalidas");
        exit();
    }
} else {
    // If accessed directly without POST, redirect to login page
    header("Location: ../views/login.php");
    exit();
}
?>

