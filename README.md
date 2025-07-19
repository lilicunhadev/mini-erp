# Mini ERP - Sistema de Gestão de Produtos

Um sistema ERP simplificado desenvolvido em **PHP puro** seguindo o padrão **MVC**, para controle de Pedidos, Produtos, Cupons e Estoque.

## 🚀 Funcionalidades Implementadas

### ✅ CRUD de Produtos
- Criar produtos com nome, preço, descrição e estoque inicial
- Listar todos os produtos com informações de estoque
- Visualizar detalhes completos do produto
- Editar informações do produto e atualizar estoque
- Suporte a variações de produto (cores, tamanhos, etc.)

### ✅ Sistema de Carrinho
- Adicionar produtos ao carrinho com controle de estoque
- Carrinho gerenciado em sessão PHP
- Validação de quantidade disponível em estoque

### ✅ Interface Responsiva
- Design moderno com Bootstrap 5
- Paleta de cores personalizada em tons terrosos
- Interface intuitiva e responsiva

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8+ (puro, sem frameworks)
- **Banco de Dados:** MySQL 8.0
- **Frontend:** Bootstrap 5, Bootstrap Icons
- **Arquitetura:** MVC (Model-View-Controller)

## 📁 Estrutura do Projeto

```
mini-erp/
├── public/               # Document root
│   └── index.php        # Front controller
├── app/
│   ├── core/            # Classes base (Controller, Model)
│   ├── controllers/     # Controllers (ProdutoController)
│   ├── models/          # Models (Produto)
│   └── views/           # Views (layout, produtos)
├── config/
│   ├── database.php     # Configurações do banco
│   └── routes.php       # Definição de rotas
├── database/
│   └── schema.sql       # Schema do banco de dados
└── .gitignore
```

## 🗄️ Banco de Dados

O sistema utiliza as seguintes tabelas:

- **produtos** - Informações básicas dos produtos
- **produto_variacoes** - Variações (cor, tamanho, etc.)
- **estoque** - Controle de estoque por produto/variação
- **pedidos** - Cabeçalho dos pedidos
- **pedido_itens** - Itens individuais dos pedidos
- **cupons** - Cupons de desconto com regras

## ⚙️ Instalação e Configuração

### Pré-requisitos
- PHP 8.0+
- MySQL 8.0+
- Servidor web (Apache/Nginx) ou PHP built-in server

### Passos para instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/lilicunhadev/mini-erp.git
   cd mini-erp
   ```

2. **Configure o banco de dados:**
   - Crie um banco MySQL chamado `mini_erp`
   - Execute o arquivo `database/schema.sql`
   - Ajuste as credenciais em `config/database.php`

3. **Inicie o servidor:**
   ```bash
   php -S localhost:8000 -t public
   ```

4. **Acesse o sistema:**
   Abra o navegador em `http://localhost:8000`

## 🎨 Paleta de Cores

- **Fundo principal:** `#eeeedd` (bege claro)
- **Elementos principais:** `#77773c` (verde oliva)
- **Títulos:** `#55552b` (verde escuro)
- **Texto da navbar:** `#f6f6ee` (bege muito claro)

## 📋 Próximas Funcionalidades

- [ ] Carrinho completo com visualização e remoção de itens
- [ ] Cálculo de frete baseado em regras
- [ ] Integração com API ViaCEP para validação de endereços
- [ ] Sistema de cupons de desconto
- [ ] Finalização de pedidos com envio de e-mail
- [ ] Webhook para atualizações de status de pedidos

## 👩‍💻 Desenvolvimento

Projeto desenvolvido seguindo boas práticas:
- Código limpo e bem estruturado
- Padrão MVC para organização
- Validações de entrada e segurança
- Interface responsiva e acessível

---

**Status:** Em desenvolvimento ativo
**Versão:** 1.0.0 (CRUD de Produtos)
