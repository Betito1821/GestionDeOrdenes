-- Crear la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS gestion_ordenes;

-- Usar la base de datos
USE gestion_ordenes;

-- Crear la tabla `usuarios`
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('planeacion', 'diseno', 'maquetado', 'retrabajo', 'impresion', 'almacen', 'embarques') NOT NULL
);

-- Insertar datos de ejemplo
INSERT INTO usuarios (username, password, role) VALUES
('planeacion', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5', 'planeacion'),
('diseno', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5k5', 'diseno'),
('maquetado', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5k5', 'maquetado'),
('retrabajo', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5k5', 'retrabajo'),
('impresion', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5k5', 'impresion'),
('almacen', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5k5', 'almacen'),
('embarques', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe.FX5Ge1Y5OZ5k5k5k5k5k5k5k5k5k5k5', 'embarques');
