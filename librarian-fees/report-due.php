
<!-- Self Note: check the checkedout_items table and see their checkout date and report on the ones soon to be due (2 weeks for students, 3 weeks for teachers) -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="report-due.css">
</head>

<body>
    <?php 
    session_start();
    
    $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
    $username = "gp9library";
    $password = "Securewalls2";
    $dbname = "library";
        
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    #query holds_waitlist (returns itemId, accountId, and position in queue for that item)
    $holds = []; #account id = [position, itemid]
    $id = $_SESSION["ID"];
    ?>
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
    
    <div class = "due-item-container">
    <h2 id="select-report">Select Checked Out Items </h2>
    
    <form method="post">
    <select class="form-control" name="standard">
        <option value="option1">Select Account Type</option>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select>
    
    <select class = "form-control" name = "select-days">
        <option value = "default"> Select Number of Days </option>
        <option value="1">1</option>
        <option value="7">7</option>
        <option value="14">14</option>
        <option value="21">21</option>
    </select>
    <button type="submit">Submit</button>
    </form>

    </div>
    
    <?php

// Check if form is submitted
//Create hash table
$sql = "SELECT item_id, checkout_date FROM checkedout_items WHERE item_type = 'book'";
$result = mysqli_query($conn, $sql);
// Stores the due dates of the 
$dueDate_hashTable = [];

// Check if any rows were returned
if (mysqli_num_rows($result) > 0) {
    // Fetch each row from the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate due date (for example, adding 2 weeks to the checkout date)
        $due_date = date('Y-m-d h:i:s', strtotime($row['checkout_date'] . ' + 1 weeks'));
        
        // Add the item_id and due_date to the hash table
        $dueDate_hashTable[$row['item_id']] = $due_date;
    }
} else {
    echo "No books found in the database.";
}
//print out each
// foreach ($dueDate_hashTable as $item_id => $due_date) {
//     echo "Item ID: $item_id, Due Date: $due_date<br>";
// }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if the 'standard' field is set in the form data
        if (isset($_POST['standard'])) {
            // Retrieve the selected value from the 'standard' field
            $account_types = $_POST['standard']; //account type
            $ndays = $_POST['select-days']; // value for the number of days to filter
            $duration = 0;

            //Get all the checked out items that have the matching account type
            $query = "SELECT checkedout_items.*
            FROM checkedout_items
            JOIN accounts ON checkedout_items.account_id = accounts.account_id
            WHERE accounts.account_type = '$account_types'";


            
            $result1 = mysqli_query($conn, $query);
            
            // Creates headers 
            echo "<h2>Items Due within $ndays Days:</h2>";
            echo "<table border='1'>";
            echo    "<tr>
                    <th>Item Name</th>
                    <th>Item ID</th>
                    <th>Checked Out By</th>
                    <th>Due Date</th>
                    </tr>";

            if($result1){
                while($row = mysqli_fetch_assoc($result1)){ // gets each row of info
                    $accountId = $row['account_id'];
                    $indv_item_Id = $row['item_id'];
                    $indv_due_date = $dueDate_hashTable[$row['item_id']];  // Get the due date for each indivual item

                    $due_date_within = date('Y-m-d h:i:s', strtotime("+$ndays days", strtotime('now')));
                    
                    // Get the book title 
                    $query = "SELECT * FROM item_book WHERE  ID = '$indv_item_Id'";
                    $result2 = mysqli_query($conn,$query);

                    if ($result2){
                        while ($row2 = mysqli_fetch_assoc($result2)){
                            $itemTitle = $row2['title'];
                            if($indv_due_date <= $due_date_within ){
                                echo "<tr>";
                                echo "<td>".$itemTitle."</td>";
                                echo "<td>".$row['item_id']."</td>";
                                echo "<td>".$row['checkout_date']."</td>";
                                echo "<td>".$indv_due_date."</td>"; // Display due date
                                echo "</tr>";
        
                                
                            }
                        }
                    }

                    }
                    echo "</table>";

                    // Get items from accounts that are =account_types and the due dates is less than the ndays selected
                    // $query2 = "SELECT * FROM checkedout_items WHERE item_id = '$indv_item_Id' AND checkout_date <= '$due_date_week'";

                } else {
                    // Displays error message if query fails
                    echo "Error: " . mysqli_error($conn);
                }
            }
            
           

            
            }
    
    // Close database connection
    mysqli_close($conn);


    ?>


</body>

</html>