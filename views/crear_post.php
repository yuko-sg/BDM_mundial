<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../models/db_connection.php';
$conn = getConnection();

// Get user's profile picture
$user_query = "SELECT foto_perfil FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$stmt->close();

// get mundiales y librera variable
$mundiales_result = $conn->query("CALL mundiales_selector()");

while ($conn->more_results()) {
    $conn->next_result();
}

// get categorias y librera variable
$categorias_result = $conn->query("CALL categorias_selector()");

while ($conn->more_results()) {
    $conn->next_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Post - Mundial 2026</title>
    <link rel="stylesheet" href="css/crear_post.css">
</head>
<body>
    <button id="darkModeToggle" class="dark-mode-toggle" title="Cambiar tema">
        <span class="icon-light">☀️</span>
        <span class="icon-dark">🌙</span>
    </button>
    
    <div class="container">
        <h1>Crear Nuevo Post</h1>
        
        <?php
        // Display error messages
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            $mensaje = '';
            switch ($error) {
                case 'campos_vacios':
                    $mensaje = 'Por favor, completa todos los campos requeridos.';
                    break;
                case 'contenido_largo':
                    $mensaje = 'El contenido no puede exceder 300 caracteres.';
                    break;
                case 'tipo_archivo_invalido':
                    $mensaje = 'El archivo debe ser una imagen o video válido.';
                    break;
                case 'archivo_muy_grande':
                    $mensaje = 'El archivo es muy grande. Tamaño máximo: 10MB.';
                    break;
                case 'error_post':
                    $mensaje = 'Hubo un error al crear el post. Inténtalo de nuevo.';
                    break;
            }
            if ($mensaje) {
                echo '<div class="alert alert-error">' . htmlspecialchars($mensaje) . '</div>';
            }
        }
        ?>
        
        <form action="../controllers/post_controller.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="contenido">Contenido del Post <span class="required">*</span></label>
                <textarea id="contenido" name="contenido" required placeholder="Escribe el contenido de tu post (máx. 300 caracteres)" maxlength="300" rows="5"></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 300 caracteres
                </div>
            </div>
            
            <div class="form-group">
                <label for="id_mundial">Mundial <span class="required">*</span></label>
                <select id="id_mundial" name="id_mundial" required>
                    <option value="">Selecciona un mundial</option>
                    <?php 
                    if ($mundiales_result && $mundiales_result->num_rows > 0) {
                        while ($mundial = $mundiales_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $mundial['id_mundial']; ?>">
                            <?php echo htmlspecialchars($mundial['mundial']); ?>
                        </option>
                    <?php 
                        endwhile;
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_categoria">Categoría <span class="required">*</span></label>
                <select id="id_categoria" name="id_categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php 
                    if ($categorias_result && $categorias_result->num_rows > 0) {
                        while ($categoria = $categorias_result->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $categoria['id_categoria']; ?>">
                            <?php echo htmlspecialchars($categoria['categoria']); ?>
                        </option>
                    <?php 
                        endwhile;
                    }
                    $conn->close();
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="multimedia">Multimedia (Opcional)</label>
                <input type="file" id="multimedia" name="multimedia" accept="image/*,video/*">
                <small class="help-text">Sube una imagen o video relacionado con tu post (máx. 10MB)</small>
            </div>
            
            <button type="submit" class="btn">Crear Post</button>
        </form>
        
        <div class="back-link">
            <a href="dashboard.php">← Volver al Dashboard</a>
        </div>
    </div>
    
    <link rel="stylesheet" href="js/crear_post.js">
</body>
</html>

