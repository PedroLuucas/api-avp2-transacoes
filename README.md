# API de Transações

Uma API RESTful simples para gerenciar transações financeiras, desenvolvida em PHP utilizando o Slim Framework, com armazenamento de dados em MySQL. Esta API permite registrar transações, consultá-las, excluí-las e obter estatísticas em tempo real.

## 🚀 Começando

Estas instruções permitirão que você tenha uma cópia do projeto funcionando em sua máquina local para fins de desenvolvimento e teste.

### Pré-requisitos

Para executar este projeto, você precisará ter o seguinte software instalado em sua máquina:

* **PHP 8.1+**
* **Composer** (Gerenciador de dependências do PHP)
* **MySQL 5.7+**
* **Servidor Web** (Apache recomendado, como parte do XAMPP/WAMP/MAMP)
* **Postman** (ou similar, para testar a API)

### Instalação

Siga os passos abaixo para configurar o projeto:

1.  **Clone o Repositório ou Baixe o Projeto:**
    ```bash
    git clone [https://github.com/seu-usuario/api-avp2-transacoes.git](https://github.com/seu-usuario/api-avp2-transacoes.git)
    cd api-avp2-transacoes
    ```
    Ou, se você baixou os arquivos, coloque a pasta `api-avp2-transacoes` diretamente em `C:\xampp\htdocs\` (ou no diretório `htdocs` do seu ambiente).

2.  **Configurar o Servidor Web (Apache - Exemplo XAMPP):**
    * **Módulo `mod_rewrite`:** Certifique-se de que o módulo `mod_rewrite` esteja habilitado em seu arquivo `httpd.conf` (geralmente em `C:\xampp\apache\conf\httpd.conf`). Descomente a linha:
        ```apache
        LoadModule rewrite_module modules/mod_rewrite.so
        ```
    * **DocumentRoot:** Configure um Virtual Host ou o `DocumentRoot` principal do Apache para apontar para a pasta `public` do seu projeto.
        **Exemplo para `httpd-vhosts.conf` (recomendado para XAMPP/WAMP):**
        Abra `C:\xampp\apache\conf\extra\httpd-vhosts.conf` e adicione (ou ajuste) o seguinte:
        ```apache
        <VirtualHost *:80>
            DocumentRoot "C:/xampp/htdocs/api-avp2-transacoes/public"
            <Directory "C:/xampp/htdocs/api-avp2-transacoes/public">
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
            </Directory>
        </VirtualHost>
        ```
        *Substitua `C:/xampp/htdocs/api-avp2-transacoes` pelo caminho real da pasta do seu projeto.*
    * **Reinicie o Apache** após as alterações.

3.  **Instalar Dependências PHP:**
    * No terminal, navegue até a raiz do projeto (`/api-avp2-transacoes/`).
    * Execute o Composer para instalar as dependências e gerar o autoloader:
        ```bash
        composer install
        composer dump-autoload
        ```

4.  **Configurar o Banco de Dados MySQL:**

    * **Crie o Banco de Dados:**
        Acesse seu gerenciador MySQL (phpMyAdmin, DBeaver, ou linha de comando) e crie um novo banco de dados:
        ```sql
        CREATE DATABASE api_avp2_transacoes CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
        ```
    * **Crie a Tabela `transacoes`:**
        Selecione o banco `api_avp2_transacoes` e execute o script SQL para criar a tabela `transacoes`:
        ```sql
        USE api_avp2_transacoes;

        CREATE TABLE IF NOT EXISTS transacoes (
          id CHAR(36) NOT NULL PRIMARY KEY,
          valor DECIMAL(10,2) NOT NULL CHECK (valor >= 0),
          dataHora DATETIME NOT NULL
        );
        ```
    * **Configurações de Acesso:**
        Edite o arquivo `config/database.php` com as credenciais do seu banco de dados:
        ```php
        <?php
        return [
            'host' => 'localhost',
            'dbname' => 'api_avp2_transacoes',
            'user' => 'root', // Seu usuário MySQL
            'pass' => ''      // Sua senha MySQL (deixe vazio se não tiver)
        ];
        ```

## 🚀 Endpoints da API

A URL base para todos os endpoints é `http://localhost/`.

### `POST /transacao`

Recebe uma nova transação, valida-a e a armazena.

* **Método:** `POST`
* **URL:** `http://localhost/transacao`
* **Headers:** `Content-Type: application/json`
* **Body (raw JSON):**
    ```json
    {
      "id": "c1f1072a-9e1d-4f8e-a2f0-1a2b3c4d5e6f",
      "valor": 123.45,
      "dataHora": "2025-06-05T16:00:00Z"
    }
    ```
    *(No Postman, você pode usar `{{$guid}}` para o `id` e `{{$timestamp}}` para `dataHora`.)*
* **Respostas Esperadas:**
    * `201 Created` (sem corpo): Transação aceita e registrada.
    * `422 Unprocessable Entity` (sem corpo): Transação inválida (ex: valor negativo, data futura, ID duplicado).
    * `400 Bad Request` (sem corpo): JSON inválido na requisição.

### `GET /transacao/{id}`

Retorna os dados de uma transação específica pelo seu ID.

* **Método:** `GET`
* **URL:** `http://localhost/transacao/c1f1072a-9e1d-4f8e-a2f0-1a2b3c4d5e6f` (substitua pelo ID real)
* **Respostas Esperadas:**
    * `200 OK` (com JSON): Dados da transação encontrada.
        ```json
        {
          "id": "c1f1072a-9e1d-4f8e-a2f0-1a2b3c4d5e6f",
          "valor": "123.45",
          "dataHora": "2025-06-05 16:00:00"
        }
        ```
    * `404 Not Found` (sem corpo): Transação não encontrada.

### `DELETE /transacao`

Apaga todas as transações armazenadas no banco de dados.

* **Método:** `DELETE`
* **URL:** `http://localhost/transacao`
* **Respostas Esperadas:**
    * `200 OK` (sem corpo): Todas as transações foram apagadas com sucesso.

### `DELETE /transacao/{id}`

Apaga uma transação específica pelo seu ID.

* **Método:** `DELETE`
* **URL:** `http://localhost/transacao/c1f1072a-9e1d-4f8e-a2f0-1a2b3c4d5e6f` (substitua pelo ID real)
* **Respostas Esperadas:**
    * `200 OK` (sem corpo): A transação foi apagada com sucesso.
    * `404 Not Found` (sem corpo): Transação não encontrada.

### `GET /estatistica`

Retorna estatísticas sobre as transações que ocorreram nos últimos 60 segundos.

* **Método:** `GET`
* **URL:** `http://localhost/estatistica`
* **Respostas Esperadas:**
    * `200 OK` (com JSON): Estatísticas das transações recentes.
        ```json
        {
          "count": 10,
          "sum": 1234.56,
          "avg": 123.45,
          "min": 12.34,
          "max": 123.56
        }
        ```
    * **Atenção:** Quando não houverem transações nos últimos 60 segundos, todos os valores (`sum`, `avg`, `min`, `max`, `count`) serão `0`.

## ⚙️ Estrutura do Projeto

/api-avp2-transacoes/
│
├── public/                       
│   └── index.php                 
│
├── app/                          
│   ├── Controllers/              
│   │   └── TransacaoController.php
│   ├── Models/                   
│   │   └── Transacao.php
│   ├── Services/                 
│   │   └── EstatisticaService.php
│   ├── Routes/                   
│   │   └── routes.php
│   └── Utils/                    
│       ├── Validator.php
│       └── SimpleContainer.php   
│
├── config/
│   ├── database.php              
│   └── settings.php              
│
├── vendor/                       
│
├── .htaccess                     
├── composer.json                 
└── README.md                     