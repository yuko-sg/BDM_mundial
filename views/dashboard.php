<?php
//pa ver los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// selector de mundiales
$mundiales_result = $conn->query("CALL mundiales_selector()");

// checa si el get tiene valor y si lo tiene lo convierte a int con intval
$selected_mundial = isset($_GET['mundial']) ? intval($_GET['mundial']) : 0;
$selected_categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
$selected_sort = isset($_GET['orden']) ? $_GET['orden'] : 'reciente';

// IMPORTANT: Free the result after using stored procedure
while ($conn->more_results()) {
    $conn->next_result();
}

// Get all categorias for filtering
$categorias_result = $conn->query("CALL categorias_selector()");

// IMPORTANT: Free the result again
while ($conn->more_results()) {
    $conn->next_result();
}

// Get posts based on sorting option
if ($selected_sort === 'gustados') {
    // Use stored procedure with por_likes view for "Más Gustados"
    $stmt = $conn->prepare("CALL sp_obtener_posts_por_likes(?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['user_id'], $selected_mundial, $selected_categoria);
    $stmt->execute();
    $posts_result = $stmt->get_result();
} else {
    // Use stored procedure with posts_completos view for other sorting options
    $stmt = $conn->prepare("CALL sp_obtener_posts_dashboard(?, ?, ?, ?)");
    $stmt->bind_param("iiis", $_SESSION['user_id'], $selected_mundial, $selected_categoria, $selected_sort);
    $stmt->execute();
    $posts_result = $stmt->get_result();
}

// Clear stored procedure results
while ($conn->more_results()) {
    $conn->next_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mundial 2026</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Left Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Mundial 2026</h2>
            <button id="darkModeToggle" class="dark-mode-toggle" title="Cambiar tema">
                <span class="icon-light">*</span>
                <span class="icon-dark">/</span>
            </button>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <?php if ($user_data && $user_data['foto_perfil']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($user_data['foto_perfil']); ?>" alt="Foto de perfil">
                <?php else: ?>
                    <span class="avatar-initial"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></span>
                <?php endif; ?>
            </div>
            <div class="user-details">
                <p class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <p class="user-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                 Feed
            </a>
            <a href="crear_post.php" class="nav-item">
                 Crear Post
            </a>
            <a href="perfil.php" class="nav-item">
                 Mi Perfil
            </a>
            <?php if ($_SESSION['user_role'] == 1): ?>
            <a href="aprobar_posts.php" class="nav-item">
                 Administrar
            </a>
            <?php endif; ?>
        </nav>
        
        <div class="sidebar-footer">
            <a href="../controllers/logout_controller.php" class="logout-btn">
                Cerrar Sesión
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Filters -->
        <div class="filters-section">
            <?php
            // Display success/error messages
            if (isset($_GET['success'])) {
                $success = $_GET['success'];
                switch ($success) {
                    case 'post_creado':
                        echo '<div class="alert alert-success">¡Post creado exitosamente! Está pendiente de aprobación.</div>';
                        break;
                    case 'post_eliminado':
                        echo '<div class="alert alert-success">Post eliminado exitosamente.</div>';
                        break;
                    case 'comentario_eliminado':
                        echo '<div class="alert alert-success">Comentario eliminado exitosamente.</div>';
                        break;
                }
            }
            
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                $mensaje = '';
                switch ($error) {
                    case 'error_eliminar':
                        $mensaje = 'Hubo un error al eliminar. Inténtalo de nuevo.';
                        break;
                    case 'sin_permiso':
                        $mensaje = 'No tienes permiso para realizar esta acción.';
                        break;
                }
                if ($mensaje) {
                    echo '<div class="alert alert-error">' . htmlspecialchars($mensaje) . '</div>';
                }
            }
            ?>
            <h1>Feed de Posts</h1>
            
            <form method="GET" action="dashboard.php" class="filters-form">
                <div class="filter-group">
                    <label for="mundial">Mundial:</label>
                    <select name="mundial" id="mundial">
                        <option value="0">Todos los Mundiales</option>
                        <?php 
                        if ($mundiales_result && $mundiales_result->num_rows > 0) {
                            while ($mundial = $mundiales_result->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $mundial['id_mundial']; ?>" 
                            <?php echo ($selected_mundial == $mundial['id_mundial']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mundial['mundial']); ?>
                        </option>
                        <?php 
                            endwhile;
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="categoria">Categoría:</label>
                    <select name="categoria" id="categoria">
                        <option value="0">Todas las Categorías</option>
                        <?php 
                        if ($categorias_result && $categorias_result->num_rows > 0) {
                            while ($categoria = $categorias_result->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>"
                                <?php echo ($selected_categoria == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['categoria']); ?>
                            </option>
                        <?php 
                            endwhile;
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="orden">Ordenar por:</label>
                    <select name="orden" id="orden">
                        <option value="reciente" <?php echo ($selected_sort === 'reciente') ? 'selected' : ''; ?>>
                            Más Recientes
                        </option>
                        <option value="antiguo" <?php echo ($selected_sort === 'antiguo') ? 'selected' : ''; ?>>
                            Más Antiguos
                        </option>
                        <option value="gustados" <?php echo ($selected_sort === 'gustados') ? 'selected' : ''; ?>>
                            Más Gustados
                        </option>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Filtrar</button>
                
                <?php if ($selected_mundial > 0 || $selected_categoria > 0 || $selected_sort !== 'reciente'): ?>
                <a href="dashboard.php" class="clear-filters-btn">Limpiar Filtros</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- posts -->
        <div class="posts-container">
            <?php 
            if ($posts_result && $posts_result->num_rows > 0) {
                while ($post = $posts_result->fetch_assoc()): 
            ?>
                <article class="post-card" id="post-<?php echo $post['id_post']; ?>">
                    <div class="post-header">
                        <div class="post-meta">
                            <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['fecha_aprobacion'])); ?></span>
                        </div>
                        <div class="post-badges">
                            <span class="badge badge-mundial"><?php echo htmlspecialchars($post['mundial']); ?></span>
                            <span class="badge badge-categoria"><?php echo htmlspecialchars($post['categoria']); ?></span>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>
                    </div>
                    
                    <?php if ($post['multimedia']): ?>
                    <div class="post-media">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($post['multimedia']); ?>" 
                             alt="Imagen del post"
                             class="post-image-clickable"
                             onclick="openImageModal(this.src)">
                    </div>
                    <?php endif; ?>
                    
                    <div class="post-actions">
                        <!-- Like Button -->
                        <form method="POST" action="../controllers/like_controller.php">
                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                            <button type="submit" class="action-btn like-btn <?php echo ($post['user_liked'] > 0) ? 'liked' : ''; ?>">
                                <span><?php echo ($post['user_liked'] > 0) ? '❤️' : '🤍'; ?></span>
                                <?php echo $post['likes_count']; ?> 
                            </button>
                        </form>
                        
                        <!-- Comment Button -->
                        <button class="action-btn comment-btn" onclick="toggleComments(<?php echo $post['id_post']; ?>)">
                            💬 <?php echo $post['comments_count']; ?>
                        </button>
                        
                        <!-- Delete Button (only for post owner or admin) -->
                        <?php if ($post['id_usuario'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 1): ?>
                        <form method="POST" action="../controllers/eliminar_post_controller.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este post? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                            <input type="hidden" name="redirect" value="dashboard.php">
                            <button type="submit" class="action-btn delete-btn">
                                Eliminar
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="comments-section" id="comments-<?php echo $post['id_post']; ?>" style="display: none;">
                        <div class="comments-list">
                            <?php
                            // Get comments for this post
                            $comments_query = "SELECT c.*, u.nombre FROM comentarios c 
                                             JOIN usuarios u ON c.id_usuario = u.id_usuario 
                                             WHERE c.id_post = " . $post['id_post'] . " 
                                             ORDER BY c.fecha ASC";
                            $comments_result = $conn->query($comments_query);
                            
                            if ($comments_result && $comments_result->num_rows > 0) {
                                while ($comment = $comments_result->fetch_assoc()):
                            ?>
                                <div class="comment">
                                    <div class="comment-header">
                                        <div class="comment-info">
                                            <strong><?php echo htmlspecialchars($comment['nombre']); ?></strong>
                                            <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['fecha'])); ?></span>
                                        </div>
                                        <?php if ($comment['id_usuario'] == $_SESSION['user_id'] || $_SESSION['user_role'] == 1): ?>
                                        <form method="POST" action="../controllers/eliminar_comentario_controller.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este comentario?');">
                                            <input type="hidden" name="id_comentario" value="<?php echo $comment['id_comentario']; ?>">
                                            <button type="submit" class="delete-comment-btn" title="Eliminar comentario">🗑️</button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                    <p class="comment-text"><?php echo htmlspecialchars($comment['comentario']); ?></p>
                                </div>
                            <?php
                                endwhile;
                            } else {
                                echo '<p class="no-comments">No hay comentarios aún.</p>';
                            }
                            ?>
                        </div>
                        
                        <!-- Add Comment Form -->
                        <form method="POST" action="../controllers/comentario_controller.php" class="comment-form">
                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                            <textarea name="comentario" placeholder="Escribe un comentario..." maxlength="200" required></textarea>
                            <button type="submit" class="btn-comment">Comentar</button>
                        </form>
                    </div>
                </article>
            <?php 
                endwhile;
            } else {
                echo '<div class="no-posts">
                        <p>No hay posts aprobados para mostrar.</p>
                        <p>Prueba con otros filtros o vuelve más tarde.</p>
                      </div>';
            }
            
            $conn->close();
            ?>
        </div>
    </main>
    
    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <span class="modal-close" onclick="closeImageModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>
    
    <script src="js/dashboard.js"></script>
</body>
</html>

