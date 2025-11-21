<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: dashboard.php");
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

// Get pending posts
$posts_query = "SELECT p.*, m.mundial, c.categoria
                FROM posts p 
                JOIN mundiales m ON p.id_mundial = m.id_mundial 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                JOIN estados e ON p.id_estado = e.id_estado
                WHERE e.estado = 'por aprobar'
                ORDER BY p.fecha_creacion DESC";
$posts_result = $conn->query($posts_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Posts - Mundial 2026</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/aprobar_posts.css">
</head>
<body>
    <!-- Left Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Mundial 2026</h2>
            <button id="darkModeToggle" class="dark-mode-toggle" title="Cambiar tema">
                <span class="icon-light">☀️</span>
                <span class="icon-dark">🌙</span>
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
            <a href="dashboard.php" class="nav-item">
                Feed
            </a>
            <a href="crear_post.php" class="nav-item">
                Crear Post
            </a>
            <a href="perfil.php" class="nav-item">
                Mi Perfil
            </a>
            <?php if ($_SESSION['user_role'] == 1): ?>
            <a href="aprobar_posts.php" class="nav-item active">
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
        <!-- Header -->
        <div class="admin-header">
            <h1>Posts Pendientes de Aprobar</h1>
            <p class="subtitle">Revisa y aprueba los posts enviados por los usuarios</p>
        </div>
        
        <?php
        // Display success/error messages
        if (isset($_GET['success'])) {
            $success = $_GET['success'];
            switch ($success) {
                case 'aprobado':
                    echo '<div class="alert alert-success">Post aprobado exitosamente.</div>';
                    break;
                case 'rechazado':
                    echo '<div class="alert alert-success">Post rechazado exitosamente.</div>';
                    break;
            }
        }
        
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-error">Hubo un error. Inténtalo de nuevo.</div>';
        }
        ?>
        
        <!-- Posts Container -->
        <div class="posts-container">
            <?php 
            if ($posts_result && $posts_result->num_rows > 0) {
                while ($post = $posts_result->fetch_assoc()): 
            ?>
                <article class="post-card pending">
                    <div class="post-header">
                        <div class="post-meta">
                            <span class="post-date">Creado: <?php echo date('d/m/Y H:i', strtotime($post['fecha_creacion'])); ?></span>
                        </div>
                        <div class="post-badges">
                            <span class="badge badge-mundial"><?php echo htmlspecialchars($post['mundial']); ?></span>
                            <span class="badge badge-categoria"><?php echo htmlspecialchars($post['categoria']); ?></span>
                            <span class="badge badge-pending">Pendiente</span>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>
                    </div>
                    
                    <?php if ($post['multimedia']): ?>
                    <div class="post-media">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($post['multimedia']); ?>" 
                             alt="Imagen del post">
                    </div>
                    <?php endif; ?>
                    
                    <div class="post-actions admin-actions">
                        <form method="POST" action="../controllers/aprobar_post_controller.php" style="display: inline;">
                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                            <input type="hidden" name="accion" value="aprobar">
                            <button type="submit" class="action-btn approve-btn">
                                <span>✓</span> Aprobar
                            </button>
                        </form>
                        
                        <form method="POST" action="../controllers/aprobar_post_controller.php" style="display: inline;">
                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                            <input type="hidden" name="accion" value="rechazar">
                            <button type="submit" class="action-btn reject-btn" onclick="return confirm('¿Estás seguro de rechazar este post?')">
                                <span>✗</span> Rechazar
                            </button>
                        </form>
                    </div>
                </article>
            <?php 
                endwhile;
            } else {
                echo '<div class="no-posts">
                        <p>🎉 No hay posts pendientes de aprobación.</p>
                        <p>Todos los posts están al día.</p>
                      </div>';
            }
            
            $conn->close();
            ?>
        </div>
    </main>
    
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

