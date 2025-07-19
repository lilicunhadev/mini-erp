-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS mini_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_erp;

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de variações de produto (cores, tamanhos, etc.)
CREATE TABLE produto_variacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    valor VARCHAR(50) NOT NULL,
    preco_adicional DECIMAL(10,2) DEFAULT 0.00,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Tabela de estoque
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    variacao_id INT NULL, -- NULL = estoque do produto base
    quantidade INT NOT NULL DEFAULT 0,
    quantidade_minima INT DEFAULT 5,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_estoque (produto_id, variacao_id)
);

-- Tabela de cupons
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    tipo ENUM('percentual', 'valor_fixo') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    valor_minimo DECIMAL(10,2) DEFAULT 0.00,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    desconto DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    cupom_id INT NULL,
    status ENUM('pendente', 'confirmado', 'enviado', 'entregue', 'cancelado') DEFAULT 'pendente',
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_cep VARCHAR(10) NOT NULL,
    cliente_endereco VARCHAR(255) NOT NULL,
    cliente_numero VARCHAR(20) NOT NULL,
    cliente_complemento VARCHAR(100),
    cliente_bairro VARCHAR(100) NOT NULL,
    cliente_cidade VARCHAR(100) NOT NULL,
    cliente_estado VARCHAR(2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL
);

-- Tabela de itens do pedido
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    variacao_id INT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (variacao_id) REFERENCES produto_variacoes(id) ON DELETE CASCADE
);

-- Inserir alguns cupons de exemplo
INSERT INTO cupons (codigo, tipo, valor, valor_minimo, data_inicio, data_fim) VALUES
('DESCONTO10', 'percentual', 10.00, 50.00, '2025-01-01', '2025-12-31'),
('FRETE15', 'valor_fixo', 15.00, 100.00, '2025-01-01', '2025-12-31'),
('BEMVINDO', 'percentual', 5.00, 0.00, '2025-01-01', '2025-12-31');

-- Inserir produtos de exemplo
INSERT INTO produtos (nome, preco, descricao) VALUES
('Camiseta Básica', 29.90, 'Camiseta 100% algodão'),
('Calça Jeans', 89.90, 'Calça jeans tradicional'),
('Tênis Esportivo', 159.90, 'Tênis para corrida e caminhada');

-- Inserir variações de exemplo
INSERT INTO produto_variacoes (produto_id, nome, tipo, valor) VALUES
(1, 'Azul P', 'cor_tamanho', 'azul-p'),
(1, 'Azul M', 'cor_tamanho', 'azul-m'),
(1, 'Azul G', 'cor_tamanho', 'azul-g'),
(1, 'Branco P', 'cor_tamanho', 'branco-p'),
(1, 'Branco M', 'cor_tamanho', 'branco-m'),
(2, 'Azul 38', 'cor_tamanho', 'azul-38'),
(2, 'Azul 40', 'cor_tamanho', 'azul-40'),
(2, 'Preto 38', 'cor_tamanho', 'preto-38');

-- Inserir estoque de exemplo
INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES
(1, 1, 15), -- Camiseta Azul P
(1, 2, 20), -- Camiseta Azul M
(1, 3, 10), -- Camiseta Azul G
(1, 4, 8),  -- Camiseta Branco P
(1, 5, 12), -- Camiseta Branco M
(2, 6, 5),  -- Calça Azul 38
(2, 7, 7),  -- Calça Azul 40
(2, 8, 3),  -- Calça Preto 38
(3, NULL, 25); -- Tênis (sem variação)
