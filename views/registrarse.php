<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Mundial 2026</title>
    <link rel="stylesheet" href="css/registrarse.css">
</head>
<body>
    <button id="darkModeToggle" class="dark-mode-toggle" title="Cambiar tema">
        <span class="icon-light">☀️</span>
        <span class="icon-dark">🌙</span>
    </button>
    
    <div class="container">
        <h1>Registrarse</h1>
        <?php
        // Display error messages
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            $mensaje = '';
            switch ($error) {
                case 'campos_vacios':
                    $mensaje = 'Por favor, completa todos los campos requeridos.';
                    break;
                case 'email_invalido':
                    $mensaje = 'El correo electrónico no es válido.';
                    break;
                case 'correo_existe':
                    $mensaje = 'Este correo electrónico ya está registrado.';
                    break;
                case 'error_registro':
                    $mensaje = 'Hubo un error al registrar. Inténtalo de nuevo.';
                    break;
                case 'foto_requerida':
                    $mensaje = 'La foto de perfil es obligatoria.';
                    break;
                case 'tipo_archivo_invalido':
                    $mensaje = 'El archivo debe ser una imagen (JPG, PNG, GIF o WEBP).';
                    break;
                case 'archivo_muy_grande':
                    $mensaje = 'La imagen es muy grande. Tamaño máximo: 5MB.';
                    break;
            }
            if ($mensaje) {
                echo '<div class="alert alert-error">' . htmlspecialchars($mensaje) . '</div>';
            }
        }
        ?>
        <form action="../controllers/register_controller.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ingresa tu nombre completo">
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required placeholder="ejemplo@correo.com">
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="Crea una contraseña segura">
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="form-group">
                <label for="foto_perfil">Foto de Perfil <span style="color: #e53e3e;">*</span></label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" required>
            </div>
            <button type="submit" class="btn">Registrarse</button>
        </form>
        <div class="back-link">
            <a href="index.php">← Volver al inicio</a>
        </div>
    </div>
    
    <script src="js/registrarse.js"></script>
</body>
</html>

