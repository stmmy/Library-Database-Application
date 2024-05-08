<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="wrapper">
        <form action="" method="POST">
            <h1>Register</h1>
            <div class="input-box">
                <input type="text" placeholder="First Name" name="fname" required maxlength="15">
            </div>
            <div class="input-box">
                <input type="text" placeholder="Last Name" name="lname" required maxlength="15">
            </div>
            <div class="input-box">
                <input type="text" placeholder="Username" name="username" required maxlength="16">
            </div>
            <div class="input-box">
                <input type="password" placeholder="Password" name="password" required maxlength="20">
            </div>
            <input type="submit" value="Register" name="register" class="btn">
        </form>
        <button class ="btn" onclick ="document.location='..\\index.php'">Login Page</button>

        <?php
        $servername = "spring2024-gp9-library-azure.mysql.database.azure.com";
        $username = "gp9library";
        $password = "Securewalls2";
        $dbname = "library";
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
            $conn = new mysqli($servername, $username , $password, $dbname); // Update credentials as necessary

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Automatically generate a unique 10-digit account ID
            do {
                $account_id = sprintf("%010d", mt_rand(0, 9999999999));
                $stmt = $conn->prepare("SELECT account_id FROM accounts WHERE account_id = ?");
                $stmt->bind_param("s", $account_id);
                $stmt->execute();
                $result = $stmt->get_result();
            } while ($result->num_rows > 0);

            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Check if username already exists
            $stmt = $conn->prepare("SELECT account_uname FROM accounts WHERE account_uname = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<p>Username already exists. Please use a different username.</p>";
            } else {
                // Insert the new user into the database
                $stmt = $conn->prepare("INSERT INTO accounts (account_id, fname, lname, account_uname, account_pw, account_type, fees) VALUES ('$account_id', '$fname', '$lname', '$username', '$password', 'student', 0.0)");

                if ($stmt->execute()) {
                    echo "<p>Registration successful! You can now log in with your username and password.</p>";
                } else {
                    echo "<p>Error: " . $stmt->error . "</p>";
                }
            }
            $stmt->close();
            $conn->close();
}
?>
    </div>
</body>
</html>