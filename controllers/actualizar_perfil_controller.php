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
    $nombre = trim($_POST['nombre']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = trim($_POST['correo']);
    $user_id = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($nombre) || empty($fecha_nacimiento) || empty($correo)) {
        header("Location: ../views/perfil.php?error=campos_vacios");
        exit();
    }
    
    // Validate email format
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../views/perfil.php?error=email_invalido");
        exit();
    }
    
    // Get database connection
    $conn = getConnection();
    
    // Check if new email already exists (for different user)
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ? AND id_usuario != ?");
    $stmt->bind_param("si", $correo, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../views/perfil.php?error=correo_existe");
        exit();
    }
    $stmt->close();
    
    // Handle profile photo if uploaded
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['foto_perfil']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            header("Location: ../views/perfil.php?error=tipo_archivo_invalido");
            exit();
        }
        
        // Check file size (max 5MB)
        if ($_FILES['foto_perfil']['size'] > 5242880) {
            header("Location: ../views/perfil.php?error=archivo_muy_grande");
            exit();
        }
        
        $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
        
        // Update with photo
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, fecha_de_nacimiento = ?, correo = ?, foto_perfil = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssssi", $nombre, $fecha_nacimiento, $correo, $foto_perfil, $user_id);
    } else {
        // Update without photo
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, fecha_de_nacimiento = ?, correo = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssi", $nombre, $fecha_nacimiento, $correo, $user_id);
    }
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['user_name'] = $nombre;
        $_SESSION['user_email'] = $correo;
        
        $stmt->close();
        $conn->close();
        header("Location: ../views/perfil.php?success=perfil_actualizado");
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

