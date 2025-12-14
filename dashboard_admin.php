<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Default sorting for books
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'book_id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Allowed sorting options
$allowed_columns = ['book_id', 'title', 'author', 'available_copies'];
$allowed_order = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_columns)) $sort_by = 'book_id';
if (!in_array($order, $allowed_order)) $order = 'ASC';

// Fetch all books
$books_result = $conn->query("SELECT * FROM books ORDER BY $sort_by $order");

// Fetch pending requests
$requests_result = $conn->query("
    SELECT 
        ib.issue_id,
        u.name AS user_name,
        b.book_id,
        b.title AS book_name,
        ib.request_date,
        ib.status
    FROM issued_books ib
    JOIN users u ON ib.user_id = u.user_id
    JOIN books b ON ib.book_id = b.book_id
    WHERE ib.status = 'requested'
    ORDER BY ib.request_date ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #74ABE2, #5563DE);
    margin: 0; padding: 0; color: #333;
    font-size: 12px;
}

.container { 
    width: 90%; margin: 20px auto;
    background: rgba(255, 255, 255, 0.95);
    padding: 20px; border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

h2 { text-align: center; font-size: 18px; }
h3 { border-left: 5px solid #5563DE; padding-left: 10px; margin-top: 30px; }

table { width: 100%; border-collapse: collapse; margin: 15px 0; border-radius: 10px;
    overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

th { background-color: #5563DE; color: white; padding: 12px; }
td { padding: 10px; text-align: center; background-color: white; }

a.action-btn {
    background: #5563DE; color: #fff;
    padding: 6px 12px; text-decoration: none;
    border-radius: 6px; font-size: 12px;
}
a.action-btn:hover { background: #3849b1; }
a.reject { background: #e74c3c !important; }

a.logout-btn {
    float: right; background: #e74c3c; color: #fff;
    padding: 5px 12px; border-radius: 5px;
    text-decoration: none;
}
</style>
</head>
<body>
<div class="container">

    <h2>Welcome, Admin</h2>
    <a href="logout.php" class="logout-btn">Logout</a>

    <h3>📚 All Books</h3>

    <form method="get">
        Sort by: 
        <select name="sort_by">
            <option value="book_id" <?php if($sort_by=='book_id') echo 'selected'; ?>>ID</option>
            <option value="title" <?php if($sort_by=='title') echo 'selected'; ?>>Title</option>
            <option value="author" <?php if($sort_by=='author') echo 'selected'; ?>>Author</option>
            <option value="available_copies" <?php if($sort_by=='available_copies') echo 'selected'; ?>>Copies</option>
        </select>

        <select name="order">
            <option value="ASC" <?php if($order=='ASC') echo 'selected'; ?>>Ascending</option>
            <option value="DESC" <?php if($order=='DESC') echo 'selected'; ?>>Descending</option>
        </select>

        <input type="submit" value="Sort">
    </form>

    <table>
        <tr><th>ID</th><th>Title</th><th>Author</th><th>Copies</th></tr>
        <?php while($row = $books_result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['book_id']; ?></td>
            <td><?= $row['title']; ?></td>
            <td><?= $row['author']; ?></td>
            <td><?= $row['available_copies']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>📬 Pending Book Requests</h3>

    <?php if ($requests_result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Request ID</th>
            <th>User</th>
            <th>Book ID</th>
            <th>Book Name</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($req = $requests_result->fetch_assoc()): ?>
        <tr>
            <td><?= $req['issue_id']; ?></td>
            <td><?= $req['user_name']; ?></td>
            <td><?= $req['book_id']; ?></td>
            <td><?= $req['book_name']; ?></td>
            <td><?= $req['request_date']; ?></td>
            <td><?= $req['status']; ?></td>
            <td>
                <a href="admin_process_request.php?action=approve&issue_id=<?= $req['issue_id']; ?>" class="action-btn">Approve</a>
                <a href="admin_process_request.php?action=reject&issue_id=<?= $req['issue_id']; ?>" class="action-btn reject">Reject</a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>
    <?php else: ?>
        <p>No pending requests.</p>
    <?php endif; ?>


    <h3>➕ Add New Book</h3>
    <form method="post">
        <label>Title:</label>
        <input type="text" name="title" required>
        
        <label>Author:</label>
        <input type="text" name="author" required>
        
        <label>Copies:</label>
        <input type="number" name="copies" min="1" required>
        
        <input type="submit" name="add_book" value="Add Book">
    </form>

    <?php
    if(isset($_POST['add_book'])){
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $copies = intval($_POST['copies']);

        $check = $conn->prepare("SELECT available_copies FROM books WHERE title=? AND author=?");
        $check->bind_param("ss", $title, $author);
        $check->execute();
        $res = $check->get_result();

        if($res->num_rows > 0){
            $update = $conn->prepare("UPDATE books SET available_copies = available_copies + ? WHERE title=? AND author=?");
            $update->bind_param("iss", $copies, $title, $author);
            $update->execute();
            echo "<script>alert('Book already exists — copies updated!'); window.location='dashboard_admin.php';</script>";
        } else {
            $insert = $conn->prepare("INSERT INTO books (title, author, available_copies) VALUES (?,?,?)");
            $insert->bind_param("ssi", $title, $author, $copies);
            $insert->execute();
            echo "<script>alert('New book added successfully!'); window.location='dashboard_admin.php';</script>";
        }
    }
    ?>

</div>
</body>
</html>
