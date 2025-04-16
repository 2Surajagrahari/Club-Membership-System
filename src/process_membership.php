<?php
require_once 'databases.php'; // Your existing database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        die("Please fill all required fields.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Save to database
    try {
        $stmt = $conn->prepare("INSERT INTO membership_applications (name, email, message, application_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $message);
        
        if ($stmt->execute()) {
            // Send confirmation email to applicant
            $applicantSubject = "Thank you for your membership application";
            $applicantBody = "
                <h2>Dear $name,</h2>
                <p>Thank you for applying to join our club. We've received your application with the following details:</p>
                <p><strong>Your message:</strong> $message</p>
                <p>Our team will review your application and get back to you within 5-7 business days.</p>
                <p>Best regards,<br>The Club Team</p>
            ";
            
            // Send notification to admin
            $adminSubject = "New Membership Application: $name";
            $adminBody = "
                <h2>New Membership Application</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong> $message</p>
                <p><strong>Application Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                <p>Please review this application in the admin panel.</p>
            ";
            
            // Use your existing email function
            if (sendClubSphereEmail($email, $name, $applicantSubject, $applicantBody)) {
                // Send to admin (replace with your admin email)
                sendClubSphereEmail('surajagrahari265@gmail.com', 'Club Admin', $adminSubject, $adminBody);
                
                // Redirect to thank you page
                header("Location: thank_you.php");
                exit();
            } else {
                throw new Exception("Failed to send confirmation email.");
            }
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Membership application error: " . $e->getMessage());
        die("An error occurred while processing your application. Please try again later.");
    }
} else {
    header("Location: membership.php");
    exit();
}
?>