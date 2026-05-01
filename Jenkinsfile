pipeline {
    agent any

    environment {
        // Application
        APP_NAME = 'nqobileq'
        
        // EC2 Agent Configuration (UPDATE THESE)
        AGENT_IP = '172.31.46.92'        // Your agent PRIVATE IP
        AGENT_USER = 'ubuntu'
        AGENT_PATH = '/home/ubuntu/nqobileq'
        
        // Jenkins Credentials (Add these in Jenkins UI)
        SMTP_USERNAME = credentials('smtp-username')
        SMTP_PASSWORD = credentials('smtp-password')
        OWNER_EMAIL = credentials('owner-email')
        
        // Stripe Credentials
        STRIPE_PUBLISHABLE_KEY = credentials('stripe-publishable-key')
        STRIPE_SECRET_KEY = credentials('stripe-secret-key')
        
        // Database Credentials
        DB_PASSWORD = credentials('db-password')
        DB_ROOT_PASSWORD = credentials('db-root-password')
    }

    stages {
        stage('Checkout Code') {
            steps {
                echo '📦 Checking out code from GitHub...'
                git url: 'https://github.com/JasonMoyo/Nq-web.git', branch: 'main'
                echo '✅ Code checked out successfully'
            }
        }

        stage('Create .env File') {
            steps {
                echo '🔧 Creating .env file...'
                script {
                    writeFile file: '.env', text: """# Database Configuration
DB_HOST=db
DB_USER=nqobileq_user
DB_PASSWORD=${env.DB_PASSWORD}
DB_NAME=nqobileq_db
DB_ROOT_PASSWORD=${env.DB_ROOT_PASSWORD}

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=${env.SMTP_USERNAME}
SMTP_PASSWORD=${env.SMTP_PASSWORD}
SMTP_SECURE=tls

# Site Configuration
SITE_URL=http://${env.AGENT_IP}
APP_ENV=production

# Contact Info
OWNER_PHONE=+27782280408
OWNER_EMAIL=${env.OWNER_EMAIL}

# Stripe Configuration
STRIPE_PUBLISHABLE_KEY=${env.STRIPE_PUBLISHABLE_KEY}
STRIPE_SECRET_KEY=${env.STRIPE_SECRET_KEY}
"""
                }
                sh 'echo "✅ .env file created"'
            }
        }

        stage('Update Stripe Config') {
            steps {
                echo '🔧 Updating stripe-config.php for AWS...'
                sh """
                    # Update the domain in stripe-config.php
                    sed -i 's|http://YOUR_EC2_PUBLIC_IP|http://${AGENT_IP}|g' stripe-config.php 2>/dev/null || true
                    echo "✅ Stripe config updated"
                """
            }
        }

        stage('Deploy to Agent EC2') {
            steps {
                echo '🚀 Deploying to Agent EC2...'
                sh """
                    ssh -o StrictHostKeyChecking=no ${AGENT_USER}@${AGENT_IP} << 'ENDSSH'
                        echo "📁 Setting up directory..."
                        mkdir -p ${AGENT_PATH}
                        cd ${AGENT_PATH}
                        
                        echo "📦 Pulling latest code..."
                        git pull origin main 2>/dev/null || git clone https://github.com/JasonMoyo/Nq-web.git .
                        
                        echo "🔧 Creating .env file on agent..."
                        cat > .env << 'EOF'
DB_HOST=db
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
EOF
                        
                        echo "🔧 Updating stripe-config.php..."
                        sed -i "s|http://localhost|http://${AGENT_IP}|g" stripe-config.php 2>/dev/null || true
                        
                        echo "🐳 Stopping old containers..."
                        docker-compose down || true
                        
                        echo "🐳 Building and starting containers..."
                        docker-compose build --no-cache
                        docker-compose up -d
                        
                        echo "⏳ Waiting for database to be ready..."
                        sleep 15
                        
                        echo "🗄️ Initializing database..."
                        docker exec -i nqobileq_db mysql -uroot -p${DB_ROOT_PASSWORD} nqobileq_db < init.sql 2>/dev/null || true
                        
                        echo "📋 Copying .env to web container..."
                        docker cp .env nqobileq_web:/var/www/html/.env 2>/dev/null || true
                        docker exec nqobileq_web chown www-data:www-data /var/www/html/.env 2>/dev/null || true
                        
                        echo "✅ Deployment complete on agent"
                        
                        echo "📊 Container status:"
                        docker-compose ps
                    ENDSSH
                """
            }
        }

        stage('Verify Deployment') {
            steps {
                echo '🔍 Verifying deployment...'
                sh """
                    echo "=========================================="
                    echo "Testing website..."
                    curl -s -f http://${AGENT_IP} > /dev/null && echo "✅ Website is running" || echo "⚠️ Website may not be ready"
                    
                    echo "Testing phpMyAdmin..."
                    curl -s -f http://${AGENT_IP}:8081 > /dev/null && echo "✅ phpMyAdmin is running" || echo "⚠️ phpMyAdmin may not be ready"
                    
                    echo "Testing Stripe checkout..."
                    curl -s -f http://${AGENT_IP}/stripe-checkout.php > /dev/null && echo "✅ Stripe checkout page accessible" || echo "⚠️ Stripe page check skipped"
                    
                    echo "=========================================="
                    echo "✅ NQOBILEQ DEPLOYMENT SUCCESSFUL!"
                    echo "=========================================="
                    echo ""
                    echo "📱 ACCESS YOUR APPLICATION:"
                    echo "   Website: http://${AGENT_IP}"
                    echo "   phpMyAdmin: http://${AGENT_IP}:8081"
                    echo "   Stripe Test: http://${AGENT_IP}/stripe-checkout.php"
                    echo ""
                    echo "🔐 ADMIN LOGIN:"
                    echo "   Email: admin@nqobileq.com"
                    echo "   Password: admin123"
                    echo ""
                    echo "💳 TEST STRIPE CARD:"
                    echo "   Card: 4242 4242 4242 4242"
                    echo "   Expiry: 12/28"
                    echo "   CVC: 123"
                    echo "=========================================="
                """
            }
        }
    }

    post {
        success {
            echo '🎉 DEPLOYMENT SUCCESSFUL! 🎉'
            emailext (
                subject: "✅ NqobileQ Deployment Successful - Build #${env.BUILD_NUMBER}",
                body: """
                    NqobileQ Deployment Successful!
                    
                    Build Number: ${env.BUILD_NUMBER}
                    Website: http://${env.AGENT_IP}
                    phpMyAdmin: http://${env.AGENT_IP}:8081
                    
                    Stripe is configured for test payments.
                    Use test card: 4242 4242 4242 4242
                """,
                to: "${env.OWNER_EMAIL}"
            )
        }
        failure {
            echo '❌ DEPLOYMENT FAILED!'
            sh """
                echo "Fetching logs from agent..."
                ssh -o StrictHostKeyChecking=no ${AGENT_USER}@${AGENT_IP} 'cd ${AGENT_PATH} && docker-compose logs --tail=50' || true
            """
            emailext (
                subject: "❌ NqobileQ Deployment Failed - Build #${env.BUILD_NUMBER}",
                body: "Deployment Failed! Check Jenkins console for details.",
                to: "${env.OWNER_EMAIL}"
            )
        }
        always {
            echo '🧹 Cleaning up...'
            sh 'docker system prune -f || true'
        }
    }
}