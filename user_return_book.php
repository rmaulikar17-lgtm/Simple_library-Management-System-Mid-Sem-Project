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

    // Get latest issue record for this book and user
    $sql = "SELECT issue_id FROM issued_books 
            WHERE user_id = '$user_id' AND book_id = '$book_id' AND status = 'issued'
            ORDER BY issue_id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $issue_id = $result->fetch_assoc()['issue_id'];

        // ✅ Update status & insert return date
        $conn->query("UPDATE issued_books 
                      SET status = 'returned', return_date = NOW()
                      WHERE issue_id = '$issue_id'");

        // ✅ Increase available book count
        $conn->query("UPDATE books 
                      SET available_copies = available_copies + 1
                      WHERE book_id = '$book_id'");
    }

    header("Location: dashboard_user.php?msg=returned");
    exit();
}
?>
