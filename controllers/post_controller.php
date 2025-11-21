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
    // Get form data
    $contenido = trim($_POST['contenido']);
    $id_mundial = intval($_POST['id_mundial']);
    $id_categoria = intval($_POST['id_categoria']);
    $user_id = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($contenido) || $id_mundial <= 0 || $id_categoria <= 0) {
        header("Location: ../views/crear_post.php?error=campos_vacios");
        exit();
    }
    
    // Validate content length
    if (strlen($contenido) > 300) {
        header("Location: ../views/crear_post.php?error=contenido_largo");
        exit();
    }
    
    // Handle multimedia file
    $multimedia = null;
    if (isset($_FILES['multimedia']) && $_FILES['multimedia']['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/mpeg', 'video/quicktime'];
        $file_type = $_FILES['multimedia']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            header("Location: ../views/crear_post.php?error=tipo_archivo_invalido");
            exit();
        }
        
        // Check file size (max 10MB)
        if ($_FILES['multimedia']['size'] > 10485760) {
            header("Location: ../views/crear_post.php?error=archivo_muy_grande");
            exit();
        }
        
        $multimedia = file_get_contents($_FILES['multimedia']['tmp_name']);
    }
    
    // Get database connection
    $conn = getConnection();
    

    $fecha_creacion = date('Y-m-d H:i:s');
    //2 es por aprobar
    $id_estado = 2; 
    
    // Insert post
    if ($multimedia) {
        $stmt = $conn->prepare("INSERT INTO posts (id_usuario, contenido, multimedia, fecha_creacion, id_mundial, id_estado, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiii", $user_id, $contenido, $multimedia, $fecha_creacion, $id_mundial, $id_estado, $id_categoria);
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (id_usuario, contenido, fecha_creacion, id_mundial, id_estado, id_categoria) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiii", $user_id, $contenido, $fecha_creacion, $id_mundial, $id_estado, $id_categoria);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // Post created successfully, redirect to dashboard
        header("Location: ../views/dashboard.php?success=post_creado");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: ../views/crear_post.php?error=error_post");
        exit();
    }
} else {
    // If accessed directly without POST, redirect to create post page
    header("Location: ../views/crear_post.php");
    exit();
}
?>

