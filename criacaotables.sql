-- =========================================================================
-- SCRIPT FINAL PETHEALTH & CARE 
-- =========================================================================

-- 1. TABELA: USUARIOS (Com cargos reais de mercado)
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('equipe', 'cliente')) DEFAULT 'cliente',
    cargo VARCHAR(30) NOT NULL CHECK (cargo IN ('Veterinário', 'Tosador', 'Recepcionista', 'Cliente')) DEFAULT 'Cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABELA: PETS
CREATE TABLE pets (
    id SERIAL PRIMARY KEY,
    cliente_id INT,
    nome VARCHAR(50) NOT NULL,
    especie VARCHAR(50) NOT NULL,
    raca VARCHAR(50) DEFAULT 'SRD',
    data_nascimento DATE,
    comportamento VARCHAR(50) DEFAULT 'Normal',
    foto VARCHAR(255) DEFAULT 'default_pet.png',
    CONSTRAINT fk_cliente FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 3. TABELA: SERVICOS
CREATE TABLE servicos (
    id SERIAL PRIMARY KEY,
    nome_servico VARCHAR(100) NOT NULL,
    categoria VARCHAR(20) NOT NULL CHECK (categoria IN ('estetica', 'clinica')),
    preco NUMERIC(10,2) NOT NULL
);

-- 4. TABELA: AGENDAMENTOS (Vinculado ao pet, ao serviço e ao funcionário que vai executar)
CREATE TABLE agendamentos (
    id SERIAL PRIMARY KEY,
    pet_id INT,
    servico_id INT,
    funcionario_id INT, -- Quem vai atender (Veterinário ou Tosador)
    data_hora TIMESTAMP NOT NULL,
    status VARCHAR(30) NOT NULL CHECK (status IN ('Agendado', 'Em Atendimento', 'Pronto para Retirada', 'Finalizado')) DEFAULT 'Agendado',
    CONSTRAINT fk_pet FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    CONSTRAINT fk_servico FOREIGN KEY (servico_id) REFERENCES servicos(id),
    CONSTRAINT fk_funcionario FOREIGN KEY (funcionario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- 5. TABELA: MURAL_AVISOS
CREATE TABLE mural_avisos (
    id SERIAL PRIMARY KEY,
    usuario_id INT,
    titulo VARCHAR(100) NOT NULL,
    conteudo TEXT NOT NULL,
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_mural FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- 6. TABELA: LOG_ATIVIDADES 
CREATE TABLE log_atividades (
    id SERIAL PRIMARY KEY,
    usuario_id INT, -- Quem fez a ação
    acao TEXT NOT NULL, -- Descrição da ação (ex: "Alterou status do agendamento #4 para Pronto")
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_log FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);


-------------------------------------------

-- Inserindo a equipe e o cliente (Senha padrão: 123456)
INSERT INTO usuarios (nome, email, senha, telefone, tipo, cargo) VALUES
('Dr. Roberto Garcia', 'roberto@pethealth.com', '$2y$10$mC/7FhYm6lH3zDk8bBvGieyPZ8.GfZFpGjO27vW14lD3kUeLpZaSy', '11999999991', 'equipe', 'Veterinário'),
('Camila Silva Tosadora', 'camila@pethealth.com', '$2y$10$mC/7FhYm6lH3zDk8bBvGieyPZ8.GfZFpGjO27vW14lD3kUeLpZaSy', '11999999992', 'equipe', 'Tosador'),
('Carlos Tutor', 'cliente@gmail.com', '$2y$10$mC/7FhYm6lH3zDk8bBvGieyPZ8.GfZFpGjO27vW14lD3kUeLpZaSy', '11988888888', 'cliente', 'Cliente');

-- Inserindo os pets do Carlos (ID 3)
INSERT INTO pets (cliente_id, nome, especie, raca, data_nascimento, comportamento) VALUES
(3, 'Thor', 'Cão', 'Golden Retriever', '2022-05-10', 'Amigável'),
(3, 'Snape', 'Cobra', 'Corn Snake', '2024-01-20', 'Agressivo');

-- Inserindo Serviços
INSERT INTO servicos (nome_servico, categoria, preco) VALUES
('Banho Geral Completo', 'estetica', 60.00),
('Atendimento Clínico Exóticos', 'clinica', 180.00);

-- Agendando o Thor para Banho com a Camila (ID 2)
INSERT INTO agendamentos (pet_id, servico_id, funcionario_id, data_hora, status) VALUES
(1, 1, 2, CURRENT_TIMESTAMP, 'Em Atendimento');

-- Agendando a Cobra Snape com o Dr. Roberto (ID 1)
INSERT INTO agendamentos (pet_id, servico_id, funcionario_id, data_hora, status) VALUES
(2, 2, 1, CURRENT_TIMESTAMP, 'Agendado');

-- Histórico de Log de teste
INSERT INTO log_atividades (usuario_id, acao) VALUES
(2, 'Camila iniciou o atendimento de Banho Geral Completo para o pet Thor.');



SELECT * FROM log_atividades;
