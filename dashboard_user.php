<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name']);

// Sorting for Available Books
$sort_books_by = $_GET['sort_books_by'] ?? 'book_id';
$sort_books_order = $_GET['sort_books_order'] ?? 'ASC';

// Sorting for Requests
$sort_requests_by = $_GET['sort_requests_by'] ?? 'issue_id';
$sort_requests_order = $_GET['sort_requests_order'] ?? 'ASC';

$allowed_book_fields = ['book_id', 'title', 'author', 'available_copies'];
$allowed_orders = ['ASC', 'DESC'];
if (!in_array($sort_books_by, $allowed_book_fields)) $sort_books_by = 'book_id';
if (!in_array($sort_books_order, $allowed_orders)) $sort_books_order = 'ASC';

$allowed_request_fields = ['issue_id', 'title', 'author', 'request_date', 'issue_date', 'due_date', 'return_date', 'status'];
if (!in_array($sort_requests_by, $allowed_request_fields)) $sort_requests_by = 'issue_id';
if (!in_array($sort_requests_order, $allowed_orders)) $sort_requests_order = 'ASC';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #74ABE2, #5563DE);
        margin: 0;
        padding: 0;
        color: #333;
    }
    header {
        background-color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header h2 { margin: 0; color: #222; }
    header a {
        background-color: #5563DE;
        color: #fff;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        transition: 0.3s;
    }
    header a:hover { background-color: #3849b1; }
    main {
        width: 90%;
        max-width: 1000px;
        margin: 40px auto;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 30px 40px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    h3 {
        border-left: 5px solid #5563DE;
        padding-left: 10px;
        margin-top: 40px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
        font-size: 15px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th {
        background-color: #5563DE;
        color: white;
        padding: 12px;
    }
    td {
        background-color: #fff;
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }
    tr:hover td { background-color: #f7f9ff; }

    a.action-btn, button.action-btn {
        position: relative;
        z-index: 10;
        background: #5563DE;
        color: #fff;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: inline-block;
    }
    a.action-btn:hover, button.action-btn:hover {
        background: #3849b1;
    }
    button[disabled] { background: #bbb; cursor: not-allowed; }
    .sort-container {
        background: #f5f7ff;
        padding: 10px;
        border-radius: 10px;
        display: flex;
        gap: 10px;
        align-items: center;
        width: fit-content;
    }
    footer {
        text-align: center;
        padding: 20px;
        color: #fff;
        margin-top: 50px;
    }
</style>
</head>
<body>

<header>
    <h2>Welcome, <?php echo $user_name; ?></h2>
    <a href="logout.php">Logout</a>
</header>

<main>

<h3>📚 Available Books</h3>

<form method="get" class="sort-container">
    Sort by:
    <select name="sort_books_by">
        <option value="book_id">ID</option>
        <option value="title">Title</option>
        <option value="author">Author</option>
        <option value="available_copies">Available Copies</option>
    </select>

    <select name="sort_books_order">
        <option value="ASC">Ascending</option>
        <option value="DESC">Descending</option>
    </select>

    <input type="submit" value="Sort">
</form>

<?php
$result = $conn->query("SELECT * FROM books ORDER BY $sort_books_by $sort_books_order");

echo "<table><tr><th>ID</th><th>Title</th><th>Author</th><th>Available Copies</th><th>Action</th></tr>";

while ($row = $result->fetch_assoc()) {
    $book_id = $row['book_id'];

    $check = $conn->query("
        SELECT status FROM issued_books
        WHERE user_id = $uid AND book_id = $book_id
        ORDER BY issue_id DESC LIMIT 1
    ");

    if ($check->num_rows == 0) {
        $action = "<a class='action-btn' href='user_request_book.php?book_id=$book_id'>Request Book</a>";
    } else {
        $status = $check->fetch_assoc()['status'];

        if ($status == 'requested') {
            $action = "<a class='action-btn' href='user_cancel_request.php?book_id=$book_id'>Cancel Request</a>";
        } elseif ($status == 'issued') {
            $action = "<a class='action-btn' href='user_return_book.php?book_id=$book_id'>Return Book</a>";
        } else {
            $action = "<a class='action-btn' href='user_request_book.php?book_id=$book_id'>Request Book</a>";
        }
    }

    echo "<tr>
        <td>{$row['book_id']}</td>
        <td>{$row['title']}</td>
        <td>{$row['author']}</td>
        <td>{$row['available_copies']}</td>
        <td>$action</td>
    </tr>";
}

echo "</table>";
?>

<!-- My Requests Table -->
<h3>📘 My Requests</h3>

<?php
$sql = "SELECT i.issue_id, b.title, b.author, i.request_date, i.issue_date, i.due_date, i.return_date, i.status
        FROM issued_books i
        JOIN books b ON i.book_id = b.book_id
        WHERE i.user_id = $uid
        ORDER BY $sort_requests_by $sort_requests_order";

$res = $conn->query($sql);

echo "<table>
<tr><th>ID</th><th>Title</th><th>Author</th><th>Request Date</th><th>Issued</th><th>Due</th><th>Returned</th><th>Status</th></tr>";

while ($row = $res->fetch_assoc()) {
    echo "<tr>
        <td>{$row['issue_id']}</td>
        <td>{$row['title']}</td>
        <td>{$row['author']}</td>
        <td>{$row['request_date']}</td>
        <td>" . ($row['issue_date'] ?? '-') . "</td>
        <td>" . ($row['due_date'] ?? '-') . "</td>
        <td>" . ($row['return_date'] ?? '-') . "</td>
        <td>{$row['status']}</td>
    </tr>";
}

echo "</table>";
?>
</main>

<footer>&copy; <?= date('Y'); ?> Library Management System</footer>

</body>
</html>
