<?php
// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Set response header
header('Content-Type: application/json');

// Load PHPMailer
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load email configuration
$config = require 'email-config.php';

// Get form data and sanitize
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

// Validate inputs
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
} elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
    $errors[] = 'Please enter a valid 10-digit phone number';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// If there are errors, return them
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'errors' => $errors
    ]);
    exit;
}

// Create HTML email template
$emailBody = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #FF9933 0%, #FF512F 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px;
        }
        .info-box {
            background: #FFF9F0;
            border-left: 4px solid #FF9933;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box h3 {
            margin: 0 0 10px;
            color: #D32F2F;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-box p {
            margin: 0;
            color: #333;
            font-size: 16px;
        }
        .message-box {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .message-box h3 {
            margin: 0 0 15px;
            color: #D32F2F;
            font-size: 16px;
        }
        .message-box p {
            margin: 0;
            color: #555;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        .email-footer {
            background: #222;
            color: #aaa;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .email-footer a {
            color: #FF9933;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            background: #FFD700;
            color: #333;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎉 New Contact Form Submission</h1>
            <p>Someone wants to connect with you!</p>
            <span class="badge">VastraFlow</span>
        </div>
        
        <div class="email-body">
            <div class="info-box">
                <h3>👤 Name</h3>
                <p>' . $name . '</p>
            </div>
            
            <div class="info-box">
                <h3>📞 Phone Number</h3>
                <p><a href="tel:+91' . $phone . '" style="color: #FF9933; text-decoration: none;">+91 ' . $phone . '</a></p>
            </div>
            
            <div class="message-box">
                <h3>💬 Message</h3>
                <p>' . nl2br($message) . '</p>
            </div>
        </div>
        
        <div class="email-footer">
            <p><strong>VastraFlow</strong> - Simplifying Ethnic Rental Businesses</p>
            <p style="margin-top: 15px; font-size: 11px;">This email was sent from your VastraFlow contact form.</p>
        </div>
    </div>
</body>
</html>
';

try {
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_username'];
    $mail->Password   = $config['smtp_password'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port       = $config['smtp_port'];
    
    // Recipients
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['to_email'], $config['to_name']);
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission - VastraFlow';
    $mail->Body    = $emailBody;
    $mail->AltBody = "Name: $name\nPhone: +91 $phone\nMessage: $message";
    
    // Send email
    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for contacting us! We will get back to you soon.'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again later.',
        'error' => $mail->ErrorInfo  // Remove this line in production
    ]);
}
?>
