<?php
require_once 'config.php';

// Check if user is logged in (they should be after registration)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['user_name'] ?? 'User';
$email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - NqobileQ</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .confirmation-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10rem 2rem 5rem;
            background: var(--bg-color);
        }
        
        .confirmation-card {
            background: var(--snd-bg-color);
            border: 2px solid var(--main-color);
            border-radius: 2rem;
            padding: 4rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 0 30px rgba(0, 184, 169, 0.3);
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-icon {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .success-icon i {
            font-size: 8rem;
            color: var(--main-color);
            animation: scaleIn 0.5s ease-out 0.2s both;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        .confirmation-card h1 {
            font-size: 3.5rem;
            text-align: center;
            color: var(--main-color);
            margin-bottom: 1rem;
        }
        
        .confirmation-card .subtitle {
            font-size: 1.8rem;
            text-align: center;
            color: var(--text-color);
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        
        .welcome-message {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: rgba(0, 184, 169, 0.1);
            border-radius: 1rem;
            border: 1px solid var(--main-color);
        }
        
        .welcome-message h2 {
            font-size: 2.5rem;
            color: var(--main-color);
            margin-bottom: 1rem;
        }
        
        .welcome-message p {
            font-size: 1.6rem;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .user-details {
            background: rgba(0, 184, 169, 0.1);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 3rem;
            border: 1px solid var(--main-color);
        }
        
        .detail-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-icon {
            flex: 0 0 4rem;
            font-size: 2.5rem;
            color: var(--main-color);
        }
        
        .detail-content {
            flex: 1;
        }
        
        .detail-label {
            font-size: 1.4rem;
            color: var(--main-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 1.8rem;
            color: var(--text-color);
            line-height: 1.4;
            word-break: break-word;
        }
        
        .detail-value.highlight {
            color: var(--main-color);
            font-weight: 600;
            font-size: 2rem;
        }
        
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 3rem 0;
        }
        
        .benefit-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            transition: transform 0.3s ease;
        }
        
        .benefit-item:hover {
            transform: translateY(-5px);
        }
        
        .benefit-item i {
            font-size: 3rem;
            color: var(--main-color);
            margin-bottom: 1rem;
        }
        
        .benefit-item h4 {
            font-size: 1.6rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .benefit-item p {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .action-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 3rem 0;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem 2.5rem;
            font-size: 1.6rem;
            font-weight: 600;
            border-radius: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--main-color);
            color: var(--bg-color);
            border: 2px solid var(--main-color);
        }
        
        .btn-primary:hover {
            background: transparent;
            color: var(--main-color);
            box-shadow: 0 0 20px var(--main-color);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--main-color);
            border: 2px solid var(--main-color);
        }
        
        .btn-secondary:hover {
            background: var(--main-color);
            color: var(--bg-color);
            box-shadow: 0 0 20px var(--main-color);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
            border: 2px solid #28a745;
        }
        
        .btn-success:hover {
            background: transparent;
            color: #28a745;
            box-shadow: 0 0 20px #28a745;
        }
        
        .btn i {
            font-size: 2rem;
        }
        
        .login-info {
            text-align: center;
            padding: 2rem;
            background: rgba(0, 184, 169, 0.05);
            border-radius: 1rem;
            margin-top: 2rem;
        }
        
        .login-info p {
            font-size: 1.5rem;
            color: var(--text-color);
        }
        
        .login-info i {
            color: var(--main-color);
            vertical-align: middle;
            margin-right: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .confirmation-card {
                padding: 2.5rem;
            }
            
            .confirmation-card h1 {
                font-size: 2.8rem;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-icon {
                margin-bottom: 1rem;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">NQOBILE<span>Q</span></a>
        <div class="bx bx-menu" id="menu-icon"></div>
        <ul class="navbar">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#plans">Pricing</a></li>
            <li><a href="index.php#review">Review</a></li>
            <li><a href="index.php#contact">Contact</a></li>
        </ul>
    </header>

    <section class="confirmation-container">
        <div class="confirmation-card">
            <div class="success-icon">
                <i class='bx bxs-check-circle'></i>
            </div>
            
            <h1>Registration Successful!</h1>
            <p class="subtitle">Welcome to the NqobileQ family</p>
            
            <div class="welcome-message">
                <h2>Hello, <?php echo htmlspecialchars($name); ?>! 👋</h2>
                <p>Your account has been created successfully. We're excited to have you on board!</p>
            </div>
            
            <div class="user-details">
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class='bx bxs-user'></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Your Name</div>
                        <div class="detail-value highlight"><?php echo htmlspecialchars($name); ?></div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value"><?php echo htmlspecialchars($email); ?></div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class='bx bxs-user-check'></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Account Status</div>
                        <div class="detail-value">
                            <span style="color: #28a745;">✓ Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 style="text-align: center; font-size: 2rem; margin: 2rem 0; color: var(--main-color);">
                Your Member Benefits
            </h3>
            
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class='bx bxs-discount'></i>
                    <h4>Exclusive Deals</h4>
                    <p>Get special discounts on all services</p>
                </div>
                
                <div class="benefit-item">
                    <i class='bx bxs-calendar-check'></i>
                    <h4>Priority Booking</h4>
                    <p>Book services with priority access</p>
                </div>
                
                <div class="benefit-item">
                    <i class='bx bxs-message-detail'></i>
                    <h4>Personalized Support</h4>
                    <p>Dedicated customer service</p>
                </div>
                
                <div class="benefit-item">
                    <i class='bx bxs-package'></i>
                    <h4>Package Discounts</h4>
                    <p>Special rates on bundled services</p>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="welcome.php" class="btn btn-success">
                    <i class='bx bxs-dashboard'></i>
                    Go to Dashboard
                </a>
                <a href="index.php#services" class="btn btn-primary">
                    <i class='bx bxs-briefcase'></i>
                    Browse Services
                </a>
                <a href="index.php#plans" class="btn btn-secondary">
                    <i class='bx bxs-package'></i>
                    View Packages
                </a>
            </div>
            
            <div class="login-info">
                <p>
                    <i class='bx bxs-info-circle'></i>
                    You are now logged in. Your session will remain active until you logout.
                </p>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="logout.php" class="auth-btn logout-btn" data-no-modal="true" style="background:#f44336; color:white; border-color:#f44336; padding: 1rem 2.5rem; display: inline-block;">
                    <i class='bx bxs-log-out'></i>
                    Logout
                </a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="social">
            <a href="https://www.facebook.com/profile.php?id=61588428180925" target="_blank"><i class='bx bxl-facebook'></i></a>
            <a href="https://www.instagram.com/nqobileq_services_/" target="_blank"><i class='bx bxl-instagram'></i></a>
            <a href="https://wa.me/+27782280408" target="_blank"><i class='bx bxl-whatsapp'></i></a>
            <a href="mailto:nqobileq.co@gmail.com"><i class='bx bxl-gmail'></i></a>
        </div>
        <p class="copyright">&copy; NqobileQ 2026 - All Rights Reserved</p>
    </footer>

    <script src="script.js"></script>
    <!-- Emergency link fix - forces all links to work -->
<script>
setTimeout(function() {
    document.querySelectorAll('a').forEach(function(link) {
        var href = link.getAttribute('href');
        if (!href) return;
        
        var newLink = document.createElement('a');
        newLink.href = href;
        newLink.innerHTML = link.innerHTML;
        newLink.className = link.className;
        newLink.style.cssText = link.style.cssText;
        
        Array.from(link.attributes).forEach(function(attr) {
            if (attr.name !== 'href' && attr.name !== 'onclick') {
                newLink.setAttribute(attr.name, attr.value);
            }
        });
        
        link.parentNode.replaceChild(newLink, link);
    });
    
    console.log('✅ Emergency link fix applied to registration success');
}, 500);
</script>
</body>
</html>