<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}

require_once 'email_config.php';

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Clear any existing error at the start
if (isset($_SESSION['error']) && !isset($_POST["login"]) && !isset($_POST["register"])) {
    unset($_SESSION['error']);
}

// Only run these queries if not on login/register pages
$current_page = basename($_SERVER['PHP_SELF']);
if (!in_array($current_page, ['login.php', 'register.php'])) {
    $sql = "SELECT * FROM events";
    $result = $conn->query($sql);
    
    if (!$result) {
        error_log("Events query failed: " . $conn->error);
    }
}

// Auto-login if session expired but cookie exists
if (!isset($_SESSION["user"]) && isset($_COOKIE["user"])) {
    $cookie_user = $conn->real_escape_string($_COOKIE["user"]);
    $stmt = $conn->prepare("SELECT name, role FROM users WHERE name = ?");
    $stmt->bind_param("s", $cookie_user);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name, $role);
    $stmt->fetch();
    
    if ($stmt->num_rows > 0) {
        $_SESSION["user"] = $name;
        $_SESSION["role"] = $role;
        
        header("Location: " . ($role === "admin" ? "admin_dashboard.php" : "dashboard.php"));
        exit();
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle Signup
if (isset($_POST["register"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

    // Check if email exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        $_SESSION["error"] = "Email already registered. Please use a different email or login.";
        header("Location: register.php");
        exit();
    }
    $check_email->close();

    // Handle file upload
    $profile_image = "uploads/default.png";
    if (!empty($_FILES["profile_image"]["name"])) {
        $uploads_dir = "uploads/";
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        $target_file = $uploads_dir . $unique_filename;
        
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        }
    }

    // Insert user
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_image, role, approved) VALUES (?, ?, ?, ?, 'user', 0)");
        $stmt->bind_param("ssss", $name, $email, $password, $profile_image);

        if ($stmt->execute()) {
            $_SESSION["user"] = $name;
            $_SESSION["role"] = "user";
            $_SESSION["profile_image"] = $profile_image;
            $_SESSION["message"] = "Account created successfully! Please wait for admin approval.";

            // Send welcome email
            $emailSubject = 'Welcome to ClubSphere!';
            $emailBody = "
                <h2>Welcome, $name!</h2>
                <p>Your ClubSphere account has been created successfully.</p>
                <p>Your account is currently pending approval from an administrator.</p>
                <p>You'll receive another email once your account has been approved.</p>
                <p>Best regards,<br>The ClubSphere Team</p>
            ";
            
            if (!sendClubSphereEmail($email, $name, $emailSubject, $emailBody)) {
                error_log("Welcome email failed to send to $email");
            }

            header("Location: pending_approval.php");
            exit();
        } else {
            throw new Exception("Database insert failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        $_SESSION["error"] = "Registration failed. Please try again.";
        header("Location: register.php");
        exit();
    }
}

// Handle Login
if (isset($_POST["login"])) {
    // Initialize login attempts if not set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = time();
    }

    // Check for too many attempts
    if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_login_attempt']) < 300) {
        $_SESSION["error"] = "Too many login attempts. Please try again in 5 minutes.";
        header("Location: login.php");
        exit();
    }

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, name, password, role, approved, profile_image FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name, $hashed_password, $role, $approved, $profile_image);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (password_verify($password, $hashed_password)) {
            if ($approved == 0) {
                $_SESSION["error"] = "Your account is pending approval. Please check back later.";
                header("Location: login.php?email=".urlencode($email));
                exit();
            }

            // Successful login - reset attempts
            $_SESSION['login_attempts'] = 0;
            
            $_SESSION["user_id"] = $user_id;
            $_SESSION["user"] = $name;
            $_SESSION["role"] = $role;
            $_SESSION["profile_image"] = $profile_image;
            
            if (isset($_POST["remember"]) && $_POST["remember"] == "on") {
                setcookie("user", $name, time() + 2592000, "/");
            }

            // Update last login
            $update_login = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_login->bind_param("i", $user_id);
            $update_login->execute();

            header("Location: " . ($role === "admin" ? "admin_dashboard.php" : "dashboard.php"));
            exit();
        } else {
            // Wrong password - increment attempts
            $_SESSION['login_attempts']++;
            $_SESSION['last_login_attempt'] = time();
            
            $_SESSION["error"] = "Invalid password!";
            header("Location: login.php?email=".urlencode($email));
            exit();
        }
    } else {
        // Email not found - increment attempts
        $_SESSION['login_attempts']++;
        $_SESSION['last_login_attempt'] = time();
        
        $_SESSION["error"] = "No account found with that email address!";
        header("Location: login.php");
        exit();
    }
}

// Handle Logout
if (isset($_GET["logout"])) {
    $_SESSION = array();
    session_destroy();
    setcookie("user", "", time() - 3600, "/");
    header("Location: login.php");
    exit();
}

// Improved Email function
function sendClubSphereEmail($recipientEmail, $recipientName, $subject, $body) {
    require_once 'phpmailer/PHPMailer.php';
    require_once 'phpmailer/SMTP.php';
    require_once 'phpmailer/Exception.php';
    require_once 'email_config.php';

    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Timeout    = 10; // seconds

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Plain text version

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error to $recipientEmail: " . $mail->ErrorInfo);
        return false;
    }
}


// Handle Payment Processing (add this to your databases.php)
if (isset($_POST['payment_method'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $amount = floatval($_POST['amount']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $transaction_id = 'TRX-' . strtoupper(uniqid());
    
    // Additional fields based on payment method
    if ($payment_method === 'qr') {
        $utr_number = $conn->real_escape_string($_POST['utr_number']);
        $card_details = 'QR Payment (UTR: ' . $utr_number . ')';
    } else {
        $card_number = substr($conn->real_escape_string($_POST['card_number']), -4);
        $expiry = $conn->real_escape_string($_POST['expiry']);
        $card_details = 'Card ending with ' . $card_number . ' (Exp: ' . $expiry . ')';
    }

    // Insert payment record
    $stmt = $conn->prepare("INSERT INTO payments (name, email, amount, payment_method, transaction_id, status, payment_date) VALUES (?, ?, ?, ?, ?, 'completed', NOW())");
    $stmt->bind_param("ssdss", $name, $email, $amount, $payment_method, $transaction_id);
    
    if ($stmt->execute()) {
        // Generate invoice details
        $invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        $invoice_date = date('F j, Y');
        $payment_date = date('F j, Y H:i:s');
        
        // Generate HTML invoice
        $invoice_html = generateInvoiceHtml($name, $email, $amount, $payment_method, $transaction_id, $invoice_number, $invoice_date, $payment_date);
        
        // Generate PDF invoice (requires TCPDF or similar)
        $pdf_path = generatePdfInvoice($invoice_html, $invoice_number);
        
        // Send email with receipt
        $emailSubject = 'Payment Receipt - ClubSphere Membership';
        $emailBody = $invoice_html; // Using the same HTML for email
        
        if (sendClubSphereEmailWithAttachment($email, $name, $emailSubject, $emailBody, $pdf_path)) {
            $_SESSION['payment_receipt'] = [
                'name' => $name,
                'email' => $email,
                'amount' => $amount,
                'payment_method' => $payment_method,
                'transaction_id' => $transaction_id,
                'invoice_number' => $invoice_number,
                'payment_date' => $payment_date,
                'pdf_path' => $pdf_path
            ];
            
            header("Location: payment_success.php");
            exit();
        } else {
            $_SESSION['payment_error'] = "Payment processed but email failed to send. Transaction ID: $transaction_id";
            header("Location: pay.php");
            exit();
        }
    } else {
        $_SESSION['payment_error'] = "Payment processing failed. Please try again.";
        header("Location: pay.php");
        exit();
    }
}

// Add these new functions to your databases.php
function generateInvoiceHtml($name, $email, $amount, $payment_method, $transaction_id, $invoice_number, $invoice_date, $payment_date) {
    return "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
        <div style='background-color: #6366f1; color: white; padding: 20px; text-align: center;'>
            <h1 style='margin: 0; font-size: 24px;'>ClubSphere</h1>
            <p style='margin: 5px 0 0; font-size: 16px;'>Payment Receipt</p>
        </div>
        
        <div style='padding: 20px;'>
            <div style='display: flex; justify-content: space-between; margin-bottom: 15px;'>
                <div>
                    <h2 style='color: #6366f1; margin: 0 0 5px;'>Invoice #$invoice_number</h2>
                    <p style='margin: 0; color: #64748b;'>Date: $invoice_date</p>
                </div>
                <div style='text-align: right;'>
                    <p style='margin: 0; font-weight: bold;'>Transaction ID</p>
                    <p style='margin: 0; color: #64748b;'>$transaction_id</p>
                </div>
            </div>
            
            <div style='background-color: #f8fafc; padding: 15px; border-radius: 6px; margin-bottom: 20px;'>
                <h3 style='margin: 0 0 10px; color: #1e293b;'>Payment Details</h3>
                <div style='display: flex; justify-content: space-between; margin-bottom: 5px;'>
                    <span style='color: #64748b;'>Amount Paid:</span>
                    <span style='font-weight: bold;'>$".number_format($amount, 2)."</span>
                </div>
                <div style='display: flex; justify-content: space-between; margin-bottom: 5px;'>
                    <span style='color: #64748b;'>Payment Method:</span>
                    <span style='font-weight: bold;'>".ucfirst($payment_method)."</span>
                </div>
                <div style='display: flex; justify-content: space-between;'>
                    <span style='color: #64748b;'>Payment Date:</span>
                    <span style='font-weight: bold;'>$payment_date</span>
                </div>
            </div>
            
            <div style='margin-bottom: 20px;'>
                <h3 style='margin: 0 0 10px; color: #1e293b;'>Member Information</h3>
                <p style='margin: 0 0 5px;'><strong>Name:</strong> $name</p>
                <p style='margin: 0;'><strong>Email:</strong> $email</p>
            </div>
            
            <div style='text-align: center; margin-top: 25px;'>
                <p style='color: #64748b; font-size: 14px;'>Thank you for your payment. This receipt confirms your transaction with ClubSphere.</p>
            </div>
        </div>
        
        <div style='background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #64748b;'>
            <p style='margin: 0;'>ClubSphere Membership System</p>
            <p style='margin: 5px 0 0;'>© ".date('Y')." All Rights Reserved</p>
        </div>
    </div>
    ";
}

function generatePdfInvoice($html, $invoice_number) {
    require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('ClubSphere');
    $pdf->SetTitle('Invoice '.$invoice_number);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->writeHTML($html);
    
    $pdf_path = "invoices/".$invoice_number.".pdf";
    
    // Ensure directory exists
    if (!file_exists('invoices')) {
        mkdir('invoices', 0755, true);
    }
    
    // Save to file
    $pdf->Output(__DIR__.'/'.$pdf_path, 'F');
    
    // Verify PDF was created
    if (!file_exists($pdf_path)) {
        error_log("PDF generation failed for: ".$pdf_path);
        throw new Exception("Failed to generate PDF");
    }
    
    return $pdf_path;
}

function sendClubSphereEmailWithAttachment($recipientEmail, $recipientName, $subject, $body, $attachment_path) {
    require_once 'phpmailer/PHPMailer.php';
    require_once 'phpmailer/SMTP.php';
    require_once 'phpmailer/Exception.php';
    require_once 'email_config.php';

    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Timeout    = 10; // seconds

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);

        // Attachments
        if (file_exists($attachment_path)) {
            $mail->addAttachment(
                $attachment_path,
                'Invoice_'.basename($attachment_path),
                'base64',
                'application/pdf'
            );
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email with attachment error to $recipientEmail: " . $mail->ErrorInfo);
        return false;
    }
}
?>