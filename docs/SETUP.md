# 🔧 Setup Guide

## Local Development Setup

### Prerequisites
- Docker Desktop
- Git
- Composer (optional)

### Step-by-Step

1. **Clone the repository**
   ```bash
   git clone https://github.com/JasonMoyo/Docker-Webs.git
   cd Docker-Webs

Configure environment

bash
cp .env.example .env
# Edit .env with your database passwords

Build and run with Docker

bash
docker-compose up -d --build
Access the site

Website: http://localhost

phpMyAdmin: http://localhost:8081 (root/rootpassword123)

Install PHP dependencies (if needed)

bash
docker exec nqobileq_web composer install
Production Deployment on AWS EC2
Step 1: Launch EC2 Instance
Ubuntu 22.04 LTS

t2.micro (free tier)

Security group: Open ports 22, 80, 8081

Step 2: Install Docker
bash
sudo apt update
sudo apt install docker.io docker-compose -y
sudo usermod -aG docker ubuntu
# Logout and login again
Step 3: Deploy
bash
git clone https://github.com/JasonMoyo/Docker-Webs.git
cd Docker-Webs
docker-compose up -d --build
Step 4: Verify
bash
docker-compose ps
curl http://localhost
Troubleshooting
Database Connection Issues
bash
# Check if database is running
docker-compose ps db

# Test connection
docker exec nqobileq_web php -r "include 'config.php'; echo 'Connected!';"
Permission Issues
bash
docker exec nqobileq_web chmod -R 755 /var/www/html
docker exec nqobileq_web chmod -R 777 /var/www/html/vendor
View Logs
bash
docker-compose logs -f web
text

---

### **File 2: `API.md` - API Documentation (if you have APIs)**

```bash
nano docs/API.md
markdown
# 📡 API Documentation

## Booking Endpoints

### Submit Booking
**POST** `/submit_booking.php`

**Parameters:**
| Field | Type | Required |
|-------|------|----------|
| name | string | Yes |
| email | string | Yes |
| phone | string | Yes |
| service_type | string | Yes |
| preferred_date | date | No |
| message | text | No |

**Response:** Redirects to confirmation page

### Submit Package Booking
**POST** `/submit_package.php`

**Parameters:**
| Field | Type | Required |
|-------|------|----------|
| name | string | Yes |
| email | string | Yes |
| phone | string | Yes |
| package_name | string | Yes |

## Authentication

### Login
**POST** `/login.php`

**Parameters:**
| Field | Type | Required |
|-------|------|----------|
| email | string | Yes |
| password | string | Yes |

### Register
**POST** `/register.php`

**Parameters:**
| Field | Type | Required |
|-------|------|----------|
| full_name | string | Yes |
| email | string | Yes |
| phone | string | Yes |
| password | string | Yes |
