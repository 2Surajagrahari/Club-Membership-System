<?php
include 'databases.php'; // Ensure this file contains the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];

    // Handle Image Upload
    $image_name = $_FILES['event_image']['name'];
    $image_tmp = $_FILES['event_image']['tmp_name'];
    $upload_directory = "uploads/";
    $image_path = $upload_directory . basename($image_name);

    if (move_uploaded_file($image_tmp, $image_path)) {
        // Insert into database
        $sql = "INSERT INTO events (event_name, event_description, event_date, event_location, event_image)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $event_name, $event_description, $event_date, $event_location, $image_path);

        if ($stmt->execute()) {
            echo "<script>alert('Event added successfully!'); window.location.href='event_planning.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error uploading image.";
    }
}

// Fetch upcoming events
$today = date('Y-m-d');
$sql = "SELECT * FROM events WHERE event_date >= ? ORDER BY event_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

// Count upcoming events
$sql_count = "SELECT COUNT(*) AS event_count FROM events WHERE event_date >= ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("s", $today);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$count_row = $count_result->fetch_assoc();
$event_count = $count_row['event_count'];

// Delete event
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    $delete_sql = "DELETE FROM events WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $event_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Event deleted successfully!'); window.location.href='event_planning.php';</script>";
    } else {
        echo "Error deleting event.";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->

    
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">Event Planning</h1>
            <a href="index.php" class="text-lg bg-white text-blue-600 px-4 py-2 rounded-md font-semibold hover:bg-gray-100 transition">Back to Home</a>
        </div>
    </nav>

    <!-- Main Section -->
    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <!-- Event Form -->
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md mb-10">
            <h2 class="text-2xl font-bold text-center text-indigo-500">Add New Event</h2>

            <form id="eventForm" method="POST" action="event_planning.php" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Event Title</label>
                    <input type="text" name="event_name" placeholder="Event Name" required class="w-full p-3 border rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Description</label>
                    <textarea name="event_description" placeholder="Short event description" required class="w-full p-3 border rounded-md"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Event Date</label>
                    <input type="date" name="event_date" required class="w-full p-3 border rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Event Location</label>
                    <input type="text" name="event_location" placeholder="Event Location" required class="w-full p-3 border rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Upload Image</label>
                    <input type="file" name="event_image" accept="image/*" required class="w-full p-3 border rounded-md">
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold shadow-md hover:scale-105 transition-transform duration-300">
                    Add Event
                </button>
            </form>
        </div>

        <!-- Event List Section -->
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-red-500">Manage Events</h2>
                <p class="text-lg font-bold text-gray-700">Upcoming Events: 
                    <span id="eventCount"><?php echo $event_count; ?></span>
                </p>
            </div>
            <ul id="eventList" class="mt-4 space-y-4">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <li class="bg-gray-100 p-4 rounded-md flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold"><?php echo htmlspecialchars($row['event_name']); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($row['event_date']); ?> | 📍 <?php echo htmlspecialchars($row['event_location']); ?></p>
                        </div>
                        <a href="event_planning.php?delete=<?php echo $row['id']; ?>" class="bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700">
                            Delete
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

</body>
</html>
