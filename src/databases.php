<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 2592000);
    session_set_cookie_params(2592000);
    session_start();
}
require_once '../email_config.php';
// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";

$conn = new mysqli($host, $user, $pass, $dbname);


if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Only run these queries if not on login/register pages to avoid unnecessary database calls
$current_page = basename($_SERVER['PHP_SELF']);
if (!in_array($current_page, ['login.php', 'register.php'])) {
    $sql = "SELECT * FROM events";
    $result = $conn->query($sql);

    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
}

// Auto-login if session expired but cookie exists
if (!isset($_SESSION["user"]) && isset($_COOKIE["user"])) {
    // Get user details to ensure proper role assignment
    $cookie_user = $conn->real_escape_string($_COOKIE["user"]);
    $stmt = $conn->prepare("SELECT name, role FROM users WHERE name = ?");
    $stmt->bind_param("s", $cookie_user);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name, $role);
    $stmt->fetch();
    
    if ($stmt->num_rows > 0) {
        $_SESSION["user"] = $name;
        $_SESSION["role"] = $role; // Important: Set the role too
        
        // Redirect based on role
        if ($role === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    }
}

// Handle Signup
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php'; // Load PHPMailer

// Handle Signup
if (isset($_POST["register"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);

    // Check if email already exists
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

    // Ensure uploads folder exists
    $uploads_dir = "uploads/";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    // Handle Image Upload
    $profile_image = "uploads/default.png";
    if (!empty($_FILES["profile_image"]["name"])) {
        // Generate unique filename to prevent overwriting
        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        $target_file = $uploads_dir . $unique_filename;
        
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;
        }
    }

    // Insert User into Database with created_at timestamp
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_image, role, approved, created_at) VALUES (?, ?, ?, ?, 'user', 0, NOW())");
    $stmt->bind_param("ssss", $name, $email, $password, $profile_image);

    if ($stmt->execute()) {
        $_SESSION["user"] = $name;
        $_SESSION["role"] = "user"; // Explicitly set role
        $_SESSION["profile_image"] = $profile_image;
        $_SESSION["message"] = "Account created successfully! Please wait for admin approval.";

        // Send confirmation email
        sendEmail($email, $name);

        header("Location: pending_approval.php"); // Redirect to a pending approval page
        exit();
    } else {
        $_SESSION["error"] = "Signup failed: " . $stmt->error;
        header("Location: register.php");
        exit();
    }
}

// Function to send email
function sendEmail($email, $name, $subject = 'Welcome to ClubSphere!', $message = '') {
    $mail = new PHPMailer(true);
    $mail->Host = SMTP_HOST;
$mail->Username = SMTP_USERNAME; 
$mail->Password = SMTP_PASSWORD;
$mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Or your SMTP provider
        $mail->SMTPAuth = true;
        $mail->Username = 'your-real-email@gmail.com'; // Your SMTP email
        $mail->Password = 'your-app-password'; // Your app password (not your regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Set timeout values to prevent hanging
        $mail->Timeout = 10; // seconds
        $mail->SMTPDebug = 0; // Set to 2 for debugging, 0 for production
        
        // Sender & Recipient
        $mail->setFrom('your-real-email@gmail.com', 'ClubSphere');
        $mail->addAddress($email, $name);
        
        // Email Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // If custom message is not provided, use default
        if (empty($message)) {
            $mail->Body = "<h3>Hi $name,</h3>
                          <p>Thank you for signing up at ClubSphere. Your account is currently pending approval from an administrator.</p>
                          <p>You will receive another email once your account has been approved.</p>
                          <p>Best regards,<br>ClubSphere Team</p>";
        } else {
            $mail->Body = $message;
        }
        
        // Send Email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
// Handle Login
if (isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Get more user info including profile_image
    $stmt = $conn->prepare("SELECT id, name, password, role, approved, profile_image FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name, $hashed_password, $role, $approved, $profile_image);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        if ($approved == 0) {
            $_SESSION["error"] = "Your account is pending approval. Please check back later.";
            header("Location: login.php");
            exit();
        }

        // Set session variables
        $_SESSION["user_id"] = $user_id;
        $_SESSION["user"] = $name;
        $_SESSION["role"] = $role;
        $_SESSION["profile_image"] = $profile_image;
        
        // Set remember me cookie if requested
        if (isset($_POST["remember"]) && $_POST["remember"] == "on") {
            setcookie("user", $name, time() + 2592000, "/"); // 30 days
        }

        // Record login time
        $update_login = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update_login->bind_param("i", $user_id);
        $update_login->execute();

        // Redirect based on role
        if ($role === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $_SESSION["error"] = "Invalid email or password!";
        header("Location: login.php");
        exit();
    }
}

// Handle Logout
if (isset($_GET["logout"])) {
    // Clean up all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Expire cookies
    setcookie("user", "", time() - 3600, "/");
    
    // Redirect to login
    header("Location: login.php");
    exit();
}
?>  