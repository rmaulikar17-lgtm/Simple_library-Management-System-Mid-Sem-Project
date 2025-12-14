<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $user;
        header("Location: dashboard_admin.php");
        exit();
    } else {
        echo "<script>alert('Invalid Admin Login');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #4b6cb7, #182848);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
    }

    .login-container {
      background-color: #ffffff;
      border-radius: 15px;
      box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
      padding: 40px 50px;
      width: 350px;
      text-align: center;
    }

    .login-container h2 {
      color: #182848;
      margin-bottom: 25px;
      font-size: 26px;
    }

    input[type="text"],
    input[type="password"] {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #4b6cb7;
      box-shadow: 0 0 5px rgba(75, 108, 183, 0.5);
      outline: none;
    }

    input[type="submit"] {
      background-color: #4b6cb7;
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      margin-top: 10px;
      transition: background 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #35518a;
    }

    .footer {
      margin-top: 15px;
      font-size: 13px;
      color: #555;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Admin Login</h2>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <input type="submit" value="Login">
    </form>
    <div class="footer">
      &copy; <?php echo date("Y"); ?> Library Management System
    </div>
  </div>

</body>
</html>
