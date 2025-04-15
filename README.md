# Travel Orders - Laravel 11 + Vue 3 + Docker

## 📋 Requisitos

- **Docker** 20.10+
- **Docker Compose** 2.0+

## 🚀 Instalação e Execução Local

### 1. Clonar o repositório

```bash
git clone https://github.com/RafaelRilen/Viagem.git
cd Viagem
```

### 2. Configurar variáveis de ambiente

Crie um arquivo `.env` dentro da pasta `backend/` com base no `.env.example`. Exemplo:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=db
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

QUEUE_CONNECTION=database

JWT_SECRET=CHAVE_SECRETA_JWT_GERADA
```

### 3. Subir os containers

```bash
docker-compose build --no-cache
docker-compose up -d
```

### 4. Instalar dependências

```bash
# Backend
docker exec -it viagem-backend composer install

Gere a chave do app e o secret JWT:
docker exec -it viagem-backend php artisan key:generate
docker exec -it viagem-backend php artisan jwt:secret

# Frontend
cd frontend
npm install
```

### 5. Rodar as migrations e seeders

```bash
docker exec -it viagem-backend php artisan migrate
docker exec -it viagem-backend php artisan migrate:fresh --seed
```

---

## 🧪 Testes

```bash
docker exec -it viagem-backend php artisan test
```

---

## 🌐 Endpoints Principais

### Autenticação (JWT - Bearer Token)

- `POST /api/register` - Registro de usuário
- `POST /api/login` - Login e geração do token JWT
- `POST /api/logout` - Logout (requer token)

### Pedidos de Viagem

- `GET /api/orders` - Lista pedidos com filtros opcionais (`status`, `destination`, `start_date`, `end_date`) (autenticado)
- `POST /api/orders` - Cria um novo pedido (autenticado)
- `GET /api/orders/{id}` - Detalhes do pedido (autenticado)
- `PATCH /api/orders/{id}/status` - Atualiza status (admin) - Nas seeds é gerado um usuário admin, ele está no arquivo UserSeeder.
- `PATCH /api/orders/{id}/cancel` - Cancela pedido (autenticado)

---

## 🗂 Estrutura de Pastas

```bash
backend/
├── app/
│   ├── DTOs/
│   ├── Http/
│   ├── Models/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
├── routes/
│   └── api.php

frontend/
├── public/
├── src/
│   ├── components/
│   ├── pages/
│   ├── router/
│   ├── store/
│   └── App.vue
├── vite.config.ts
```

---

## 🐳 docker-compose.yml

```yaml
services:
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: viagem-backend
    volumes:
      - ./backend:/var/www
    ports:
      - "8000:8000"
    depends_on:
      - db
    environment:
      DB_HOST: db
      DB_PORT: 3306
    working_dir: /var/www

  frontend:
    build:
      context: ./frontend
    container_name: viagem-frontend
    ports:
      - "5173:5173"
    volumes:
      - ./frontend:/app
      - /app/node_modules

  db:
    image: mysql:8.0
    container_name: viagem-db
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
    volumes:
      - ./docker/mysql:/var/lib/mysql
    ports:
      - "3309:3306"
```

---

## 🔐 Autenticação JWT

Este projeto utiliza autenticação via JWT (Bearer Token). Após o login, você deve armazenar o token recebido e utilizá-lo no cabeçalho das requisições:

```http
Authorization: Bearer SEU_TOKEN_AQUI
```

No frontend, o token é salvo no `localStorage` e injetado automaticamente via `axios`.

---

## 📎 Comandos úteis

### Backend

```bash
# Criar uma nova migration
docker-compose exec backend php artisan make:migration nome_migration

# Rodar seeders
docker-compose exec backend php artisan db:seed

# Acessar container
docker-compose exec backend bash
```

### Banco de Dados

```bash
# Acessar banco MySQL
docker-compose exec db mysql -uroot -p

# Resetar ambiente completamente
docker-compose down -v
rm -rf ./docker/mysql/*
```

---

## 📝 Licença

Projeto open source sob [Licença MIT](https://opensource.org/licenses/MIT).
