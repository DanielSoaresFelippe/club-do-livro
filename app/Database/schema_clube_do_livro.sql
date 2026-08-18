CREATE DATABASE IF NOT EXISTS clube_do_livro
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE clube_do_livro;

CREATE TABLE usuarios (
  id_usuario         INT AUTO_INCREMENT PRIMARY KEY,
  nome               VARCHAR(150) NOT NULL,
  email              VARCHAR(150) NOT NULL UNIQUE,
  senha              VARCHAR(255) NOT NULL,
  tipo               ENUM('admin','colaborador','cliente') NOT NULL DEFAULT 'cliente',
  foto_perfil        VARCHAR(255),
  telefone           VARCHAR(20),
  ativo              TINYINT(1) NOT NULL DEFAULT 1,
  data_cadastro      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE usuariorecuperasenha (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id     INT NOT NULL,
  chave          VARCHAR(255) NOT NULL,
  statusRegistro TINYINT NOT NULL DEFAULT 1,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_recuperasenha_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE generos (
  id_genero      INT AUTO_INCREMENT PRIMARY KEY,
  nome           VARCHAR(100) NOT NULL UNIQUE,
  descricao      TEXT,
  imagem_banner  VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE livros (
  id_livro            INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario          INT NOT NULL,
  id_genero           INT NOT NULL,
  titulo              VARCHAR(200) NOT NULL,
  autor               VARCHAR(150) NOT NULL,
  editora             VARCHAR(150),
  ano_publicacao      SMALLINT,
  descricao           TEXT,
  imagem_capa         VARCHAR(255),
  estado_conservacao  ENUM('novo','seminovo','usado','desgastado') NOT NULL DEFAULT 'usado',
  tipo_transacao      ENUM('venda','troca','ambos') NOT NULL DEFAULT 'ambos',
  preco               DECIMAL(10,2),
  status              ENUM('disponivel','reservado','indisponivel') NOT NULL DEFAULT 'disponivel',
  data_cadastro       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_livros_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_livros_genero  FOREIGN KEY (id_genero)  REFERENCES generos(id_genero)   ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE favoritos (
  id_favorito     INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario      INT NOT NULL,
  id_livro        INT NOT NULL,
  data_favoritado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_favoritos_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_favoritos_livro   FOREIGN KEY (id_livro)   REFERENCES livros(id_livro)     ON DELETE CASCADE,
  UNIQUE KEY uq_usuario_livro (id_usuario, id_livro)
) ENGINE=InnoDB;

CREATE TABLE propostas (
  id_proposta            INT AUTO_INCREMENT PRIMARY KEY,
  id_livro               INT NOT NULL,             -- livro desejado
  id_usuario_interessado INT NOT NULL,
  id_livro_oferecido     INT,                       -- preenchido quando tipo = troca
  tipo                   ENUM('troca','compra','emprestimo') NOT NULL,
  valor_oferecido        DECIMAL(10,2),
  status                 ENUM('pendente','aceita','recusada','expirada','concluida') NOT NULL DEFAULT 'pendente',
  prazo_limite           DATETIME NOT NULL,
  data_criacao           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_propostas_livro       FOREIGN KEY (id_livro)               REFERENCES livros(id_livro)     ON DELETE CASCADE,
  CONSTRAINT fk_propostas_interessado FOREIGN KEY (id_usuario_interessado) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_propostas_oferecido   FOREIGN KEY (id_livro_oferecido)     REFERENCES livros(id_livro)     ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE conversas (
  id_conversa   INT AUTO_INCREMENT PRIMARY KEY,
  id_livro      INT NOT NULL,
  id_usuario_dono   INT NOT NULL,   -- dono do livro
  id_usuario_interessado   INT NOT NULL,   -- interessado
  data_inicio   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_conversas_livro     FOREIGN KEY (id_livro)    REFERENCES livros(id_livro)     ON DELETE CASCADE,
  CONSTRAINT fk_conversas_usuario_dono  FOREIGN KEY (id_usuario_dono) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_conversas_usuario_interessado  FOREIGN KEY (id_usuario_interessado) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mensagens (
  id_mensagem   INT AUTO_INCREMENT PRIMARY KEY,
  id_conversa   INT NOT NULL,
  id_remetente  INT NOT NULL,
  texto         TEXT NOT NULL,
  lida          TINYINT(1) NOT NULL DEFAULT 0,
  data_envio    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mensagens_conversa  FOREIGN KEY (id_conversa)  REFERENCES conversas(id_conversa) ON DELETE CASCADE,
  CONSTRAINT fk_mensagens_remetente FOREIGN KEY (id_remetente) REFERENCES usuarios(id_usuario)   ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE historico_transacoes (
  id_transacao   INT AUTO_INCREMENT PRIMARY KEY,
  id_proposta    INT NOT NULL,
  id_livro       INT NOT NULL,
  id_comprador   INT NOT NULL,
  id_vendedor    INT NOT NULL,
  tipo           ENUM('venda','troca','emprestimo') NOT NULL,
  valor          DECIMAL(10,2),
  data_conclusao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_historico_proposta  FOREIGN KEY (id_proposta)  REFERENCES propostas(id_proposta) ON DELETE CASCADE,
  CONSTRAINT fk_historico_livro     FOREIGN KEY (id_livro)     REFERENCES livros(id_livro)       ON DELETE RESTRICT,
  CONSTRAINT fk_historico_comprador FOREIGN KEY (id_comprador) REFERENCES usuarios(id_usuario)   ON DELETE RESTRICT,
  CONSTRAINT fk_historico_vendedor  FOREIGN KEY (id_vendedor)  REFERENCES usuarios(id_usuario)   ON DELETE RESTRICT
) ENGINE=InnoDB;

-- NOTIFICACOES (e-mail quando proposta expira, mensagem nova etc.)
CREATE TABLE notificacoes (
  id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario     INT NOT NULL,
  tipo           ENUM('proposta','mensagem','prazo_expirado','sistema') NOT NULL,
  titulo         VARCHAR(150) NOT NULL,
  mensagem       TEXT,
  lida           TINYINT(1) NOT NULL DEFAULT 0,
  data_envio     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notificacoes_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Índices auxiliares de performance
CREATE INDEX idx_livros_status       ON livros(status);
CREATE INDEX idx_livros_genero       ON livros(id_genero);
CREATE INDEX idx_propostas_status    ON propostas(status);
CREATE INDEX idx_propostas_prazo     ON propostas(prazo_limite);
CREATE INDEX idx_mensagens_conversa  ON mensagens(id_conversa);