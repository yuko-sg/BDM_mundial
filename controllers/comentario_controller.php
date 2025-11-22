<?php
session_start();
require_once '../models/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = intval($_POST['id_post']);
    $comentario = trim($_POST['comentario']);
    $id_usuario = $_SESSION['user_id'];
    $fecha = date('Y-m-d H:i:s');
    
    if ($id_post <= 0 || empty($comentario)) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=comentario_vacio");
        exit();
    }
    
    if (strlen($comentario) > 200) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=comentario_largo");
        exit();
    }
    
    $conn = getConnection();
    
    // comentario
    $stmt = $conn->prepare("INSERT INTO comentarios (id_usuario, id_post, comentario, fecha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $id_usuario, $id_post, $comentario, $fecha);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER'] . "#post-" . $id_post);
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=error_comentario");
        exit();
    }
} else {
    header("Location: ../views/dashboard.php");
    exit();
}
?>

