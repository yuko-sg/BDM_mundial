<?php
require_once '../models/db_connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //conseguir datos
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    
    // validarlos
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($fecha_nacimiento)) {
        header("Location: ../views/registrarse.php?error=campos_vacios");
        exit();
    }
    
    // formato email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../views/registrarse.php?error=email_invalido");
        exit();
    }
    
    // manejo de la foto
    $foto_perfil = null;
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        // validar tipo de archivo
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['foto_perfil']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            header("Location: ../views/registrarse.php?error=tipo_archivo_invalido");
            exit();
        }
        
        // tamaño del archivo
        if ($_FILES['foto_perfil']['size'] > 5242880) {
            header("Location: ../views/registrarse.php?error=archivo_muy_grande");
            exit();
        }
        
        $foto_perfil = file_get_contents($_FILES['foto_perfil']['tmp_name']);
    } else {
        // Photo is required by the database
        header("Location: ../views/registrarse.php?error=foto_requerida");
        exit();
    }
    
    // Get database connection
    $conn = getConnection();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header("Location: ../views/registrarse.php?error=correo_existe");
        exit();
    }
    $stmt->close();
    
    // hachiar la contraseña
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    
    // default rol es 2 pa los normies
    $id_rol = 2;
    
    // Insert new user using stored procedure
    $stmt = $conn->prepare("CALL sp_registrar_usuario(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nombre, $fecha_nacimiento, $foto_perfil, $correo, $contrasena_hash, $id_rol);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();
        
        // Clear stored procedure results
        while ($conn->more_results()) {
            $conn->next_result();
        }
        
        $conn->close();
        // Registration successful, redirect to login
        header("Location: ../views/login.php?success=registro_exitoso");
        exit();
    } else {
        $stmt->close();
        
        // Clear stored procedure results
        while ($conn->more_results()) {
            $conn->next_result();
        }
        
        $conn->close();
        header("Location: ../views/registrarse.php?error=error_registro");
        exit();
    }
} else {
    // If accessed directly without POST, redirect to registration page
    header("Location: ../views/login.php");
    exit();
}
?>

