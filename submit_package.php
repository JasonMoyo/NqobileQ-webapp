<?php
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = getDB();

    $name         = $conn->real_escape_string($_POST['name'] ?? '');
    $email        = $conn->real_escape_string($_POST['email'] ?? '');
    $phone        = $conn->real_escape_string($_POST['phone'] ?? '');
    $package_name = $conn->real_escape_string($_POST['package_name'] ?? '');
    $amount       = $conn->real_escape_string($_POST['amount'] ?? '0'); // amount in cents

    if (empty($name) || empty($email) || empty($package_name)) {
        die("Error: Required fields are missing.");
    }

    $sql = "INSERT INTO package_bookings (name, email, phone, package_name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $phone, $package_name);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;

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
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Package Booking - NqobileQ';
            $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #00b8a9; color: white; padding: 20px; text-align: center; }
                    .details { background: #f5f5f5; padding: 20px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Package Booking Confirmation</h2>
                    </div>
                    <div class='details'>
                        <p><strong>Name:</strong> $name</p>
                        <p><strong>Email:</strong> $email</p>
                        <p><strong>Phone:</strong> $phone</p>
                        <p><strong>Package:</strong> $package_name</p>
                        <p><strong>Amount:</strong> $" . ($amount / 100) . " USD</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }

        // Redirect to Stripe checkout page with amount in cents
        header("Location: stripe-checkout.php?booking_id=" . $booking_id . "&amount=" . $amount . "&service=" . urlencode($package_name . " Package") . "&email=" . urlencode($email));
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