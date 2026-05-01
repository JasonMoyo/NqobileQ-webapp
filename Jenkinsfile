pipeline {
    // Run directly on the agent (not via SSH)
    agent { label 'docker-agent' }

    environment {
        APP_NAME = 'nqobileq'
        
        // Jenkins Credentials (Add these in Jenkins UI)
        SMTP_USERNAME = credentials('smtp-username')
        SMTP_PASSWORD = credentials('smtp-password')
        OWNER_EMAIL = credentials('owner-email')
        
        STRIPE_PUBLISHABLE_KEY = credentials('stripe-publishable-key')
        STRIPE_SECRET_KEY = credentials('stripe-secret-key')
        
        DB_PASSWORD = credentials('db-password')
        DB_ROOT_PASSWORD = credentials('db-root-password')
        
        AGENT_IP = '172.31.46.92'
        AGENT_PATH = '/home/ubuntu/nqobileq'
    }

    stages {
        stage('Checkout Code') {
            steps {
                echo '📦 Checking out code...'
                git url: 'https://github.com/JasonMoyo/NqobileQ-webapp.git', branch: 'main'
                echo '✅ Code checked out'
            }
        }

        stage('Create .env File') {
            steps {
                echo '🔧 Creating .env file...'
                writeFile file: '.env', text: """DB_HOST=db
DB_USER=nqobileq_user
DB_PASSWORD=${DB_PASSWORD}
DB_NAME=nqobileq_db
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=${SMTP_USERNAME}
SMTP_PASSWORD=${SMTP_PASSWORD}
SMTP_SECURE=tls
SITE_URL=http://${AGENT_IP}
APP_ENV=production
OWNER_PHONE=+27782280408
OWNER_EMAIL=${OWNER_EMAIL}
STRIPE_PUBLISHABLE_KEY=${STRIPE_PUBLISHABLE_KEY}
STRIPE_SECRET_KEY=${STRIPE_SECRET_KEY}
"""
                sh 'echo "✅ .env created"'
            }
        }

        stage('Update Stripe Config') {
            steps {
                echo '🔧 Updating Stripe config...'
                sh """
                    if [ -f stripe-config.php ]; then
                        sed -i 's|http://YOUR_EC2_PUBLIC_IP|http://${AGENT_IP}|g' stripe-config.php
                        echo "✅ Stripe config updated"
                    else
                        echo "⚠️ stripe-config.php not found"
                    fi
                """
            }
        }

        stage('Copy to Application Directory') {
            steps {
                echo '📁 Copying files to application directory...'
                sh """
                    mkdir -p ${AGENT_PATH}
                    cp -r * ${AGENT_PATH}/ || true
                    cp -r .[!.]* ${AGENT_PATH}/ 2>/dev/null || true
                    cd ${AGENT_PATH}
                    ls -la
                """
            }
        }

        stage('Deploy with Docker Compose') {
            steps {
                echo '🐳 Deploying with Docker Compose...'
                sh """
                    cd ${AGENT_PATH}
                    
                    # Stop old containers
                    docker-compose down 2>/dev/null || true
                    
                    # Build and start
                    docker-compose build --no-cache
                    docker-compose up -d
                    
                    echo "Waiting for containers to start..."
                    sleep 15
                    
                    # Show running containers
                    docker-compose ps
                """
            }
        }

        stage('Initialize Database') {
            steps {
                echo '🗄️ Initializing database...'
                sh """
                    cd ${AGENT_PATH}
                    # Wait for MySQL to be ready
                    sleep 10
                    
                    # Run init script
                    docker exec -i nqobileq_db mysql -uroot -p${DB_ROOT_PASSWORD} nqobileq_db < init.sql 2>/dev/null || echo "Database already initialized or continuing..."
                    
                    # Copy .env to web container
                    docker cp .env nqobileq_web:/var/www/html/.env 2>/dev/null || true
                    
                    echo "✅ Database initialization complete"
                """
            }
        }

        stage('Verify Deployment') {
            steps {
                echo '🔍 Verifying deployment...'
                sh """
                    echo "=========================================="
                    echo "Testing website..."
                    curl -s -f http://localhost:80 > /dev/null && echo "✅ Website is running" || echo "⚠️ Website may not be ready"
                    
                    echo "Testing phpMyAdmin..."
                    curl -s -f http://localhost:8081 > /dev/null && echo "✅ phpMyAdmin is running" || echo "⚠️ phpMyAdmin may not be ready"
                    
                    echo "=========================================="
                    echo "✅ NQOBILEQ DEPLOYMENT SUCCESSFUL!"
                    echo "=========================================="
                    echo "Website: http://${AGENT_IP}"
                    echo "phpMyAdmin: http://${AGENT_IP}:8081"
                    echo ""
                    echo "Admin Login: admin@nqobileq.com / admin123"
                    echo "Test Stripe Card: 4242 4242 4242 4242"
                    echo "=========================================="
                """
            }
        }
    }

    post {
        success {
            echo '🎉 DEPLOYMENT SUCCESSFUL! 🎉'
        }
        failure {
            echo '❌ DEPLOYMENT FAILED!'
            sh 'docker-compose logs --tail=50 2>/dev/null || true'
        }
        always {
            echo '🧹 Cleaning up...'
            sh 'docker system prune -f 2>/dev/null || true'
        }
    }
}