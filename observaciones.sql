-- =========================================================
-- INSTITUTO FRANKLIN DELANO ROOSEVELT
-- Estructura de la base de datos usada por las Sugerencias
-- (Coincide con la tabla creada en MySQL Workbench)
-- =========================================================

CREATE DATABASE IF NOT EXISTS proyecto_fdr
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE proyecto_fdr;

-- 1. Tabla de Carreras
CREATE TABLE IF NOT EXISTS carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre_carrera VARCHAR(100) NOT NULL
);

-- 2. Tabla de Grados (Ligada a Carreras)
CREATE TABLE IF NOT EXISTS grados (
    id_grado INT AUTO_INCREMENT PRIMARY KEY,
    nombre_grado VARCHAR(50) NOT NULL,
    id_carrera INT NOT NULL,
    FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera) ON DELETE CASCADE
);

-- 3. Tabla de Secciones (Independiente)
CREATE TABLE IF NOT EXISTS secciones (
    id_seccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre_seccion VARCHAR(10) NOT NULL
);

-- 4. Tabla de Sugerencias
CREATE TABLE IF NOT EXISTS sugerencias (
    id_sugerencia INT AUTO_INCREMENT PRIMARY KEY,
    nombre_remitente VARCHAR(100) NOT NULL,
    id_carrera INT NOT NULL,
    id_grado INT NOT NULL,
    id_seccion INT NOT NULL,
    texto_sugerencia TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_carrera) REFERENCES carreras(id_carrera),
    FOREIGN KEY (id_grado) REFERENCES grados(id_grado),
    FOREIGN KEY (id_seccion) REFERENCES secciones(id_seccion)
);

-- ========================================================
-- DATOS DE CONFIGURACIÓN (carreras, grados y secciones)
-- ========================================================

INSERT INTO carreras (id_carrera, nombre_carrera) VALUES
(1, 'Bachillerato en Informática'),
(2, 'Bachillerato en Robótica'),
(3, 'Administración Hotelera'),
(4, 'Bachillerato en Ciencias y Humanidades'),
(5, 'Bach. Marítimo portuario'),
(6, 'Administración de empresas');

INSERT INTO grados (nombre_grado, id_carrera) VALUES
('10 Grado', 1), ('11 Grado', 1), ('12 Grado', 1),
('10 Grado', 2), ('11 Grado', 2), ('12 Grado', 2),
('10 Grado', 3), ('11 Grado', 3), ('12 Grado', 3),
('10 Grado', 4), ('11 Grado', 4),
('10 Grado', 5), ('11 Grado', 5), ('12 Grado', 5),
('10 Grado', 6), ('11 Grado', 6), ('12 Grado', 6);

INSERT INTO secciones (nombre_seccion) VALUES
('A'), ('B'), ('C');

-- Consulta de prueba para ver las sugerencias guardadas
-- SELECT * FROM sugerencias ORDER BY fecha_creacion DESC;