<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="checkoutpage.css">

<title>Checked out items</title>

</head>
<body>

<div class="container2">
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

<div class="container">
  <h2>Checked out items</h2>
  <div class="box">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Checked Out</th>
          <th>Check In By</th>
          <th>Checkin</th>
        </tr>
      </thead>

      
  <?PHP
    session_start();
    $uid = $_SESSION["ID"];

    $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
    $username = "gp9library";
    $password = "Securewalls2";
    $dbname = "library";

    $conn = mysqli_connect($servername, $username, $password, $dbname);


    $checkedItems = [];
    $query = "SELECT * FROM checkedout_items WHERE account_id = '$uid'";
    $results = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($results)) {
      $checkedItems[$row['item_id']] = array($row['checkout_date'], $row['item_type']);
    }


    echo "<tbody>";
    foreach ($checkedItems as $item_id => $arr) {
      
      $name = '';
      if ($arr[1] = 'book') {
        $query = "SELECT title from item_book WHERE ID = '$item_id'";
        $results = mysqli_query($conn, $query);
        while ($row = mysqli_fetch_assoc($results)) {
          $name = $row['title']; 
        }
      }
      
      $dt = new DateTime($arr[0]);
      $dt->add(new DateInterval('P1W'));
      $nd = $dt->format('Y-m-d H:i:s');



      echo "<form action='' method='get'>";
      echo "<tr>";
      echo "<td>$name</td>";
      echo "<td>$arr[0]</td>";
      echo "<td>$nd</td>";
      echo "<input type='hidden' name='id' value='$item_id'>";
      echo "<input type='hidden' name='date' value='$arr[0]'>"; 
      echo "<td><input type='submit' name='checkin' value='Check In'></td>";
      echo "</tr>";
      echo "</form>";
    }
    echo "</tbody>";

    if (isset($_GET['checkin'])) {
      #delete user entry in checkedout_items
      #if there are holds on the item have the first person in queue checkout the item
      #lower everyone esle's position in the queue for the item
      $id = $_GET['id'];

      #check if item is overdue and add fees if it is
      $currentDateTime = new DateTime(); 
      $days_diff = date_diff(new DateTime($_GET['date']), $currentDateTime)->days;
      $feeamt = $days_diff * 0.5;

      if ($feeamt > 0) {
        $query = "INSERT INTO fees(f_item_id, f_account_id, fee_amount) VALUES ('$id', '$uid', '$feeamt')";
        mysqli_query($conn, $query);
      }

      #delete user's checked entry
      $query = "DELETE FROM checkedout_items WHERE item_id = '$id' and account_id = '$uid'";
      mysqli_query($conn, $query);

      #check if there is hold on the item
      $query = "SELECT * FROM holds_waitlist where h_item_id = '$id'";
      $results = mysqli_query($conn, $query);

      if (mysqli_num_rows($results) > 0) {

        while($row = mysqli_fetch_assoc($results)) {
          $tuid = $row['h_account_id'];
          if ($row['position'] == 1) {
            #check out book for first person in queue
            $query = "INSERT INTO checkedout_items(item_type, item_id, account_id, checkout_date,fees) VALUES ('book', 
            '$id', '$tuid', '2024-06-01 01:01:01', 0)";
            mysqli_query($conn, $query);

            #delete first person's postion in queue
            $query = "DELETE FROM holds_waitlist WHERE h_item_id = '$id' AND h_account_id = '$tuid'";
            mysqli_query($conn, $query);

          }
          else {
            #decremenet everyone else position in queue by one
            $pos = $row['position'] - 1;
            $query = "UPDATE holds_waitlist SET position = '$pos' WHERE h_account_id = '$tuid'";
            echo $query . "    ";
            mysqli_query($conn, $query);
          }
        }
      }
      header('location: checked-items.php');
    }
  ?>

    </table>
  </div>
</div>

</body>
</html>