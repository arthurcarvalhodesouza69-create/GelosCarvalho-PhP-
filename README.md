# Gelos Carvalho — Site e CMS

Site institucional e sistema de gerenciamento de conteúdo (CMS) da **Gelos Carvalho**, empresa familiar de fabricação e venda de gelo na Agrovila 9, em Serra do Ramalho (BA).

O site estático da primeira etapa agora é alimentado por um banco MySQL: o que é alterado no painel administrativo aparece diretamente nas páginas públicas, e o formulário de contato envia as mensagens para o painel.

Projeto da disciplina de Programação para Web — Instituto Federal Baiano, Campus Bom Jesus da Lapa. Professor: Tiago do Carmo Nogueira.

## Tecnologias

- PHP 8 procedural, com PDO e consultas preparadas
- MySQL / MariaDB
- HTML5, CSS3, JavaScript e Bootstrap 5 (painel)

## Como executar (XAMPP)

1. Copie a pasta do projeto para `C:\xampp\htdocs\geloscarvalho`.
2. Inicie o **Apache** e o **MySQL** no painel do XAMPP.
3. Acesse `http://localhost/phpmyadmin`, abra a aba **Importar** e envie o arquivo `database/gelos_carvalho.sql`. O banco `gelos_carvalho` é criado com o conteúdo inicial do site.
4. Abra o site em `http://localhost/geloscarvalho/` e o painel em `http://localhost/geloscarvalho/admin/`.

Se o MySQL usar outro usuário ou senha, ajuste `config/database.php` ou defina as variáveis de ambiente `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS`.

### Acesso inicial ao painel

| E-mail | Senha |
|---|---|
| `admin@geloscarvalho.com` | `Gelos@2026` |

Troque essa senha em **Usuários** antes de publicar o sistema em um servidor real.

## Módulos do painel

| Módulo | O que gerencia | Onde aparece |
|---|---|---|
| Mensagens e pedidos | Contatos recebidos e seu andamento | Formulário de Contato |
| Produtos | Preço, imagem, descrições, tópicos e comparativo | Início, Serviços, Contato e rodapé |
| Fotos da galeria | Imagens, legendas, categoria e tamanho | Galeria |
| Categorias da galeria | Filtros da galeria | Galeria |
| Equipe | Nome, cargo, descrição e foto | Sobre |
| Depoimentos | Autor, localidade, texto e nota | Galeria |
| Perguntas frequentes | Perguntas e respostas | Contato |
| Diferenciais | Cartões e valores da empresa | Início, Serviços e Sobre |
| Números | Estatísticas de destaque | Início e Sobre |
| Horários | Tabela de funcionamento | Contato |
| Dados da empresa | Telefone, endereço e textos gerais | Rodapé e Contato |
| Usuários | Administradores e editores | Somente administradores |

As listagens têm paginação e, conforme o módulo, busca e filtros. Os formulários validam os dados no servidor.

## Segurança

- Senhas armazenadas com `password_hash` e conferidas com `password_verify`.
- Páginas do painel protegidas por sessão, com redirecionamento para o login.
- Logout por POST, com destruição da sessão e do cookie.
- Token CSRF em todos os formulários e consultas preparadas em todas as operações com dados do usuário.
- Saídas escapadas com `htmlspecialchars` contra XSS.
- Uploads validados pelo conteúdo real do arquivo (JPG, PNG ou WebP, até 5 MB), gravados com nome aleatório em uma pasta que não executa scripts.
- E-mail único, confirmação de senha, bloqueio da exclusão do próprio usuário e garantia de ao menos um administrador.

## Banco de dados

O script `database/gelos_carvalho.sql` cria 13 tabelas InnoDB em `utf8mb4`, com chaves primárias e índices únicos.

| Relacionamento | Regra ao excluir |
|---|---|
| `produtos` 1:N `produto_itens` | Remove os tópicos do produto (`CASCADE`) |
| `categorias_galeria` 1:N `fotos_galeria` | Impede excluir categoria em uso (`RESTRICT`) |
| `produtos` 1:N `mensagens` | Mantém a mensagem sem o produto (`SET NULL`) |

## Organização do código

```text
├── index.php, sobre.php, servicos.php, galeria.php, contato.php   Páginas públicas
├── config/database.php       Credenciais do banco
├── includes/
│   ├── conexao.php           Conexão PDO e funções de consulta
│   ├── funcoes.php           Escape, sessão, CSRF, avisos e upload
│   ├── site.php              Consultas de conteúdo do site
│   └── site_topo.php, site_rodape.php
├── admin/
│   ├── login.php, logout.php, index.php
│   ├── usuarios.php, configuracoes.php
│   ├── produtos.php, fotos.php, categorias.php, equipe.php, depoimentos.php,
│   │   faqs.php, diferenciais.php, estatisticas.php, horarios.php, mensagens.php
│   ├── includes/
│   │   ├── auth.php          Autenticação e controle de acesso
│   │   ├── layout.php        Estrutura visual e paginação
│   │   └── crud.php          Motor comum de cadastro, listagem, edição e exclusão
│   └── assets/               CSS e JavaScript do painel
├── database/gelos_carvalho.sql
├── uploads/                  Imagens enviadas pelo painel
├── css/, img/, audio/
```

Cada módulo de conteúdo declara sua tabela, colunas, campos e filtros em um array e usa o motor de `admin/includes/crud.php`. Usuários e dados da empresa têm páginas próprias porque seguem regras específicas.

## Autoria

Arthur Carvalho de Souza
