-- Criação das tabelas principais do sistema Morya (MySQL)

CREATE TABLE niveis_acesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

-- Inserir níveis de acesso padrão
INSERT INTO niveis_acesso (nome, descricao) VALUES 
('administrador', 'Acesso total ao sistema'),
('gestor', 'Acesso total, exceto deleção de registros'),
('operador', 'Acesso apenas para visualização e disparo de orçamentos');

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(30),
    cargo VARCHAR(50),
    foto VARCHAR(255),
    nivel_acesso_id INT NOT NULL,
    smtp_host VARCHAR(100),
    smtp_port INT,
    smtp_user VARCHAR(100),
    smtp_pass VARCHAR(100),
    smtp_secure VARCHAR(10),
    FOREIGN KEY (nivel_acesso_id) REFERENCES niveis_acesso(id)
);

CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    responsavel VARCHAR(100),
    cidade VARCHAR(50),
    estado VARCHAR(2)
);

CREATE TABLE certidoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fornecedor_id INT NOT NULL,
    cnpj VARCHAR(20),
    certidao_federal VARCHAR(255),
    certidao_estadual VARCHAR(255),
    certidao_municipal VARCHAR(255),
    certidao_trabalhista VARCHAR(255),
    certidao_fgts VARCHAR(255),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE CASCADE
);

CREATE TABLE historico_envios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255),
    corpo TEXT,
    destinatarios TEXT,
    status VARCHAR(50),
    materias TEXT,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
); 