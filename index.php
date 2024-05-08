<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="wrapper">
        <form action="" method="GET">
            <h1>Library Login</h1>
            <div class="input-box">
                <input type="text" placeholder="Username" name="username" id="username">
            </div>
            <div class="input-box">
                <input type="password" placeholder="Password" name="pw">
            </div>
            <input type="submit" value="Submit" name="submit" class="btn">
            <br><br>
            <input type="submit" value="register" name="register" class="btn">
        </form>
        

        <?php
            $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
            $username = "gp9library";
            $password = "Securewalls2";
            $dbname = "library";
            $conn = mysqli_connect($servername, $username, $password, $dbname);

            if (isset($_GET['register'])) {
                header('location: register/register.php');
            }
            if (isset($_GET['submit']) && !empty($_GET['username']) && !empty($_GET['pw'])) {
                $uname = $_GET['username'];
                $pw = $_GET['pw'];
                $query = "SELECT * FROM accounts WHERE account_uname = '$uname' AND account_pw = '$pw'";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result)==1) {
                    session_start();
                    $r = mysqli_fetch_assoc($result);
                    $_SESSION["ID"] = $r["account_id"];
                    if ($r['account_type'] == 'student' || $r['account_type'] == 'teacher') {
                        header('location: account/account.php');
                    }
                    else {
                        header('location: librarian-fees/report-fees.php');
                    }
                }


                unset($_GET['submit']);
            }
        ?>
    </div>

</body>
</html>