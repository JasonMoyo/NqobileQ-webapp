<?php
require_once 'config.php';

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION["user_name"] ?? "User";
$email = $_SESSION["user_email"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | NqobileQ</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            text-align: center;
            padding: 100px;
            font-family: -apple-system, sans-serif;
            background-color: #111;
            color: #fff;
        }
        .welcome-container {
            max-width: 600px;
            margin: auto;
            padding: 40px;
            background: var(--snd-bg-color);
            border: 2px solid var(--main-color);
            border-radius: 20px;
            box-shadow: 0 0 20px var(--main-color);
        }
        h1 {
            font-size: 4rem;
            color: var(--main-color);
            margin-bottom: 20px;
        }
        p {
            font-size: 1.8rem;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background-color: var(--main-color);
            color: #000;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.6rem;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .btn:hover {
            background-color: #009688;
            box-shadow: 0 0 15px var(--main-color);
        }
        .btn-logout {
            background-color: #f44336;
            color: white;
        }
        .btn-logout:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>Thank you for joining NqobileQ. We're excited to help you with your health and home needs.</p>
        
        <div style="margin: 30px 0;">
            <a href="index.php#services" class="btn">Browse Services</a>
            <a href="index.php#plans" class="btn">View Packages</a>
        </div>
        
        <!-- Fixed logout button with onclick to stop propagation -->
        <a href="logout.php" class="btn btn-logout" onclick="event.stopPropagation();">Logout</a>
    </div>
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
    
    console.log('✅ Emergency link fix applied to welcome page');
}, 500);
</script>
</body>
</html>