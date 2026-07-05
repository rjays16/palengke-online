# Palengke Online — Backend Setup

## Prerequisites

- **Docker** + **Docker Compose** (for running the backend)
- **Git**

---

## Step 1: Clone the Repository

```bash
git clone <repository-url>
cd palengke-online/palengke-online-backend
```

---

## Step 2: Environment Configuration

```bash
cp .env.example .env
```

Default settings (no changes needed):
```
DB_DATABASE=palengke_db
DB_USERNAME=palengke_user
DB_PASSWORD=palengke_password
DB_ROOT_PASSWORD=root_password
```

---

## Step 3: Start Docker Containers

```bash
./run docker compose up -d
```

This starts three containers:
- **palengke-app** — PHP-FPM (Laravel application)
- **palengke-nginx** — Nginx web server (serves on port **8000**)
- **palengke-mysql** — MySQL database (port **3307**)

Verify:
```bash
./run docker compose ps
```

---

## Step 4: Install PHP Dependencies

```bash
./run composer install
```

---

## Step 5: Generate Application Key

```bash
./run php artisan key:generate
```

---

## Step 6: Create Storage Symlink

```bash
./run php artisan storage:link
```

---

## Step 7: Run Database Migrations

```bash
./run php artisan migrate
```

---

## Step 8: Seed the Database

```bash
./run php artisan db:seed
```

---

## Step 9: Verify Backend is Running

```bash
curl http://localhost:8000
```

Test a public endpoint:
```bash
curl http://localhost:8000/api/countries
```

Expected: a JSON array of available countries.

---

## Common Commands

| Command | Description |
|---------|-------------|
| `./run docker compose up -d` | Start all containers |
| `./run docker compose down` | Stop all containers |
| `./run docker compose ps` | Check container status |
| `./run docker compose logs` | View container logs |
| `./run php artisan migrate` | Run migrations |
| `./run php artisan db:seed` | Seed database |
| `./run php artisan migrate:rollback` | Rollback last migration |
| `./run composer install` | Install PHP dependencies |
| `./run composer require <package>` | Add a Composer package |
| `./run bash` | Open bash shell inside container |
| `./run php artisan tinker` | Open Laravel tinker |

---

## Troubleshooting

### Port 8000 already in use
Edit `docker-compose.yml` and change the port mapping:
```yaml
ports:
  - "8001:80"   # use any available port
```

### Permission errors
```bash
./run bash -c "chmod -R 777 storage bootstrap/cache"
```

### Migration fails ("table already exists")
```bash
./run php artisan migrate --force
```

### MySQL connection refused
The app container may have started before MySQL was ready:
```bash
./run docker compose restart app
```

---

## Importing a Database Dump

If you receive an updated `.sql` dump:

```bash
./run docker compose exec -T mysql mysql -u root -proot_password palengke_db < dump.sql
```

---

## Accessing the Backend

- **API Base URL:** `http://localhost:8000`
- **MySQL (external):** `localhost:3307` / user: `palengke_user` / pass: `palengke_password` / db: `palengke_db`
- **MySQL (inside Docker):** hostname `mysql`, port `3306`
