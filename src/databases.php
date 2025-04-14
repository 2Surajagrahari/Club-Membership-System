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
?>