-- triggers.sql
-- Triggers for BDM Mundial Database

USE bdm_mundial;

-- Drop trigger if exists
DROP TRIGGER IF EXISTS tr_prevenir_auto_like;

DELIMITER $$

-- Trigger: Prevent users from liking their own posts
CREATE TRIGGER tr_prevenir_auto_like
BEFORE INSERT ON likes
FOR EACH ROW
BEGIN
    DECLARE post_owner INT;
    
    -- Get the owner of the post
    SELECT id_usuario INTO post_owner 
    FROM posts 
    WHERE id_post = NEW.id_post;
    
    -- Check if the user trying to like is the post owner
    IF post_owner = NEW.id_usuario THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'No puedes dar like a tu propio post';
    END IF;
END$$

DELIMITER ;

