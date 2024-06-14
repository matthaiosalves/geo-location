# Geo localização

# Sistema de Rotas com Vue.js e Laravel

Este projeto utiliza Vue.js e Laravel para criar um sistema de gerenciamento de rotas.

## Pré-requisitos

Antes de iniciar, certifique-se de ter instalado em seu sistema:

- PHP
- Composer
- Node.js e npm
- Um servidor local como XAMPP ou WampServer

## Configuração Inicial

1. **Criação de Pasta**:
   Crie uma pasta em seu sistema para armazenar o projeto.

2. **Clonar e Instalar Dependências**:
   Abra o terminal, navegue até a pasta criada e execute os seguintes comandos para instalar as dependências necessárias:

   composer install # Instala as dependências do PHP
   npm install # Instala as dependências do Node.js

3. **Configuração do Ambiente:**
   Configure o arquivo .env na raiz do projeto com as informações do seu banco de dados:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desafio3
DB_USERNAME=root
DB_PASSWORD=

4. **Banco de Dados**

   Migrations:
   Para criar as tabelas no banco de dados, execute:

php artisan migrate

5. **Importar Dados CSV:**
   Antes de importar dados de arquivos CSV, instale a biblioteca league/csv:
   composer require league/csv

Em seguida, execute os comandos para importar os dados para o banco de dados:

php artisan import:csv app/public/rodovias.csv
php artisan import:csv app/public/uf.csv

6. **Executando o Projeto**

   Servidor de Desenvolvimento:
   Para iniciar o servidor Laravel:
   php artisan serve
   Em um outro terminal, inicie o servidor Vue.js
   npm run dev
   Acesse a URL fornecida pelo comando php artisan serve para visualizar o projeto.
