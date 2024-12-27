<?php
// Подключение к базе данных
require('db.php');
session_start();
include 'navbar.php';
// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // Получаем роль текущего пользователя

if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);

    // Условие для удаления книги
    if ($role === 'moderator' || $role === 'admin') {
        $query = "DELETE FROM books WHERE id='$book_id'";
    } else {
        $query = "DELETE FROM books WHERE id='$book_id' AND username='$username'";
    }

    // Выполнение запроса на удаление
    if (mysqli_query($con, $query)) {
        header("Location: author_instrument.php?message=book_deleted");
        exit();
    } else {
        echo "<p>Ошибка при удалении книги.</p>";
    }
}
?>
