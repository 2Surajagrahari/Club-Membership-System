<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance & Budgeting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">Finance & Budgeting</h1>
            <a href="index.php" class="text-lg bg-white text-blue-600 px-4 py-2 rounded-md font-semibold hover:bg-gray-100 transition">Back to Home</a>
        </div>
    </nav>

    <!-- Finance Section -->
    <section class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-3xl font-bold mb-4 text-center">Club Financial Management</h2>

        <!-- Summary -->
        <div class="flex justify-between bg-blue-100 p-4 rounded-md">
            <p class="text-lg font-semibold">Total Income: <span id="total-income" class="text-green-600">$0</span></p>
            <p class="text-lg font-semibold">Total Expenses: <span id="total-expense" class="text-red-600">$0</span></p>
            <p class="text-lg font-semibold">Balance: <span id="balance" class="text-blue-600">$0</span></p>
        </div>

        <!-- Add Finance Entry -->
        <div class="mt-6">
            <h3 class="text-2xl font-bold">Add Transaction</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <input type="text" id="desc" placeholder="Description" class="p-2 border rounded-md w-full">
                <input type="number" id="amount" placeholder="Amount" class="p-2 border rounded-md w-full">
                <input type="date" id="date" class="p-2 border rounded-md w-full">
                <select id="type" class="p-2 border rounded-md w-full">
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
                <select id="category" class="p-2 border rounded-md w-full">
                    <option value="General">General</option>
                    <option value="Rent">Rent</option>
                    <option value="Salaries">Salaries</option>
                    <option value="Events">Events</option>
                    <option value="Equipment">Equipment</option>
                </select>
                <button onclick="addTransaction()" class="col-span-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Add</button>
            </div>
        </div>

        <!-- Filters -->
        <!-- <div class="mt-6 bg-gray-200 p-4 rounded-md">
            <h3 class="text-xl font-bold">Filter Transactions</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <select id="filter-type" class="p-2 border rounded-md w-full">
                    <option value="all">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
                <select id="filter-category" class="p-2 border rounded-md w-full">
                    <option value="all">All Categories</option>
                    <option value="Rent">Rent</option>
                    <option value="Salaries">Salaries</option>
                    <option value="Events">Events</option>
                    <option value="Equipment">Equipment</option>
                </select>
                <input type="date" id="from-date" class="p-2 border rounded-md w-full">
                <input type="date" id="to-date" class="p-2 border rounded-md w-full">
            </div>
        </div> -->


        <!-- Transaction History -->
        <h3 class="text-2xl font-bold mt-6"> Transaction History</h3>
        <table class="w-full mt-4 border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Description</th>
                    <th class="p-2">Amount</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody id="transaction-list"></tbody>
        </table>

        <!-- Charts Section -->
        <section class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-2xl font-bold text-center mb-4">Financial Overview</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Income vs Expenses Pie Chart -->
                <div class="bg-gray-100 p-4 rounded-md shadow-sm flex flex-col items-center">
                    <h4 class="text-lg font-semibold mb-2">Income vs Expenses</h4>
                    <canvas id="pieChart"></canvas>
                </div>

                <!-- Financial Trends Bar Chart -->
                <div class="bg-gray-100 p-4 rounded-md shadow-sm flex flex-col items-center">
                    <h4 class="text-lg font-semibold mb-2">Financial Trends</h4>
                    <canvas id="barChart"></canvas>
                </div>

                <!-- Budget Categories Doughnut Chart -->
                <div class="bg-gray-100 p-4 rounded-md shadow-sm flex flex-col items-center">
                    <h4 class="text-lg font-semibold mb-2">Budget Categories</h4>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </section>
        <button onclick="downloadReport()" class="mt-6 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Download Finance Report</button>
    </section>

    
    

    <script>
        let transactions = JSON.parse(localStorage.getItem("transactions")) || [];
        let pieChart, barChart, categoryChart;

        function updateFinance() {
            let totalIncome = 0, totalExpense = 0;
            let categoryTotals = {};
            document.getElementById("transaction-list").innerHTML = "";

            transactions.forEach((transaction, index) => {
                if (transaction.type === "income") totalIncome += transaction.amount;
                else totalExpense += transaction.amount;

                if (transaction.type === "expense") {
                    categoryTotals[transaction.category] = (categoryTotals[transaction.category] || 0) + transaction.amount;
                }

                document.getElementById("transaction-list").innerHTML += `
                    <tr class="border">
                        <td class="p-2">${transaction.desc}</td>
                        <td class="p-2">$${transaction.amount}</td>
                        <td class="p-2 ${transaction.type === "income" ? 'text-green-600' : 'text-red-600'}">${transaction.type}</td>
                        <td class="p-2">${transaction.category}</td>
                        <td class="p-2">${transaction.date ? new Date(transaction.date).toLocaleDateString() : "N/A"}</td>
                        <td class="p-2">
                            <button onclick="deleteTransaction(${index})" class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-700">Delete</button>
                        </td>
                    </tr>`;
            });

            document.getElementById("total-income").textContent = `$${totalIncome}`;
            document.getElementById("total-expense").textContent = `$${totalExpense}`;
            document.getElementById("balance").textContent = `$${totalIncome - totalExpense}`;
            localStorage.setItem("transactions", JSON.stringify(transactions));

            updateCharts(totalIncome, totalExpense, categoryTotals);
        }

        function downloadReport() {
            let report = "Description, Amount, Type, Category\n";
            transactions.forEach(tr => {
                report += `${tr.desc}, ${tr.amount}, ${tr.type}, ${tr.category}\n`;
            });

            let blob = new Blob([report], { type: "text/csv" });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "finance_report.csv";
            link.click();
        }

        function updateCharts(income, expense, categoryTotals) {
    if (pieChart) pieChart.destroy();
    if (barChart) barChart.destroy();
    if (categoryChart) categoryChart.destroy();

    let categories = Object.keys(categoryTotals);
    let categoryValues = Object.values(categoryTotals);

    // If no expenses exist, add a placeholder to avoid "undefined" labels
    if (categories.length === 0) {
        categories = ["No Expenses"];
        categoryValues = [0]; 
    }

    // Income vs Expense Pie Chart
    let ctx1 = document.getElementById("pieChart").getContext("2d");
    pieChart = new Chart(ctx1, {
        type: "pie",
        data: {
            labels: ["Income", "Expense"],
            datasets: [{ data: [income, expense], backgroundColor: ["#4CAF50", "#F44336"] }]
        }
    });

    // Financial Trends Bar Chart (Fixed Undefined Issue)
    let ctx2 = document.getElementById("barChart").getContext("2d");
    barChart = new Chart(ctx2, {
        type: "bar",
        data: {
            labels: categories, // Now always has valid labels
            datasets: [{
                label: "Expenses by Category",
                data: categoryValues, // Now always has valid data
                backgroundColor: "#2196F3"
            }]
        }
    });

    // Budget Categories Doughnut Chart
    let ctx3 = document.getElementById("categoryChart").getContext("2d");
    categoryChart = new Chart(ctx3, {
        type: "doughnut",
        data: {
            labels: categories,
            datasets: [{ data: categoryValues, backgroundColor: ["#FF9800", "#03A9F4", "#E91E63", "#8BC34A", "#FFEB3B"] }]
        }
    });
}


        function addTransaction() {
            let desc = document.getElementById("desc").value;
            let amount = parseFloat(document.getElementById("amount").value);
            let type = document.getElementById("type").value;
            let category = document.getElementById("category").value;
            let date = document.getElementById("date").value; 

            transactions.push({ desc, amount, type, category, date });
            localStorage.setItem("transactions", JSON.stringify(transactions));
            updateFinance();
        }

        function deleteTransaction(index) {
            transactions.splice(index, 1);
            updateFinance();
        }

        function filterTransactions() {
    let filterType = document.getElementById("filter-type").value;
    let filterCategory = document.getElementById("filter-category").value;
    let fromDate = document.getElementById("from-date").value;
    let toDate = document.getElementById("to-date").value;

    let filteredTransactions = transactions.filter(transaction => {
        let transactionDate = new Date(transaction.date).getTime();
        let from = fromDate ? new Date(fromDate).getTime() : null;
        let to = toDate ? new Date(toDate).getTime() : null;

        let typeMatch = filterType === "all" || transaction.type === filterType;
        let categoryMatch = filterCategory === "all" || transaction.category === filterCategory;
        let dateMatch = (!from || transactionDate >= from) && (!to || transactionDate <= to);

        return typeMatch && categoryMatch && dateMatch;
    });

    displayTransactions(filteredTransactions);
}

function displayTransactions(transactionList) {
    let totalIncome = 0, totalExpense = 0;
    let categoryTotals = {};
    let transactionHTML = "";

    transactionList.forEach((transaction, index) => {
        if (transaction.type === "income") totalIncome += transaction.amount;
        else totalExpense += transaction.amount;

        if (transaction.type === "expense") {
            categoryTotals[transaction.category] = (categoryTotals[transaction.category] || 0) + transaction.amount;
        }

        transactionHTML += `
            <tr class="border">
                <td class="p-2">${transaction.desc}</td>
                <td class="p-2">$${transaction.amount}</td>
                <td class="p-2 ${transaction.type === "income" ? 'text-green-600' : 'text-red-600'}">${transaction.type}</td>
                <td class="p-2">${transaction.category}</td>
                <td class="p-2">${transaction.date || "N/A"}</td>
                <td class="p-2">
                    <button onclick="deleteTransaction(${index})" class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-700">Delete</button>
                </td>
            </tr>`;
    });

    document.getElementById("transaction-list").innerHTML = transactionHTML;
    document.getElementById("total-income").textContent = `$${totalIncome}`;
    document.getElementById("total-expense").textContent = `$${totalExpense}`;
    document.getElementById("balance").textContent = `$${totalIncome - totalExpense}`;
}

// Attach filter event listeners
document.getElementById("filter-type").addEventListener("change", filterTransactions);
document.getElementById("filter-category").addEventListener("change", filterTransactions);
document.getElementById("from-date").addEventListener("change", filterTransactions);
document.getElementById("to-date").addEventListener("change", filterTransactions);


        document.addEventListener("DOMContentLoaded", updateFinance);

        
    </script>

</body>
</html>
