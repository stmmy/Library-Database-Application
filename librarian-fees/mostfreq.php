<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freq Checkout</title>
    <link rel="stylesheet" href="mostFreq.css">
</head>
<body>
    <div class="container">
        <div id="title">
            <h1>Cougar Library</h1>
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
    <div id="report">
        <h2>Most Frequently Checked-Out Items</h2>
        <br>
        <form action='' method='get'>
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name='sd'>
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name='ed'>
        <input type='submit' name='filter' value='Filter'>
        <p id="timePeriod"></p>
        </form> 
        <table id="checkoutTable">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Number of Checkouts</th>
                </tr>
            </thead>
            <tbody>

            <?php
                session_start();
                $id = $_SESSION["ID"];
                $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
                $username = "gp9library";
                $password = "Securewalls2";
                $dbname = "library";
            
                $conn = mysqli_connect($servername, $username, $password, $dbname);
            

                if (isset($_GET['filter'])) {
                    $sd = new DateTime($_GET['sd']);
                    $ed = new DateTime($_GET['ed']);


                    $sql = "SELECT * FROM checkout_log";
                    $result = mysqli_query($conn, $sql);


                    
                    if ($result->num_rows > 0) {
                        $freq = [];
                        while($row = mysqli_fetch_assoc($result)) {
                            $d = new DateTime($row['check_date']);
                            $df = new DateTime($d->format('Y-m-d'));
                            
                            if ($df < $ed && $df > $sd){
                                if (isset($freq[$row['item_id']])) {
                                    $freq[$row['item_id']] = $freq[$row['item_id']]+1;
                                }
                                else {
                                    $freq[$row['item_id']] = 1;
                                }
                            }
                        }
                        foreach ($freq as $id => $f) {
                            echo "<tr>";
                            echo "<td>" . $id. "</td>";
                            echo "<td>" . $f . "</td>";
                            echo "</tr>";
                        }
      
                    } else {
                        echo "<tr><td colspan='3'>No items found</td></tr>";
                    }
                }
            ?>
            </tbody>
        </table>
        <p id="noResultsMessage" style="display: none; color: red;">No results found for the specified period.</p>
    </div>
</body>
</html>