DROP TABLE IF EXISTS permissao_usuario;
DROP TABLE IF EXISTS perfil_usuario;
DROP TABLE IF EXISTS permissao_perfil;
DROP TABLE IF EXISTS perfis;
DROP TABLE IF EXISTS permissoes;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS email_verifications;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS access_logs;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS configuracoes;
DROP TABLE IF EXISTS eleitores;
DROP TABLE IF EXISTS escola_segmentos;
DROP TABLE IF EXISTS segmentos;
DROP TABLE IF EXISTS candidatos;
DROP TABLE IF EXISTS escolas;
DROP TABLE IF EXISTS cedulas;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    status BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
);

CREATE TABLE email_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (email)
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE access_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    logout_time DATETIME NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE perfis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) UNIQUE NOT NULL,
    label VARCHAR(255) UNIQUE NOT NULL,
    descricao VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE permissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) UNIQUE NOT NULL,
    label VARCHAR(255) UNIQUE NOT NULL,
    descricao VARCHAR(255),
    agrupamento VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE perfil_usuario (
    usuario_id INT,
    perfil_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, perfil_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (perfil_id) REFERENCES perfis(id) ON DELETE CASCADE
);

CREATE TABLE permissao_perfil (
    perfil_id INT,
    permissao_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (perfil_id, permissao_id),
    FOREIGN KEY (perfil_id) REFERENCES perfis(id) ON DELETE CASCADE,
    FOREIGN KEY (permissao_id) REFERENCES permissoes(id) ON DELETE CASCADE
);

CREATE TABLE permissao_usuario (
    usuario_id INT,
    permissao_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, permissao_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (permissao_id) REFERENCES permissoes(id) ON DELETE CASCADE
);

CREATE TABLE configuracoes (
    chave VARCHAR(255) UNIQUE NOT NULL,
    valor TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE segmentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE escolas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255)
);

CREATE TABLE escola_segmentos (
    escola_id INT,
    segmento_id INT,
    PRIMARY KEY (escola_id, segmento_id),
    FOREIGN KEY (escola_id) REFERENCES escolas(id),
    FOREIGN KEY (segmento_id) REFERENCES segmentos(id)
);

CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(50) NOT NULL,
    chapa VARCHAR(50) NOT NULL,
    escola_id INT,
    FOREIGN KEY (escola_id) REFERENCES escolas(id)
);

CREATE TABLE eleitores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    segmento_id INT,
    documento VARCHAR(20) NOT NULL UNIQUE,
    registrado BOOLEAN DEFAULT FALSE,
    escola_id INT,
    FOREIGN KEY (escola_id) REFERENCES escolas(id),
    FOREIGN KEY (segmento_id) REFERENCES segmentos(id)
);

CREATE TABLE cedulas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_seguranca VARCHAR(100) NOT NULL UNIQUE,
    escola_id INT NOT NULL,
    eleitor_id INT NULL,  -- pode ser NULL até que um eleitor específico use esta cédula
    usado BOOLEAN DEFAULT FALSE,
    data_emissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (escola_id) REFERENCES escolas(id)
);

INSERT INTO usuarios (name, email, email_verified_at, password, status) VALUES 
('João da Silva', 'joao.silva@example.com', '2024-11-03 16:00:00', '$2y$10$PgJZ8QykkZmU7IMiOu4/Q.dKo6JPbyLl1mxJiRuHr7xNVEFYWdXZe', TRUE),
('Usuario 2 Teste', 'usuario1@example.com', '2024-11-03 16:00:00', '$2y$10$PgJZ8QykkZmU7IMiOu4/Q.dKo6JPbyLl1mxJiRuHr7xNVEFYWdXZe', TRUE),
('Usuario Fulano de Tal', 'usuario2@example.com', '2024-11-03 16:00:00', '$2y$10$PgJZ8QykkZmU7IMiOu4/Q.dKo6JPbyLl1mxJiRuHr7xNVEFYWdXZe', TRUE);

INSERT INTO perfis (nome, label, descricao) VALUES
('administrador', 'Administrador', 'Tem acesso total ao sistema'),
('editor', 'Editor', 'Pode editar conteúdos'),
('visualizador', 'Visualizador', 'Pode visualizar conteúdos');

INSERT INTO permissoes (nome, label, descricao, agrupamento) VALUES
('dashboard', 'Dashboard', 'Pode adicionar, editar e remover usuários', null),
('categorias', 'Categorias', 'Pode editar qualquer conteúdo', 'Cadastros'),
('formas_pagamento', 'Formas de Pagamento', 'Pode acessar relatórios do sistema', 'Cadastros'),
('clientes', 'Clientes', 'Pode acessar relatórios do sistema', 'Gerenciamentos'),
('produtos', 'Produtos', 'Pode acessar relatórios do sistema', 'Gerenciamentos'),
('kits', 'Kits', 'Pode acessar relatórios do sistema', 'Gerenciamentos'),
('vendas', 'Vendas', 'Pode acessar relatórios do sistema', 'Gerenciamentos'),
('pdv', 'PDV', 'Pode acessar relatórios do sistema', 'Gerenciamentos'),
('permissoes', 'Permissões', 'Pode acessar relatórios do sistema', 'Sistema'),
('perfis', 'Perfis', 'Pode acessar relatórios do sistema', 'Sistema'),
('usuarios', 'Usuários', 'Pode acessar relatórios do sistema', 'Sistema');

INSERT INTO perfil_usuario (usuario_id, perfil_id) VALUES
(1, 1), -- Usuário 1 tem perfil de Administrador
(2, 2), -- Usuário 2 tem perfil de Editor
(3, 3), -- Usuário 3 tem perfil de Visualizador
(3, 2); -- Usuário 3 também tem perfil de Editor

INSERT INTO permissao_perfil (perfil_id, permissao_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11);

INSERT INTO permissao_usuario (usuario_id, permissao_id) VALUES
(2, 3); -- Usuário 2 tem permissão específica para visualizar relatórios, além do seu perfil

INSERT INTO configuracoes (chave, valor) VALUES
('site_name', 'Meu Site'),
('maintenance_mode', '0'),
('items_per_page', '10'),
('contact_email', 'contato@meusite.com'),
('timezone', 'America/Sao_Paulo'),
('enable_signups', '1'),
('currency', 'BRL');

INSERT INTO segmentos (nome) VALUES 
('aluno'),
('professor'),
('servidor'),
('responsavel');

INSERT INTO escolas (nome, endereco) VALUES 
('Escola Municipal João XXIII', 'Rua das Flores, 123, Centro'), 
('Escola Estadual Pedro Álvares Cabral', 'Av. Central, 456, Bairro Novo'), 
('Escola Municipal Maria da Penha', 'Rua da Esperança, 789, Vila Velha'),
('Escola Estadual Tiradentes', 'Rua da Liberdade, 234, Nova Vila'),
('Escola Municipal Antonio Prado', 'Av. das Nações, 321, Centro Histórico');

INSERT INTO escola_segmentos (escola_id, segmento_id) VALUES
(1, 1), -- aluno
(1, 2), -- professor
(1, 3); -- servidor

INSERT INTO escola_segmentos (escola_id, segmento_id) VALUES
(2, 2), -- professor
(2, 3); -- servidor

INSERT INTO candidatos (nome, cargo, chapa, escola_id) VALUES 
-- Escola 1
('Ana Silva', 'Diretor', 'Chapa 1', 1),
('Helena Costa', 'Diretor Adjunto', 'Chapa 1', 1),
('Marcos Lima', 'Diretor', 'Chapa 2', 1),
('Carla Nunes', 'Diretor Adjunto', 'Chapa 2', 1),
-- Escola 2
('Carlos Souza', 'Diretor', 'Chapa 1', 2),
('Fernanda Luz', 'Diretor Adjunto', 'Chapa 1', 2),
('Rodrigo Mendes', 'Diretor', 'Chapa 2', 2),
('Bianca Oliveira', 'Diretor Adjunto', 'Chapa 2', 2),
-- Escola 3
('José Lima', 'Diretor', 'Chapa 1', 3),
('Patrícia Almeida', 'Diretor Adjunto', 'Chapa 1', 3),
('Lucas Moraes', 'Diretor', 'Chapa 2', 3),
('Juliana Souza', 'Diretor Adjunto', 'Chapa 2', 3),
-- Escola 4
('Rafael Costa', 'Diretor', 'Chapa 1', 4),
('Luciana Reis', 'Diretor Adjunto', 'Chapa 1', 4),
('André Santos', 'Diretor', 'Chapa 2', 4),
('Simone Duarte', 'Diretor Adjunto', 'Chapa 2', 4),
-- Escola 5
('Gabriel Pena', 'Diretor', 'Chapa 1', 5),
('Renata Lima', 'Diretor Adjunto', 'Chapa 1', 5),
('Felipe Araújo', 'Diretor', 'Chapa 2', 5),
('Carolina Martins', 'Diretor Adjunto', 'Chapa 2', 5);

-- Escola 1
INSERT INTO eleitores (nome, segmento_id, documento, registrado, escola_id) VALUES
('João Santos', 1, '12345678901', TRUE, 1),
('Maria Oliveira', 2, '98765432101', TRUE, 1),
('Pedro Fernandes', 3, '11122233301', TRUE, 1),
('Ana Paula', 1, '44455566601', TRUE, 1),
('Lucas Martins', 2, '77788899901', TRUE, 1);
-- Continue adicionando registros até atingir entre 30 a 50 registros para Escola 1

-- Escola 2
INSERT INTO eleitores (nome, segmento_id, documento, registrado, escola_id) VALUES
('Tiago Silva', 1, '12345678902', TRUE, 2),
('Vanessa Costa', 2, '98765432102', TRUE, 2),
('Bruno Lima', 3, '11122233302', TRUE, 2),
('Fernanda Dias', 1, '44455566602', TRUE, 2),
('Rafael Nunes', 2, '77788899902', TRUE, 2);
