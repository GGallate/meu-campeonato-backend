# Meu Campeonato BackEnd

O Meu Campeonato é uma API RESTful desenvolvida em Laravel 11 para gerenciar e simular campeonatos de futebol no formato mata-mata (eliminatório). 

O sistema recebe 8 times, gera o chaveamento automaticamente (Quartas de Final, Semifinais, Disputa de 3º Lugar e Final) e utiliza um script nativo em Python para gerar os placares das partidas de forma randômica, simulando um serviço externo de inteligência artificial.

## O que o sistema faz (Features)

- **Cadastro de Times:** Permite o registro dos 8 times participantes do torneio.
- **Validação de Regras:** Impede a simulação do campeonato caso o número de times seja diferente de 8.
- **Integração PHP + Python:** Executa o script `teste.py` diretamente pelo backend para gerar os placares.
- **Sistema de Desempate:**
  1. Maior pontuação acumulada (saldo de gols do campeonato).
  2. Ordem de inscrição (time cadastrado primeiro vence).
- **Histórico de Campeonatos:** Salva o resultado final (Campeão, Vice, Terceiro Lugar e todo o chaveamento) no banco de dados para consultas futuras.
- **Testes Automatizados:** Cobertura de testes de integração (Feature Tests) para garantir a estabilidade das rotas e regras de negócio.

---

## Tecnologias Utilizadas
- **PHP 8+** (Laravel 11)
- **Python 3** (Script de simulação)
- **MySQL** (Banco de dados relacional)
- **Docker & Laravel Sail** (Containerização)
- **PHPUnit** (Testes automatizados)

---

## Como executar o projeto

Você pode rodar este projeto de duas maneiras: 

Utilizando Docker (Recomendado, pois o ambiente já contém o Python configurado) ou diretamente via PHP local.

### Opção 1: Executando via Docker **(Recomendado)**

**Pré-requisitos:** Ter o [Docker](https://www.docker.com/) e o [Composer](https://getcomposer.org/) instalados na sua máquina.

**1.** Clone o repositório:
```bash
git clone https://github.com/GGallate/meu-campeonato-backend.git
cd meu-campeonato-backend
```

**2.** Instale as dependências do PHP:
```bash
composer install
```

**3.** Configure o ambiente::

Copie o arquivo de configuração de exemplo e gere a chave da aplicação.
```bash
cp .env.example .env
php artisan key:generate
```

Atenção: Certifique-se de que no arquivo .env, as credenciais do banco estejam apontando para o Docker:

`DB_CONNECTION=mysql`

`DB_HOST=mysql`

`DB_PORT=3306`

**4.** Suba os containers (O Docker irá baixar o Linux, PHP, MySQL e Python automaticamente):
```bash
./vendor/bin/sail up -d --build
```

**5.** Crie as tabelas no banco de dados::
```bash
./vendor/bin/sail artisan migrate
```

O servidor estará rodando em `http://127.0.0.1`.

### Opção 2: Executando Localmente (Sem Docker)

**Pré-requisitos**: Ter o PHP 8.2+, Composer, MySQL e Python 3 instalados diretamente no seu sistema operacional.

**1.** Clone o repositório e acesse a pasta:
```bash
git clone https://github.com/GGallate/meu-campeonato-backend.git
cd meu-campeonato-backend
```

**2.** Instale as dependências:
```bash
composer install
```

**3.** Configure o ambiente e o banco de dados:
```bash
cp .env.example .env
php artisan key:generate
```

Crie um banco de dados no seu MySQL local (ex: `campeonato_api`). Em seguida, edite o arquivo .env com as suas credenciais:

`DB_HOST=127.0.0.1`

`DB_DATABASE=campeonato_api`

`DB_USERNAME=seu_usuario`

`DB_PASSWORD=sua_senha`


**4.** Rode as migrations:
```bash
php artisan migrate
```

**5.** Instale as dependências do PHP:
```bash
php artisan serve
```

O servidor estará rodando em `http://127.0.0.1:8000`.

## Endpoints da API (Rotas)
Uma collection completa com exemplos de requisições está disponível na raiz do projeto no arquivo ``postman_collection.json`` (Pode ser importada no Postman, Insomnia ou Thunder Client).

- `POST /api/times` - Cadastra um novo time. (Body: {"nome": "Nome do Time"})
- `GET /api/times` - Lista todos os times cadastrados.
- `POST /api/campeonatos/simular` - Executa o script Python, simula os jogos, salva no banco e retorna o resultado final.
- `GET /api/campeonatos` - Retorna o histórico de todos os campeonatos simulados anteriormente.

## Como rodar os Testes Automatizados

O projeto conta com testes de integração para garantir o funcionamento do fluxo de simulação e das validações de negócio.

**Se estiver usando Docker (Sail):**
```bash
./vendor/bin/sail artisan test
```

**Se estiver usando o ambiente local:**
```bash
php artisan test
```