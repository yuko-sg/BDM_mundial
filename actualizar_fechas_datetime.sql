-- Update posts table to use DATETIME instead of DATE for time support
-- Run this SQL in phpMyAdmin

-- Change fecha_creacion to DATETIME with default current timestamp
ALTER TABLE posts 
MODIFY COLUMN fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Change fecha_aprobacion to DATETIME (nullable)
ALTER TABLE posts 
MODIFY COLUMN fecha_aprobacion DATETIME DEFAULT NULL;

-- Update likes table to include time
ALTER TABLE likes 
MODIFY COLUMN fecha DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Update comentarios table to include time
ALTER TABLE comentarios 
MODIFY COLUMN fecha DATETIME DEFAULT CURRENT_TIMESTAMP;

-- If you have existing data with DATE format (00:00:00 times), this is normal
-- New posts, likes, and comments will now include the actual time they were created/approved

