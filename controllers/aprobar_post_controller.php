<?php
session_start();
require_once '../models/db_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../views/dashboard.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = intval($_POST['id_post']);
    $accion = $_POST['accion'];
    
    // Validate inputs
    if ($id_post <= 0 || !in_array($accion, ['aprobar', 'rechazar'])) {
        header("Location: ../views/aprobar_posts.php?error=datos_invalidos");
        exit();
    }
    
    // Get database connection
    $conn = getConnection();
    
    if ($accion === 'aprobar') {
        // Approve post: set estado to "aprobado" (id 1) and set fecha_aprobacion
        $fecha_aprobacion = date('Y-m-d H:i:s'); 
        $stmt = $conn->prepare("UPDATE posts SET id_estado = 1, fecha_aprobacion = ? WHERE id_post = ?");
        $stmt->bind_param("si", $fecha_aprobacion, $id_post);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../views/aprobar_posts.php?success=aprobado");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../views/aprobar_posts.php?error=error_aprobar");
            exit();
        }
    } else if ($accion === 'rechazar') {
        // Reject post: delete it from database
        $stmt = $conn->prepare("DELETE FROM posts WHERE id_post = ?");
        $stmt->bind_param("i", $id_post);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: ../views/aprobar_posts.php?success=rechazado");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: ../views/aprobar_posts.php?error=error_rechazar");
            exit();
        }
    }
} else {
    // If accessed directly without POST, redirect to aprobar posts page
    header("Location: ../views/aprobar_posts.php");
    exit();
}
?>

