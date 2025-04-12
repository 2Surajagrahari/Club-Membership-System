<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "clubsphere";
$conn = new mysqli($host, $user, $pass, $dbname);

// Fetch User Profile Image
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE name = ?");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$stmt->bind_result($profile_image);
$stmt->fetch();
$stmt->close();

// Handle Profile Image Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        // Update database
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE name = ?");
        $stmt->bind_param("ss", $target_file, $_SESSION["user"]);
        $stmt->execute();
        
        // Update session and refresh
        $_SESSION["profile_image"] = $target_file;
        header("Location: edit_profile.php");
        exit();
    } else {
        $error = "❌ Error: Unable to upload the file!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Edit Profile | ClubSphere</title>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">Edit Profile</h2>
        
        <!-- Profile Image -->
        <div class="flex justify-center mb-4">
            <img id="profilePreview" 
                src="<?php echo !empty($profile_image) ? $profile_image : 'uploads/default-profile.png'; ?>" 
                alt="Profile Picture" 
                class="w-24 h-24 rounded-full border-2 border-blue-500 object-cover">
        </div>

        <!-- File Upload Form -->
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <label class="block text-gray-700 font-medium">Upload New Profile Picture</label>
            <input type="file" name="profile_image" id="profileInput" class="w-full p-2 border border-gray-300 rounded-lg cursor-pointer">
            
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
                Upload
            </button>
        </form>

        <!-- Back to Dashboard -->
        <a href="dashboard.php" class="block text-center text-blue-600 mt-4 hover:underline">⬅ Back to Dashboard</a>

        <!-- Error Message -->
        <?php if (!empty($error)) { ?>
            <p class="text-red-500 text-center mt-3"><?php echo $error; ?></p>
        <?php } ?>
    </div>

    <script>
        document.getElementById("profileInput").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("profilePreview").src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
