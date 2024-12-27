<?php
require('db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = mysqli_real_escape_string($con, $_POST['book_id']);
    $user_id = $_SESSION['username'];
    $username = $_SESSION['username'];
    $comment = mysqli_real_escape_string($con, trim($_POST['comment']));

    if (empty($comment)) {
        echo "<p>Комментарий не может быть пустым</p>";
        exit();
    }

    $query = "INSERT INTO comments (book_id, user_id, username, comment) 
              VALUES ('$book_id', '$user_id', '$username', '$comment')";
    if (mysqli_query($con, $query)) {
        header("Location: book.php?id=$book_id");
        exit();
    } else {
        echo "<p>Ошибка добавления комментария: " . mysqli_error($con) . "</p>";
    }
}
?>

