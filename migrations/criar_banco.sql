CREATE DATABASE IF NOT EXISTS my_food;

USE my_food;

CREATE TABLE categoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL
);

CREATE TABLE alimento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  preco DECIMAL(10, 2) NOT NULL,
  descricao TEXT,
  foto VARCHAR(255),
  categoria_id INT NOT NULL,
  FOREIGN KEY (categoria_id) REFERENCES categoria(id)
);

CREATE TABLE adicional (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  preco DECIMAL(10, 2) NOT NULL,
  descricao VARCHAR(255) NOT NULL
);

CREATE TABLE forma_de_pagamento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL
);

CREATE TABLE pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  endereco VARCHAR(255) NOT NULL,
  whatsApp VARCHAR(20) NOT NULL,
  forma_pagamento_id INT NOT NULL,
  FOREIGN KEY (forma_pagamento_id) REFERENCES forma_de_pagamento(id)
);

CREATE TABLE alimento_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  alimento_id INT NOT NULL,
  pedido_id INT NOT NULL,
  FOREIGN KEY (alimento_id) REFERENCES alimento(id),
  FOREIGN KEY (pedido_id) REFERENCES pedido(id)
);

CREATE TABLE alimento_pedido_adicional (
  alimento_pedido_id INT NOT NULL,
  adicional_id INT NOT NULL,
  PRIMARY KEY (alimento_pedido_id, adicional_id),
  FOREIGN KEY (alimento_pedido_id) REFERENCES alimento_pedido(id),
  FOREIGN KEY (adicional_id) REFERENCES adicional(id)
);

CREATE TABLE status_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL
);

CREATE TABLE pedido_status (
  pedido_id INT,
  status_pedido_id INT,
  data_status DATETIME NOT NULL,
  PRIMARY KEY (pedido_id, status_pedido_id),
  FOREIGN KEY (pedido_id) REFERENCES pedido(id),
  FOREIGN KEY (status_pedido_id) REFERENCES status_pedido(id)
);
