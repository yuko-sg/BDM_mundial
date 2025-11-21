-- Sample data for testing the dashboard
USE bdm_mundial;

-- Insert roles (if not already inserted)
INSERT INTO roles (rol) VALUES ('Administrador'), ('Usuario');

-- Insert estados
INSERT INTO estados (estado) VALUES ('aprobado'), ('por aprobar');

-- Insert default users for testing
-- IMPORTANT: Run generate_passwords.php first to get the real password hashes!
-- Then replace these INSERT statements with the ones generated

-- Default Admin User
-- Email: admin@mundial.com
-- Password: admin123
INSERT INTO usuarios (nombre, fecha_de_nacimiento, foto_perfil, correo, contrasena, id_rol) VALUES
('Admin Test', '1990-01-01', '', 'admin@mundial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Default Regular User  
-- Email: user@mundial.com
-- Password: user123
INSERT INTO usuarios (nombre, fecha_de_nacimiento, foto_perfil, correo, contrasena, id_rol) VALUES
('Usuario Test', '1995-05-15', '', 'user@mundial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- Insert mundiales
INSERT INTO mundiales (mundial) VALUES 
('Mundial 1930 - Uruguay'),
('Mundial 1934 - Italia'),
('Mundial 1938 - Francia'),
('Mundial 1950 - Brasil'),
('Mundial 1954 - Suiza'),
('Mundial 1958 - Suecia'),
('Mundial 1962 - Chile'),
('Mundial 1966 - Inglaterra'),
('Mundial 1970 - México'),
('Mundial 1974 - Alemania'),
('Mundial 1978 - Argentina'),
('Mundial 1982 - España'),
('Mundial 1986 - México'),
('Mundial 1990 - Italia'),
('Mundial 1994 - Estados Unidos'),
('Mundial 1998 - Francia'),
('Mundial 2002 - Corea/Japón'),
('Mundial 2006 - Alemania'),
('Mundial 2010 - Sudáfrica'),
('Mundial 2014 - Brasil'),
('Mundial 2018 - Rusia'),
('Mundial 2022 - Qatar'),
('Mundial 2026 - USA/CAN/MEX');

-- Insert categorias
INSERT INTO categorias (categoria) VALUES 
('Goles'),
('Jugadas'),
('Noticias'),
('Historia'),
('Estadísticas'),
('Curiosidades');

-- Insert sample posts (aprobados) - Using id_usuario = 2 (Usuario Test)
INSERT INTO posts (id_usuario, contenido, multimedia, fecha_creacion, fecha_aprobacion, id_mundial, id_estado, id_categoria) VALUES
(2, '¡El gol de Maradona contra Inglaterra en 1986 es considerado el mejor gol de la historia de los mundiales! La jugada donde eludió a más de 5 jugadores quedó marcada para siempre.', NULL, '2024-01-15', '2024-01-15', 13, 1, 1),
(2, 'Diego Forlán fue el goleador del Mundial 2010. Uruguay llegó a semifinales con grandes actuaciones del delantero.', NULL, '2024-01-20', '2024-01-20', 19, 1, 1),
(2, 'Brasil es el único país que ha participado en todos los mundiales de fútbol. ¡23 participaciones consecutivas!', NULL, '2024-02-01', '2024-02-01', 1, 1, 5),
(2, 'El Mundial 2026 será histórico: se jugarán 48 selecciones por primera vez en la historia. Se disputará en Estados Unidos, Canadá y México.', NULL, '2024-02-10', '2024-02-10', 23, 1, 3),
(2, 'Pelé es el único jugador en ganar 3 Copas del Mundo (1958, 1962, 1970). Una hazaña que difícilmente se repetirá.', NULL, '2024-02-15', '2024-02-15', 6, 1, 4),
(2, '¿Sabías que en el Mundial de 1930 no hubo partidos de clasificación? Solo 13 equipos participaron.', NULL, '2024-02-20', '2024-02-20', 1, 1, 6),
(2, 'La final del Mundial 2022 entre Argentina y Francia es considerada una de las mejores finales de la historia. Messi finalmente consiguió su ansiada Copa del Mundo.', NULL, '2024-03-01', '2024-03-01', 22, 1, 3),
(2, 'Roger Milla, de Camerún, se convirtió en el jugador más veterano en marcar un gol en un Mundial con 42 años en 1990.', NULL, '2024-03-05', '2024-03-05', 14, 1, 6);

-- Insert some pending posts - Using id_usuario = 2
INSERT INTO posts (id_usuario, contenido, multimedia, fecha_creacion, fecha_aprobacion, id_mundial, id_estado, id_categoria) VALUES
(2, 'Post pendiente de aprobación sobre estadísticas del Mundial 2018.', NULL, '2024-03-10', NULL, 21, 2, 5);

-- Insert some likes (user 1 likes posts from user 2)
INSERT INTO likes (id_usuario, id_post, fecha) VALUES
(1, 1, '2024-01-16 10:30:00'),
(1, 3, '2024-02-02 14:20:00'),
(1, 7, '2024-03-02 09:15:00');

-- Insert some comments
INSERT INTO comentarios (id_usuario, id_post, comentario, fecha) VALUES
(1, 1, '¡La mano de Dios también fue en ese partido! Inolvidable.', '2024-01-16 11:00:00'),
(1, 7, 'Qué partidazo! Mejor final imposible.', '2024-03-02 10:00:00'),
(2, 1, 'Gracias! Es mi gol favorito de todos los tiempos.', '2024-01-16 12:00:00');


