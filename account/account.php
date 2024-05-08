<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>

    <div class="container">
            <div id="title">
                <h1>Cougar Library</h1>
            </div>

            <div id="buttons">
                <button class = "navButton" onclick="document.location='\\item-search\\item-search.php'">Item Search</button>
                <button class = "navButton" onclick="document.location='\\room-search\\room-search.php'">Room Search</button>
                <button class = "navButton" onclick="document.location='\\holds\\holds.php'">Holds</button>
                <button class = "navButton" onclick="document.location='\\checked-items\\checked-items.php'">Checkedout Items</button>
                <button class = "navButton" onclick="document.location='\\fees\\fees.php'">Fees</button>
                <button class = "navButton" onclick="document.location='\\account\\account.php'">Account</button>
                <button class ="navButton" onclick ="document.location='\\index.php'">Logout</button>
        </div>
    </div>

    <?PHP 
        session_start();
        $uid = $_SESSION["ID"];

        $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
        $username = "gp9library";
        $password = "Securewalls2";
        $dbname = "library";
    
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        #get user's name
        $query = "SELECT * FROM accounts WHERE account_id = '$uid'";
        $results = mysqli_query($conn, $query);
        $userInfo = mysqli_fetch_assoc($results);

        #getting checked out IDs
        $checkedItemIds = [];
        $query = "SELECT * FROM checkedout_items WHERE account_id = '$uid'";
        $results = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($results)) {
            array_push($checkedItemIds, $row['item_id']);
        }

        #getting checked out Titles from their IDs
        $displayBooks = [];
        foreach($checkedItemIds as &$id) {
            $query = "SELECT * FROM item_book WHERE ID = '$id'";
            $results = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($results)) {
                array_push($displayBooks, $row['title']);
            }
        }

        #get fees
        $feeIds = [];
        $query = "SELECT * FROM fees where f_account_id = '$uid'";
        $results = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($results)) {
            $feeIds[$row['f_item_id']] = $row['fee_amount'];
        }

        $displayFees = [];
        foreach($feeIds as $id => $feeamt) {
            $query = "SELECT * FROM item_book WHERE ID = '$id'";
            $results = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($results)) {
                $displayFees[$row['title']] = $feeamt;
            }
        }

        #Get reserved Rooms
        $displayRooms = [];
        $query = "SELECT * FROM room_reserved WHERE reserved_by_id = '$uid'";
        $results = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($results)) {
            $displayRooms[$row['room_no']] = $row['reserved_time_start'];
        }
    ?>

    <div class="container2">
        <h2>Account Summary</h2>
        <div class="summary">
        
        <div class="section user-info">
            <h3>User Information</h3>
            <p><strong>Name:</strong> <?PHP echo $userInfo['fname'], " ", $userInfo['lname'];?></p>
            <p><strong>ID:</strong> <?PHP echo $uid?> </p>
        </div>
        <div class="section fees">
            <h3>Overdue Fees</h3>
            <ul>
            <?PHP 
                foreach($displayFees as $feeId => $feeamt) {
                    echo "<li><strong>$feeId:</strong> $$feeamt</li>";
                }
            ?>
            </ul>
        </div>
        <div class="section checked-items">
            <h3>Checked Items</h3>
            <ul>
            <?PHP 
                foreach($displayBooks as &$title) {
                    echo "<li><strong>$title</strong></li>";
                }
            ?>
            </ul>
        </div>
        <div class="section reserved-rooms">
            <h3>Reserved Rooms</h3>
            <ul>
            <?PHP 
                foreach($displayRooms as $rn => $time) {
                    echo "<li><strong>$rn</strong>: $time</li>";
                }
            ?>
            </ul>
        </div>
        </div>
    </div>
</body>
</html>