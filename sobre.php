<?php
require_once __DIR__ . '/includes/site.php';

$pagina = [
    'titulo'    => 'Sobre Nós | Gelos Carvalho',
    'descricao' => 'Conheça a história da Gelos Carvalho, empresa familiar de Serra do Ramalho, Agrovila 9.',
    'ativo'     => 'sobre.php',
    'audio'     => 'E_TA_CALOR.mp3',
];
require __DIR__ . '/includes/site_topo.php';
?>

    <!-- ======== MAIN ======== -->
    <main>

        <!-- PAGE HERO -->
        <section class="page-hero">
            <div class="cristais" aria-hidden="true">
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
            </div>
            <span class="page-hero-label">Nossa história</span>
            <h1>Quem Somos</h1>
            <p>Uma família unida pelo propósito de levar frescor e qualidade para toda a região de Serra do Ramalho.</p>
        </section>

        <!-- CONTEÚDO SOBRE -->
        <div class="sobre-conteudo">

            <!-- BLOCO 1 — A Nossa história -->
            <div class="sobre-grid animar">
                <div class="sobre-imagem-wrap">
                    <img src="img/empresa.png" alt="Exterior da fábrica Gelos Carvalho">
                </div>
                <div class="sobre-texto">
                    <span class="label">A Nossa história</span>
                    <h2>Nascemos do Coração do Sertão</h2>
                    <p>A Gelos Carvalho nasceu na Agrovila 9, em Serra do Ramalho, Bahia — uma região onde o calor do sertão é constante e a necessidade de gelo de qualidade era real. Foi então que a família Carvalho enxergou uma oportunidade e transformou sonho em negócio.</p>
                    <p>Com muito trabalho e dedicação, construímos uma pequena, mas poderosa fábrica de gelo que hoje atende moradores locais, pequenos comércios e eventos da região. Somos uma empresa familiar, onde cada membro tem um papel essencial para o sucesso do empreendimento.</p>
                    <p>Nosso compromisso desde o primeiro dia é simples: entregar gelo de qualidade, com higiene, pontualidade e um preço justo para todos.</p>

                    <div class="valores-lista">
<?php foreach (diferenciais_da_secao('sobre') as $valor): ?>
                        <div class="valor-item">
                            <div class="valor-icone"><?= e($valor['icone']) ?></div>
                            <div class="valor-texto">
                                <h4><?= e($valor['titulo']) ?></h4>
                                <p><?= e($valor['descricao']) ?></p>
                            </div>
                        </div>
<?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="divisor"></div>

            <!-- ESTATÍSTICAS -->
            <div class="stats-section animar">
<?php foreach (estatisticas_da_pagina('sobre') as $estatistica): ?>
                <div class="stat-item">
                    <div class="numero"><?= e($estatistica['numero']) ?></div>
                    <p><?= e($estatistica['rotulo']) ?></p>
                </div>
<?php endforeach; ?>
            </div>

            <div class="divisor"></div>

            <!-- NOSSA MISSÃO -->
            <div class="sobre-grid animar" style="direction: rtl; gap: 60px; margin-bottom:60px;">
                <div class="sobre-imagem-wrap" style="direction: ltr;">
                    <img src="img/familia.jpg" alt="Gelo de qualidade Gelos Carvalho" style="height:380px; object-fit:cover;">
                </div>
                <div class="sobre-texto" style="direction: ltr;">
                    <span class="label">Missão, Visão e Valores</span>
                    <h2>O que Nos Move Todo Dia</h2>

                    <h3 style="color: var(--azul-gelo); font-size:0.9rem; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px; margin-top:20px;">🎯 Missão</h3>
                    <p>Oferecer gelo de alta qualidade à comunidade de Serra do Ramalho e região, com atendimento rápido, preço acessível e higiene total em cada pacote entregue.</p>

                    <h3 style="color: var(--azul-gelo); font-size:0.9rem; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px; margin-top:20px;">🔭 Visão</h3>
                    <p>Ser a referência em fornecimento de gelo no Vale do São Francisco, expandindo nosso atendimento para mais municípios da região e conquistando novos parceiros comerciais.</p>

                    <h3 style="color: var(--azul-gelo); font-size:0.9rem; letter-spacing:2px; text-transform:uppercase; margin-bottom:8px; margin-top:20px;">💡 Valores</h3>
                    <ul class="lista-simples" style="margin-top:8px;">
                        <li>Qualidade e higiene em cada produto</li>
                        <li>Respeito ao cliente e à comunidade</li>
                        <li>Trabalho honesto e família unida</li>
                        <li>Comprometimento com os prazos</li>
                        <li>Crescimento sustentável e local</li>
                    </ul>
                </div>
            </div>

            <div class="divisor"></div>

            <!-- NOSSA EQUIPE -->
            <div class="secao-titulo animar">
                <span class="label">As pessoas por trás do gelo</span>
                <h2>Nossa Família</h2>
                <p>Uma empresa de família, feita com amor, suor e muito trabalho.</p>
            </div>

            <div class="equipe-grid animar" style="margin-bottom:40px;">
<?php foreach (consulta('SELECT * FROM membros_equipe WHERE ativo = 1 ORDER BY ordem, id') as $membro): ?>
                <div class="membro-card">
                    <div class="membro-foto">
                        <img src="<?= e($membro['foto']) ?>" alt="<?= e($membro['nome'] . ' — ' . $membro['cargo']) ?>">
                    </div>
                    <div class="membro-info">
                        <h3><?= e($membro['nome']) ?></h3>
                        <div class="cargo"><?= e($membro['cargo']) ?></div>
                        <p><?= e($membro['descricao']) ?></p>
                    </div>
                </div>
<?php endforeach; ?>
            </div>

            <!-- LISTA ORDENADA — LINHA DO TEMPO -->
            <div class="animar" style="background: rgba(12,42,69,0.5); border:1px solid rgba(14,165,233,0.2); border-radius: var(--raio-grande); padding:48px 40px; margin-bottom:40px;">
                <div class="secao-titulo" style="margin-bottom:36px; text-align:left;">
                    <span class="label">Nossa Trajetória</span>
                    <h2 style="font-size:2rem;">Como Chegamos Aqui</h2>
                </div>
                <ol class="lista-ordenada">
                    <li>Identificamos a necessidade de gelo de qualidade na Agrovila 9 e na região de Serra do Ramalho, onde o acesso era limitado.</li>
                    <li>A família Carvalho investiu em equipamentos de fabricação e aprendeu as melhores práticas de higiene e produção de gelo.</li>
                    <li>Iniciamos as vendas de forma local, atendendo vizinhos, amigos e os primeiros clientes da Agrovila 9.</li>
                    <li>Com o crescimento da demanda, expandimos nossa capacidade de produção e passamos a atender eventos e comércios da região.</li>
                    <li>Hoje, a Gelos Carvalho é referência em qualidade e confiança, com centenas de clientes satisfeitos em toda a Serra do Ramalho.</li>
                </ol>
            </div>

        </div>
    </main>

<?php require __DIR__ . '/includes/site_rodape.php'; ?>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visivel');
            });
        }, { threshold: 0.15 });
        document.querySelectorAll('.animar').forEach(el => observer.observe(el));
    </script>
</body>
</html>
