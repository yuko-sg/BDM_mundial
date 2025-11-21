-- Sample data for testing the dashboard
USE bdm_mundial;

-- Insert roles (if not already inserted)
INSERT INTO roles (rol) VALUES ('Administrador'), ('Usuario');

-- Insert estados
INSERT INTO estados (estado) VALUES ('aprobado'), ('por aprobar');

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

-- Insert sample posts (aprobados)
INSERT INTO posts (contenido, multimedia, fecha_creacion, fecha_aprobacion, id_mundial, id_estado, id_categoria) VALUES
('¡El gol de Maradona contra Inglaterra en 1986 es considerado el mejor gol de la historia de los mundiales! La jugada donde eludió a más de 5 jugadores quedó marcada para siempre.', NULL, '2024-01-15', '2024-01-15', 13, 1, 1),
('Diego Forlán fue el goleador del Mundial 2010. Uruguay llegó a semifinales con grandes actuaciones del delantero.', NULL, '2024-01-20', '2024-01-20', 19, 1, 1),
('Brasil es el único país que ha participado en todos los mundiales de fútbol. ¡23 participaciones consecutivas!', NULL, '2024-02-01', '2024-02-01', 1, 1, 5),
('El Mundial 2026 será histórico: se jugarán 48 selecciones por primera vez en la historia. Se disputará en Estados Unidos, Canadá y México.', NULL, '2024-02-10', '2024-02-10', 23, 1, 3),
('Pelé es el único jugador en ganar 3 Copas del Mundo (1958, 1962, 1970). Una hazaña que difícilmente se repetirá.', NULL, '2024-02-15', '2024-02-15', 6, 1, 4),
('¿Sabías que en el Mundial de 1930 no hubo partidos de clasificación? Solo 13 equipos participaron.', NULL, '2024-02-20', '2024-02-20', 1, 1, 6),
('La final del Mundial 2022 entre Argentina y Francia es considerada una de las mejores finales de la historia. Messi finalmente consiguió su ansiada Copa del Mundo.', NULL, '2024-03-01', '2024-03-01', 22, 1, 3),
('Roger Milla, de Camerún, se convirtió en el jugador más veterano en marcar un gol en un Mundial con 42 años en 1990.', NULL, '2024-03-05', '2024-03-05', 14, 1, 6);

-- Insert some pending posts
INSERT INTO posts (contenido, multimedia, fecha_creacion, fecha_aprobacion, id_mundial, id_estado, id_categoria) VALUES
('Post pendiente de aprobación sobre estadísticas del Mundial 2018.', NULL, '2024-03-10', NULL, 21, 2, 5);


