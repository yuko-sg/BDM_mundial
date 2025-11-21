use bdm_mundial;

DROP PROCEDURE IF EXISTS sp_login;
DROP PROCEDURE IF EXISTS sp_registrar_usuario;
DROP PROCEDURE IF EXISTS sp_aprobar_post;
DROP PROCEDURE IF EXISTS sp_rechazar_post;
DROP PROCEDURE IF EXISTS mundiales_selector;
DROP PROCEDURE IF EXISTS categorias_selector;

DELIMITER $$

CREATE PROCEDURE sp_login(
    IN p_correo VARCHAR(30)
)
BEGIN
    SELECT id_usuario, nombre, correo, contrasena, id_rol 
    FROM usuarios 
    WHERE correo = p_correo;
END$$

CREATE PROCEDURE sp_registrar_usuario(
    IN p_nombre VARCHAR(30),
    IN p_fecha_nacimiento DATE,
    IN p_foto_perfil MEDIUMBLOB,
    IN p_correo VARCHAR(30),
    IN p_contrasena VARCHAR(255),
    IN p_id_rol INT
)
BEGIN

    INSERT INTO usuarios (nombre, fecha_de_nacimiento, foto_perfil, correo, contrasena, id_rol) 
    VALUES (p_nombre, p_fecha_nacimiento, p_foto_perfil, p_correo, p_contrasena, p_id_rol);
    SELECT LAST_INSERT_ID() as id_usuario;

END$$

CREATE PROCEDURE sp_aprobar_post(
    IN p_id_post INT,
    IN p_fecha_aprobacion DATETIME
)
BEGIN
    UPDATE posts 
    SET id_estado = 1, 
        fecha_aprobacion = p_fecha_aprobacion 
    WHERE id_post = p_id_post;
    SELECT ROW_COUNT() as affected_rows;
END$$

CREATE PROCEDURE sp_rechazar_post(
    IN p_id_post INT
)
BEGIN
    DELETE FROM likes WHERE id_post = p_id_post;
    
    DELETE FROM comentarios WHERE id_post = p_id_post;
    
    DELETE FROM posts WHERE id_post = p_id_post;
    
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


