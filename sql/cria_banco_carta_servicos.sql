DROP TABLE IF EXISTS areas_mte;
DROP TABLE IF EXISTS pessoas_alvo;
DROP TABLE IF EXISTS URLs;

CREATE TABLE URLs (
    id_chave_url INT NOT NULL AUTO_INCREMENT,
    nome_url VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
    nome_url_hash CHAR(64) GENERATED ALWAYS AS (SHA2(nome_url, 256)) STORED,
    valida ENUM('S', 'N') DEFAULT 'N', -- Melhor do que VARCHAR(3)
    id_arquivo_fonte INT,
    data_teste DATE, -- Melhor do que VARCHAR(10)
    comentarios TEXT,
    PRIMARY KEY (id_chave_url),
    UNIQUE (nome_url_hash)
    #atenção: o add constraint relativo a arquivos_fonte mais adiante
);


CREATE TABLE `pessoas_alvo` (
  `id_chave_pessoa_alvo` int NOT NULL AUTO_INCREMENT,
  `nome_pessoa_alvo` varchar(40) NOT NULL,
  `descricao` text,
  `data_insercao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_chave_pessoa_alvo`),
  UNIQUE KEY `nome_pessoa_alvo` (`nome_pessoa_alvo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `areas_mte` (
  `id_chave_area_mte` int NOT NULL AUTO_INCREMENT,
  `nome_area_mte` varchar(40) NOT NULL,
  `descricao` text,
  `pai` int DEFAULT NULL,
  `data_insercao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_chave_area_mte`),
  UNIQUE KEY `nome_area_mte` (`nome_area_mte`),
  FOREIGN KEY (`pai`) REFERENCES `areas_mte` (`id_chave_area_mte`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO pessoas_alvo (nome_pessoa_alvo) VALUES ('trabalhadores');
INSERT INTO pessoas_alvo (nome_pessoa_alvo) VALUES ('empregadores');
INSERT INTO pessoas_alvo (nome_pessoa_alvo) VALUES ('entidades');
INSERT INTO pessoas_alvo (nome_pessoa_alvo) VALUES ('justiça');


-- Script seguro para popular a tabela areas_mte com hierarquia
SET FOREIGN_KEY_CHECKS=0;
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('MTE', NULL);
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('SE', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'MTE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('SEMP', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'MTE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('SPT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'MTE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('SIT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'MTE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('SRT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'MTE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGUD', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CODIN', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('SEET', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SE'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGDT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SEET'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DER', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SEMP'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DEQ', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SEMP'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DJP', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SEMP'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGEST', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'DER'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('COAD', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'CGEST'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DGB', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SPT'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DGF', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SPT'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGSAP', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'DGB'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CIRP', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'CGSAP'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('COAS', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'CGSAP'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('COSED', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'CGSAP'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGFGTS', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'DGF'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGIF', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SIT'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGR', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SIT'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DNIT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'CGIF'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('DRT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'SRT'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGRT', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'DRT'));
INSERT INTO areas_mte (nome_area_mte, pai) VALUES ('CGRS', (SELECT id_chave_area_mte FROM (SELECT * FROM areas_mte) AS temp WHERE nome_area_mte = 'DRT'));
SET FOREIGN_KEY_CHECKS=1;
