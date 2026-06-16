# DOCUMENTAÇÃO OPERACIONAL: PLATAFORMA PETHEALTH & CARE

## 1. O que é o sistema e por que ele foi criado?

Gerenciar em um único lugar um petshop (serviços estéticos) e uma clínica veterinária (atendimentos médicos) é um desafio diário grande. A maioria dos sistemas do mercado ou ignora o lado clínico ou simplifica tudo achando que o negócio só atende cães e gatos. Isso deixa na mão os tutores e profissionais que lidam com pets não-convencionais (como aves, répteis e pequenos roedores), que exigem cuidados, alimentação e manejos completamente diferentes.

No dia a dia, os maiores problemas são puramente operacionais:
* O tosador não sabe se o animal que está na baia tem alguma restrição médica séria trazida pelo veterinário.
* O tutor fica na sala de espera ansioso sem saber se o seu pet já tomou banho ou se está aguardando o atendimento.
* Clientes antigos somem do estabelecimento e a recepção simplesmente esquece de mandar uma mensagem para trazê-los de volta.

> **A nossa solução:** A plataforma **PetHealth & Care** nasceu para ser o coração desse ecossistema, operando no modelo "concierge" (onde a equipe interna cuida de toda a entrada de dados). Desenvolvido com um código PHP limpo e a robustez do PostgreSQL, o sistema entrega um painel visual prático. Ele avisa sobre animais agressivos ou ansiosos, cria um mural de recados urgentes para o plantão, exibe uma linha do tempo em tempo real para os donos acompanharem o atendimento e automatiza mensagens de retorno para clientes sumidos via WhatsApp. É o controle total da operação, sem complicar a vida de quem está usando.

---

## 2. Objetivos

### 2.1 Objetivo Geral
Criar um sistema web amigável, rápido e seguro para centralizar a rotina de cuidados de clínicas e petshops, usando PHP estruturado no backend, banco de dados PostgreSQL para persistência segura e telas responsivas que funcionam bem em computadores e celulares.

### 2.2 Objetivos Práticos
* **Portais Inteligentes:** Separar o acesso para que cada profissional (Recepcionistas, Veterinários, Tosadores, Gerentes) veja apenas o que importa para o seu trabalho, além de uma área exclusiva para os tutores.
* **Foco no Bem-Estar do Pet:** Permitir o cadastro de qualquer espécie de animal, deixando marcadores visuais explícitos caso o pet precise de cuidado extra (ex: se é arisco ou cardiopata).
* **Organização do Plantão:** Criar uma esteira de agendamento onde cada serviço é vinculado a um funcionário responsável, mudando de status conforme o progresso real.
* **Visão de Negócio:** Disponibilizar um painel gerencial simples para ver o faturamento do mês e um gráfico direto que mostra quais serviços estão trazendo mais dinheiro.
* **Controle Administrativo:** Permitir que o gerente mude preços de serviços e cadastre novos funcionários com senhas padrões de forma rápida através de janelas pop-up (modais).

---

## 3. O que o sistema faz (Escopo)

### 3.1 Funcionalidades que fazem parte do sistema:
* **Acesso Controlado por Cargo:** Sistema de login seguro que reconhece quem está entrando e molda as ferramentas do menu de acordo com a função da pessoa.
* **Cadastro Completo de Clientes e Pets:** Registro unificado dos donos e seus animais, com suporte a upload de fotos para humanizar o atendimento.
* **Linha do Tempo do Atendimento:** Acompanhamento do pet dentro da loja passo a passo: *Agendado* ➔ *Em Atendimento* ➔ *Pronto para Retirada* ➔ *Finalizado*.
* **Mural de Avisos Urgentes:** Um espaço na tela inicial onde a equipe fixa lembretes cruciais para o dia, destacados por cores de acordo com a gravidade (*Geral, Importante ou Crítico*).
* **Painel Estatístico Avançado:** Gráficos interativos (estilo pizza) que mostram o comportamento das vendas e cartões com o faturamento bruto mensal.
* **Resgate de Clientes (Alerta de Retorno):** O sistema varre o banco e lista animais que não aparecem há mais de 20 dias, gerando um link que abre o WhatsApp com uma mensagem carinhosa já digitada para o tutor.
* **Ajustes Rápidos do Sistema:** Uma tela de configurações exclusiva para gerenciar os preços da clínica/estética e o cadastro de colaboradores técnicos.

### 3.2 O que não faz parte do sistema (Fora de Escopo):
* Emissão de Notas Fiscais Eletrônicas (NF-e).
* Controle de estoque de mercadorias físicas (Rações, brinquedos, coleiras).
* Maquininha de cartão integrada ou gateways de pagamento online.

---

## 4. Regras e Requisitos do Sistema

### 4.1 O que o sistema deve entregar (Requisitos Funcionais)

* **RF01 (Acesso Protegido):** O sistema precisa validar as credenciais e garantir que um tutor não acesse o painel financeiro ou o cadastro de outros pets.
* **RF02 (Gestão de Clientes):** A recepção deve conseguir cadastrar ou remover tutores.
* **RF03 (Prontuário Multiespécies):** O cadastro de pets deve aceitar qualquer texto no campo espécie para abranger animais exóticos, além de alertas comportamentais.
* **RF04 (Tabela de Preços Dinâmica):** O sistema deve aceitar novos serviços divididos obrigatoriamente entre as categorias de mercado: 'clínica', 'estética' ou 'outros'.
* **RF05 (Vínculo Operacional):** Todo agendamento precisa de um funcionário responsável associado para que ele saiba o que deve executar.
* **RF06 (Comunicação Interna):** O mural deve destacar visualmente avisos críticos (como "Faltou luz na sala de cirurgia") para chamar atenção imediata.
* **RF07 (Leitura Financeira):** O dashboard precisa somar as vendas concluídas do mês e atualizar os gráficos automaticamente.
* **RF08 (Ações de Marketing):** A lista de pets ausentes deve puxar o telefone formatado do tutor para agilizar o clique de contato.
* **RF09 (Segurança de Equipe):** Ao cadastrar um funcionário, o sistema gera a senha padrão `123456`. O sistema não permite que um administrador exclua o próprio perfil logado.
* **RF10 (Transparência com o Cliente):** O tutor, ao logar no celular ou computador, deve ver uma barra de progresso mostrando exatamente onde o seu animal de estimação está.

### 4.2 Detalhes Técnicos de Bastidores (Requisitos Não-Funcionais)

* **Segurança de Senhas:** Nenhuma senha é salva em texto limpo. Usamos a função `password_hash()` do PHP para transformá-las em códigos criptografados irreversíveis.
* **Linguagem e Banco:** Todo o motor do sistema roda em PHP 8 ou superior, conversando com o banco de dados PostgreSQL através da camada de segurança PDO.
* **Visual Moderno e Fluido:** A interface foi toda montada com Bootstrap 5, usando estruturas leves de Grid e Flexbox, ícones do FontAwesome e gráficos gerados pela biblioteca Chart.js.
* **Banco de Dados Inteligente:** As tabelas do banco possuem travas de segurança (`CHECK CONSTRAINTS`) e exclusão em cascata. Se um cliente for deletado, os pets dele são removidos juntos de forma limpa, evitando lixo no banco.

---

## 5. Estrutura do Banco de Dados (Como as informações conversam)

### 5.1 Usuários (`usuarios`)
Guarda os dados de todas as pessoas do sistema, sejam funcionários ou clientes.
* `id`: Número único de identificação (Gerado automaticamente).
* `nome` / `email` / `senha`: Credenciais básicas de acesso e contato.
* `telefone`: Guardado com DDD para o envio de mensagens.
* `tipo`: Define se é 'equipe' ou 'cliente'.
* `cargo`: Papel real no dia a dia ('Veterinário', 'Tosador', 'Atendente', 'Recepcionista', 'Gerente' ou 'Cliente'). Protegido por uma trava de validação no banco.

### 5.2 Animais (`pets`)
O prontuário de identificação de cada animal atendido.
* `id`: Código do pet.
* `cliente_id`: Aponta diretamente para quem é o dono do animal (Se o dono sumir, o pet é deletado em cascata).
* `nome` / `especie` / `raca`: Identidade do pet.
* `comportamento`: Alerta de temperamento ('Normal', 'Ansioso', 'Agressivo', etc.).
* `foto`: Nome do arquivo de imagem salvo na pasta de uploads.

### 5.3 Catálogo (`servicos`)
A tabela de preços dos procedimentos oferecidos pelo estabelecimento.
* `id`: Código do serviço.
* `nome_servico`: O nome do procedimento (Ex: "Vacina Quádrupla Felina").
* `categoria`: Classificação direta ('clinica', 'estetica' ou 'outros').
* `preco`: Valor cobrado (Formato numérico decimal).

### 5.4 Agenda (`agendamentos`)
Onde a mágica acontece, unindo o pet, o serviço e o profissional.
* `id`: Código da marcação.
* `pet_id` / `servico_id`: Qual animal vai receber qual procedimento.
* `funcionario_id`: Qual membro da equipe vai executar a tarefa.
* `data_hora`: Momento exato marcado.
* `status`: O estágio atual na esteira ('Agendado', 'Em Atendimento', 'Pronto para Retirada', 'Finalizado').

### 5.5 Avisos (`mural_avisos`)
Recados dinâmicos deixados pela equipe.
* `id`: Código do aviso.
* `usuario_id`: Quem escreveu o recado.
* `titulo` / `conteudo`: O texto do aviso.
* `urgencia`: Nível de risco selecionado ('baixa', 'media' ou 'alta').

---

## 6. Organização dos Arquivos do Projeto

O projeto foi dividido seguindo uma lógica organizada: as telas visuais ficam separadas dos scripts que fazem os cálculos e salvam os dados no banco.

```text
petshop/
│
├── config/
│   └── conexao.php          # Onde o PHP se conecta ao banco PostgreSQL via PDO
│
├── includes/
│   ├── header.php           # Barra de navegação do topo (muda conforme quem está logado)
│   └── footer.php           # Fechamento das páginas e carregamento dos scripts visuais
│
├── assets/                  # Tudo o que é visual estático do sistema
│   ├── css/style.css        # Estilos customizados da nossa linha do tempo e painéis
│   ├── js/main.js           # Funções que dão vida aos elementos da tela
│   └── uploads/             # Pasta viva que recebe as fotos que os usuários enviam dos pets
│
├── views/                   # As telas do sistema (O que o usuário vê e interage)
│   ├── login.php            # Tela de entrada única para funcionários e clientes
│   ├── dashboard.php        # O painel de controle (Gráficos, KPIs e alertas do WhatsApp)
│   ├── clientes.php         # Tela para cadastrar e listar os tutores (CRUD)
│   ├── pets.php             # Espaço para gerenciar os animais (CRUD)
│   ├── configuracoes.php    # Painel administrativo de abas para controlar preços e equipe
│   ├── agendamentos.php     # Central de marcações e controle de status
│   ├── mural.php            # Mural de recados completo filtrado por importância
│   ├── alterar-senha.php    # Permite o usuário alterar a própria senha
│   └── painel-tutor.php     # O painel exclusivo e limpo para o cliente ver seu pet
│
└── actions/                 # Os Motores do Sistema (Scripts PHP puros que processam dados)
    ├── login-action.php     # Confere a senha criptografada e inicia a sessão
    ├── logout.php           # Fecha a sessão com segurança e desloga o usuário
    ├── cliente-action.php   # Insere, edita ou remove tutores no banco
    ├── pet-action.php       # Processa o cadastro do pet e salva a foto na pasta de uploads
    ├── config-action.php    # Ajusta preços, cria serviços e contrata/demite funcionários
    ├── agenda-action.php    # Cria agendamentos e atualiza os passos da linha do tempo
    ├── resetar-senha-action.php    # Reseta a senha do usuário pra 123456 (Apenas o Gerente pode)
    └── mural-action.php     # Publica recados com urgência ou limpa o quadro de avisos
```
---

## 7. Como as Telas Funcionam na Prática

### 7.1 Detalhes Visuais de Cada Painel

#### 1. Painel Principal (`views/dashboard.php`)
É a central onde a equipe passa a maior parte do tempo. Ela foi desenhada para dar um panorama completo do negócio em segundos:
* **Indicadores Rápidos:** Cartões coloridos no topo mostram o dinheiro que entrou no mês e quantos animais estão sendo atendidos hoje.
* **Gráfico de Demanda (Chart.js):** Um gráfico de rosca lindo que mostra visualmente quais serviços são os mais procurados, ajudando o gerente a planejar promoções ou focar em contratações.
* **Mural de Recados Compacto:** Mostra os 3 avisos mais recentes da clínica. Se houver algum aviso de nível "Alta", ele brilha na tela em vermelho para chamar atenção total do plantão.
* **Gatilho de Re-fidelização (WhatsApp):** Uma lista inteligente com os animais sumidos há mais de 20 dias. Ao lado de cada pet, há um botão verde que, ao ser clicado, abre o WhatsApp Web com uma mensagem pronta: *"Olá [Nome do Tutor], faz tempo que o [Nome do Pet] não vem nos visitar para um carinho e cuidados. Vamos agendar um horário?"*.

#### 2. Painel de Configurações (`views/configuracoes.php`)
Uma interface administrativa moderna dividida em duas abas limpas e fáceis de navegar:
* **Aba Serviços:** Mostra a lista de preços atual. O administrador pode clicar em um pequeno lápis para abrir um modal pequeno e alterar o preço do serviço instantaneamente sem recarregar a página toda.
* **Aba Equipe:** Lista os funcionários contratados. Permite adicionar novos veterinários ou tosadores informando o e-mail, gerando o acesso deles de forma imediata com a senha padrão. 

#### 3. Área do Tutor (`views/painel-tutor.php`)
Uma tela pensada para trazer paz de espírito para o cliente. Ela exibe os cartões com as fotos dos pets do tutor logado e uma barra de progresso horizontal em tempo real. O tutor vê o status mudando e descobre exatamente quem é o profissional (ex: "Dr. Marcos") que está cuidando do seu bichinho naquele momento.

---

## 8. Como Executar o Projeto

### 8.1 Clonar o Repositório
git clone https://github.com/gabi0403/petshop.git

Entre na pasta do projeto:

cd petshop

--- 

### 8.2 Pré-requisitos
Tenha instalado no computador:

PHP 8+
PostgreSQL

Verifique se os comandos funcionam no terminal:

php -v
psql --version

--- 

### 8.3 Criar o Banco de Dados

O dump do banco está em:

config/dump_pethealth2.sql

Crie o banco:

createdb -h localhost -p 5432 -U postgres petshop_php

Importe o dump:

psql -h localhost -p 5432 -U postgres -d petshop_php -f config/dump_pethealth2.sql

--- 

### 8.4 Rodar o Projeto

Na pasta raiz do projeto:

php -S 127.0.0.1:8080

--- 

### 8.5 Acessar no Navegador

Abra: http://10.87.38.10:8080/index.php

Credenciais de Teste

Administrador
Login: roberto@pethealth.com
Senha: 123456

Cliente
Login: cliente@gmail.com
Senha: 123456