<?php
// ����������� � ���� ������
require('db.php');
session_start();
include 'navbar.php';
// ���������, ����������� �� ������������
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // �������� ���� �������� ������������

if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);

    // ������� ��� �������� �����
    if ($role === 'moderator' || $role === 'admin') {
        $query = "DELETE FROM books WHERE id='$book_id'";
    } else {
        $query = "DELETE FROM books WHERE id='$book_id' AND username='$username'";
    }

    // ���������� ������� �� ��������
    if (mysqli_query($con, $query)) {
        header("Location: author_instrument.php?message=book_deleted");
        exit();
    } else {
        echo "<p>������ ��� �������� �����.</p>";
    }
}
?>
