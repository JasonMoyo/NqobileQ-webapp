#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}   NqobileQ AWS EC2 Deployment Script   ${NC}"
echo -e "${BLUE}========================================${NC}"

# Function to print colored output
print_status() {
    echo -e "${YELLOW}[$(date +'%H:%M:%S')]${NC} $1"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if Docker is installed
print_status "Checking Docker installation..."
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Installing Docker..."
    sudo apt update
    sudo apt install docker.io -y
    sudo systemctl start docker
    sudo systemctl enable docker
    sudo usermod -aG docker $USER
    print_success "Docker installed successfully!"
else
    print_success "Docker is already installed"
fi

# Check if Docker Compose is installed
print_status "Checking Docker Compose installation..."
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Installing..."
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    print_success "Docker Compose installed successfully!"
else
    print_success "Docker Compose is already installed"
fi

# Pull latest code from GitHub (if using git)
print_status "Checking for git repository..."
if [ -d ".git" ]; then
    print_status "Pulling latest code from GitHub..."
    git pull origin main
    print_success "Code updated successfully!"
else
    print_status "Not a git repository, skipping pull..."
fi

# Stop and remove existing containers
print_status "Stopping and removing old containers..."
docker-compose down 2>/dev/null
docker stop nqobileq-web nqobileq-db nqobileq-phpmyadmin 2>/dev/null
docker rm nqobileq-web nqobileq-db nqobileq-phpmyadmin 2>/dev/null
print_success "Old containers removed"

# Update config.php for Docker (if needed)
print_status "Updating configuration files..."
if [ -f "config.php" ]; then
    # Backup config
    cp config.php config.php.backup.$(date +%Y%m%d_%H%M%S)
    
    # Update database host for Docker
    sed -i "s/define('DB_HOST', 'localhost'/define('DB_HOST', 'db'/" config.php
    sed -i "s/define('DB_HOST', '127.0.0.1'/define('DB_HOST', 'db'/" config.php
    
    print_success "Configuration updated"
fi

# Build and start containers
print_status "Building Docker images (this may take a few minutes)..."
docker-compose build

print_status "Starting Docker containers..."
docker-compose up -d

# Wait for containers to be ready
print_status "Waiting for containers to be ready..."
sleep 10

# Check if containers are running
print_status "Checking container status..."
if [ "$(docker-compose ps -q web)" ]; then
    print_success "Web container is running"
else
    print_error "Web container failed to start"
fi

if [ "$(docker-compose ps -q db)" ]; then
    print_success "Database container is running"
else
    print_error "Database container failed to start"
fi

# Show running containers
echo ""
print_status "Currently running containers:"
docker-compose ps

# Get EC2 public IP
EC2_IP=$(curl -s http://checkip.amazonaws.com)
if [ -z "$EC2_IP" ]; then
    EC2_IP="YOUR_EC2_PUBLIC_IP"
fi

# Display success message
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✅ DEPLOYMENT COMPLETE!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}Your website is now live!${NC}"
echo -e "${YELLOW}🌐 Website:${NC} http://$EC2_IP"
echo -e "${YELLOW}📊 phpMyAdmin:${NC} http://$EC2_IP:8081"
echo ""
echo -e "${BLUE}Database Credentials:${NC}"
echo -e "  Username: ${YELLOW}root${NC} or ${YELLOW}nqobileq_user${NC}"
echo -e "  Password: ${YELLOW}rootpassword123${NC} or ${YELLOW}userpassword123${NC}"
echo ""
echo -e "${BLUE}Useful Commands:${NC}"
echo -e "  View logs:     ${YELLOW}docker-compose logs -f${NC}"
echo -e "  Stop site:     ${YELLOW}docker-compose down${NC}"
echo -e "  Restart site:  ${YELLOW}docker-compose restart${NC}"
echo -e "  Check status:  ${YELLOW}docker-compose ps${NC}"
echo -e "  SSH into web:  ${YELLOW}docker exec -it nqobileq_web bash${NC}"
echo ""
echo -e "${BLUE}========================================${NC}"

# Optional: Send notification (if you have curl)
if command -v curl &> /dev/null; then
    # You can add a webhook notification here if desired
    # curl -X POST https://api.telegram.org/botTOKEN/sendMessage -d "chat_id=CHAT_ID&text=Deployment completed for NqobileQ"
    echo ""
fi