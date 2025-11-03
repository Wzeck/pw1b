-- ===============================
-- CRIAÇÃO DO BANCO DE DADOS
-- ===============================
CREATE DATABASE IF NOT EXISTS agenda_etec;
USE agenda_etec;

-- ===============================
-- TABELA DE USUÁRIOS
-- ===============================
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================
-- TABELA DE SALAS
-- ===============================
CREATE TABLE IF NOT EXISTS salas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_sala VARCHAR(255) NOT NULL,
  capacidade INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================
-- TABELA DE RESERVAS
-- ===============================
CREATE TABLE IF NOT EXISTS reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_sala INT NOT NULL,
  id_usuario INT NOT NULL,
  data_reserva DATE NOT NULL,
  hora_inicio TIME NOT NULL,
  hora_fim TIME NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'confirmada',
  KEY id_sala (id_sala),
  KEY id_usuario (id_usuario),
  CONSTRAINT reservas_ibfk_1 FOREIGN KEY (id_sala) REFERENCES salas (id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT reservas_ibfk_2 FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================
-- INSERINDO USUÁRIOS DE TESTE
-- ===============================
INSERT INTO usuarios (nome, email, senha, tipo) VALUES
('Prof. Ana', 'ana.prof@etec.sp.gov.br',
 '$2y$10$D0qJXKcW4P3mNHvKM2gK4OV7t3Q/0oJmx8vUje8Uq2bZWjsFZkN1W', 'professor'),
('Aluno João', 'joao.aluno@etec.sp.gov.br',
 '$2y$10$D0qJXKcW4P3mNHvKM2gK4OV7t3Q/0oJmx8vUje8Uq2bZWjsFZkN1W', 'aluno');

-- ===============================
-- INSERINDO SALAS DE EXEMPLO
-- ===============================
INSERT INTO salas (nome_sala, capacidade) VALUES
('Laboratório de Informática 1', 32),
('Laboratório de Informática 2', 36),
('Oficina de Eletrônica', 20),
('Auditório', 120);

-- ===============================
-- INSERINDO RESERVAS DE EXEMPLO
-- ===============================
INSERT INTO reservas (id_sala, id_usuario, data_reserva, hora_inicio, hora_fim, status) VALUES
(4, 1, CURDATE(), '19:00:00', '20:30:00', 'confirmada'),
(1, 2, CURDATE(), '10:00:00', '11:30:00', 'confirmada');
