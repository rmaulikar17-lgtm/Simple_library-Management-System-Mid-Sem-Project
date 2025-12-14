<?php
session_start();
include('db_connect.php');
if (!isset($_SESSION['user_id'])) { header("Location: user_login.php"); exit(); }

$book_id = $_GET['book_id'];
$user_id = $_SESSION['user_id'];

$conn->query("INSERT INTO issued_books (user_id, book_id, issue_date) VALUES ($user_id, $book_id, CURDATE())");

echo "<script>alert('Book Issued Successfully');window.location='dashboard_user.php';</script>";
?>
