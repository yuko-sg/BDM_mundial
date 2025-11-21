<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mundial 2026</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <button id="darkModeToggle" class="dark-mode-toggle" title="Cambiar tema">
        <span class="icon-light">☀️</span>
        <span class="icon-dark">🌙</span>
    </button>
    
    <div class="container">
        <h1>¡Bienvenido a Mundial 2026!</h1>
        <div class="buttons-container">
            <a href="login.php" class="btn">Iniciar Sesión</a>
            <a href="registrarse.php" class="btn btn-secondary">Regístrate</a>
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

