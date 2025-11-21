-- Stored procedures for approving and rejecting posts
USE bdm_mundial;

-- Drop procedures if they exist
DROP PROCEDURE IF EXISTS sp_aprobar_post;
DROP PROCEDURE IF EXISTS sp_rechazar_post;

-- Change delimiter to allow semicolons inside procedures
DELIMITER $$

-- ========================================
-- PROCEDURE: sp_aprobar_post
-- ========================================
-- This procedure approves a pending post
-- It sets the estado to 'aprobado' (id=1) and records the approval date

CREATE PROCEDURE sp_aprobar_post(
    IN p_id_post INT,
    IN p_fecha_aprobacion DATETIME
)
BEGIN
    -- Update the post status to approved (id_estado = 1)
    -- and set the approval date
    UPDATE posts 
    SET id_estado = 1, 
        fecha_aprobacion = p_fecha_aprobacion 
    WHERE id_post = p_id_post;
    
    -- Return the number of rows affected (should be 1 if successful)
    SELECT ROW_COUNT() as affected_rows;
END$$

-- ========================================
-- PROCEDURE: sp_rechazar_post
-- ========================================
-- This procedure rejects (deletes) a post
-- It first deletes related records (likes, comments) to avoid foreign key errors

CREATE PROCEDURE sp_rechazar_post(
    IN p_id_post INT
)
BEGIN
    -- Delete related likes first (foreign key constraint)
    DELETE FROM likes WHERE id_post = p_id_post;
    
    -- Delete related comments (foreign key constraint)
    DELETE FROM comentarios WHERE id_post = p_id_post;
    
    -- Now delete the post itself
    DELETE FROM posts WHERE id_post = p_id_post;
    
    -- Return the number of rows affected (should be 1 if post was deleted)
    SELECT ROW_COUNT() as affected_rows;
END$$

-- Reset delimiter back to semicolon
DELIMITER ;

-- Test that procedures were created successfully
SELECT 'Stored procedures created successfully!' as status;
SELECT 'sp_aprobar_post - Approves a post and sets approval date' as procedure_1;
SELECT 'sp_rechazar_post - Rejects (deletes) a post and related records' as procedure_2;

