# DOCUMENTAÇÃO TÉCNICA: PLATAFORMA PETHEALTH & CARE

## 1. Introdução e Justificativa

O gerenciamento de estabelecimentos que unificam serviços estéticos (petshop) e atendimentos médicos (clínica veterinária) exige um controle operacional rigoroso. A maioria dos sistemas acadêmicos foca em modelos simplificados que atendem apenas cães e gatos, ignorando a crescente demanda por pets não-convencionais (aves, répteis e pequenos roedores), que necessitam de cuidados e restrições específicas durante o manejo.

Os principais problemas enfrentados por esses negócios são operacionais: a falta de acompanhamento visual do status do animal no estabelecimento (se ele já tomou banho, se está aguardando o tutor), a ausência de um canal direto de avisos operacionais entre veterinários e tosadores baseado em níveis de gravidade, e o esquecimento de clientes recorrentes que deixam de frequentar o local.

> **Justificativa:** A plataforma **PetHealth & Care** foi desenvolvida para centralizar a gestão de clínicas e petshops sob o modelo "concierge" (onde a equipe interna gerencia os dados). O sistema se justifica por entregar, através de uma arquitetura limpa em PHP e PostgreSQL, ferramentas práticas de retenção de clientes por análise de dados, um prontuário operacional de avisos com triagem de urgência para a segurança do animal, um painel administrativo dinâmico para regulação de preços/equipe, uma linha do tempo visual para o acompanhamento dos serviços e um registro de auditoria interna, elevando o nível de controle do estabelecimento sem elevar a complexidade de desenvolvimento.

---

## 2. Objetivos

### 2.1 Objetivo Geral
Desenvolver um sistema web para gestão e acompanhamento de cuidados em clínicas veterinárias e petshops, utilizando PHP estruturado, persistência em banco de dados PostgreSQL através de PDO, e interfaces responsivas em HTML5 e CSS3.

### 2.2 Objetivos Específicos
* Implementar um sistema de autenticação seguro que separe as interfaces de cada membro da equipe (Recepcionistas, Veterinários, Tosadores, Gerentes) e dos clientes (Tutores).
* Estruturar um banco de dados flexível capaz de registrar animais de múltiplas espécies (tradicionais e não-convencionais), com suporte a campos de observações críticas de comportamento e saúde.
* Criar uma esteira de status para os agendamentos, permitindo vincular o profissional responsável e acompanhar o progresso do atendimento através de uma linha do tempo visual (Timeline).
* Desenvolver um módulo gerencial (Dashboard) contendo indicadores financeiros (faturamento mensal), distribuição estatística de serviços por gráficos dinâmicos de rosca e um feed consolidado de avisos do plantão.
* Centralizar o controle administrativo do negócio permitindo o cadastro de novos colaboradores com senhas seguras padronizadas e a regulação de preços do catálogo de serviços em tempo real via modais.

---

## 3. Definição de Escopo

### 3.1 Funcionalidades Inclusas (Escopo)
* **Autenticação Multi-Nível:** Controle de login para equipe interna (com base em cargos regulados pelo banco) e tutores via sessões do PHP (`$_SESSION`).
* **CRUD de Clientes e Pets:** Cadastro unificado de tutores e animais de qualquer espécie, contendo alertas visuais de comportamento (ex: "Ansioso", "Agressivo", "Requer Cuidado").
* **Esteira de Atendimento (Timeline) por Profissional:** Atualização de agendamentos associados a um funcionário específico, transitando pelos status: *Agendado*, *Em Atendimento*, *Pronto para Retirada* e *Finalizado*.
* **Mural Operacional de Alertas com Níveis de Urgência:** Canal de comunicação interna onde notas e avisos são fixados com cores e badges dinâmicas baseadas na gravidade selecionada (*Baixa - Geral*, *Média - Importante*, *Alta - Crítico*).
* **Dashboard Gerencial Integrado:** Bloco de KPIs com faturamento bruto real e total de atendimentos diários; Gráfico de rosca gerado dinamicamente via *Chart.js* mapeando a distribuição dos serviços mais procurados; e um mini-feed dos últimos avisos do mural.
* **Alerta de Retorno:** Listagem de animais ausentes do estabelecimento há mais de 20 dias com acionamento direto via API pública do WhatsApp, pré-configurando mensagens de re-fidelização personalizadas com o nome do tutor e do pet.
* **Painel de Configurações Administrativas:** Interface em abas (Tabs) exclusiva para gerenciamento do catálogo de serviços (com distinção obrigatória de categorias de atendimento) e contratação/demissão de membros do corpo técnico da equipe.
* **Upload de Fotos:** Upload de imagens de perfil para os pets, humanizando a interface.

### 3.2 Funcionalidades Exclusas (Fora do Escopo)
* Emissão de Notas Fiscais Eletrônicas (NF-e).
* Sistema de Vendas de Produtos Físicos (Frente de Caixa / PDV).
* Integração com gateways de pagamento ou maquininhas de cartão.

---

## 4. Engenharia de Requisitos

### 4.1 Requisitos Funcionais (RF)

| Código | Requisito Funcional | Descrição Detalhada | Nível de Acesso |
| :--- | :--- | :--- | :--- |
| **RF01** | Autenticação Personalizada | O sistema deve validar o acesso diferenciando as interfaces com base nos cargos da equipe interna e nos tutores. | Todos |
| **RF02** | Gerenciamento de Tutores | O sistema deve permitir o cadastro, edição, listagem e exclusão de clientes. | Recepcionista / Equipe |
| **RF03** | Registro Multiespécies | O sistema deve cadastrar pets registrando Nome, Idade, Espécie (livre), Foto de perfil e marcadores de comportamento. | Equipe Interna |
| **RF04** | Catálogo de Serviços e Categorias | O sistema deve gerenciar os serviços oferecidos, exigindo a divisão explícita entre as categorias 'clínica', 'estetica' ou 'outros'. | Admin / Gerente |
| **RF05** | Esteira com Responsável | O sistema deve criar agendamentos, associar o funcionário executor (Veterinário/Tosador) e atualizar a Timeline do serviço. | Equipe Interna |
| **RF06** | Quadro de Avisos por Urgência | Permite à equipe publicar notas técnicas internas associando uma classificação de risco/urgência (baixa, media, alta) com estilização dinâmica. | Equipe Interna |
| **RF07** | Dashboard de Indicadores e Gráficos | O sistema deve exibir gráficos de rosca em tempo real com a distribuição de demanda de serviços e cards de faturamento bruto mensal. | Equipe Interna |
| **RF08** | Filtro de Pets Ausentes | O sistema deve rastrear e listar animais sem registros de serviços há mais de 20 dias, com link direto de contato formatado para o WhatsApp. | Recepcionista / Equipe |
| **RF09** | Gestão de Acessos da Equipe | Permite a contratação e desligamento de membros do corpo técnico, fornecendo uma senha segura padrão criptografada (123456) no cadastro. | Admin / Gerente |
| **RF10** | Tela de Leitura do Tutor | O cliente, ao logar, deve visualizar qual funcionário está cuidando do seu pet, o status na linha do tempo e o histórico. | Cliente |

### 4.2 Requisitos Não-Funcionais (RNF)

* **RNF01 - Segurança:** Criptografia obrigatória de senhas no banco de dados utilizando a função nativa `password_hash()` do PHP com algoritmo padrão do sistema.
* **RNF02 - Tecnologia e Banco de Dados:** Backend construído em PHP 8+, utilizando a extensão PDO conectada a um banco de dados relacional PostgreSQL.
* **RNF03 - Interface e Estética:** Front-end responsivo construído em HTML5, CSS3 e Bootstrap 5, utilizando componentes de Grid, Flexbox, Modais, Janelas de Abas (Tabs) e injeção assíncrona de gráficos via biblioteca Chart.js.
* **RNF04 - Integridade Relacional:** Uso de restrições relacionais (`FOREIGN KEY` com `ON DELETE CASCADE`) e travas de consistência de domínio (`CHECK CONSTRAINTS`) para validação rigorosa de dados inseridos na camada de persistência.

---

## 5. Modelagem de Dados e Dicionário de Dados (PostgreSQL)

### 5.1 Tabela: `usuarios`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do usuário. |
| `nome` | VARCHAR(100) | NOT NULL | Nome completo do usuário. |
| `email` | VARCHAR(100) | NOT NULL, UNIQUE | E-mail utilizado para login. |
| `senha` | VARCHAR(255) | NOT NULL | Senha criptografada. |
| `telefone` | VARCHAR(20) | NULL | Telefone com DDD (usado no Alerta de Retorno). |
| `tipo` | VARCHAR(20) | NOT NULL | Grupo de acesso: 'equipe' ou 'cliente'. |
| `cargo` | VARCHAR(30) | NOT NULL, CHECK (cargo IN ('Veterinário', 'Tosador', 'Atendente', 'Recepcionista', 'Gerente')) | Papel real do usuário dentro da hierarquia da empresa. |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de cadastro do registro. |

### 5.2 Tabela: `pets`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do animal. |
| `cliente_id` | INT | FOREIGN KEY (`usuarios.id`) ON DELETE CASCADE | ID do tutor proprietário (Cascata). |
| `nome` | VARCHAR(50) | NOT NULL | Nome do pet. |
| `especie` | VARCHAR(50) | NOT NULL | Espécie do animal (ex: Cão, Gato, Cobra, Furão). |
| `raca` | VARCHAR(50) | DEFAULT 'SRD' | Raça do animal. |
| `data_nascimento`| DATE | NULL | Data de nascimento. |
| `comportamento` | VARCHAR(50) | DEFAULT 'Normal' | Marcador de humor/alerta (ex: 'Ansioso', 'Agressivo'). |
| `foto` | VARCHAR(255) | DEFAULT 'default_pet.png' | Caminho da imagem de perfil do pet no servidor. |

### 5.3 Tabela: `servicos`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do serviço. |
| `nome_servico` | VARCHAR(100) | NOT NULL | Nome do procedimento (ex: Banho Geral). |
| `categoria` | VARCHAR(20) | NOT NULL, CHECK (categoria IN ('clinica', 'estetica', 'outros')) | Segmentação operacional do serviço. |
| `preco` | NUMERIC(10,2) | NOT NULL | Valor financeiro cobrado pelo serviço. |

### 5.4 Tabela: `agendamentos`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do agendamento. |
| `pet_id` | INT | FOREIGN KEY (`pets.id`) ON DELETE CASCADE | ID do pet que receberá o atendimento (Cascata). |
| `servico_id` | INT | FOREIGN KEY (`servicos.id`) | ID do serviço/procedimento. |
| `funcionario_id` | INT | FOREIGN KEY (`usuarios.id`) | ID do funcionário alocado para o atendimento. |
| `data_hora` | TIMESTAMP | NOT NULL | Data e hora exata reservada (Tratada com funções temporais do Postgres). |
| `status` | VARCHAR(30) | DEFAULT 'Agendado' | Controle da esteira: 'Agendado', 'Em Atendimento', 'Pronto para Retirada', 'Finalizado'. |

### 5.5 Tabela: `mural_avisos`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do aviso. |
| `usuario_id` | INT | FOREIGN KEY (`usuarios.id`) | ID do funcionário que publicou o aviso. |
| `titulo` | VARCHAR(100) | NOT NULL | Título direto do recado. |
| `conteudo` | TEXT | NOT NULL | Descrição detalhada do aviso interno. |
| `urgencia` | VARCHAR(10) | DEFAULT 'baixa', CHECK (urgencia IN ('baixa', 'media', 'alta')) | Nível de risco da informação postada. |
| `data_publicacao`| TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Momento em que o aviso foi postado. |

---

## 6. Arquitetura do Software e Estrutura de Arquivos

```text
petshop/
│
├── config/
│   └── conexao.php          # Configuração do PDO para conexão com o PostgreSQL
│
├── includes/
│   ├── header.php           # Cabeçalho global (Navbar adaptativa com base no cargo logado)
│   └── footer.php           # Rodapé global (Inclui os scripts e fechamentos das tags)
│
├── assets/                  # Arquivos estáticos de estilização e comportamento
│   ├── css/
│   │   └── style.css        # Design customizado e responsivo do painel
│   ├── js/
│   │   └── main.js          # Elementos visuais e gatilhos comportamentais
│   └── uploads/             # Diretório físico das fotos dos pets no servidor
│
├── views/                   # Camada de Apresentação (Apenas visualização e formulários)
│   ├── login.php            # Portal de acesso unificado
│   ├── dashboard.php        # Painel Geral da Equipe (KPIs, Gráfico Chart.js, WhatsApp Ativo e Mural Resumido)
│   ├── clientes.php         # Gestão de Tutores (CRUD)
│   ├── pets.php             # Gestão de Pets de todas as espécies (CRUD)
│   ├── configuracoes.php    # Interface em Abas (Tabs) para Controle de Serviços e Cadastro/Exclusão de Equipe
│   ├── agendamentos.php     # Central de agendamentos vinculando profissionais executores
│   ├── mural.php            # Quadro completo do Mural de Avisos com filtros de Urgência por cards
│   └── painel-tutor.php     # Área exclusiva do cliente (Visualização da Linha do Tempo e dados do Pet)
│
└── actions/                 # Inteligência de Back-end (Scripts PHP puros para processamento)
    ├── login-action.php     # Validação de credenciais e controle de sessão
    ├── logout.php           # Encerramento seguro da sessão
    ├── cliente-action.php   # Processa dados dos tutores e gera logs
    ├── pet-action.php       # Processa dados, faz o upload físico da foto e gera logs
    ├── config-action.php    # Processa CRUD de serviços, alterações de preços e admissão/demissão da equipe
    ├── agenda-action.php    # Processa agendamentos, atualiza a Timeline e gera logs
    └── mural-action.php     # Processa a inserção de avisos (com campo urgência) e exclusão do quadro

```

## 7. Mapeamento de Telas e Fluxo de Navegação

### 7.1 Mapeamento Visual e Componentes

#### 1. views/dashboard.php (A Central Gerencial Adaptativa)
Esta é a tela de maior impacto operacional e analítico do projeto, estruturada em componentes responsivos do Bootstrap 5:
* **Cards de Indicadores (KPIs):** Dispostos na parte superior para leitura rápida do faturamento consolidado obtido por queries temporais (`date_trunc`), volume total de atendimentos do dia atual e o nome do serviço mais requisitado.
* **Distribuição de Serviços (Gráfico de Rosca):** Uma seção alimentada por uma query SQL de agrupamento (`GROUP BY`) que injeta dados assincronamente em um componente de rosca (`doughnut`) da biblioteca *Chart.js*, ilustrando a porcentagem de demanda de cada serviço da empresa.
* **Avisos do Plantão (Mural Integrado):** Exibição compacta das 3 notas internas mais recentes. O componente altera dinamicamente sua cor (`alert-danger`, `alert-warning`, `alert-info`) e adiciona animações piscantes nos ícones se a nota for cadastrada com nível de urgência "Alta".
* **Alerta de Retorno Ativo:** Painel que varre o banco localizando pets ausentes por um intervalo maior que 20 dias através de subqueries negativas (`NOT IN`). Renderiza um botão direto para o WhatsApp do cliente que dispara uma mensagem pré-definida de engajamento contendo variáveis nativas do banco de dados.

#### 2. views/configuracoes.php (Painel Administrativo do Negócio)
Interface unificada em Abas Dinâmicas (`nav-tabs`) que isola processos gerenciais:
* **Aba Serviços e Preços:** Tabela responsiva de listagem que aciona Modais individuais de ajuste rápido de preços (enviando o ID oculto via formulário `POST` para o `config-action.php`) e inserção de novos serviços exigindo a escolha da categoria.
* **Aba Equipe / Colaboradores:** Listagem do corpo técnico (Veterinários, Tosadores, Atendentes, etc). Apresenta um modal completo de inserção que gera credenciais automáticas com a senha padronizada `123456` criptografada por `password_hash`. Possui um gatilho de segurança lógica que impede que o funcionário logado exclua a si mesmo por acidente.

#### 3. views/painel-tutor.php (O Espaço do Cliente)
Focado no minimalismo e clareza para o cliente final (tutor):
* **Meus Pets:** Exibe cartões visuais para cada animal vinculado ao tutor logado com sua respectiva foto de perfil, espécie e o marcador de comportamento/humor atual (ex: "Ansioso" em vermelho, alertando cuidado).
* **Esteira de Atendimento (Timeline Visual):** Exibe uma barra de progresso horizontal construída em CSS que acende dinamicamente de acordo com o status atual do pet no estabelecimento (`Agendado` ➔ `Em Atendimento` ➔ `Pronto para Retirada` ➔ `Finalizado`), exibindo de forma transparente o nome do profissional que está cuidando do animal naquele momento.

---

## 8. Como Rodar o Projeto Localmente

Siga o passo a passo abaixo para configurar e executar a plataforma **PetHealth & Care** na sua máquina.

### 📋 Pré-requisitos
1. **Servidor Web:** XAMPP, WampServer ou Laragon (com PHP 8.0 ou superior instalado).
2. **Banco de Dados:** PostgreSQL instalado e configurado no sistema.

---

### 🚀 Passo 1: Clonar ou Copiar o Projeto
Mova a pasta completa do projeto `petshop/` para o diretório de arquivos públicos do seu servidor local:
* No XAMPP: `C:\xampp\htdocs\petshop\`
* No WampServer: `C:\wamp64\www\petshop\`

### 💾 Passo 2: Criar o Banco de Dados no PostgreSQL
Abra o terminal do seu sistema (Prompt de Comando / PowerShell) ou o **SQL Shell (psql)** do Postgres e crie um banco de dados vazio chamado `pethealth`:

```sql
CREATE DATABASE pethealth;