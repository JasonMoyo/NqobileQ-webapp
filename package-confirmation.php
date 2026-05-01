<?php
require_once 'config.php';

// Get package details from URL parameters
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Guest';
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';
$package = isset($_GET['package']) ? htmlspecialchars($_GET['package']) : '';

// Package details based on package name
$package_details = [
    'ESSENTIAL' => [
        'price' => 'R5,000',
        'features' => [
            'Pharmacy Consultation',
            'Prescription Delivery',
            'Basic Garden Maintenance',
            'Interior Design Consultation'
        ]
    ],
    'COMPREHENSIVE' => [
        'price' => 'R15,000',
        'features' => [
            'All Essential Services',
            'Room Renovation',
            'Full Interior Design Plan',
            'Complete Landscaping',
            'Priority Support'
        ]
    ],
    'PREMIUM' => [
        'price' => 'R30,000',
        'features' => [
            'All Comprehensive Services',
            'Full Home Renovation',
            'Premium Interior Design',
            'Complete Garden Makeover',
            'Monthly Medication Delivery',
            '24/7 Priority Support'
        ]
    ]
];

$selected_package = $package_details[$package] ?? ['price' => '', 'features' => []];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Booking Confirmed - NqobileQ</title>
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
            max-width: 700px;
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
        
        .package-badge {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .package-badge span {
            background: var(--main-color);
            color: var(--bg-color);
            padding: 0.8rem 2rem;
            border-radius: 5rem;
            font-size: 2rem;
            font-weight: 700;
            display: inline-block;
        }
        
        .package-details {
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
            font-size: 2.2rem;
        }
        
        .features-list {
            margin-top: 2rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 1rem;
            padding: 2rem;
        }
        
        .features-list h3 {
            font-size: 1.8rem;
            color: var(--main-color);
            margin-bottom: 1.5rem;
        }
        
        .features-list ul {
            list-style: none;
        }
        
        .features-list li {
            font-size: 1.6rem;
            color: var(--text-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .features-list li i {
            color: var(--main-color);
            font-size: 1.8rem;
        }
        
        .price-tag {
            text-align: center;
            margin: 2rem 0;
        }
        
        .price-tag .price {
            font-size: 3rem;
            color: var(--main-color);
            font-weight: 700;
        }
        
        .action-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
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
        
        .btn-wa {
            background: #25D366;
            color: white;
            border: 2px solid #25D366;
        }
        
        .btn-wa:hover {
            background: transparent;
            color: #25D366;
            box-shadow: 0 0 20px #25D366;
        }
        
        .btn i {
            font-size: 2rem;
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
            
            <h1>Package Booking Confirmed!</h1>
            <p class="subtitle">Thank you for choosing NqobileQ</p>
            
            <div class="package-badge">
                <span><?php echo $package; ?> PACKAGE</span>
            </div>
            
            <div class="package-details">
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class='bx bxs-user'></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value highlight"><?php echo $name; ?></div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value"><?php echo $email; ?></div>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-icon">
                        <i class='bx bxs-phone'></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value"><?php echo $phone; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="price-tag">
                <span class="price"><?php echo $selected_package['price']; ?></span>
            </div>
            
            <?php if (!empty($selected_package['features'])): ?>
            <div class="features-list">
                <h3>Package Features:</h3>
                <ul>
                    <?php foreach ($selected_package['features'] as $feature): ?>
                    <li>
                        <i class='bx bxs-check-circle'></i>
                        <?php echo $feature; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">
                    <i class='bx bxs-home'></i>
                    Back to Home
                </a>
                <a href="https://wa.me/27782280408?text=Hi%20NqobileQ%2C%20I%20just%20booked%20the%20<?php echo urlencode($package); ?>%20package" target="_blank" class="btn btn-wa">
                    <i class='bx bxl-whatsapp'></i>
                    WhatsApp Us
                </a>
                <a href="tel:+27782280408" class="btn btn-secondary">
                    <i class='bx bxs-phone-call'></i>
                    Call Now
                </a>
            </div>
            
            <p style="text-align: center; margin-top: 2rem; font-size: 1.4rem; color: rgba(255,255,255,0.5);">
                A confirmation email has been sent to your inbox
            </p>
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
        
        if (href.includes('wa.me') || href.includes('whatsapp')) {
            newLink.setAttribute('target', '_blank');
            newLink.setAttribute('rel', 'noopener noreferrer');
        }
        
        link.parentNode.replaceChild(newLink, link);
    });
    
    console.log('✅ Emergency link fix applied to package confirmation');
}, 500);
</script>
</body>
</html>