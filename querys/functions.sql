USE bdm_mundial;

-- Drop functions if they exist
DROP FUNCTION IF EXISTS fn_obtener_likes;
DROP FUNCTION IF EXISTS fn_obtener_comentarios;

DELIMITER $$

-- Function to get the number of likes for a post
CREATE FUNCTION fn_obtener_likes(p_id_post INT) 
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_likes INT;
    SELECT COUNT(*) INTO v_likes FROM likes WHERE id_post = p_id_post;
    RETURN v_likes;
END$$

-- Function to get the number of comments for a post
CREATE FUNCTION fn_obtener_comentarios(p_id_post INT) 
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_comentarios INT;
    SELECT COUNT(*) INTO v_comentarios FROM comentarios WHERE id_post = p_id_post;
    RETURN v_comentarios;
END$$

DELIMITER ;

-- Test the functions
SELECT 'Functions created successfully!' as status;
