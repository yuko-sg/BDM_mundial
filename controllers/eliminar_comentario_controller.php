<?php
session_start();
require_once '../models/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_comentario'])) {
    $id_comentario = intval($_POST['id_comentario']);
    $user_id = $_SESSION['user_id'];
    $is_admin = $_SESSION['user_role'] == 1;
    
    $conn = getConnection();
    
    // verificar admin
    $check_query = "SELECT id_usuario, id_post FROM comentarios WHERE id_comentario = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id_comentario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $comentario = $result->fetch_assoc();
        
        // habilitar eliminacion si es el propietario o admin
        if ($comentario['id_usuario'] == $user_id || $is_admin) {
            $stmt->close();
            
            // borrar comments
            $stmt = $conn->prepare("DELETE FROM comentarios WHERE id_comentario = ?");
            $stmt->bind_param("i", $id_comentario);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                
                //bye bye
                header("Location: ../views/dashboard.php?success=comentario_eliminado#post-" . $comentario['id_post']);
                exit();
            } else {
                $stmt->close();
                $conn->close();
                header("Location: ../views/dashboard.php?error=error_eliminar#post-" . $comentario['id_post']);
                exit();
            }
        } else {
            // bye bye
            $stmt->close();
            $conn->close();
            header("Location: ../views/dashboard.php?error=sin_permiso#post-" . $comentario['id_post']);
            exit();
        }
    } else {
        //error
        $stmt->close();
        $conn->close();
        header("Location: ../views/dashboard.php?error=comentario_no_encontrado");
        exit();
    }
} else {
    header("Location: ../views/dashboard.php");
    exit();
}
?>

