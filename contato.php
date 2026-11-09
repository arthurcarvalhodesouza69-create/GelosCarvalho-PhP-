<?php
require_once __DIR__ . '/includes/site.php';

// O token CSRF do formulário depende da sessão, aberta antes de qualquer saída.
sessao_iniciar();

$produtos = produtos_ativos();
$campos = ['nome' => '', 'telefone' => '', 'email' => '', 'pedido' => '', 'quantidade' => '', 'mensagem' => ''];
$errosContato = [];
$enviado = isset($_GET['enviado']);

// Recebe o formulário de contato e grava a mensagem para o painel.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campos as $nome => $valor) {
        $campos[$nome] = is_string($_POST[$nome] ?? null) ? trim($_POST[$nome]) : '';
    }

    $tipo = null;
    $produtoId = null;
    if (preg_match('/^p(\d+)$/', $campos['pedido'], $partes) && isset($produtos[(int) $partes[1]])) {
        $tipo = 'produto';
        $produtoId = (int) $partes[1];
    } elseif (in_array($campos['pedido'], ['ambos', 'atacado', 'duvida'], true)) {
        $tipo = $campos['pedido'];
    } elseif ($campos['pedido'] !== '') {
        $errosContato[] = 'Selecione um tipo de pedido válido.';
    }

    if (!csrf_valido()) {
        $errosContato[] = 'Sua sessão expirou. Envie o formulário novamente.';
    }
    if ($campos['nome'] === '' || mb_strlen($campos['nome']) > 120) {
        $errosContato[] = 'Informe seu nome (até 120 caracteres).';
    }
    if ($campos['telefone'] === '' || mb_strlen($campos['telefone']) > 30) {
        $errosContato[] = 'Informe um telefone válido.';
    }
    if ($campos['email'] !== '' && (!filter_var($campos['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($campos['email']) > 160)) {
        $errosContato[] = 'Informe um e-mail válido.';
    }
    $quantidade = null;
    if ($campos['quantidade'] !== '') {
        $quantidade = filter_var($campos['quantidade'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100000]]);
        if ($quantidade === false) {
            $errosContato[] = 'Informe uma quantidade válida.';
        }
    }
    if ($campos['mensagem'] === '' || mb_strlen($campos['mensagem']) > 2000) {
        $errosContato[] = 'Escreva sua mensagem (até 2000 caracteres).';
    }

    if (!$errosContato) {
        db()->prepare('INSERT INTO mensagens (nome, telefone, email, tipo, produto_id, quantidade, mensagem) VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([$campos['nome'], $campos['telefone'], $campos['email'] ?: null, $tipo, $produtoId, $quantidade, $campos['mensagem']]);
        redirecionar('contato.php?enviado=1#form-sucesso');
    }
}

$pagina = [
    'titulo'    => 'Contato | Gelos Carvalho',
    'descricao' => 'Entre em contato com a Gelos Carvalho — faça seu pedido pelo telefone, WhatsApp ou formulário.',
    'ativo'     => 'contato.php',
    'audio'     => 'aquatic_ambience.mp3',
    'css'       => 'css/contato.css',
];
require __DIR__ . '/includes/site_topo.php';
?>

    <!-- WhatsApp Flutuante -->
    <a href="https://wa.me/<?= e(cfg('telefone_link')) ?>" target="_blank" class="whatsapp-float" title="Fale pelo WhatsApp">💬</a>

    <!-- ======== MAIN ======== -->
    <main>

        <!-- PAGE HERO -->
        <section class="page-contato">
            <div class="cristais" aria-hidden="true">
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
                <div class="cristal"></div>
            </div>
            <span class="page-contato-label">Fale com a gente</span>
            <h1>Entre em Contato</h1>
            <p>Peça seu gelo, tire dúvidas ou nos mande uma mensagem. Respondemos rápido!</p>
        </section>

        <!-- CONTEÚDO CONTATO -->
        <div class="contato-conteudo">

            <div class="contato-grid animar">

                <!-- INFORMAÇÕES DE CONTATO -->
                <div>
                    <h2>Como nos<br>Encontrar</h2>
                    <p>Estamos disponíveis pelo WhatsApp, telefone ou pessoalmente na nossa fábrica, na Agrovila 9.</p>

                    <div style="margin-top:32px;">

                        <div class="contato-item">
                            <div class="contato-item-icone">📞</div>
                            <div class="contato-item-texto">
                                <h4>Telefone &amp; WhatsApp</h4>
                                <p><a href="tel:+<?= e(cfg('telefone_link')) ?>" style="color:var(--branco);"><?= e(cfg('telefone_exibicao')) ?></a></p>
                                <span>Ligue ou mande mensagem</span>
                            </div>
                        </div>

                        <div class="contato-item">
                            <div class="contato-item-icone">📍</div>
                            <div class="contato-item-texto">
                                <h4>Endereço</h4>
                                <p><?= e(cfg('endereco')) ?></p>
                                <span><?= e(cfg('regiao')) ?></span>
                            </div>
                        </div>

                        <div class="contato-item">
                            <div class="contato-item-icone">🕐</div>
                            <div class="contato-item-texto">
                                <h4>Horário de Funcionamento</h4>
                                <p><?= e(cfg('horario_resumo')) ?></p>
                                <span><?= e(cfg('horario_domingo')) ?></span>
                            </div>
                        </div>

                        <div class="contato-item">
                            <div class="contato-item-icone">🚗</div>
                            <div class="contato-item-texto">
                                <h4>Área de Entrega</h4>
                                <p><?= e(cfg('area_entrega')) ?></p>
                                <span><?= e(cfg('area_entrega_detalhe')) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Mapa placeholder -->
                    <div class="mapa-placeholder" style="padding: 20px; text-align: center;">
                        <h4>Agrovila 9 — Serra do Ramalho, BA</h4>
                        <p style="margin-bottom: 15px;">Bahia — Região do Vale do São Francisco</p>
                        
                        <div style="width: 100%; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.15); margin-bottom: 15px;">
                            <iframe 
                                src="https://maps.google.com/maps?q=Agrovila%209,%20Serra%20do%20Ramalho%20-%20BA&output=embed" 
                                width="100%" 
                                height="350" 
                                style="border:0; display:block;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                        <a href="https://www.google.com/maps/place/R.+Pernambuco,+778+-+Centro,+Serra+do+Ramalho+-+BA,+47630-041/@-13.5443156,-43.5774371,19z/data=!3m1!4b1!4m5!3m4!1s0x75be9976fe8d4a1:0xc565da5f3442310d!8m2!3d-13.5443169!4d-43.5767934?entry=ttu&g_ep=EgoyMDI2MDUyNy4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="btn btn-secundario" style="font-size:0.8rem; padding:10px 20px; display: inline-block; text-decoration: none;">
                            Ver no Google Maps 🗺️
                        </a>
                    </div>

                    <!-- Horários em tabela -->
                    <div style="margin-top:32px;">
                        <h3 style="font-family:var(--fonte-titulo); font-size:0.9rem; letter-spacing:1px; color:var(--azul-gelo); text-transform:uppercase; margin-bottom:16px;">📅 Horários Detalhados</h3>
                        <div class="horarios-tabela">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Dia</th>
                                        <th>Horário</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php foreach (consulta('SELECT * FROM horarios ORDER BY ordem, id') as $horario): ?>
                                    <tr>
                                        <td><?= e($horario['dia']) ?></td>
                                        <td><?= e($horario['horario']) ?></td>
                                        <td style="color:<?= $horario['aberto'] ? '#4ade80' : '#f87171' ?>;">● <?= $horario['aberto'] ? 'Aberto' : 'Consultar' ?></td>
                                    </tr>
<?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FORMULÁRIO -->
                <div class="form-card animar animar-delay-2">
                    <h3>✉️ Envie uma Mensagem</h3>

<?php if ($errosContato): ?>
                    <div role="alert" style="background:rgba(248,113,113,0.12); border:1px solid #f87171; border-radius:var(--raio); padding:16px; margin-bottom:20px; color:var(--branco-gelo); font-size:0.88rem;">
<?php foreach ($errosContato as $erroContato): ?>
                        <p><?= e($erroContato) ?></p>
<?php endforeach; ?>
                    </div>
<?php endif; ?>

<?php if (!$enviado): ?>
                    <form id="formulario-contato" method="post" action="contato.php" novalidate>
                        <?= csrf_campo() ?>
                        <div class="form-row">
                            <div class="form-grupo">
                                <label for="nome">Nome Completo *</label>
                                <input type="text" id="nome" name="nome" placeholder="Seu nome" maxlength="120" value="<?= e($campos['nome']) ?>" required>
                            </div>
                            <div class="form-grupo">
                                <label for="telefone">Telefone / WhatsApp *</label>
                                <input type="tel" id="telefone" name="telefone" placeholder="(77) 99999-9999" maxlength="30" value="<?= e($campos['telefone']) ?>" required>
                            </div>
                        </div>

                        <div class="form-grupo">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" placeholder="seu@email.com (opcional)" maxlength="160" value="<?= e($campos['email']) ?>">
                        </div>

                        <div class="form-grupo">
                            <label for="pedido">Tipo de Pedido</label>
                            <select id="pedido" name="pedido">
                                <option value="">Selecione...</option>
<?php foreach ($produtos as $produto): ?>
                                <option value="p<?= (int) $produto['id'] ?>" <?= $campos['pedido'] === 'p' . $produto['id'] ? 'selected' : '' ?>><?= e($produto['nome'] . ' — ' . moeda($produto['preco'])) ?></option>
<?php endforeach; ?>
<?php foreach (['ambos' => 'Ambos os Tamanhos', 'atacado' => 'Compra em Atacado', 'duvida' => 'Dúvida / Informação'] as $opcao => $rotulo): ?>
                                <option value="<?= $opcao ?>" <?= $campos['pedido'] === $opcao ? 'selected' : '' ?>><?= $rotulo ?></option>
<?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-grupo">
                            <label for="quantidade">Quantidade de Pacotes</label>
                            <input type="number" id="quantidade" name="quantidade" placeholder="Ex: 5" min="1" value="<?= e($campos['quantidade']) ?>">
                        </div>

                        <div class="form-grupo">
                            <label for="mensagem">Mensagem *</label>
                            <textarea id="mensagem" name="mensagem" placeholder="Escreva sua mensagem, pedido ou dúvida aqui..." maxlength="2000" required rows="5"><?= e($campos['mensagem']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primario form-btn">
                            📩 Enviar Mensagem
                        </button>
                    </form>
<?php endif; ?>

                    <!-- Mensagem de sucesso -->
                    <div class="form-sucesso<?= $enviado ? ' visivel' : '' ?>" id="form-sucesso">
                        <div class="sucesso-icone">✅</div>
                        <h4>Mensagem Enviada!</h4>
                        <p>Obrigado pelo contato! Em breve a família Carvalho entrará em contato com você.</p>
                    </div>

                    <!-- Aviso alternativo -->
                    <div style="margin-top:24px; padding:16px; background:rgba(14,165,233,0.08); border-radius:var(--raio); border:1px dashed rgba(14,165,233,0.3);">
                        <p style="color:var(--cinza-medio); font-size:0.82rem; text-align:center;">
                            💬 Prefere o <strong style="color:var(--azul-gelo);">WhatsApp</strong>?
                            <a href="https://wa.me/<?= e(cfg('telefone_link')) ?>" target="_blank" style="color:var(--azul-gelo); font-weight:700; display:block; margin-top:8px;">
                                Clique aqui — <?= e(cfg('telefone_exibicao')) ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="divisor"></div>

            <!-- PERGUNTAS FREQUENTES -->
            <div class="secao-titulo animar">
                <span class="label">Dúvidas Frequentes</span>
                <h2>FAQ — Perguntas &amp; Respostas</h2>
            </div>

            <div class="cards-grid animar" style="margin-bottom:40px;">
<?php foreach (consulta('SELECT * FROM faqs WHERE ativo = 1 ORDER BY ordem, id') as $faq): ?>
                <article class="card">
                    <h3 style="font-size:0.9rem; margin-bottom:10px;">❓ <?= e($faq['pergunta']) ?></h3>
                    <p><?= e($faq['resposta']) ?></p>
                </article>
<?php endforeach; ?>
            </div>

        </div>
    </main>

    <!-- ======== FOOTER ======== -->
<?php require __DIR__ . '/includes/site_rodape.php'; ?>

    <script>
        /* Animações */
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visivel');
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.animar').forEach(el => observer.observe(el));


        /* Máscara de telefone */
        const campoTelefone = document.getElementById('telefone');
        if (campoTelefone) {
            campoTelefone.addEventListener('input', function() {
                let v = this.value.replace(/\D/g, '');
                if (v.length <= 10) {
                    v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                } else {
                    v = v.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                }
                this.value = v;
            });
        }
    </script>
</body>
</html>
