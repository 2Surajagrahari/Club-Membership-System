<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Design & Marketing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">Design & Marketing</h1>
            <a href="index.php" class="text-lg bg-white text-blue-600 px-4 py-2 rounded-md font-semibold hover:bg-gray-100 transition">Back to Home</a>
        </div>
    </nav>

    <!-- Upload Section -->
    <div class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-6">
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md mb-10">
            <h2 class="text-2xl font-bold text-center text-indigo-500">Upload Poster</h2>
            <form id="posterForm">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Poster Title</label>
                    <input type="text" id="posterTitle" placeholder="Enter poster title" required class="w-full p-3 border rounded-md">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Upload Poster</label>
                    <input type="file" id="posterImage" accept="image/*" required class="w-full p-3 border rounded-md">
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md font-semibold shadow-md hover:scale-105 transition-transform duration-300">
                    Upload Poster
                </button>
            </form>
        </div>

        <!-- Poster Gallery -->
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-3xl">
            <h2 class="text-2xl font-bold text-center text-green-500">Poster Gallery</h2>
            <div id="posterGallery" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6"></div>
        </div>
    </div>

    <script>
        document.getElementById("posterForm").addEventListener("submit", function(event) {
            event.preventDefault();

            let title = document.getElementById("posterTitle").value;
            let imageInput = document.getElementById("posterImage");

            if (!imageInput.files.length) {
                alert("Please upload an image!");
                return;
            }

            let reader = new FileReader();

            reader.onload = function(e) {
                let imageBase64 = e.target.result;
                let posters = JSON.parse(localStorage.getItem("posters")) || [];

                let newPoster = {
                    id: Date.now(),
                    title: title,
                    image: imageBase64
                };

                posters.push(newPoster);
                localStorage.setItem("posters", JSON.stringify(posters));

                alert("✅ Poster uploaded successfully!");
                window.location.href = "index.php";  // Redirect to homepage
            };

            reader.readAsDataURL(imageInput.files[0]);
        });

        function loadPosters() {
            let posters = JSON.parse(localStorage.getItem("posters")) || [];
            let gallery = document.getElementById("posterGallery");
            gallery.innerHTML = "";

            if (posters.length === 0) {
                gallery.innerHTML = "<p class='text-center text-gray-500 col-span-3'></p>";
                return;
            }

            posters.forEach(poster => {
                let posterCard = document.createElement("div");
                posterCard.classList.add("bg-gray-100", "p-4", "rounded-md", "shadow-md", "flex", "flex-col", "items-center");

                posterCard.innerHTML = `
                    <img src="${poster.image}" alt="Poster Image" class="w-full h-40 object-cover rounded-md mb-3">
                    <h3 class="text-lg font-bold text-center">${poster.title}</h3>
                    <button onclick="deletePoster(${poster.id})" class="mt-3 bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700">
                        Delete
                    </button>
                `;

                gallery.appendChild(posterCard);
            });
        }

        function deletePoster(posterId) {
            let posters = JSON.parse(localStorage.getItem("posters")) || [];
            let updatedPosters = posters.filter(poster => poster.id !== posterId);

            localStorage.setItem("posters", JSON.stringify(updatedPosters));
            loadPosters(); // Refresh the gallery
        }

        document.addEventListener("DOMContentLoaded", loadPosters);
    </script>

</body>
</html>
