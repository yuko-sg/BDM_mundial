-- Create stored procedure for user registration
USE bdm_mundial;

-- Drop the procedure if it exists
DROP PROCEDURE IF EXISTS sp_registrar_usuario;

-- Change delimiter to allow semicolons inside the procedure
DELIMITER $$

CREATE PROCEDURE sp_registrar_usuario(
    IN p_nombre VARCHAR(30),
    IN p_fecha_nacimiento DATE,
    IN p_foto_perfil MEDIUMBLOB,
    IN p_correo VARCHAR(30),
    IN p_contrasena VARCHAR(255),
    IN p_id_rol INT
)
BEGIN
    -- Insert the new user into the usuarios table
    INSERT INTO usuarios (nombre, fecha_de_nacimiento, foto_perfil, correo, contrasena, id_rol) 
    VALUES (p_nombre, p_fecha_nacimiento, p_foto_perfil, p_correo, p_contrasena, p_id_rol);
    
    -- Return the ID of the newly created user
    SELECT LAST_INSERT_ID() as id_usuario;
END$$

-- Reset delimiter back to semicolon
DELIMITER ;

-- Test that the procedure was created successfully
SELECT 'Stored procedure sp_registrar_usuario created successfully!' as status;

