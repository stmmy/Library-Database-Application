<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="report-fees.css">
<title>Fees Report</title>
<script>
function checkCustom(selected) {
    var customDateInput = document.getElementById('customDate');
    if (selected.value === 'custom') {
        customDateInput.style.display = 'block';
    } else {
        customDateInput.style.display = 'none';
    }
}
</script>
</head>
<body>

<div class="container">
    <div id="title">
        <h1>Cougar Library - Fees Report</h1>
    </div>
    <div id="buttons">
    <button class = "navButton" onclick="document.location='\\librarian-fees\\manage-fees.php'">Manage Fees</button>
                <button class = "navButton" onclick="document.location='\\librarian-fees\\report-fees.php'">Report Fees</button>
                <button class = "navButton" onclick="document.location='\\librarian-fees\\manage-items.php'">Manage Items</button>
                <button class = "navButton" onclick="document.location='\\librarian-fees\\alerts.php'">Alerts</button>
                <button class = "navButton" onclick="document.location='\\librarian-fees\\mostfreq.php'">Frequency Report</button>
                <button class ="navButton" onclick ="document.location='\\librarian-fees\\report-due.php'">Due Soon</button>
                <button class ="navButton" onclick ="document.location='\\index.php'">Logout</button>
    </div>
</div>

<div class="bodyContainer">
    
    <form action="" method="post">

        <label for="amount" id ="mfa">Minimum Fee Amount ($):</label>
        <input type="number" name="amount" id="amount" value="10">
        <input type="submit" value="Generate Report">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $amount = $_POST["amount"];
        $customDays = $_POST["customDays"] ?? null;
        $current_date = date('Y-m-d H:i:s');

        $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
        $username = "gp9library";
        $password = "Securewalls2";
        $dbname = "library";
            
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $query = "SELECT * FROM fees WHERE fee_amount >= $amount";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "<table><tr><th>Student ID</th><th>Date of Fee</th><th>Fee Amount</th></tr>";
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr><td>".$row["f_account_id"]."</td><td>".$row["f_item_id"]."</td><td>".$row["fee_amount"]."</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No results found.";
        }
        mysqli_close($conn);
    }
    ?>
</div>

</body>
</html>