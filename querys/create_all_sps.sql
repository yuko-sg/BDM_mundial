USE bdm_mundial;

DELIMITER $$

CREATE PROCEDURE sp_login(IN p_correo VARCHAR(30))
BEGIN
    SELECT id_usuario, nombre, correo, contrasena, id_rol FROM usuarios WHERE correo = p_correo;
END$$

CREATE PROCEDURE sp_registrar_usuario(
    IN p_nombre VARCHAR(30), IN p_fecha_nacimiento DATE, IN p_foto_perfil MEDIUMBLOB,
    IN p_correo VARCHAR(30), IN p_contrasena VARCHAR(255), IN p_id_rol INT)
BEGIN
    INSERT INTO usuarios (nombre, fecha_de_nacimiento, foto_perfil, correo, contrasena, id_rol) 
    VALUES (p_nombre, p_fecha_nacimiento, p_foto_perfil, p_correo, p_contrasena, p_id_rol);
    SELECT LAST_INSERT_ID() as id_usuario;
END$$

CREATE PROCEDURE sp_verificar_email_disponible(IN p_correo VARCHAR(30), IN p_id_usuario INT)
BEGIN
    IF p_id_usuario IS NULL THEN
        SELECT id_usuario FROM usuarios WHERE correo = p_correo;
    ELSE
        SELECT id_usuario FROM usuarios WHERE correo = p_correo AND id_usuario != p_id_usuario;
    END IF;
END$$

CREATE PROCEDURE sp_crear_post(
    IN p_id_usuario INT, IN p_contenido TEXT, IN p_multimedia MEDIUMBLOB,
    IN p_fecha_creacion DATETIME, IN p_id_mundial INT, IN p_id_estado INT, IN p_id_categoria INT)
BEGIN
    INSERT INTO posts (id_usuario, contenido, multimedia, fecha_creacion, id_mundial, id_estado, id_categoria) 
    VALUES (p_id_usuario, p_contenido, p_multimedia, p_fecha_creacion, p_id_mundial, p_id_estado, p_id_categoria);
    SELECT LAST_INSERT_ID() as id_post;
END$$

CREATE PROCEDURE sp_verificar_propietario_post(IN p_id_post INT)
BEGIN
    SELECT id_usuario FROM posts WHERE id_post = p_id_post;
END$$

CREATE PROCEDURE sp_eliminar_post(IN p_id_post INT)
BEGIN
    DELETE FROM likes WHERE id_post = p_id_post;
    DELETE FROM comentarios WHERE id_post = p_id_post;
    DELETE FROM posts WHERE id_post = p_id_post;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_aprobar_post(IN p_id_post INT, IN p_fecha_aprobacion DATETIME)
BEGIN
    UPDATE posts SET id_estado = 1, fecha_aprobacion = p_fecha_aprobacion WHERE id_post = p_id_post;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_rechazar_post(IN p_id_post INT)
BEGIN
    DELETE FROM likes WHERE id_post = p_id_post;
    DELETE FROM comentarios WHERE id_post = p_id_post;
    DELETE FROM posts WHERE id_post = p_id_post;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_obtener_posts_pendientes()
BEGIN
    SELECT 
        pa.id_post,
        pa.id_usuario,
        pa.contenido,
        pa.multimedia,
        pa.fecha_creacion,
        pa.fecha_aprobacion,
        pa.id_mundial,
        pa.id_categoria,
        pa.id_estado,
        pa.mundial,
        pa.categoria,
        pa.estado
    FROM por_aprobar pa
    ORDER BY pa.fecha_creacion DESC;
END$$

CREATE PROCEDURE sp_obtener_posts_usuario(IN p_id_usuario INT)
BEGIN
    SELECT p.*, m.mundial, c.categoria, e.estado,
           fn_obtener_likes(p.id_post) as likes_count,
           fn_obtener_comentarios(p.id_post) as comments_count,
           (SELECT COUNT(*) FROM likes l WHERE l.id_post = p.id_post AND l.id_usuario = p_id_usuario) as user_liked
    FROM posts p 
    JOIN mundiales m ON p.id_mundial = m.id_mundial 
    JOIN categorias c ON p.id_categoria = c.id_categoria
    JOIN estados e ON p.id_estado = e.id_estado
    WHERE p.id_usuario = p_id_usuario AND e.estado = 'aprobado'
    ORDER BY p.fecha_creacion DESC;
END$$

CREATE PROCEDURE sp_obtener_posts_likes_usuario(IN p_id_usuario INT)
BEGIN
    SELECT p.*, m.mundial, c.categoria, l.fecha as fecha_like,
           fn_obtener_likes(p.id_post) as likes_count,
           fn_obtener_comentarios(p.id_post) as comments_count,
           (SELECT COUNT(*) FROM likes lk WHERE lk.id_post = p.id_post AND lk.id_usuario = p_id_usuario) as user_liked
    FROM likes l
    JOIN posts p ON l.id_post = p.id_post
    JOIN mundiales m ON p.id_mundial = m.id_mundial 
    JOIN categorias c ON p.id_categoria = c.id_categoria
    JOIN estados e ON p.id_estado = e.id_estado
    WHERE l.id_usuario = p_id_usuario AND e.estado = 'aprobado'
    ORDER BY l.fecha DESC;
END$$

CREATE PROCEDURE sp_verificar_like(IN p_id_usuario INT, IN p_id_post INT)
BEGIN
    SELECT id_like FROM likes WHERE id_usuario = p_id_usuario AND id_post = p_id_post;
END$$

CREATE PROCEDURE sp_crear_like(IN p_id_usuario INT, IN p_id_post INT, IN p_fecha DATETIME)
BEGIN
    INSERT INTO likes (id_usuario, id_post, fecha) VALUES (p_id_usuario, p_id_post, p_fecha);
    SELECT LAST_INSERT_ID() as id_like;
END$$

CREATE PROCEDURE sp_eliminar_like(IN p_id_usuario INT, IN p_id_post INT)
BEGIN
    DELETE FROM likes WHERE id_usuario = p_id_usuario AND id_post = p_id_post;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_crear_comentario(IN p_id_usuario INT, IN p_id_post INT, IN p_comentario VARCHAR(200), IN p_fecha DATETIME)
BEGIN
    INSERT INTO comentarios (id_usuario, id_post, comentario, fecha) 
    VALUES (p_id_usuario, p_id_post, p_comentario, p_fecha);
    SELECT LAST_INSERT_ID() as id_comentario;
END$$

CREATE PROCEDURE sp_verificar_propietario_comentario(IN p_id_comentario INT)
BEGIN
    SELECT id_usuario, id_post FROM comentarios WHERE id_comentario = p_id_comentario;
END$$

CREATE PROCEDURE sp_eliminar_comentario(IN p_id_comentario INT)
BEGIN
    DELETE FROM comentarios WHERE id_comentario = p_id_comentario;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_obtener_perfil_usuario(IN p_id_usuario INT)
BEGIN
    SELECT nombre, fecha_de_nacimiento, foto_perfil, correo FROM usuarios WHERE id_usuario = p_id_usuario;
END$$

CREATE PROCEDURE sp_obtener_foto_perfil(IN p_id_usuario INT)
BEGIN
    SELECT foto_perfil FROM usuarios WHERE id_usuario = p_id_usuario;
END$$

CREATE PROCEDURE sp_actualizar_perfil_con_foto(
    IN p_id_usuario INT, IN p_nombre VARCHAR(30), IN p_fecha_nacimiento DATE,
    IN p_correo VARCHAR(30), IN p_foto_perfil MEDIUMBLOB)
BEGIN
    UPDATE usuarios SET nombre = p_nombre, fecha_de_nacimiento = p_fecha_nacimiento, 
        correo = p_correo, foto_perfil = p_foto_perfil WHERE id_usuario = p_id_usuario;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_actualizar_perfil_sin_foto(
    IN p_id_usuario INT, IN p_nombre VARCHAR(30), IN p_fecha_nacimiento DATE, IN p_correo VARCHAR(30))
BEGIN
    UPDATE usuarios SET nombre = p_nombre, fecha_de_nacimiento = p_fecha_nacimiento, 
        correo = p_correo WHERE id_usuario = p_id_usuario;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_obtener_password(IN p_id_usuario INT)
BEGIN
    SELECT contrasena FROM usuarios WHERE id_usuario = p_id_usuario;
END$$

CREATE PROCEDURE sp_cambiar_password(IN p_id_usuario INT, IN p_contrasena VARCHAR(255))
BEGIN
    UPDATE usuarios SET contrasena = p_contrasena WHERE id_usuario = p_id_usuario;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE mundiales_selector()
BEGIN
    SELECT id_mundial, mundial FROM mundiales ORDER BY mundial;
END$$

CREATE PROCEDURE categorias_selector()
BEGIN
    SELECT id_categoria, categoria FROM categorias ORDER BY categoria;
END$$

DELIMITER ;

SELECT 'All stored procedures created successfully!' as status;

