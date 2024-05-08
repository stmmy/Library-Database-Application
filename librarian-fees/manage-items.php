<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Library Items</title>
    <link rel="stylesheet" href="manage-items.css">
    <script>
        function updateFields(itemType) {
            const allFields = document.querySelectorAll('.specific');
            allFields.forEach(field => {
                field.style.display = 'none';
            });

            const activeFields = document.querySelector('.' + itemType + '-fields');
            if (activeFields) {
                activeFields.style.display = 'block';
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Cougar Library - Item Management</h1>
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

<?php
        session_start();
        $uid = $_SESSION["ID"];

        $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
        $username = "gp9library";
        $password = "Securewalls2";
        $dbname = "library";
            
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (isset($_POST['add_item'])) {
            $item_id = $_POST['item_id'];
            $issn = $_POST['issn'];
            $title = $_POST['title'];
            $Author = $_POST['author'];
            
            $query = "INSERT INTO ITEM (item_id, item_type, cost) VALUES ('$item_id', 'book', 0)";
            $success = mysqli_query($conn, $query);

            $query = "INSERT INTO item_book (id, issn, title, author) VALUES ('$item_id', '$issn', '$title', '$Author')";
            $success = mysqli_query($conn, $query);

            if ($success === TRUE) {
                echo "<div class='results'>New item added successfully.</div>";
            } else {
                echo "<div class='results'>Error </div>";
            }
        }

        if (isset($_POST['remove_item'])) {
            $id = $_POST['item_id_remove'];
            $book_remove_q = "DELETE FROM item_book WHERE ID='$id'";
            mysqli_query($conn, $book_remove_q);
            $item_remove_q = "DELETE FROM item WHERE item_id='$id'";
            $success = mysqli_query($conn, $item_remove_q);

            if ($success === TRUE) {
                echo "<div class='results'>Item removed successfully.</div>";
            } else {
                echo "<div class='results'>Error removing item</div>";
            }
        }

        $conn->close();
    ?>


<div class="container2">
    <form action="" method="post">
        <div class="input-icon-container">
            <input type="text" name="item_id" placeholder="Item ID" required>
            <input type="text" name="issn" placeholder="ISSN" required>
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author">
        </div>

        <div class="input-icon-container">
            <input type="submit" name="add_item" value="Add Item" class="navButton">
        </div>
    </form>

    <form action="" method="post">
        <div class="input-icon-container">
            <input type="text" name="item_id_remove" placeholder="Item ID to Remove" required>
            <input type="submit" name="remove_item" value="Remove Item" class="navButton">
        </div>
    </form>
</div>

 

</body>
</html>