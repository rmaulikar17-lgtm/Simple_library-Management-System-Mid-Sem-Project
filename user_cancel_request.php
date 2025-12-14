<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

if (isset($_GET['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_GET['book_id'];

    $sql = "UPDATE issued_books
            SET status = 'cancelled'
            WHERE user_id = '$user_id' AND book_id = '$book_id' AND status = 'requested'
            ORDER BY issue_id DESC LIMIT 1";

    $conn->query($sql);

    header("Location: dashboard_user.php?msg=cancelled");
    exit();
}
?>
