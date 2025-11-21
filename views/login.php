<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Mundial 2026</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <button id="darkModeToggle" class="dark-mode-toggle" title="Cambiar tema">
        <span class="icon-light">☀️</span>
        <span class="icon-dark">🌙</span>
    </button>
    
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <?php
        // Display success message
        if (isset($_GET['success']) && $_GET['success'] === 'registro_exitoso') {
            echo '<div class="alert alert-success">¡Registro exitoso! Ahora puedes iniciar sesión.</div>';
        }
        
        // Display error messages
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            $mensaje = '';
            switch ($error) {
                case 'campos_vacios':
                    $mensaje = 'Por favor, completa todos los campos.';
                    break;
                case 'credenciales_invalidas':
                    $mensaje = 'Correo o contraseña incorrectos.';
                    break;
            }
            if ($mensaje) {
                echo '<div class="alert alert-error">' . htmlspecialchars($mensaje) . '</div>';
            }
        }
        ?>
        <form action="../controllers/login_controller.php" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
            </div>
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
        <div class="back-link">
            <a href="index.php">← Volver al inicio</a>
        </div>
    </div>
    
    <script>
        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        // Check for saved dark mode preference
        const darkMode = localStorage.getItem('darkMode');
        
        if (darkMode === 'enabled') {
            body.classList.add('dark-mode');
        }
        
        // Toggle dark mode
        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            
            // Save preference
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        });
    </script>
</body>
</html>

