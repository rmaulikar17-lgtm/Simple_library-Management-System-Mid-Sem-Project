<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_name'] = $row['name'];
        header("Location: dashboard_user.php");
        exit();
    } else {
        echo "<script>alert('Invalid User Login');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User Login</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #74ABE2, #5563DE);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-box {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      padding: 40px 50px;
      width: 350px;
      text-align: center;
      animation: fadeIn 0.7s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-box h2 {
      color: #333;
      margin-bottom: 25px;
    }

    .login-box input[type="text"],
    .login-box input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: 0.3s;
    }

    .login-box input[type="text"]:focus,
    .login-box input[type="password"]:focus {
      border-color: #5563DE;
      outline: none;
      box-shadow: 0 0 5px rgba(85,99,222,0.5);
    }

    .login-box input[type="submit"] {
      background: #5563DE;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 15px;
      transition: 0.3s;
      width: 100%;
    }

    .login-box input[type="submit"]:hover {
      background: #3849b1;
    }

    .footer {
      margin-top: 15px;
      font-size: 13px;
      color: #666;
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
  <div class="login-box">
    <h2>User Login</h2>
    <form method="post">
      <input type="text" name="email" placeholder="Enter your email" required>
      <input type="password" name="password" placeholder="Enter your password" required>
      <input type="submit" value="Login">
    </form>
    <div class="footer">
      <p>Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
  </div>
</body>
</html>
