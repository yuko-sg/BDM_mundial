<?php
session_start();
require_once '../models/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_post'])) {
    $id_post = intval($_POST['id_post']);
    $user_id = $_SESSION['user_id'];
    $is_admin = $_SESSION['user_role'] == 1;
    
    $conn = getConnection();
    
    $check_query = "SELECT id_usuario FROM posts WHERE id_post = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id_post);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        
        if ($post['id_usuario'] == $user_id || $is_admin) {
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM likes WHERE id_post = ?");
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM comentarios WHERE id_post = ?");
            $stmt->bind_param("i", $id_post);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM posts WHERE id_post = ?");
            $stmt->bind_param("i", $id_post);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                
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
            $stmt->close();
            $conn->close();
            $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'perfil.php';
            header("Location: ../views/" . $redirect . "?error=sin_permiso");
            exit();
        }
    } else {
        $stmt->close();
        $conn->close();
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'perfil.php';
        header("Location: ../views/" . $redirect . "?error=post_no_encontrado");
        exit();
    }
} else {
    header("Location: ../views/dashboard.php");
    exit();
}
?>

