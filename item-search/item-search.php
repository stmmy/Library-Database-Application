<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="item-search.css">
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
    <div class="header"><h2>Item Search</h2></div>
    
    <form action="" method="GET">
        
        <div class="search-wrapper">
        <div class="input-icon-container">
            <input type="text" name="search" placeholder="search" class="search-input">
            </div>
        </div>
        </div>
        
        <div class="dropdowns-container">
            <input type='submit' value='submit' name='submit' id="srch">
        </div>
    </form>


<div class="results">

    <?php
    mysqli_report(MYSQLI_REPORT_OFF);
    session_start();
    $uid = $_SESSION["ID"];

    $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
    $username = "gp9library";
    $password = "Securewalls2";
    $dbname = "library";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (isset($_GET['submit'])) {
        #get not checked out books
        $s = $_GET['search'];
        $books = [];
        $query = "SELECT ID, ISSN, title, author from item_book WHERE title LIKE '%$s%' AND ID NOT IN (SELECT item_id FROM checkedout_items);";
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {
           $books[$row['ID']] = array($row['title'], $row['author'], $row['ISSN']);
        }
        mysqli_free_result($result);


        ## TODO
        #get checked out books that isnt already checked out by user and user does not have a hold on them
        $checkedBooks = [];
        $query = "SELECT ID, title, author, ISSN FROM item_book WHERE ID IN
        (SELECT item_id FROM checkedout_items 
        WHERE account_id != '$uid' 
        AND title LIKE '%$s%'
        AND item_id NOT IN (SELECT h_item_id FROM holds_waitlist WHERE h_account_id = '$uid'))";
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)) {$checkedBooks[$row['ID']] = array($row['title'], $row['author'], $row['ISSN']);}
        mysqli_free_result($result);

        #display items for user
        foreach($books as $id => $arr) {
            echo "<form action='' method='GET'>";
            echo "<div class='result-row'>" ;
                echo "<div class='text-content'>";
                    echo "<h3>$arr[0]</h3>";
                    echo "<p>Author: $arr[1]</p>";
                    echo "<p>ISSN: $arr[2]</p>";
                echo "</div>"; 
                echo "<input type='hidden' name='item_id' value='$id'>";
                echo "<input type='submit' class='subin' value='Checkout' name='checkout'>";
            echo "</div>"; 
            echo "</form>";
        }

        #display checked items for user
        foreach($checkedBooks as $id => $arr) {
            echo "<form action='' method='GET'>";
            echo "<div class='result-row'>" ;
                echo "<div class='text-content'>";
                    echo "<h3>$arr[0]</h3>";
                    echo "<p>Author: $arr[1]</p>";
                    echo "<p>ISSN: $arr[2]</p>";
                echo "</div>"; 
                echo "<input type='hidden' name='item_id' value='$id'>";
                echo "<input type='submit' class='subin' value='Place Hold' name='hold'>";
            echo "</div>"; 
            echo "</form>";
        }

        unset($_GET['submit']);
    }


    if (isset($_GET['checkout'])){
        $id = $_GET['item_id'];
        $uid = $_SESSION['ID'];
        $date = date("y-m-d h:i:s");
        $query = "SELECT * FROM checkedout_items WHERE item_id = '$id'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            $query = "INSERT INTO checkedout_items(item_type, item_id, account_id, checkout_date,fees) VALUES ('book', '$id', '$uid', '$date', 0)";
            $success = mysqli_query($conn, $query);
            if ($success == true) {
                mysqli_free_result($result);
                header('location: item-search.php?search=&table=item_book&submit=submit');
            }
            else {
                $err_msg = mysqli_error($conn);
                echo '<script type="text/javascript">';
                echo 'window.onload = function() {';
                echo 'alert("' . $err_msg . '");';
                echo '}';
                echo '</script>';
            }
        }

        unset($_GET['checkout']);
    }

    if (isset($_GET['hold'])) {
        # get all people in queue for item
        # find last spot in queue
        # add entry with user in last position in queue
        $id = $_GET['item_id'];
        $uid = $_SESSION['ID'];
        $query = "SELECT * FROM holds_waitlist WHERE h_item_id = '$id'";
        $result = mysqli_query($conn, $query);
        $highestPos = 0;
        $highestUID = '';

        while($row = mysqli_fetch_assoc($result)) {
            if ($row['position'] > $highestPos) {
                $highestPos = $row['position'];
                $highestUID = $row['h_account_id'];
            }
        }
        mysqli_free_result($result);

        $highestPos += 1;
        $query = "INSERT INTO holds_waitlist(h_item_id, position, h_account_id) VALUES ('$id', $highestPos, '$uid')";
        
        $success = mysqli_query($conn, $query);
        if ($success === true) {
            header('location: item-search.php?search=&table=item_book&submit=submit');
        }
        else {
            $query = "INSERT INTO hold_attempts_logs(item_id, account_id, message) VALUES ('$id', '$uid', 'Hold Limit Exceeded')";
            mysqli_query($conn, $query);
            header('location: item-search.php?search=&table=item_book&submit=submit');
        }
    }  
    ?>
</div>

</body>
</html>