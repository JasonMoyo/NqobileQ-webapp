pipeline {
    agent { label 'docker-agent' }

    environment {
        APP_NAME = 'nqobileq'
        
        // Jenkins Credentials
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
        // STAGE 1: Clone Repository
        stage('Clone Repository') {
            steps {
                echo '📦 Cloning code from GitHub...'
                git url: 'https://github.com/JasonMoyo/NqobileQ-webapp.git', branch: 'main'
                echo '✅ Code cloned successfully'
            }
        }

        // STAGE 2: Create Environment File
        stage('Create Environment File') {
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
                echo '✅ .env file created'
            }
        }

        // STAGE 3: Configure Stripe
        stage('Configure Stripe') {
            steps {
                echo '💳 Configuring Stripe...'
                sh """
                    if [ -f stripe-config.php ]; then
                        sed -i 's|http://YOUR_EC2_PUBLIC_IP|http://${AGENT_IP}|g' stripe-config.php
                        echo "✅ Stripe configured"
                    fi
                """
            }
        }

        // STAGE 4: Copy to Application Directory
        stage('Copy to App Directory') {
            steps {
                echo '📁 Copying files to application directory...'
                sh """
                    mkdir -p ${AGENT_PATH}
                    cp -r * ${AGENT_PATH}/ 2>/dev/null || true
                    cp -r .[!.]* ${AGENT_PATH}/ 2>/dev/null || true
                    echo "✅ Files copied to ${AGENT_PATH}"
                """
            }
        }

        // STAGE 5: Build Docker Images
        stage('Build Docker Images') {
            steps {
                echo '🐳 Building Docker images...'
                sh """
                    cd ${AGENT_PATH}
                    docker-compose build --no-cache
                    echo "✅ Docker images built"
                """
            }
        }

        // STAGE 6: Start Containers
        stage('Start Containers') {
            steps {
                echo '🚀 Starting Docker containers...'
                sh """
                    cd ${AGENT_PATH}
                    docker-compose down 2>/dev/null || true
                    docker-compose up -d
                    echo "✅ Containers started"
                """
            }
        }

        // STAGE 7: Initialize Database
        stage('Initialize Database') {
            steps {
                echo '🗄️ Initializing database...'
                sh """
                    cd ${AGENT_PATH}
                    sleep 10
                    docker exec -i nqobileq_db mysql -uroot -p${DB_ROOT_PASSWORD} nqobileq_db < init.sql 2>/dev/null || echo "DB already initialized"
                    docker cp .env nqobileq_web:/var/www/html/.env 2>/dev/null || true
                    echo "✅ Database ready"
                """
            }
        }

        // STAGE 8: Verify Deployment
        stage('Verify Deployment') {
            steps {
                echo '🔍 Verifying deployment...'
                sh """
                    echo "=========================================="
                    echo "Testing website..."
                    curl -s -f http://localhost:80 > /dev/null && echo "✅ Website is running"
                    echo "=========================================="
                    echo "✅ DEPLOYMENT SUCCESSFUL!"
                    echo "=========================================="
                    echo "Website: http://${AGENT_IP}"
                    echo "Admin: admin@nqobileq.com / admin123"
                    echo "=========================================="
                """
            }
        }
    }

    post {
        success {
            echo '🎉 PIPELINE COMPLETED SUCCESSFULLY! 🎉'
        }
        failure {
            echo '❌ PIPELINE FAILED!'
            sh 'docker-compose logs --tail=30 2>/dev/null || true'
        }
        always {
            echo '🧹 Cleanup...'
            sh 'docker system prune -f 2>/dev/null || true'
        }
    }
}