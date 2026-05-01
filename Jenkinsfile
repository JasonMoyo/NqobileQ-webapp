pipeline {
    agent { label 'docker-agent' }

    triggers {
        githubPush()
        pollSCM('H/5 * * * *')
    }

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
        
        // No hardcoded IP here - will be detected dynamically
        AGENT_PATH = '/home/ubuntu/nqobileq'
    }

    stages {
        stage('Clone Repository') {
            steps {
                echo '📦 Cloning code from GitHub...'
                git url: 'https://github.com/JasonMoyo/NqobileQ-webapp.git', branch: 'main'
                echo '✅ Code cloned successfully'
                sh 'git log -1 --oneline'
            }
        }

        stage('Detect Public IP') {
            steps {
                echo '🔍 Detecting public IP address...'
                script {
                    // Auto-detect public IP using multiple services (fallback)
                    def public_ip = sh(script: 'curl -s ifconfig.me || curl -s icanhazip.com || curl -s ipinfo.io/ip', returnStdout: true).trim()
                    env.AGENT_IP = public_ip
                    echo "✅ Detected public IP: ${env.AGENT_IP}"
                }
            }
        }

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
SITE_URL=http://${env.AGENT_IP}
APP_ENV=production
OWNER_PHONE=+27782280408
OWNER_EMAIL=${OWNER_EMAIL}
STRIPE_PUBLISHABLE_KEY=${STRIPE_PUBLISHABLE_KEY}
STRIPE_SECRET_KEY=${STRIPE_SECRET_KEY}
"""
                echo '✅ .env file created'
            }
        }

        stage('Verify Composer Dependencies') {
            steps {
                echo '📦 Verifying composer.json...'
                sh '''
                    if [ -f composer.json ]; then
                        echo "✅ composer.json found"
                        cat composer.json | grep -E "phpmailer|stripe|vlucas" || echo "⚠️ Check dependencies"
                    else
                        echo "❌ composer.json not found!"
                        exit 1
                    fi
                '''
            }
        }

        stage('Configure Stripe') {
            steps {
                echo '💳 Configuring Stripe...'
                sh """
                    if [ -f stripe-config.php ]; then
                        sed -i 's|http://YOUR_EC2_PUBLIC_IP|http://${env.AGENT_IP}|g' stripe-config.php
                        echo "✅ Stripe configured with IP: ${env.AGENT_IP}"
                    fi
                """
            }
        }

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
                    echo "Website: http://${env.AGENT_IP}"
                    echo "Admin: admin@nqobileq.com / admin123"
                    echo "=========================================="
                """
            }
        }
    }

    post {
        success {
            echo '🎉 PIPELINE TRIGGERED BY GITHUB PUSH - DEPLOYMENT SUCCESSFUL! 🎉'
            echo "🌐 Website available at: http://${env.AGENT_IP}"
        }
        failure {
            echo '❌ PIPELINE FAILED!'
            sh 'cd ${AGENT_PATH} && docker-compose logs --tail=30 2>/dev/null || true'
        }
        always {
            echo '🧹 Cleanup...'
            sh 'docker system prune -f 2>/dev/null || true'
        }
    }
}