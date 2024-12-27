<?php
require('db.php');
session_start();
include 'navbar.php';

if (!isset($_SESSION['username'])) {
    echo "Вы должны быть авторизованы, чтобы добавлять книги в избранное.";
    exit();
}

$username = $_SESSION['username']; // Используем username вместо user_id

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = mysqli_real_escape_string($con, $_POST['book_id']);

    // Проверяем, не добавлена ли книга уже в избранное
    $check_query = "SELECT * FROM favourites WHERE user_id='$username' AND book_id='$book_id'";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "Эта книга уже в избранном.";
    } else {
        $insert_query = "INSERT INTO favourites (user_id, book_id) VALUES ('$username', '$book_id')";
        if (mysqli_query($con, $insert_query)) {
            echo "Книга успешно добавлена в избранное.";
        } else {
            echo "Ошибка при добавлении книги.";
        }
    }
} else {
    echo "Неверный запрос.";
}
?>
