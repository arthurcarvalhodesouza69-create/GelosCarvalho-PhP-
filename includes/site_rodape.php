    <!-- ======== FOOTER ======== -->
    <footer>
        <div class="footer-grid">
            <div class="footer-marca">
                <div class="logo">
                    <div class="logo-icone"><img src="img/Gelos_Carvalho_Logo_Mestre.png" alt="logo_gelo"></div>
                    <div class="logo-texto">
                        <span class="logo-nome">Gelos Carvalho</span>
                        <span class="logo-slogan">Frescor que você sente</span>
                    </div>
                </div>
                <p><?= e(cfg('descricao_rodape')) ?></p>
                <a href="contato.php" class="btn btn-primario" style="font-size:0.78rem; padding:10px 22px;">Fazer Pedido</a>
            </div>

            <div>
                <h4 class="footer-titulo">Navegação</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Início</a></li>
                    <li><a href="sobre.php">Sobre Nós</a></li>
                    <li><a href="servicos.php">Serviços e Produtos</a></li>
                    <li><a href="galeria.php">Galeria</a></li>
                    <li><a href="contato.php">Contato</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-titulo">Produtos</h4>
                <ul class="footer-links">
<?php foreach (produtos_ativos() as $produtoRodape): ?>
                    <li><a href="servicos.php">Pacote <?= e(moeda($produtoRodape['preco'])) ?></a></li>
<?php endforeach; ?>
                    <li><a href="contato.php">Encomendas</a></li>
                    <li><a href="contato.php">Atacado</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-titulo">Contato</h4>
                <div class="footer-contato">
                    <p>📍 <?= e(cfg('endereco_rodape')) ?></p>
                    <p>📞 <a href="tel:+<?= e(cfg('telefone_link')) ?>" style="color:var(--azul-gelo);"><?= e(cfg('telefone_exibicao')) ?></a></p>
                    <p>💬 WhatsApp disponível</p>
                    <p>🕐 <?= e(cfg('horario_resumo')) ?></p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Gelos Carvalho. Todos os direitos reservados.</p>
            <p>Desenvolvido por <span>Arthur Carvalho de Souza</span> &mdash; Programação Web &mdash; IF Baiano</p>
        </div>
    </footer>
