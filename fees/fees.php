<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="fees.css">

<title>Checked out items</title>

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

<div class="bodyContainer">
<h2>Fees</h2>
  <div class="box">
    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th>Item ID</th>
          <th>Fee Amount</th>
          <th>Pay Fee</th>
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
    
    #get all users fees
    $query = "SELECT * FROM fees where f_account_id = $uid";
    $results = mysqli_query($conn, $query);
    $fee_items = [];
    while($row = mysqli_fetch_assoc($results)) {
        $fee_items[$row['f_item_id']] = array($row['item_type'], $row['fee_amount']);
    }
    
    #display user fees and button to payy them
    echo "<tbody>";
    
    $fee_total = 0;
    foreach($fee_items as $id => $arr) {
        $query = "SELECT title FROM item_book WHERE ID = '$id'";
        $results = mysqli_query($conn, $query);
        $r = mysqli_fetch_assoc($results);
        $name = $r['title'];
        echo "<tr>";
        echo "<td>$name</td>";
        echo "<td>$id</td>";
        echo "<td>$arr[1]</td>";

        echo "<form action='' method='get'>";
        echo "<td>";
        echo "<input type='hidden' name='id' value='$id'>";
        echo "<input type='hidden' name='fee' value='$arr[1]'>";
        echo "<input type='text' name='pay_amount' placeholder='amount'>";
        echo "<input type='submit' name='fee_submit' value='Pay Amount'>";
        echo "<input type='submit' name='pay_all' value='Pay All'";
        echo "</td>";
        echo "</form>";
        echo "</tr>";
        $fee_total += $arr[1];
    }
    echo "<tr>";
    echo "<td>Total</td>";
    echo "<td> </td>";
    echo "<td>$fee_total</td>";
    echo "</tr>";
    echo "</tbody>";

    #pay fee with amount provided
    if (isset($_GET['fee_submit'])) {
      $id = $_GET['id'];
      if (is_numeric($_GET['pay_amount'])) {
        $payint = floatval($_GET['pay_amount']);
        $feetotal = $_GET['fee'];
        if ($payint > $feetotal) {
          echo "Pay amount larger than fee amount";
        }
        else {
          $newfee = $feetotal - $payint;
          if ($newfee == 0) { #delete entry if amount paid makes fee = 0
            $query = "DELETE FROM fees WHERE f_account_id = '$uid' AND f_item_id = '$id'";
            mysqli_query($conn, $query);
          }
          else { #update fee with new value after fee amount pad
            $query = "UPDATE fees SET fee_amount = $newfee WHERE f_account_id = '$uid' AND f_item_id = '$id'";
            mysqli_query($conn, $query);
          }
        }
      }
      header('location: fees.php');
    }

    if (isset($_GET['pay_all'])) {
      $id = $_GET['id'];
      $query = "DELETE FROM fees WHERE f_account_id = '$uid' AND f_item_id = '$id'";
      mysqli_query($conn, $query);
      header('location: fees.php');
    }
  ?>

    </table>
  </div>
    
</div>

</body>
</html>