

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Room Reservation</title>
    <link rel="stylesheet" href="room-search.css">
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
        <h2>Study Room Reservation</h2>
        <div class="calendar">
            <form action="" method="GET">
                <input type="date" id="calendarDate" name="calendarDate" required>
                <input type="submit" value="Check Rooms" name="calendarSubmit" id="rmsbmit">
            </form>
        </div>
    </div>

    <div class='results'>
    <?php
        session_start();
        $id = $_SESSION["ID"];
        $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
        $username = "gp9library";
        $password = "Securewalls2";
        $dbname = "library";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        #get reserved rooms to display
        if (isset($_GET['calendarSubmit'])) {
            #Convert inputed date into date format, so that we are able to check if date is valid or not
            $strDate = $_GET['calendarDate'];
            $date = date_create_from_format('Y-m-d', $_GET['calendarDate']);
            $now = date_create_from_format('Y-m-d',  date("Y-m-d"));

            if ($date > $now) {
                #Get an array of all rooms and their time slots
                $rooms = [];
                $query = "SELECT * FROM room";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $rooms[$row['room_no']] = array('9:00', '11:00', '13:00', '15:00');
                }
                mysqli_free_result($result);
                
                #modify time slots in rooms array by replacing time slot with 'R' if room is reserved for Date inputed
                $query = "SELECT * FROM room_reserved WHERE reserved_time_start LIKE '%$strDate%'";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $dt = DateTime::createFromFormat("Y-m-d H:i:s", $row['reserved_time_start']);
                    $h = $dt->format('H');

                    if ($h == '09') {$rooms[$row['room_no']][0] = "R";}
                    if ($h == '11') {$rooms[$row['room_no']][1] = "R";}
                    if ($h == '13') {$rooms[$row['room_no']][2] = "R";}
                    if ($h == '15') {$rooms[$row['room_no']][3] = "R";}
                }
                mysqli_free_result($result);

                #Display rooms and their specified reservation times
                foreach($rooms as $room_no => $arr) {
                    echo "<form action='' method='GET'>";
                    echo "<div class='result-row'>" ;
                        echo "<div class='text-content'>";
                            echo "<h3> Room No: $room_no </h3>";
                            echo "<div class='timeslots'>";
                            echo "<h4>Available Time Slots:</h4>";
                            echo "<input type='hidden' name='room_no' value='$room_no'>";
                            echo "<input type='hidden' name='date' value='$strDate'>";
                            foreach ($arr as &$timeSlot) {
                                if ($timeSlot != 'R') {
                                    echo "<input type='submit' value='$timeSlot' name='room_time'>";
                                }
                            }
                            echo "</div>";
                        echo "</div>"; 
                    echo "</div>"; 
                    echo "</form>";
                }
            }
        }

        if (isset($_GET['room_time'])) {
            #update database with users reservation
            $rt = $_GET['room_time'];
            $rn = $_GET['room_no'];
            $strDate = $_GET['date'];
            $query = "INSERT INTO room_reserved(room_no, reserved_by_id, reserved_time_start) VALUES ('$rn', '$id', '$strDate $rt:00')";
            mysqli_query($conn, $query);
        }
    ?>
    </div>
</body>
</html>

   
