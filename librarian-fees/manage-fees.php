<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Library Fees</title>
    <link rel="stylesheet" href="manage_fees.css">
</head>
<body>
    <div class="container">
        <div id="title">
            <h1>Cougar Library - Fee Management</h1>
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
        <h1>Fee Details</h1>

        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Fee Amount</th>
                        <th> Change </th>
                    </tr>
                </thead>
                <tbody>

<?php
session_start();
$uid = $_SESSION["ID"];

$servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
$username = "gp9library";
$password = "Securewalls2";
$dbname = "library";
    
$conn = mysqli_connect($servername, $username, $password, $dbname);


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_fees'])) {
    $search_account_id = $_POST['search_account_id'];
    
    $sql = "SELECT * FROM fees WHERE f_account_id = '$search_account_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row["f_item_id"] ;
            $aid = $row['f_account_id'];
            echo "<form action='' method='get'>";
            echo "<tr>";
            echo "<td>" . $row["f_item_id"] . "</td>";
            echo "<td>" . $row["fee_amount"] . "</td>";
            echo "<td>";
            echo "<input type='hidden' name='id' value='$id'>";
            echo "<input type='hidden' name='aid' value='$aid'>";
            echo "<input type='text' name='amt' placeholder='change amount'>";
            echo "<input type='submit' name='change_fee' value='Change'>";
            echo "</td>";
            echo "</tr>";
            echo "</form>";
        }
    } else {
        echo "<tr><td colspan='4'>No fees found for Account ID $search_account_id.</td></tr>";
    }
}

if (isset($_GET['change_fee'])) {
    $updnum = $_GET['amt'];
    $id = $_GET['id'];
    $aid = $_GET['aid'];
    $query = "UPDATE fees SET fee_amount = $updnum WHERE f_item_id = '$id' AND f_account_id = '$aid'";
    mysqli_query($conn, $query);
    header('location: \\librarian-fees\\manage-fees.php');
}

$conn->close();
?>

                </tbody>
            </table>
            <form method="post" class="box">
            <input type="text" name="search_account_id" placeholder="Search Account ID" required>
            <input type="submit" name="search_fees" value="Search Fees">
        </form>
        </div>
    </div>






</body>
</html>

