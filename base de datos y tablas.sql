CREATE DATABASE bdm_mundial;

CREATE TABLE usuarios(
	id_usuario INT PRIMARY KEY AUTO_INCREMENT,
	nombre varchar(30) NOT NULL,
    apellido_m varchar(30) NOT NULL,
    apellido_p varchar(30) NOT NULL,
    fecha_de_nacimiento DATE NOT NULL,
    foto_perfil MEDIUMBLOB NOT NULL,
    genero varchar(30) NOT NULL,
    correo varchar(30) NOT NULL,
    contrasena varchar(30) NOT NULL,
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

CREATE TABLE roles(
	id_rol INT PRIMARY KEY AUTO_INCREMENT,
	rol varchar(30) NOT NULL
);

CREATE TABLE posts(
	id_post INT PRIMARY KEY AUTO_INCREMENT,
    contenido varchar(300),
    multimedia LONGBLOB,
    fecha_creacion DATE,
    fecha_aprobacion DATE,
    id_mundial INT NOT NULL,
    id_estado INT NOT NULL,
    id_categoria INT NOT NULL,
    FOREIGN KEY (id_mundial) 
    REFERENCES mundiales(id_mundial),
	FOREIGN KEY (id_estado) 
    REFERENCES estados(id_estado),
	FOREIGN KEY (id_categoria) 
    REFERENCES categorias(id_categoria) 
);

CREATE TABLE categorias(
	id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    categoria varchar(50)
);

CREATE TABLE estados(
	#aprobado o por aprobar.
	id_estado INT PRIMARY KEY AUTO_INCREMENT,
    estado varchar(50)
);

CREATE TABLE mundiales(
	id_mundial INT PRIMARY KEY AUTO_INCREMENT,
    mundial varchar(50)
);

CREATE TABLE likes (
	id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    fecha DATE,
    #el primary key es una mezcla de los 2 ID que hace que sea único y evita que 1 usuario pueda dar 2 likes.
    PRIMARY KEY (id_usuario, id_post),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_post) REFERENCES posts(id_post)
);

CREATE TABLE comentarios (
	id_comentario INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_post INT NOT NULL,
    comentario varchar(200),
    fecha DATE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_post) REFERENCES posts(id_post)
);

