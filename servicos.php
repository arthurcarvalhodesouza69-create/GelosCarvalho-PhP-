<?php
require_once __DIR__ . '/includes/site.php';

$pagina = [
    'titulo'    => 'Serviços e Produtos | Gelos Carvalho',
    'descricao' => 'Produtos e serviços da Gelos Carvalho — Pacotes de gelo em Serra do Ramalho, Bahia.',
    'ativo'     => 'servicos.php',
    'audio'     => 'aquatic_ambience.mp3',
];
require __DIR__ . '/includes/site_topo.php';
?>

    <!-- ======== MAIN ======== -->
    <main>

        <!-- PAGE HERO -->
        <section class="page-servico">
            <div class="cristais" aria-hidden="true">
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
            </div>
            <span class="page-servico-label">O que oferecemos</span>
            <h1>Produtos &amp; Serviços</h1>
            <p>Tudo o que você precisa saber sobre nossos pacotes de gelo e como nos encontrar.</p>
        </section>

        <!-- CONTEÚDO -->
        <div class="servicos-conteudo">

            <!-- PRODUTOS COMPLETOS -->
            <div class="secao-titulo animar">
                <span class="label">Nosso catálogo</span>
                <h2>Escolha o Seu Pacote</h2>
                <p>Produzidos diariamente com água filtrada e higiene garantida.</p>
            </div>

            <div class="produtos-completos animar">

<?php foreach (produtos_ativos() as $produto): ?>
                <article class="produto-completo">
                    <div class="produto-completo-img">
                        <img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?> — Gelos Carvalho">
                        <span class="preco-tag"><?= e(moeda($produto['preco'])) ?></span>
                    </div>
                    <div class="produto-completo-body">
                        <h3><?= e(trim($produto['icone'] . ' ' . $produto['nome'] . ($produto['subtitulo'] ? ' — ' . $produto['subtitulo'] : ''))) ?></h3>
                        <p><?= e($produto['descricao']) ?></p>
                        <ul class="produto-lista">
<?php foreach ($produto['itens']['caracteristica'] as $item): ?>
                            <li><?= e($item) ?></li>
<?php endforeach; ?>
                        </ul>
                        <h4 style="color: var(--azul-gelo); font-size:0.8rem; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">Indicado para:</h4>
                        <ul class="lista-simples">
<?php foreach ($produto['itens']['indicacao'] as $item): ?>
                            <li><?= e($item) ?></li>
<?php endforeach; ?>
                        </ul>
                        <a href="contato.php" class="btn <?= $produto['em_destaque'] ? 'btn-dourado' : 'btn-primario' ?>" style="margin-top:20px; width:100%; justify-content:center;">Pedir Agora <?= e($produto['icone']) ?></a>
                    </div>
                </article>
<?php endforeach; ?>

            </div>

            <div class="divisor"></div>

            <!-- TABELA COMPARATIVA -->
            <div class="tabela-secao animar">
                <div class="secao-titulo" style="margin-bottom:40px;">
                    <span class="label">Compare e escolha</span>
                    <h2>Tabela Comparativa</h2>
                    <p>Confira as diferenças entre nossos produtos para tomar a melhor decisão.</p>
                </div>

                <div class="tabela-wrap">
<?php
$comparativo = [
    'tamanho' => 'Tamanho', 'tipo_gelo' => 'Tipo de Gelo', 'agua' => 'Água utilizada',
    'embalagem' => 'Embalagem', 'ideal_para' => 'Ideal para', 'higiene' => 'Higiene',
    'disponibilidade' => 'Disponibilidade',
];
?>
                    <table>
                        <thead>
                            <tr>
                                <th>Característica</th>
<?php foreach (produtos_ativos() as $produto): ?>
                                <th><?= e($produto['nome']) ?></th>
<?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Preço</td>
<?php foreach (produtos_ativos() as $produto): ?>
                                <td><strong><?= e(moeda($produto['preco'])) ?></strong></td>
<?php endforeach; ?>
                            </tr>
<?php foreach ($comparativo as $coluna => $rotulo): ?>
                            <tr>
                                <td><?= e($rotulo) ?></td>
<?php foreach (produtos_ativos() as $produto): ?>
                                <td><?= e($produto[$coluna] ?: '—') ?></td>
<?php endforeach; ?>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="divisor"></div>

            <!-- COMO FUNCIONA -->
            <div class="como-funciona animar">
                <div class="secao-titulo" style="text-align:left; margin-bottom:0;">
                    <span class="label">Passo a passo</span>
                    <h2>Como Fazer Seu Pedido</h2>
                    <p style="color: var(--cinza-medio); margin-top:8px;">É simples, rápido e fácil!</p>
                </div>

                <div class="etapas">
                    <div class="etapa">
                        <div class="etapa-numero">01</div>
                        <h4>Entre em Contato</h4>
                        <p>Ligue ou mande mensagem no WhatsApp: <strong style="color:var(--azul-gelo);"><?= e(cfg('telefone_exibicao')) ?></strong></p>
                    </div>
                    <div class="etapa">
                        <div class="etapa-numero">02</div>
                        <h4>Informe a Quantidade</h4>
                        <p>Diga quantos pacotes deseja e qual o tamanho.</p>
                    </div>
                    <div class="etapa">
                        <div class="etapa-numero">03</div>
                        <h4>Confirme o Endereço</h4>
                        <p>Informe o local de entrega na Agrovila 9 ou região vizinha.</p>
                    </div>
                    <div class="etapa">
                        <div class="etapa-numero">04</div>
                        <h4>Receba seu Gelo</h4>
                        <p>Entregamos rapidinho! O gelo chegará fresquinho na sua mão.</p>
                    </div>
                </div>
            </div>

            <div class="divisor"></div>

            <!-- DIFERENCIAIS -->
            <div class="secao-titulo animar">
                <span class="label">Nossos diferenciais</span>
                <h2>Por Que a Gelos Carvalho?</h2>
            </div>

            <div class="cards-grid animar" style="margin-bottom:40px;">
<?php foreach (diferenciais_da_secao('servicos') as $diferencial): ?>
                <article class="card">
                    <div class="card-icone"><?= e($diferencial['icone']) ?></div>
                    <h3><?= e($diferencial['titulo']) ?></h3>
                    <p><?= e($diferencial['descricao']) ?></p>
                </article>
<?php endforeach; ?>
            </div>

        </div>
    </main>

<?php require __DIR__ . '/includes/site_rodape.php'; ?>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visivel');
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.animar').forEach(el => observer.observe(el));
    </script>
</body>
</html>
