<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $dept = $_POST['department'];

    // Check if the email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already registered! Please login instead.');</script>";
    } else {
        // Insert new user into table
        $sql = "INSERT INTO users (name, email, password, department) 
                VALUES ('$name', '$email', '$pass', '$dept')";
        if ($conn->query($sql)) {
            echo "<script>alert('Registration successful! Please login.'); 
                  window.location='user_login.php';</script>";
        } else {
            echo "<script>alert('Error while registering. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Registration</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #74ABE2, #5563DE);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    .register-box {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        width: 380px;
        padding: 40px;
        text-align: center;
    }

    .register-box h2 {
        margin-bottom: 25px;
        color: #333;
    }

    .register-box input,
    .register-box select {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .register-box input:focus,
    .register-box select:focus {
        border-color: #5563DE;
        box-shadow: 0 0 5px rgba(85,99,222,0.5);
        outline: none;
    }

    .register-box input[type="submit"] {
        background-color: #5563DE;
        color: #fff;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .register-box input[type="submit"]:hover {
        background-color: #3849b1;
    }

    .footer {
        margin-top: 15px;
        font-size: 14px;
        color: #555;
    }

    .footer a {
        color: #5563DE;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="register-box">
    <h2>Create Account</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Enter your full name" required>
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <select name="department" required>
            <option value="" disabled selected>Select Department</option>
            <option value="Computer Science">Computer Science</option>
            <option value="IT">IT</option>
            <option value="Electronics">Electronics</option>
            <option value="Mechanical">Mechanical</option>
        </select>
        <input type="submit" value="Register">
    </form>
    <div class="footer">
        <p>Already have an account? <a href="user_login.php">Login</a></p>
    </div>
</div>
</body>
</html>
