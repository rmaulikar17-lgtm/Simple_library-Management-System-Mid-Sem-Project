<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Validate request
if (!isset($_GET['action']) || !isset($_GET['issue_id'])) {
    header("Location: dashboard_admin.php");
    exit();
}

$issue_id = intval($_GET['issue_id']);
$action = $_GET['action'];

// Fetch the book id of this request
$request = $conn->prepare("SELECT book_id FROM issued_books WHERE issue_id = ?");
$request->bind_param("i", $issue_id);
$request->execute();
$result = $request->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Invalid request'); window.location='dashboard_admin.php';</script>";
    exit();
}

$data = $result->fetch_assoc();
$book_id = $data['book_id'];

if ($action === "approve") {

    $issue_date = date("Y-m-d");
    $due_date = date("Y-m-d", strtotime("+14 days"));

    $approve = $conn->prepare("
        UPDATE issued_books 
        SET status = 'issued', issue_date = ?, due_date = ?
        WHERE issue_id = ?
    ");
    $approve->bind_param("ssi", $issue_date, $due_date, $issue_id);
    $approve->execute();

    $update_copies = $conn->prepare("
        UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?
    ");
    $update_copies->bind_param("i", $book_id);
    $update_copies->execute();

    echo "<script>alert('✅ Book approved and issued!'); window.location='dashboard_admin.php';</script>";
    exit();
}


if ($action === "reject") {

    $reject = $conn->prepare("
        UPDATE issued_books SET status = 'rejected'
        WHERE issue_id = ?
    ");
    $reject->bind_param("i", $issue_id);
    $reject->execute();

    echo "<script>alert('❌ Book request rejected'); window.location='dashboard_admin.php';</script>";
    exit();
}

// fallback
header("Location: dashboard_admin.php");
exit();
?>
