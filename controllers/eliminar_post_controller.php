<?php
session_start();
require_once '../models/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_post'])) {
    $id_post = intval($_POST['id_post']);
    $user_id = $_SESSION['user_id'];
    $is_admin = $_SESSION['user_role'] == 1;
    
    $conn = getConnection();
    
    // First, verify ownership or admin status
    $check_query = "SELECT id_usuario FROM posts WHERE id_post = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id_post);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        
        // Allow deletion if user owns the post OR is admin
        if ($post['id_usuario'] == $user_id || $is_admin) {
            $stmt->close();
            
            // Delete related records first (foreign keys)
            // Delete likes
            $stmt = $conn->prepare("DELETE FROM likes WHERE id_post = ?");
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $stmt->close();
            
            // Delete comments
            $stmt = $conn->prepare("DELETE FROM comentarios WHERE id_post = ?");
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $stmt->close();
            
            // Delete the post
            $stmt = $conn->prepare("DELETE FROM posts WHERE id_post = ?");
            $stmt->bind_param("i", $id_post);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                
                // Redirect based on referrer
                $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'perfil.php';
                header("Location: ../views/" . $redirect . "?success=post_eliminado");
                exit();
            } else {
                $stmt->close();
                $conn->close();
                $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'perfil.php';
                header("Location: ../views/" . $redirect . "?error=error_eliminar");
                exit();
            }
        } else {
            // User doesn't have permission
            $stmt->close();
            $conn->close();
            $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'perfil.php';
            header("Location: ../views/" . $redirect . "?error=sin_permiso");
            exit();
        }
    } else {
        // Post not found
        $stmt->close();
        $conn->close();
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'perfil.php';
        header("Location: ../views/" . $redirect . "?error=post_no_encontrado");
        exit();
    }
} else {
    // Invalid request
    header("Location: ../views/dashboard.php");
    exit();
}
?>

