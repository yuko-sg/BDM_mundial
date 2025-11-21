<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../models/db_connection.php';
$conn = getConnection();

// Get user information
$user_query = "SELECT nombre, fecha_de_nacimiento, foto_perfil, correo FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// Get user's profile picture
$user_query = "SELECT foto_perfil FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$stmt->close();

// Get user's posts
$posts_query = "SELECT p.*, m.mundial, c.categoria, e.estado
                FROM posts p 
                JOIN mundiales m ON p.id_mundial = m.id_mundial 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                JOIN estados e ON p.id_estado = e.id_estado
                WHERE p.id_usuario = ?
                ORDER BY p.fecha_creacion DESC";
$stmt = $conn->prepare($posts_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();

// Get user's liked posts
$likes_query = "SELECT p.*, m.mundial, c.categoria, l.fecha as fecha_like
                FROM likes l
                JOIN posts p ON l.id_post = p.id_post
                JOIN mundiales m ON p.id_mundial = m.id_mundial 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                JOIN estados e ON p.id_estado = e.id_estado
                WHERE l.id_usuario = ? AND e.estado = 'aprobado'
                ORDER BY l.fecha DESC";
$stmt = $conn->prepare($likes_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$likes_result = $stmt->get_result();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Mundial 2026</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/perfil.css">
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
            <a href="perfil.php" class="nav-item active">
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
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-picture">
                <?php if ($user['foto_perfil']): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($user['foto_perfil']); ?>" alt="Foto de perfil">
                <?php else: ?>
                    <div class="default-avatar">
                        <?php echo strtoupper(substr($user['nombre'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['nombre']); ?></h1>
                <p class="email"><?php echo htmlspecialchars($user['correo']); ?></p>
                <p class="birth-date">📅 <?php echo date('d/m/Y', strtotime($user['fecha_de_nacimiento'])); ?></p>
            </div>
        </div>

        <?php
        // Display success/error messages
        if (isset($_GET['success'])) {
            $success = $_GET['success'];
            switch ($success) {
                case 'perfil_actualizado':
                    echo '<div class="alert alert-success">¡Perfil actualizado exitosamente!</div>';
                    break;
                case 'password_actualizado':
                    echo '<div class="alert alert-success">¡Contraseña actualizada exitosamente!</div>';
                    break;
            }
        }
        
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            $mensaje = '';
            switch ($error) {
                case 'password_actual_incorrecto':
                    $mensaje = 'La contraseña actual es incorrecta.';
                    break;
                case 'passwords_no_coinciden':
                    $mensaje = 'Las contraseñas nuevas no coinciden.';
                    break;
                case 'error_actualizar':
                    $mensaje = 'Hubo un error al actualizar. Inténtalo de nuevo.';
                    break;
            }
            if ($mensaje) {
                echo '<div class="alert alert-error">' . htmlspecialchars($mensaje) . '</div>';
            }
        }
        ?>
        
        <!-- Tabs Navigation -->
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="mis-posts">
                Mis Posts
            </button>
            <button class="tab-btn" data-tab="mis-likes">
                Mis Likes
            </button>
            <button class="tab-btn" data-tab="editar-perfil">
                Editar Perfil
            </button>
        </div>
        
        <!-- Tab Content: Mis Posts -->
        <div class="tab-content active" id="mis-posts">
            <h2 class="section-title">Mis Posts</h2>
            <div class="posts-container">
                <?php 
                if ($posts_result && $posts_result->num_rows > 0) {
                    while ($post = $posts_result->fetch_assoc()): 
                ?>
                    <article class="post-card">
                        <div class="post-header">
                            <div class="post-meta">
                                <span class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['fecha_creacion'])); ?></span>
                            </div>
                            <div class="post-badges">
                                <span class="badge badge-mundial"><?php echo htmlspecialchars($post['mundial']); ?></span>
                                <span class="badge badge-categoria"><?php echo htmlspecialchars($post['categoria']); ?></span>
                                <span class="badge badge-<?php echo $post['estado'] === 'aprobado' ? 'approved' : 'pending'; ?>">
                                    <?php echo ucfirst($post['estado']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <p><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>
                        </div>
                        
                        <?php if ($post['multimedia']): ?>
                        <div class="post-media">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($post['multimedia']); ?>" alt="Imagen del post">
                        </div>
                        <?php endif; ?>
                    </article>
                <?php 
                    endwhile;
                } else {
                    echo '<div class="no-posts">
                            <p>Aún no has creado ningún post.</p>
                            <a href="crear_post.php" class="btn-link">Crear mi primer post</a>
                          </div>';
                }
                ?>
            </div>
        </div>
        
        <!-- Tab Content: Mis Likes -->
        <div class="tab-content" id="mis-likes">
            <h2 class="section-title">Posts que me Gustan</h2>
            <div class="posts-container">
                <?php 
                if ($likes_result && $likes_result->num_rows > 0) {
                    while ($post = $likes_result->fetch_assoc()): 
                ?>
                    <article class="post-card">
                        <div class="post-header">
                            <div class="post-meta">
                                <span class="post-date">❤️ <?php echo date('d/m/Y H:i', strtotime($post['fecha_like'])); ?></span>
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
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($post['multimedia']); ?>" alt="Imagen del post">
                        </div>
                        <?php endif; ?>
                    </article>
                <?php 
                    endwhile;
                } else {
                    echo '<div class="no-posts">
                            <p>Aún no has dado like a ningún post.</p>
                            <a href="dashboard.php" class="btn-link">Explorar posts</a>
                          </div>';
                }
                ?>
            </div>
        </div>
        
        <!-- Tab Content: Editar Perfil -->
        <div class="tab-content" id="editar-perfil">
            <h2 class="section-title">Editar Perfil</h2>
            
            <div class="edit-forms">
                <!-- Edit Profile Info Form -->
                <div class="form-card">
                    <h3>Información Personal</h3>
                    <form action="../controllers/actualizar_perfil_controller.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $user['fecha_de_nacimiento']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="correo">Correo Electrónico</label>
                            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="foto_perfil">Nueva Foto de Perfil (Opcional)</label>
                            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                            <small class="help-text">Deja en blanco si no quieres cambiar la foto</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
                
                <!-- Change Password Form -->
                <div class="form-card">
                    <h3>Cambiar Contraseña</h3>
                    <form action="../controllers/cambiar_password_controller.php" method="POST">
                        <div class="form-group">
                            <label for="password_actual">Contraseña Actual</label>
                            <input type="password" id="password_actual" name="password_actual" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_nueva">Nueva Contraseña</label>
                            <input type="password" id="password_nueva" name="password_nueva" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmar">Confirmar Nueva Contraseña</label>
                            <input type="password" id="password_confirmar" name="password_confirmar" required>
                        </div>
                        
                        <button type="submit" class="btn btn-secondary">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        const darkMode = localStorage.getItem('darkMode');
        if (darkMode === 'enabled') {
            body.classList.add('dark-mode');
        }
        
        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        });
        
        // Tabs functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button
                btn.classList.add('active');
                
                // Show corresponding content
                const tabId = btn.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>

