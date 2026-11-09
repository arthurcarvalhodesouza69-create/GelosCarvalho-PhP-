<?php
require_once __DIR__ . '/includes/site.php';

$pagina = [
    'titulo'    => 'Galeria | Gelos Carvalho',
    'descricao' => 'Galeria de fotos da Gelos Carvalho — nossa fábrica, produtos e atendimentos.',
    'ativo'     => 'galeria.php',
    'audio'     => 'aquatic_ambience.mp3',
    'css'       => 'css/galeria.css',
];
require __DIR__ . '/includes/site_topo.php';
?>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox" onclick="fecharLightbox()">
        <span class="lightbox-fechar" onclick="fecharLightbox()">&times;</span>
        <img src="" alt="" class="lightbox-img" id="lightbox-img" onclick="event.stopPropagation()">
        <div class="lightbox-legenda" id="lightbox-legenda"></div>
    </div>

    <!-- ======== MAIN ======== -->
    <main>

        <!-- PAGE HERO -->
        <section class="page-galeria">
            <div class="cristais" aria-hidden="true">
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
            </div>
            <span class="page-galeria-label">Nossas fotos</span>
            <h1>Galeria</h1>
            <p>Conheça nossa fábrica, nossos produtos e os momentos que fazem da Gelos Carvalho uma empresa especial.</p>
        </section>

        <!-- GALERIA -->
        <div class="galeria-conteudo">

            <!-- FILTROS -->
            <div class="galeria-filtros animar">
                <button class="filtro-btn ativo" data-filtro="todos">Todos</button>
<?php foreach (consulta('SELECT id, nome FROM categorias_galeria ORDER BY ordem, id') as $categoria): ?>
                <button class="filtro-btn" data-filtro="<?= (int) $categoria['id'] ?>"><?= e($categoria['nome']) ?></button>
<?php endforeach; ?>
            </div>

            <!-- GRID GALERIA -->
            <div class="galeria-grid animar">

<?php foreach (consulta('SELECT * FROM fotos_galeria WHERE ativo = 1 ORDER BY ordem, id') as $foto): ?>
                <div class="galeria-item<?= $foto['grande'] ? ' grande' : '' ?>" data-categoria="<?= (int) $foto['categoria_id'] ?>" onclick="abrirLightbox(this)">
                    <img src="<?= e($foto['imagem']) ?>" alt="<?= e($foto['texto_alt']) ?>">
                    <div class="galeria-overlay"><p><?= e($foto['legenda']) ?></p></div>
                </div>
<?php endforeach; ?>

            </div>

            <div class="divisor"></div>

            <!-- DEPOIMENTOS (bonus) -->
            <div class="secao-titulo animar">
                <span class="label">O que dizem sobre nós</span>
                <h2>Clientes Satisfeitos</h2>
            </div>

            <div class="cards-grid animar" style="margin-bottom:40px;">
<?php foreach (consulta('SELECT * FROM depoimentos WHERE ativo = 1 ORDER BY ordem, id') as $depoimento): ?>
                <article class="card">
                    <div style="color:var(--dourado); font-size:1.2rem; margin-bottom:12px;" aria-label="Nota <?= (int) $depoimento['nota'] ?> de 5"><?= str_repeat('⭐', (int) $depoimento['nota']) ?></div>
                    <p style="font-style:italic; color:var(--cinza-medio); font-size:0.9rem; margin-bottom:16px;">"<?= e($depoimento['texto']) ?>"</p>
                    <h4 style="color:var(--branco); font-size:0.85rem;">— <?= e($depoimento['autor']) ?></h4>
                    <span style="color:var(--azul-gelo); font-size:0.75rem;"><?= e($depoimento['localidade']) ?></span>
                </article>
<?php endforeach; ?>
            </div>

        </div>
    </main>

<?php require __DIR__ . '/includes/site_rodape.php'; ?>

    <script>
        /* Animações de entrada */
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visivel');
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.animar').forEach(el => observer.observe(el));

        /* Filtros de galeria */
        const filtros = document.querySelectorAll('.filtro-btn');
        const itens = document.querySelectorAll('.galeria-item');

        filtros.forEach(btn => {
            btn.addEventListener('click', () => {
                filtros.forEach(b => b.classList.remove('ativo'));
                btn.classList.add('ativo');

                const filtro = btn.dataset.filtro;
                itens.forEach(item => {
                    if (filtro === 'todos' || item.dataset.categoria === filtro) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeUp 0.4s ease both';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        /* Lightbox */
        function abrirLightbox(el) {
            const img = el.querySelector('img');
            const legenda = el.querySelector('.galeria-overlay p').textContent;
            document.getElementById('lightbox-img').src = img.src;
            document.getElementById('lightbox-img').alt = img.alt;
            document.getElementById('lightbox-legenda').textContent = legenda;
            document.getElementById('lightbox').classList.add('aberto');
            document.body.style.overflow = 'hidden';
        }

        function fecharLightbox() {
            document.getElementById('lightbox').classList.remove('aberto');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') fecharLightbox();
        });
    </script>
</body>
</html>
