<?php
session_start();
require 'databases.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_post'])) {
        // Create new post
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id'];
        
        // Handle image upload
        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $uploadsDir = 'uploads/posts/';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);
            
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadsDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $content, $imagePath);
        
        if ($stmt->execute()) {
            $post_id = $conn->insert_id;
            
            // Handle categories
            if (!empty($_POST['categories'])) {
                $stmt = $conn->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                foreach ($_POST['categories'] as $cat_id) {
                    $stmt->bind_param("ii", $post_id, intval($cat_id));
                    $stmt->execute();
                }
            }
            
            $_SESSION['success'] = "Post created successfully!";
            header("Location: community.php");
            exit();
        }
    }
    elseif (isset($_POST['add_comment'])) {
        // Add comment
        $post_id = intval($_POST['post_id']);
        $content = trim($_POST['content']);
        $user_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $content);
        $stmt->execute();
        
        header("Location: community.php#post-".$post_id);
        exit();
    }
    elseif (isset($_POST['react'])) {
        // Handle reaction
        $post_id = intval($_POST['post_id']);
        $user_id = $_SESSION['user_id'];
        $type = $_POST['type'];
        
        // Check if already reacted
        $stmt = $conn->prepare("SELECT id FROM reactions WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Update existing reaction
            $stmt = $conn->prepare("UPDATE reactions SET type = ? WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param("sii", $type, $post_id, $user_id);
        } else {
            // Insert new reaction
            $stmt = $conn->prepare("INSERT INTO reactions (post_id, user_id, type) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $post_id, $user_id, $type);
        }
        $stmt->execute();
        
        header("Location: community.php#post-".$post_id);
        exit();
    }
    elseif (isset($_POST['edit_post'])) {
        // Edit post
        $post_id = intval($_POST['post_id']);
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        
        // Verify ownership
        $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($author_id);
        $stmt->fetch();
        
        if ($author_id == $_SESSION['user_id'] || $_SESSION['role'] === 'admin') {
            $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $content, $post_id);
            $stmt->execute();
            
            // Update categories
            $conn->query("DELETE FROM post_categories WHERE post_id = $post_id");
            if (!empty($_POST['categories'])) {
                $stmt = $conn->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                foreach ($_POST['categories'] as $cat_id) {
                    $stmt->bind_param("ii", $post_id, intval($cat_id));
                    $stmt->execute();
                }
            }
            
            $_SESSION['success'] = "Post updated successfully";
        }
        
        header("Location: community.php#post-".$post_id);
        exit();
    }
    elseif (isset($_POST['delete_post'])) {
        // Delete post
        $post_id = intval($_POST['post_id']);
        
        // Verify ownership
        $stmt = $conn->prepare("SELECT user_id, image FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($author_id, $imagePath);
        $stmt->fetch();
        
        if ($author_id == $_SESSION['user_id'] || $_SESSION['role'] === 'admin') {
            // Delete post
            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            
            // Delete image if exists
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            $_SESSION['success'] = "Post deleted successfully";
        }
        
        header("Location: community.php");
        exit();
    }
}

// Fetch all posts
$posts = $conn->query("
    SELECT p.*, u.name, u.profile_image 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");

// Fetch all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community - ClubSphere</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .reaction-btn.active {
            transform: scale(1.2);
            transition: transform 0.2s;
        }
        .category-tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 12px;
            margin-right: 4px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-4 max-w-4xl">
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Create Post Form (Visible to logged in users) -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">Create a Post</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <input type="text" name="title" placeholder="Post title" 
                               class="w-full p-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <textarea name="content" rows="3" placeholder="What's on your mind?" 
                                  class="w-full p-2 border rounded" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Categories</label>
                        <div class="flex flex-wrap gap-2">
                            <?php while ($cat = $categories->fetch_assoc()): ?>
                                <label class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                                    <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>">
                                    <span class="text-sm" style="color: <?= $cat['color'] ?>"><?= htmlspecialchars($cat['name']) ?></span>
                                </label>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Image (Optional)</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <button type="submit" name="create_post" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Post
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Posts Feed -->
        <div class="space-y-6">
            <?php if ($posts->num_rows > 0): ?>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <div id="post-<?= $post['id'] ?>" class="bg-white rounded-lg shadow p-6">
                        <!-- Post Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <img src="<?= htmlspecialchars($post['profile_image'] ?? 'uploads/default.png') ?>" 
                                     class="w-10 h-10 rounded-full mr-3">
                                <div>
                                    <h3 class="font-semibold"><?= htmlspecialchars($post['name']) ?></h3>
                                    <p class="text-gray-500 text-sm">
                                        <?= date('M j, Y g:i a', strtotime($post['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $post['user_id'] || $_SESSION['role'] === 'admin')): ?>
                                <div class="flex space-x-2">
                                    <!-- Edit Button -->
                                    <button onclick="openEditModal(<?= $post['id'] ?>, '<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($post['content'], ENT_QUOTES) ?>')"
                                            class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button type="submit" name="delete_post" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Post Categories -->
                        <?php 
                        $postCategories = $conn->query("
                            SELECT c.id, c.name, c.color 
                            FROM post_categories pc 
                            JOIN categories c ON pc.category_id = c.id 
                            WHERE pc.post_id = {$post['id']}
                        ");
                        if ($postCategories->num_rows > 0): ?>
                            <div class="mb-3">
                                <?php while ($cat = $postCategories->fetch_assoc()): ?>
                                    <span class="category-tag" style="background-color: <?= $cat['color'] ?>20; color: <?= $cat['color'] ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </span>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Post Content -->
                        <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($post['title']) ?></h2>
                        <p class="text-gray-700 mb-4 whitespace-pre-line"><?= htmlspecialchars($post['content']) ?></p>
                        
                        <!-- Post Image -->
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" 
                                 class="max-w-full h-auto rounded mb-4">
                        <?php endif; ?>
                        
                        <!-- Reactions -->
                        <div class="flex items-center border-t border-b py-2 my-3">
                            <?php
                            $reactions = $conn->query("
                                SELECT type, COUNT(*) as count 
                                FROM reactions 
                                WHERE post_id = {$post['id']} 
                                GROUP BY type
                            ");
                            $userReaction = isset($_SESSION['user_id']) ? 
                                $conn->query("SELECT type FROM reactions WHERE post_id = {$post['id']} AND user_id = {$_SESSION['user_id']}")->fetch_assoc() : null;
                            ?>
                            
                            <div class="flex items-center mr-4">
                                <?php while ($react = $reactions->fetch_assoc()): ?>
                                    <span class="text-sm mr-2"><?= $react['count'] ?> <?= $react['type'] ?></span>
                                <?php endwhile; ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="flex space-x-2">
                                    <form method="POST">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <input type="hidden" name="type" value="like">
                                        <button type="submit" name="react" 
                                                class="<?= ($userReaction && $userReaction['type'] === 'like') ? 'text-blue-500 reaction-btn active' : 'text-gray-500 reaction-btn' ?>">
                                            <i class="far fa-thumbs-up"></i> Like
                                        </button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <input type="hidden" name="type" value="love">
                                        <button type="submit" name="react" 
                                                class="<?= ($userReaction && $userReaction['type'] === 'love') ? 'text-red-500 reaction-btn active' : 'text-gray-500 reaction-btn' ?>">
                                            <i class="far fa-heart"></i> Love
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Comments Section -->
                        <div class="mt-4">
                            <h4 class="font-semibold mb-3">Comments</h4>
                            
                            <!-- Comment Form (for logged in users) -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" class="mb-6">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <div class="flex">
                                        <img src="<?= htmlspecialchars($_SESSION['profile_image'] ?? 'uploads/default.png') ?>" 
                                             class="w-8 h-8 rounded-full mr-2">
                                        <textarea name="content" placeholder="Add a comment..." 
                                                  class="flex-1 border rounded-lg p-2" required></textarea>
                                    </div>
                                    <button type="submit" name="add_comment" 
                                            class="mt-2 bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                        Post Comment
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <!-- Comments List -->
                            <?php
                            $comments = $conn->query("
                                SELECT c.*, u.name, u.profile_image 
                                FROM comments c 
                                JOIN users u ON c.user_id = u.id 
                                WHERE c.post_id = {$post['id']} 
                                ORDER BY c.created_at DESC
                            ");
                            ?>
                            <div class="space-y-4">
                                <?php while ($comment = $comments->fetch_assoc()): ?>
                                    <div class="flex">
                                        <img src="<?= htmlspecialchars($comment['profile_image']) ?>" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div class="flex-1">
                                            <div class="bg-gray-100 rounded-lg p-3">
                                                <div class="flex justify-between">
                                                    <span class="font-semibold"><?= htmlspecialchars($comment['name']) ?></span>
                                                    <span class="text-xs text-gray-500">
                                                        <?= date('M j, g:i a', strtotime($comment['created_at'])) ?>
                                                    </span>
                                                </div>
                                                <p class="mt-1 whitespace-pre-line"><?= htmlspecialchars($comment['content']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-500">No posts yet. Be the first to post!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Edit Post</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="post_id" id="editPostId">
                <input type="hidden" name="edit_post" value="1">
                
                <div class="mb-4">
                    <input type="text" name="title" id="editTitle" 
                           class="w-full p-2 border rounded" required>
                </div>
                
                <div class="mb-4">
                    <textarea name="content" id="editContent" rows="3" 
                              class="w-full p-2 border rounded" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Categories</label>
                    <div class="flex flex-wrap gap-2" id="editCategories">
                        <?php 
                        $categories->data_seek(0); // Reset categories pointer
                        while ($cat = $categories->fetch_assoc()): ?>
                            <label class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                                <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>">
                                <span class="text-sm" style="color: <?= $cat['color'] ?>"><?= htmlspecialchars($cat['name']) ?></span>
                            </label>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" 
                            class="px-4 py-2 border rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open edit modal with post data
        function openEditModal(postId, title, content) {
            document.getElementById('editPostId').value = postId;
            document.getElementById('editTitle').value = title;
            document.getElementById('editContent').value = content;
            
            // Fetch post categories and check the boxes
            fetch(`get_post_categories.php?post_id=${postId}`)
                .then(response => response.json())
                .then(categories => {
                    const checkboxes = document.querySelectorAll('#editCategories input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = categories.includes(parseInt(checkbox.value));
                    });
                    
                    document.getElementById('editModal').classList.remove('hidden');
                });
        }
        
        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    </script>
</body>
</html>