# Gym Management Platform

A full-featured gym management system built with PHP, MySQL, and Docker. Manage courses, equipment, and staff through a modern web interface.

---

## Features

- **User Authentication**: Login and registration with role-based access (admin/staff)
- **Course Management**: Create, edit, view, and schedule fitness courses
- **Equipment Tracking**: Inventory management with condition monitoring and maintenance scheduling
- **Course-Equipment Linking**: Assign equipment to specific courses
- **Data Export**: Export functionality for reports
- **Responsive Design**: Modern UI with dark theme and glassmorphism effects

---

## Tech Stack

- **Backend**: PHP 8.x with PDO
- **Database**: MySQL 8.0
- **Web Server**: Nginx
- **Containerization**: Docker and Docker Compose
- **Database Admin**: phpMyAdmin (optional)

---

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git

---

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd gym-project
```

### 2. Configure Environment Variables

Copy the example environment file and modify it with your settings:

```bash
cp .env.example .env
```

Edit `.env` and configure the following variables:

```env
# Application Settings
APP_NAME="Gym Management Platform"
APP_ENV=development
APP_DEBUG=true
APP_TIMEZONE=UTC

# Database Configuration
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=gym_management
DB_USERNAME=gym_user
DB_PASSWORD=your_secure_password

# MySQL Root Password
MYSQL_ROOT_PASSWORD=your_root_password

# Port Mappings
NGINX_PORT=8080
MYSQL_PORT=3306
PHPMYADMIN_PORT=8081

# Session Configuration
SESSION_LIFETIME=120
SESSION_SECURE=false
```

**Important**: Change the default passwords before deploying to production.

### 3. Start the Application

```bash
docker-compose up -d
```

This command will:
- Build the PHP container with required extensions
- Start MySQL with automatic database initialization
- Launch Nginx as the web server
- Start phpMyAdmin for database management

### 4. Verify Installation

Wait approximately 30-60 seconds for MySQL to initialize, then verify all services are running:

```bash
docker-compose ps
```

All services should show status "Up (healthy)".

---

## Accessing the Application

| Service     | URL                          | Description              |
|-------------|------------------------------|--------------------------|
| Application | http://localhost:8080        | Main web interface       |
| phpMyAdmin  | http://localhost:8081        | Database administration  |

Ports can be changed in the `.env` file.

---

## Default Credentials

### Application Users

| Username | Email           | Password | Role  |
|----------|-----------------|----------|-------|
| admin    | admin@gym.com   | password | Admin |
| staff1   | staff@gym.com   | password | Staff |

**Warning**: Change these credentials immediately after first login.

### Database Access

Use the credentials from your `.env` file to access MySQL directly or via phpMyAdmin.

---

## Database Schema

The application uses four main tables:

- **users**: User accounts with roles (admin/staff)
- **courses**: Fitness class scheduling and details
- **equipment**: Gym equipment inventory
- **course_equipment**: Junction table linking courses to equipment

The schema is automatically created on first run from `sql/database.sql`.

### Manual Database Setup (Without Docker)

If running without Docker, import the schema manually:

```bash
mysql -u your_username -p your_database < sql/database.sql
```

---

## Project Structure

```
gym-project/
├── docker/
│   ├── mysql/
│   │   └── my.cnf              # MySQL configuration
│   ├── nginx/
│   │   └── default.conf        # Nginx configuration
│   └── php/
│       ├── Dockerfile          # PHP container build
│       └── php.ini             # PHP configuration
├── includes/
│   ├── auth/                   # Authentication functions
│   ├── courses/                # Course-related functions
│   ├── equipment/              # Equipment-related functions
│   ├── course_equipment/       # Course-equipment linking
│   ├── export/                 # Export functionality
│   ├── config.php              # Application configuration
│   ├── db.php                  # Database connection
│   ├── functions.php           # Helper functions
│   ├── header.php              # Page header template
│   ├── footer.php              # Page footer template
│   └── sidebar.php             # Navigation sidebar
├── public/
│   ├── assets/                 # CSS, JS, images
│   ├── courses/                # Course management pages
│   ├── equipment/              # Equipment management pages
│   ├── course-equipment/       # Equipment assignment pages
│   ├── export/                 # Export pages
│   ├── index.php               # Dashboard
│   ├── login.php               # Login page
│   ├── logout.php              # Logout handler
│   └── register.php            # Registration page
├── sql/
│   └── database.sql            # Database schema and seed data
├── docker-compose.yml          # Docker services configuration
├── .env.example                # Environment template
└── .gitignore                  # Git ignore rules
```

---

## Common Commands

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### View Logs
```bash
docker-compose logs -f
```

### Rebuild Containers
```bash
docker-compose up -d --build
```

### Reset Database
```bash
docker-compose down -v
docker-compose up -d
```

**Note**: The `-v` flag removes volumes, deleting all database data.

---

## Configuration

### PHP Settings

Modify `docker/php/php.ini` to change PHP configuration:
- Upload limits
- Memory limits
- Error reporting
- Timezone

### MySQL Settings

Modify `docker/mysql/my.cnf` to change MySQL configuration:
- Character set
- Buffer sizes
- Connection limits

### Nginx Settings

Modify `docker/nginx/default.conf` to change web server configuration:
- Server name
- Timeouts
- Fast CGI settings

---

## Troubleshooting

### Database Connection Failed

1. Ensure MySQL container is healthy:
   ```bash
   docker-compose ps mysql
   ```
2. Check MySQL logs:
   ```bash
   docker-compose logs mysql
   ```
3. Verify `.env` credentials match `docker-compose.yml` defaults

### Port Already in Use

Change the port in `.env`:
```env
NGINX_PORT=8888
PHPMYADMIN_PORT=8889
```

Then restart:
```bash
docker-compose down
docker-compose up -d
```

### Permission Issues

Ensure proper file ownership:
```bash
sudo chown -R www-data:www-data public/
```

---

## Production Deployment

Before deploying to production:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Change all default passwords
3. Set `SESSION_SECURE=true` if using HTTPS
4. Configure proper SSL/TLS certificates
5. Review and harden Nginx configuration
6. Set up proper backup procedures for the database

---

## License

This project is provided as-is for educational and development purposes.
