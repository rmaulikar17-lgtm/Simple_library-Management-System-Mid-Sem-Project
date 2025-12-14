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

    // Insert a new request for the book
    $sql = "INSERT INTO issued_books (user_id, book_id, request_date, status)
            VALUES ('$user_id', '$book_id', NOW(), 'requested')";

    $conn->query($sql);

    header("Location: dashboard_user.php?msg=requested");
    exit();
}
?>
