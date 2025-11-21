<?php
session_start();
require_once '../models/db_connection.php';

// validar login usuario
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// validar form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $correo = trim($_POST['correo']);
    $user_id = $_SESSION['user_id'];
    
    // validar kamposss
    if (empty($nombre) || empty($fecha_nacimiento) || empty($correo)) {
        header("Location: ../views/perfil.php?error=campos_vacios");
        exit();
    }
    
    // validar formato email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../views/perfil.php?error=email_invalido");
        exit();
    }
    
    $conn = getConnection();
    
    // checar email duplicado
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
    
    // pfp
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        // pfp data type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['foto_perfil']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            header("Location: ../views/perfil.php?error=tipo_archivo_invalido");
            exit();
        }
        
        // pfp size
        if ($_FILES['foto_perfil']['size'] > 5242880) {
            header("Location: ../views/perfil.php?error=archivo_muy_grande");
            exit();
        }
        
        $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
        
        // actualizar con foto
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, fecha_de_nacimiento = ?, correo = ?, foto_perfil = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssssi", $nombre, $fecha_nacimiento, $correo, $foto_perfil, $user_id);
    } else {
        // sin foto
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, fecha_de_nacimiento = ?, correo = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssi", $nombre, $fecha_nacimiento, $correo, $user_id);
    }
    
    if ($stmt->execute()) {
        // actualizar session vars
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

