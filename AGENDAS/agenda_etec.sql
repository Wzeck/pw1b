-- ===============================
-- CRIAÇÃO DO BANCO DE DADOS
-- ===============================
CREATE DATABASE IF NOT EXISTS agenda_etec; -- Cria o banco "agenda_etec" se ainda não existir
USE agenda_etec;                           -- Define esse banco como o ativo

-- ===============================
-- TABELA DE USUÁRIOS
-- ===============================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,     -- Identificador único (chave primária)
  `nome` varchar(255) NOT NULL,             -- Nome do usuário
  `email` varchar(255) NOT NULL,            -- Email do usuário (não pode repetir)
  `senha` varchar(255) NOT NULL,            -- Senha criptografada (bcrypt/hash)
  `tipo` varchar(50) NOT NULL,              -- Tipo de usuário (ex: professor, aluno)
  PRIMARY KEY (`id`),                       -- Define "id" como chave primária
  UNIQUE KEY `email` (`email`)              -- Garante que não haja dois usuários com o mesmo email
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- TABELA DE SALAS
-- ===============================
CREATE TABLE IF NOT EXISTS `salas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,     -- Identificador único da sala
  `nome_sala` varchar(255) NOT NULL,        -- Nome da sala (ex: Laboratório, Auditório)
  `capacidade` int(11) DEFAULT NULL,        -- Capacidade de pessoas na sala
  PRIMARY KEY (`id`)                        -- Define "id" como chave primária
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- TABELA DE RESERVAS
-- ===============================
CREATE TABLE IF NOT EXISTS `reservas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,     -- Identificador único da reserva
  `id_sala` int(11) NOT NULL,               -- Relaciona com a tabela "salas"
  `id_usuario` int(11) NOT NULL,            -- Relaciona com a tabela "usuarios"
  `data_reserva` date NOT NULL,             -- Dia da reserva
  `hora_inicio` time NOT NULL,              -- Hora de início
  `hora_fim` time NOT NULL,                 -- Hora de fim
  `status` varchar(50) NOT NULL DEFAULT 'confirmada', -- Status (padrão = confirmada)
  PRIMARY KEY (`id`),                       -- Define "id" como chave primária
  KEY `id_sala` (`id_sala`),                -- Cria índice para melhorar busca por sala
  KEY `id_usuario` (`id_usuario`),          -- Cria índice para melhorar busca por usuário
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`),      -- Chave estrangeira -> salas.id
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) -- Chave estrangeira -> usuarios.id
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- INSERINDO USUÁRIOS DE TESTE
-- ===============================
-- A senha "12345" foi criptografada com bcrypt
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
(4, 1, CURDATE(), '19:00:00', '20:30:00', 'confirmada'),  -- Auditório reservado pela Prof. Ana
(1, 2, CURDATE(), '10:00:00', '11:30:00', 'confirmada'); -- Lab de Informática 1 reservado pelo Aluno João
