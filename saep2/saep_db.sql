CREATE DATABASE IF NOT EXISTS saep_db;
USE saep_db;

-- Tabela de Usuários (Funcionários da Clínica)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Tabela de Tutores (CPF e Senha criptografados)
CREATE TABLE tutores (
    id_tutor INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- Tabela de Pets (Associada a um Tutor)
CREATE TABLE pets (
    id_pet INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(50) NOT NULL,
    idade INT NOT NULL,
    id_tutor INT NOT NULL,
    FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE
);

-- Tabela de Agendamentos
CREATE TABLE agendamentos (
    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
    data_agendamento DATE NOT NULL,
    horario TIME NOT NULL,
    procedimento VARCHAR(150) NOT NULL,
    id_tutor INT NOT NULL,
    id_pet INT NOT NULL,
    FOREIGN KEY (id_tutor) REFERENCES tutores(id_tutor) ON DELETE CASCADE,
    FOREIGN KEY (id_pet) REFERENCES pets(id_pet) ON DELETE CASCADE
);

-- POPULAÇÃO DAS TABELAS (3 registros em cada)

-- 1. Usuários (Login: admin / Senha: 123456)
INSERT INTO usuarios (nome, login, senha) VALUES 
('Dr. Carlos Silva', 'admin', '123456'),
('Ana Recepcionista', 'ana', '123456'),
('Marcos Veterinário', 'marcos', '123456');

-- 2. Tutores (CPF e Senha criptografados em Base64)
-- CPFs originais: 111.111.111-11, 222.222.222-22, 333.333.333-33 | Senhas: senha1, senha2, senha3
INSERT INTO tutores (nome, cpf, telefone, email, senha) VALUES 
('João Pereira', 'MTExLjExMS4xMTEtMTE=', '(11) 98888-1111', 'joao@email.com', 'c2VuaGEx'),
('Maria Oliveira', 'MjIyLjIyMi4yMjItMjI=', '(11) 98888-2222', 'maria@email.com', 'c2VuaGEy'),
('Pedro Santos', 'MzMzLjMzMy4zMzMtMzM=', '(11) 98888-3333', 'pedro@email.com', 'c2VuaGEz');

-- 3. Pets
INSERT INTO pets (nome, especie, raca, idade, id_tutor) VALUES 
('Rex', 'Cachorro', 'Labrador', 3, 1),
('Mimi', 'Gato', 'Siamês', 2, 2),
('Thor', 'Cachorro', 'Bulldog', 4, 3);

-- 4. Agendamentos
INSERT INTO agendamentos (data_agendamento, horario, procedimento, id_tutor, id_pet) VALUES 
('2026-11-15', '09:00:00', 'Consulta de Rotina', 1, 1),
('2026-11-13', '14:30:00', 'Vacinação V10', 2, 2),
('2026-11-20', '10:00:00', 'Exame de Sangue', 3, 3);