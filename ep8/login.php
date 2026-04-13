<?php
$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Dummy check
    if ($email == "admin@gmail.com" && $password == "123456") {
        $message = "Login Successful";
        $messageClass = "success";
    } else {
        $message = "Invalid Credentials";
        $messageClass = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Result</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 400px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #4facfe;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn:hover {
            background: #00c6fb;
        }
    </style>
</head>
<body>

    <div class="container">

        <?php if ($message == "Login Successful") { ?>
            <h2 class="Login success"><?php echo $message; ?></h2>
        <?php } else { ?>
            <h2 class="error"><?php echo $message; ?></h2>
            <a href="login.html" class="btn">Back to Login</a>
        <?php } ?>

    </div>

</body>
</html>