<?php
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = getDB();

    $name    = $conn->real_escape_string($_POST['name'] ?? '');
    $email   = $conn->real_escape_string($_POST['email'] ?? '');
    $message = $conn->real_escape_string($_POST['message'] ?? '');
    $rating  = intval($_POST['rating'] ?? 5);

    if (empty($name) || empty($email) || empty($message)) {
        die("Error: Required fields are missing.");
    }

    $sql = "INSERT INTO testimonials (name, email, message, rating) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $message, $rating);

    if ($stmt->execute()) {

        // Send email notification
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USERNAME');
            $mail->Password   = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('no-reply@nqobileq.com', 'NqobileQ Website');
            $mail->addAddress(OWNER_EMAIL);
            $mail->addAddress($email); // Send confirmation to customer

            $mail->isHTML(true);
            $mail->Subject = 'New Testimonial - NqobileQ';
            $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #00b8a9; color: white; padding: 20px; text-align: center; }
                    .details { background: #f5f5f5; padding: 20px; margin-top: 20px; }
                    .detail-row { margin-bottom: 10px; }
                    .label { font-weight: bold; color: #333; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New Testimonial Received</h2>
                    </div>
                    <div class='details'>
                        <div class='detail-row'><span class='label'>Name:</span> $name</div>
                        <div class='detail-row'><span class='label'>Email:</span> $email</div>
                        <div class='detail-row'><span class='label'>Rating:</span> $rating Stars</div>
                        <div class='detail-row'><span class='label'>Message:</span> $message</div>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }

        // Redirect to testimonial confirmation page
        header("Location: testimonial-confirmation.php?name=" . urlencode($name) . 
               "&email=" . urlencode($email) . 
               "&message=" . urlencode($message) . 
               "&rating=" . $rating);
        exit;

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Invalid request method.";
}
?>
