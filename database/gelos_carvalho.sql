-- ====================================================
-- GELOS CARVALHO — Banco de dados do CMS (MySQL/MariaDB)
-- Importe este arquivo uma única vez (phpMyAdmin ou cliente mysql).
-- ====================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS gelos_carvalho
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE gelos_carvalho;

-- ── Usuários do painel administrativo ──
CREATE TABLE usuarios (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome          VARCHAR(120) NOT NULL,
    email         VARCHAR(160) NOT NULL,
    senha_hash    VARCHAR(255) NOT NULL,
    papel         ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    criado_em     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Dados gerais da empresa (telefone, endereço, horários resumidos) ──
CREATE TABLE configuracoes (
    chave  VARCHAR(60)  NOT NULL,
    valor  VARCHAR(500) NOT NULL,
    PRIMARY KEY (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Catálogo de produtos ──
CREATE TABLE produtos (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome             VARCHAR(100) NOT NULL,
    subtitulo        VARCHAR(100) NULL,
    icone            VARCHAR(16)  NULL,
    preco            DECIMAL(8,2) NOT NULL,
    descricao_curta  VARCHAR(255) NOT NULL,
    descricao        TEXT NOT NULL,
    imagem           VARCHAR(255) NOT NULL,
    tamanho          VARCHAR(80)  NULL,
    tipo_gelo        VARCHAR(80)  NULL,
    agua             VARCHAR(80)  NULL,
    embalagem        VARCHAR(80)  NULL,
    ideal_para       VARCHAR(80)  NULL,
    higiene          VARCHAR(80)  NULL,
    disponibilidade  VARCHAR(80)  NULL,
    em_destaque      TINYINT(1)   NOT NULL DEFAULT 0,
    ordem            INT          NOT NULL DEFAULT 0,
    ativo            TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_produtos_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Listas de tópicos de cada produto (1:N) ──
CREATE TABLE produto_itens (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    produto_id  INT UNSIGNED NOT NULL,
    tipo        ENUM('destaque','caracteristica','indicacao') NOT NULL,
    texto       VARCHAR(160) NOT NULL,
    ordem       INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_produto_itens_produto (produto_id),
    CONSTRAINT fk_produto_itens_produto FOREIGN KEY (produto_id)
        REFERENCES produtos (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Galeria ──
CREATE TABLE categorias_galeria (
    id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome   VARCHAR(60) NOT NULL,
    ordem  INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fotos_galeria (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    categoria_id  INT UNSIGNED NOT NULL,
    legenda       VARCHAR(120) NOT NULL,
    texto_alt     VARCHAR(160) NOT NULL,
    imagem        VARCHAR(255) NOT NULL,
    grande        TINYINT(1) NOT NULL DEFAULT 0,
    ordem         INT NOT NULL DEFAULT 0,
    ativo         TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_fotos_categoria (categoria_id),
    CONSTRAINT fk_fotos_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias_galeria (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Equipe (página Sobre) ──
CREATE TABLE membros_equipe (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome       VARCHAR(120) NOT NULL,
    cargo      VARCHAR(100) NOT NULL,
    descricao  VARCHAR(400) NOT NULL,
    foto       VARCHAR(255) NOT NULL,
    ordem      INT NOT NULL DEFAULT 0,
    ativo      TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Depoimentos de clientes (página Galeria) ──
CREATE TABLE depoimentos (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    autor       VARCHAR(120) NOT NULL,
    localidade  VARCHAR(100) NULL,
    texto       VARCHAR(600) NOT NULL,
    nota        TINYINT UNSIGNED NOT NULL DEFAULT 5,
    ordem       INT NOT NULL DEFAULT 0,
    ativo       TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Perguntas frequentes (página Contato) ──
CREATE TABLE faqs (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    pergunta  VARCHAR(200) NOT NULL,
    resposta  VARCHAR(600) NOT NULL,
    ordem     INT NOT NULL DEFAULT 0,
    ativo     TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Cartões de diferenciais e valores exibidos nas páginas ──
CREATE TABLE diferenciais (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    secao      ENUM('inicio','servicos','sobre') NOT NULL,
    icone      VARCHAR(32)  NOT NULL,
    titulo     VARCHAR(100) NOT NULL,
    descricao  VARCHAR(400) NOT NULL,
    ordem      INT NOT NULL DEFAULT 0,
    ativo      TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY idx_diferenciais_secao (secao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Números de destaque ──
CREATE TABLE estatisticas (
    id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    pagina  ENUM('inicio','sobre') NOT NULL,
    numero  VARCHAR(20) NOT NULL,
    rotulo  VARCHAR(80) NOT NULL,
    ordem   INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_estatisticas_pagina (pagina)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Horários detalhados (página Contato) ──
CREATE TABLE horarios (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    dia      VARCHAR(30) NOT NULL,
    horario  VARCHAR(40) NOT NULL,
    aberto   TINYINT(1) NOT NULL DEFAULT 1,
    ordem    INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_horarios_dia (dia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Mensagens e pedidos recebidos pelo formulário de contato ──
CREATE TABLE mensagens (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(120) NOT NULL,
    telefone    VARCHAR(30)  NOT NULL,
    email       VARCHAR(160) NULL,
    tipo        ENUM('produto','ambos','atacado','duvida') NULL,
    produto_id  INT UNSIGNED NULL,
    quantidade  INT UNSIGNED NULL,
    mensagem    TEXT NOT NULL,
    status      ENUM('nova','em_atendimento','concluida') NOT NULL DEFAULT 'nova',
    criado_em   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_mensagens_produto (produto_id),
    KEY idx_mensagens_status (status),
    CONSTRAINT fk_mensagens_produto FOREIGN KEY (produto_id)
        REFERENCES produtos (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- DADOS INICIAIS (conteúdo do site estático)
-- ====================================================

INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES
('Administrador', 'admin@geloscarvalho.com', '$2y$10$YryDi1GNB6VeLm5x.HfeUurFbMy2D6njI/efCXvMVyYAeXbafCfjq', 'admin');

INSERT INTO configuracoes (chave, valor) VALUES
('telefone_exibicao', '+55 77 92054-5000'),
('telefone_link', '5577920545000'),
('endereco', 'Agrovila 9 — Serra do Ramalho'),
('endereco_rodape', 'Agrovila 9, Serra do Ramalho — BA'),
('regiao', 'Bahia, Brasil'),
('horario_resumo', 'Seg. a Sex.: 7h–19h | Sáb.: 7h–16h'),
('horario_domingo', 'Domingo: sob consulta'),
('area_entrega', 'Agrovila 9 e regiões vizinhas'),
('area_entrega_detalhe', 'Serra do Ramalho — BA'),
('descricao_rodape', 'Empresa familiar de fabricação e venda de gelo, localizada na Agrovila 9, Serra do Ramalho — Bahia.');

INSERT INTO produtos (id, nome, subtitulo, icone, preco, descricao_curta, descricao, imagem, tamanho, tipo_gelo, agua, embalagem, ideal_para, higiene, disponibilidade, em_destaque, ordem) VALUES
(1, 'Pacote Gelado', 'Tamanho Básico', '🧊', 5.00,
 'Tamanho perfeito para uso doméstico, bebidas no dia a dia e pequenas reuniões. Prático e econômico.',
 'O clássico e mais prático. Ideal para uso no dia a dia, bebidas, churrascos rápidos e uso doméstico em geral.',
 'img/gelo-pacote-5.jpg', 'Padrão doméstico', 'Cubos uniformes', 'Filtrada e tratada', 'Plástico selado', 'Uso residencial', '✅ Garantida', 'Todos os dias', 0, 1),
(2, 'Pacote Família', 'Tamanho Grande', '🎉', 7.00,
 'O maior, o mais frio e o mais duradouro! Feito para festas, eventos, frigoríficos e quem precisa de muito gelo de vez.',
 'Para quem precisa de mais! O pacote maior garante horas de frescor em festas, eventos e estabelecimentos comerciais.',
 'img/gelo-pacote-7.jpg', 'Grande (para eventos)', 'Cubos uniformes', 'Filtrada e tratada', 'Plástico reforçado e selado', 'Festas e comércios', '✅ Garantida', 'Todos os dias', 1, 2);

INSERT INTO produto_itens (produto_id, tipo, texto, ordem) VALUES
(1, 'destaque', 'Ideal para bebidas e churrasco familiar', 1),
(1, 'destaque', 'Gelo em cubos uniformes', 2),
(1, 'destaque', 'Embalagem selada e higiênica', 3),
(1, 'caracteristica', 'Perfeito para neveras e coolers domésticos', 1),
(1, 'caracteristica', 'Cubos uniformes de fácil uso', 2),
(1, 'caracteristica', 'Embalagem plástica selada', 3),
(1, 'caracteristica', 'Água filtrada e de qualidade', 4),
(1, 'caracteristica', 'Durabilidade garantida', 5),
(1, 'indicacao', 'Uso residencial cotidiano', 1),
(1, 'indicacao', 'Pequenos churrascos e reuniões', 2),
(1, 'indicacao', 'Comércios locais', 3),
(2, 'destaque', 'Maior volume, maior durabilidade', 1),
(2, 'destaque', 'Ideal para festas e eventos', 2),
(2, 'destaque', 'Gelo de alta qualidade', 3),
(2, 'caracteristica', 'Volume maior que o pacote básico', 1),
(2, 'caracteristica', 'Maior durabilidade no calor', 2),
(2, 'caracteristica', 'Ideal para caixas de isopor grandes', 3),
(2, 'caracteristica', 'Embalagem reforçada e resistente', 4),
(2, 'caracteristica', 'Ótimo custo-benefício para eventos', 5),
(2, 'indicacao', 'Festas e eventos maiores', 1),
(2, 'indicacao', 'Estabelecimentos comerciais', 2),
(2, 'indicacao', 'Distribuidores e revendas', 3);

INSERT INTO categorias_galeria (id, nome, ordem) VALUES
(1, 'Fábrica', 1),
(2, 'Produtos', 2),
(3, 'Atendimentos', 3),
(4, 'Nossa Região', 4);

INSERT INTO fotos_galeria (categoria_id, legenda, texto_alt, imagem, grande, ordem) VALUES
(1, '🏭 Nossa Fábrica — Vista Geral', 'Vista geral da fábrica Gelos Carvalho', 'img/galeria-fabrica-geral.jpg', 1, 1),
(2, '🧊 Pacote R$ 5,00', 'Pacote de gelo R$5', 'img/galeria-pacote-5.jpg', 0, 2),
(1, '⚙️ Máquina em Operação', 'Máquina de gelo em operação', 'img/galeria-maquina.jpg', 0, 3),
(2, '🎉 Pacote Família R$ 7,00', 'Pacote de gelo R$7 — Família', 'img/galeria-pacote-7.jpg', 0, 4),
(4, '📍 Agrovila 9, Serra do Ramalho', 'Agrovila 9 — Serra do Ramalho', 'img/galeria-agrovila.jpg', 0, 5),
(3, '🚗 Entrega Satisfeita!', 'Entrega de gelo ao cliente', 'img/galeria-entrega.jpg', 1, 6),
(1, '📦 Estoque Organizado', 'Estoque de pacotes de gelo', 'img/galeria-estoque.jpg', 0, 7),
(3, '😄 Cliente Feliz!', 'Cliente satisfeito com o gelo', 'img/galeria-cliente.jpg', 0, 8),
(4, '🌵 Sertão Baiano', 'Paisagem do Sertão da Bahia', 'img/galeria-sertao.jpg', 0, 9),
(2, '🎊 Gelo no Evento', 'Gelo servido em mesa de evento', 'img/galeria-gelo-mesa.jpg', 0, 10),
(1, '🔬 Processo de Produção', 'Processo de produção do gelo', 'img/galeria-producao.jpg', 0, 11),
(3, '🍖 Churrasco do Cliente!', 'Gelo usado em churrasco de cliente', 'img/galeria-churrasco.jpg', 0, 12);

INSERT INTO membros_equipe (nome, cargo, descricao, foto, ordem) VALUES
('Arthur Carvalho de Souza', 'Fundador & Gestor', 'Responsável pela gestão, vendas e expansão dos negócios. Empreendedor desde cedo, acredita que o sertão tem muito potencial.', 'img/arthur_carvalho.jpg', 1),
('Mãe Carvalho', 'Direção de Produção', 'A matriarca da família garante que cada pacote seja produzido com capricho, higiene e o cuidado de quem coloca amor no trabalho.', 'img/mae__carvalho.jpg', 2),
('Irmã Carvalho', 'Atendimento & Logística', 'Responsável pelo atendimento ao cliente e pela organização das entregas. Sua dedicação garante que ninguém fique esperando.', 'img/irma_carvalho.jpg', 3);

INSERT INTO depoimentos (autor, localidade, texto, nota, ordem) VALUES
('Seu João Emanuel', 'Agrovila 2', 'O gelo da Gelos Carvalho é o melhor da região! Sempre limpo, bem embalado e entregue rapidinho. Não compro de outro lugar.', 5, 1),
('Arthur Carvalho', 'Serra do Ramalho', 'Comprei para o meu aniversário e todo mundo amou! O pacote família (R$7) durou a festa toda. Recomendo de olhos fechados! É beeem mais frio que o coração da morena.', 5, 2),
('Barack Obama', 'Agrovila 8', 'Atendimento nota 10. Fiz o pedido de manhã e já chegou antes do meio-dia. Família Carvalho de confiança total!', 5, 3);

INSERT INTO faqs (pergunta, resposta, ordem) VALUES
('Vocês fazem entrega?', 'Sim! Entregamos na Agrovila 9 e nas comunidades vizinhas de Serra do Ramalho. Entre em contato para confirmar sua área.', 1),
('Posso pedir em grandes quantidades?', 'Claro! Atendemos tanto pedidos individuais quanto atacado para comércios e eventos. Consulte disponibilidade conosco.', 2),
('O gelo é feito com água filtrada?', 'Sim! Usamos apenas água filtrada e tratada em todo o processo de fabricação, garantindo um produto limpo e seguro.', 3),
('Qual a diferença entre os pacotes?', 'O pacote de R$5 é tamanho padrão, ideal para uso doméstico. O de R$7 é maior, perfeito para festas e eventos.', 4),
('Atendem no domingo?', 'Aos domingos o atendimento é sob consulta. Ligue ou mande WhatsApp para verificar disponibilidade no dia.', 5),
('Posso retirar na fábrica?', 'Sim! Você pode retirar pessoalmente na nossa fábrica, localizada na Agrovila 9, Serra do Ramalho — BA.', 6);

INSERT INTO diferenciais (secao, icone, titulo, descricao, ordem) VALUES
('inicio', '💧', 'Água Filtrada', 'Utilizamos apenas água de qualidade, filtrada e tratada, garantindo um gelo limpo, transparente e seguro para o consumo.', 1),
('inicio', '🧊', 'Produção Artesanal', 'Cada pacote é produzido com cuidado e atenção. Nossa fábrica familiar garante o padrão que nossos clientes já conhecem e confiam.', 2),
('inicio', '📦', 'Embalagem Resistente', 'Os pacotes são selados de forma segura para evitar contaminação e manter o gelo por mais tempo, mesmo no calor do sertão baiano.', 3),
('inicio', '🚀', 'Entrega Ágil', 'Atendemos a Agrovila 9 e região com rapidez. Seu pedido fica pronto e sai fresquinho direto da nossa fábrica para a sua mão.', 4),
('servicos', '🏠', 'Empresa Familiar', 'Somos uma família que se dedicou inteiramente à qualidade do produto. Cada pacote é feito com o cuidado de quem coloca amor no que faz.', 1),
('servicos', '💰', 'Preço Justo', 'Nossos preços são acessíveis porque acreditamos que todo mundo merece gelo de qualidade. R$5 e R$7 para qualquer bolso.', 2),
('servicos', '📍', 'Atendimento Local', 'Estamos aqui na Agrovila 9, pertinho de você. Conhecemos nossa comunidade e sabemos o que o cliente precisa.', 3),
('sobre', '🫱🏾‍🫲🏽', 'Confiança acima de tudo', 'Construímos nossa reputação entregando o que prometemos, sempre com transparência e honestidade.', 1),
('sobre', '💎', 'Qualidade sem abrir mão', 'Água filtrada, processo limpo e embalagens seguras. Cada detalhe importa no que entregamos.', 2),
('sobre', '🌱', 'Crescimento com raízes', 'Crescemos pensando na nossa comunidade. Cada venda fortalece famílias e a economia local.', 3);

INSERT INTO estatisticas (pagina, numero, rotulo, ordem) VALUES
('inicio', '500+', 'Pacotes Vendidos', 1),
('inicio', '3', 'Anos de Mercado', 2),
('inicio', '100%', 'Satisfação Garantida', 3),
('inicio', '24H', 'Produção Diária', 4),
('sobre', '3+', 'Anos no Mercado', 1),
('sobre', '500+', 'Clientes Atendidos', 2),
('sobre', '2', 'Produtos Disponíveis', 3),
('sobre', '100%', 'Satisfação Garantida', 4);

INSERT INTO horarios (dia, horario, aberto, ordem) VALUES
('Segunda-feira', '7h00 – 19h00', 1, 1),
('Terça-feira', '7h00 – 19h00', 1, 2),
('Quarta-feira', '7h00 – 19h00', 1, 3),
('Quinta-feira', '7h00 – 19h00', 1, 4),
('Sexta-feira', '7h00 – 19h00', 1, 5),
('Sábado', '7h00 – 16h00', 1, 6),
('Domingo', '—', 0, 7);
