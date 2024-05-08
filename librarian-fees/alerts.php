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
    <?php
        session_start();
        $uid = $_SESSION["ID"];

        $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
        $username = "gp9library";
        $password = "Securewalls2";
        $dbname = "library";
            
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        $query = "SELECT * FROM hold_attempts_logs";
        $results = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($results)) {
            $id = $row['item_id'];
            $uid = $row['account_id'];
            $msg = $row['message'];
            echo "<h2>Hold limit reached on item: $id</h2>";
        }
        
    ?>
</div>

</body>
</html>