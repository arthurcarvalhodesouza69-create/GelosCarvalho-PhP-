<?php
require_once __DIR__ . '/includes/site.php';

$pagina = [
    'titulo'    => 'Gelos Carvalho | Frescor que você Sente',
    'descricao' => 'Gelos Carvalho — Fabricação e venda de gelo em Serra do Ramalho, Agrovila 9. Gelo de qualidade para sua mesa!',
    'ativo'     => 'index.php',
    'audio'     => 'aquatic_ambience.mp3',
];
require __DIR__ . '/includes/site_topo.php';
?>


    <!-- ======== MAIN ======== -->
    <main>

        <!-- HERO -->
        <section class="hero">
            <!-- Cristais flutuantes -->
            <div class="cristais" aria-hidden="true">
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
            </div>
            <div class="hero-conteudo">
                <div class="hero-badge">🏔️ Serra do Ramalho &mdash; Agrovila 9</div>
                <h1>
                    Gelo
                    <span>Carvalho</span>
                </h1>
                <p class="hero-subtitulo">
                    Fabricação artesanal, qualidade garantida e entrega rápida.<br>
                    O gelo que refresca todo o Sertão da Bahia.
                </p>
                <div class="hero-botoes">
                    <a href="servicos.php" class="btn btn-primario"> Nossos Produtos</a>
                    <a href="contato.php" class="btn btn-secundario">📞 Fazer Pedido</a>
                </div>
            </div>
        </section>

        <!-- DESTAQUES -->
        <section class="destaques">
            <div class="secao-titulo animar">
                <span class="label">Por que nos escolher?</span>
                <h2>Qualidade em Cada Cubo</h2>
                <p>Produzimos gelo com água filtrada, higiene total e embalagem resistente.</p>
            </div>

            <div class="cards-grid">
<?php foreach (diferenciais_da_secao('inicio') as $indice => $diferencial): ?>
                <article class="card animar animar-delay-<?= ($indice % 4) + 1 ?>">
                    <div class="card-icone"><?= e($diferencial['icone']) ?></div>
                    <h3><?= e($diferencial['titulo']) ?></h3>
                    <p><?= e($diferencial['descricao']) ?></p>
                </article>
<?php endforeach; ?>
            </div>
        </section>

        <!-- PRODUTOS EM DESTAQUE -->
        <section class="produtos-destaque">
            <div class="produtos-destaque-inner">
                <div class="secao-titulo animar">
                    <span class="label">Nosso Cardápio de Gelo</span>
                    <h2>Escolha o Seu Tamanho</h2>
                    <p>Dois tamanhos para atender desde o churrasquinho em família até o evento maior.</p>
                </div>

                <div class="produtos-grid">
<?php $posicao = 0; foreach (produtos_ativos() as $produto): $posicao++; ?>
                    <article class="produto-card animar animar-delay-<?= (($posicao - 1) % 4) + 1 ?>">
                        <div class="produto-imagem">
                            <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?> — Gelos Carvalho">
                            <span class="produto-badge"><?= e(moeda($produto['preco'])) ?></span>
                        </div>
                        <div class="produto-info">
                            <h3><?= e($produto['nome']) ?></h3>
                            <p><?= e($produto['descricao_curta']) ?></p>
                            <ul class="lista-simples">
<?php foreach ($produto['itens']['destaque'] as $item): ?>
                                <li><?= e($item) ?></li>
<?php endforeach; ?>
                            </ul>
                            <a href="contato.php" class="btn <?= $produto['em_destaque'] ? 'btn-dourado' : 'btn-primario' ?>" style="margin-top:16px; font-size:0.8rem; padding:12px 24px;">Pedir Agora</a>
                        </div>
                    </article>
<?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- NUMEROS -->
        <section style="padding: 80px 24px; max-width:1200px; margin:0 auto;">
            <div class="stats-section animar">
<?php foreach (estatisticas_da_pagina('inicio') as $estatistica): ?>
                <div class="stat-item">
                    <div class="numero"><?= e($estatistica['numero']) ?></div>
                    <p><?= e($estatistica['rotulo']) ?></p>
                </div>
<?php endforeach; ?>
            </div>
        </section>

        <!-- CTA BANNER -->
        <section class="cta-banner">
            <div class="cta-banner-inner animar">
                <h2>Precisa de Gelo Agora?</h2>
                <p>Entre em contato conosco pelo WhatsApp ou telefone. Atendemos a Agrovila 9 e regiões vizinhas em Serra do Ramalho.</p>
                <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
                    <a href="tel:+<?= e(cfg('telefone_link')) ?>" class="btn btn-primario">📞 Ligar Agora</a>
                    <a href="https://wa.me/<?= e(cfg('telefone_link')) ?>" target="_blank" class="btn btn-dourado">💬 WhatsApp</a>
                </div>
            </div>
        </section>

    </main>

<?php require __DIR__ . '/includes/site_rodape.php'; ?>

    <!-- Script de animação ao rolar -->
    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visivel');
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.animar').forEach(el => observer.observe(el));
    </script>
</body>
</html>
