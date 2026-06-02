# DOCUMENTAÇÃO TÉCNICA: PLATAFORMA PETHEALTH & CARE

## 1. Introdução e Justificativa

O gerenciamento de estabelecimentos que unificam serviços estéticos (petshop) e atendimentos médicos (clínica veterinária) exige um controle operacional rigoroso. A maioria dos sistemas acadêmicos foca em modelos simplificados que atendem apenas cães e gatos, ignorando a crescente demanda por pets não-convencionais (aves, répteis e pequenos roedores), que necessitam de cuidados e restrições específicas durante o manejo.

Os principais problemas enfrentados por esses negócios são operacionais: a falta de acompanhamento visual do status do animal no estabelecimento (se ele já tomou banho, se está aguardando o tutor), a ausência de um canal direto de avisos operacionais entre veterinários e tosadores, e o esquecimento de clientes recorrentes que deixam de frequentar o local.

> **Justificativa:** A plataforma **PetHealth & Care** foi desenvolvida para centralizar a gestão de clínicas e petshops sob o modelo "concierge" (onde a equipe interna gerencia os dados). O sistema se justifica por entregar, através de uma arquitetura limpa em PHP e PostgreSQL, ferramentas práticas de retenção de clientes por análise de dados, um prontuário operacional de avisos para a segurança do animal, uma linha do tempo visual para o acompanhamento dos serviços e um registro de auditoria interna, elevando o nível de controle do estabelecimento sem elevar a complexidade de desenvolvimento.

---

## 2. Objetivos

### 2.1 Objetivo Geral
Desenvolver um sistema web para gestão e acompanhamento de cuidados em clínicas veterinárias e petshops, utilizando PHP estruturado, persistência em banco de dados PostgreSQL através de PDO, e interfaces responsivas em HTML5 e CSS3.

### 2.2 Objetivos Específicos
* Implementar um sistema de autenticação seguro que separe as interfaces de cada membro da equipe (Recepcionistas, Veterinários, Tosadores) e dos clientes (Tutores).
* Estruturar um banco de dados flexível capaz de registrar animais de múltiplas espécies (tradicionais e não-convencionais), com suporte a campos de observações críticas de comportamento e saúde.
* Criar uma esteira de status para os agendamentos, permitindo vincular o profissional responsável e acompanhar o progresso do atendimento através de uma linha do tempo visual (Timeline).
* Desenvolver um módulo gerencial (Dashboard) contendo indicadores financeiros (faturamento mensal) e estatísticos (serviço mais procurado do mês).
* Construir um sistema automático de logs de atividades para auditoria interna das ações realizadas pelos funcionários.

---

## 3. Definição de Escopo

### 3.1 Funcionalidades Inclusas (Escopo)
* **Autenticação Multi-Nível:** Controle de login para equipe interna (com base em cargos: Veterinário, Tosador, Recepcionista) e tutores via sessões do PHP (`$_SESSION`).
* **CRUD de Clientes e Pets:** Cadastro unificado de tutores e animais de qualquer espécie, contendo alertas visuais de comportamento (ex: "Ansioso", "Agressivo", "Requer Cuidado").
* **Esteira de Atendimento (Timeline) por Profissional:** Atualização de agendamentos associados a um funcionário específico, transitando pelos status: *Agendado*, *Em Atendimento*, *Pronto para Retirada* e *Finalizado*.
* **Mural Operacional de Alertas:** Registro de avisos internos sobre a rotina da clínica ou restrições de pets específicos em atendimento no dia.
* **Dashboard Gerencial:** Cards com faturamento bruto, total de serviços prestados e exibição do serviço de maior demanda no mês corrente.
* **Alerta de Retorno:** Listagem de animais ausentes do estabelecimento há mais de 20 dias com acionamento direto via API pública do WhatsApp.
* **Upload de Fotos:** Upload de imagens de perfil para os pets, humanizando a interface.
* **Log de Auditoria Interna:** Histórico automatizado e imutável que registra qual funcionário realizou qualquer alteração crítica no sistema.

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
| **RF02** | Gerenciamento de Tutores | O sistema deve permitir o cadastro, edição, listagem e exclusão de clientes. | Recepcionista / Admin |
| **RF03** | Registro Multiespécies | O sistema deve cadastrar pets registrando Nome, Idade, Espécie (livre), Foto de perfil e marcadores de comportamento. | Equipe Interna |
| **RF04** | Catálogo de Serviços | O sistema deve gerenciar os serviços oferecidos, divididos entre as categorias 'Clínica' e 'Estética'. | Recepcionista / Admin |
| **RF05** | Esteira com Responsável | O sistema deve criar agendamentos, associar o funcionário executor (Veterinário/Tosador) e atualizar a Timeline do serviço. | Equipe Interna |
| **RF06** | Quadro de Avisos Operacionais | Permite à equipe publicar notas técnicas internas ou avisos sobre restrições de atendimento dos animais do dia. | Equipe Interna |
| **RF07** | Dashboard de Indicadores | O sistema deve exibir em tempo real gráficos de faturamento e destacar o "Serviço Mais Procurado". | Equipe Interna |
| **RF08** | Filtro de Pets Ausentes | O sistema deve rastrear e listar animais sem registros de serviços há mais de 20 dias, com link direto de contato. | Recepcionista / Admin |
| **RF09** | Log de Atividades | O sistema deve gerar registros automáticos (Logs) a cada inserção ou mudança de status realizada pela equipe. | Sistema / Admin |
| **RF10** | Tela de Leitura do Tutor | O cliente, ao logar, deve visualizar qual funcionário está cuidando do seu pet, o status na linha do tempo e o histórico. | Cliente |

### 4.2 Requisitos Não-Funcionais (RNF)

* **RNF01 - Segurança:** Criptografia obrigatória de senhas no banco de dados utilizando a função nativa `password_hash()` do PHP.
* **RNF02 - Tecnologia e Banco de Dados:** Backend construído em PHP 8+, utilizando a extensão PDO conectada a um banco de dados relacional PostgreSQL.
* **RNF03 - Interface e Estética:** Front-end responsivo construído em HTML5 e CSS3 puro, utilizando componentes modernos de Grid e Flexbox para simular barras de progresso (Timeline) e painéis de indicadores (Cards).
* **RNF04 - Integridade:** Uso de restrições relacionais (`FOREIGN KEY` com `ON DELETE CASCADE`) para garantir a consistência do banco de dados ao excluir registros pai.

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
| `tipo` | VARCHAR(20) | NOT NULL CHECK... | Grupo de acesso: 'equipe' ou 'cliente'. |
| `cargo` | VARCHAR(30) | NOT NULL CHECK... | Papel real: 'Veterinário', 'Tosador', 'Recepcionista', 'Cliente'. |
| `criado_em` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data de cadastro do usuário. |

### 5.2 Tabela: `pets`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do animal. |
| `cliente_id` | INT | FOREIGN KEY (`usuarios.id`) | ID do tutor proprietário (Cascata). |
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
| `categoria` | VARCHAR(20) | NOT NULL CHECK... | Diferencia a área: 'estetica' ou 'clinica'. |
| `preco` | NUMERIC(10,2) | NOT NULL | Valor financeiro cobrado pelo serviço. |

### 5.4 Tabela: `agendamentos`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do agendamento. |
| `pet_id` | INT | FOREIGN KEY (`pets.id`) | ID do pet que receberá o atendimento (Cascata). |
| `servico_id` | INT | FOREIGN KEY (`servicos.id`) | ID do serviço/procedimento. |
| `funcionario_id` | INT | FOREIGN KEY (`usuarios.id`) | ID do funcionário alocado para o atendimento. |
| `data_hora` | TIMESTAMP | NOT NULL | Data e hora exata reservada. |
| `status` | VARCHAR(30) | DEFAULT 'Agendado' | Controle da esteira: 'Agendado', 'Em Atendimento', 'Pronto para Retirada', 'Finalizado'. |

### 5.5 Tabela: `mural_avisos`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do aviso. |
| `usuario_id` | INT | FOREIGN KEY (`usuarios.id`) | ID do funcionário que publicou o aviso. |
| `titulo` | VARCHAR(100) | NOT NULL | Título direto do recado. |
| `conteudo` | TEXT | NOT NULL | Descrição detalhada do aviso interno. |
| `data_publicacao`| TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Momento em que o aviso foi postado. |

### 5.6 Tabela: `log_atividades`
| Campo | Tipo | Restrições | Descrição |
| :--- | :--- | :--- | :--- |
| `id` | SERIAL | PRIMARY KEY | Identificador único do registro de log. |
| `usuario_id` | INT | FOREIGN KEY (`usuarios.id`) | ID do funcionário que realizou a ação. |
| `acao` | TEXT | NOT NULL | Frase descritiva da ação realizada no sistema. |
| `data_hora` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora exata do acontecimento. |

---

## 6. Arquitetura do Software e Estrutura de Arquivos

### 6.1 Árvore do Diretório do Projeto

```text
petshop/
│
├── config/
│   └── conexao.php          # Configuração do PDO para conexão com o PostgreSQL
│
├── includes/
│   ├── header.php           # Cabeçalho global (Navbar com base no cargo logado)
│   └── footer.php           # Rodapé global (Inclui os scripts e fechamentos)
│
├── assets/                  # Arquivos estáticos de estilização e comportamento
│   ├── css/
│   │   └── style.css        # Design customizado e responsivo do painel
│   ├── js/
│   │   └── main.js          # Controla elementos visuais dinâmicos da interface
│   └── uploads/             # Pasta física que armazenará as fotos dos pets
│
├── views/                   # Telas do Sistema (Apenas visualização e formulários)
│   ├── login.php            # Portal de acesso unificado
│   ├── dashboard.php        # Painel Geral Admin (Gráficos, Alerta de Retorno e Logs)
│   ├── clientes.php         # Gestão de Tutores (CRUD)
│   ├── pets.php             # Gestão de Pets de todas as espécies (CRUD)
│   ├── servicos.php         # Gestão de Serviços Clínicos e Estéticos (CRUD)
│   ├── agendamentos.php     # Central de marcações vinculando profissionais
│   ├── mural.php            # Interface do Mural de Avisos da equipe (CRUD Interno)
│   └── painel-tutor.php     # Área exclusiva do cliente (Histórico e Linha do Tempo)
│
└── actions/                 # Inteligência de Back-end (Scripts PHP puros)
    ├── login-action.php     # Validação de credenciais e controle de sessão
    ├── logout.php           # Encerramento seguro da sessão
    ├── cliente-action.php   # Processa dados dos tutores e gera log
    ├── pet-action.php       # Processa dados, faz o upload da foto e gera log
    ├── servico-action.php   # Processa dados do catálogo de serviços e gera log
    ├── agenda-action.php    # Processa agendamentos, atualiza a Timeline e gera log
    └── mural-action.php     # Processa a inserção e exclusão de avisos e gera log

```

---

## 7. Mapeamento de Telas e Fluxo de Navegação

### 7.1 Mapeamento Visual e Componentes

#### 1. views/dashboard.php (A Central Gerencial Adaptativa)

Esta é a tela de maior impacto visual do projeto, organizada em uma malha (Grid) responsiva:

* Bloco Superior (Cards de Indicadores): Pequenos quadros coloridos contendo o faturamento bruto do mês, o total de atendimentos do dia e um destaque para o "Serviço Mais Procurado" do mês corrente.
* Bloco Lateral (Alerta de Retorno Ativo): Uma lista estilizada com os cards dos pets "sumidos" há mais de 20 dias. Cada linha exibirá a mini foto da espécie, o nome do pet, o telefone do tutor e um botão customizado do WhatsApp Web.
* Bloco Inferior (Fita de Auditoria): Uma lista compacta de leitura rápida que exibe os últimos 5 registros de log_atividades, mostrando a transparência das ações da equipe no sistema.

#### 2. views/painel-tutor.php (O Espaço do Cliente)
Focado no minimalismo para o cliente final:

* Meus Pets: Exibe cartões visuais para cada animal do cliente com sua respectiva foto de perfil, espécie e o marcador de comportamento/humor atual.
* Esteira de Atendimento (Timeline Visual): Exibe uma barra de progresso horizontal que acende dinamicamente de acordo com o status atual do pet no estabelecimento, exibindo também o nome do profissional (ex: Dr. Roberto) que está cuidando do animal naquele momento: [Agendado] ➔ [Em Atendimento] ➔ [Pronto para Retirada] ➔ [Finalizado].