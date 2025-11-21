use bdm_mundial;

CREATE PROCEDURE sp_rechazar_post(IN p_id_post INT)
BEGIN
    -- Delete related records first to avoid foreign key constraints
    DELETE FROM likes WHERE id_post = p_id_post;
    DELETE FROM comentarios WHERE id_post = p_id_post;
    DELETE FROM posts WHERE id_post = p_id_post;
    SELECT ROW_COUNT() as affected_rows;
END$$