<?php
require('db.php');
session_start();

if ($_SESSION['role'] !== 'reader') {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = mysqli_real_escape_string($con, $_POST['book_id']);
    $user_id = $_SESSION['username'];
    $rating = intval($_POST['rating']);

    if ($rating < 1 || $rating > 10) {
        echo "<p>Недопустимая оценка</p>";
        exit();
    }

    // Проверяем, есть ли уже оценка пользователя
    $query = "SELECT * FROM ratings WHERE book_id = '$book_id' AND user_id = '$user_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Обновляем существующую оценку
        $query = "UPDATE ratings SET rating = '$rating' WHERE book_id = '$book_id' AND user_id = '$user_id'";
    } else {
        // Вставляем новую оценку
        $query = "INSERT INTO ratings (book_id, user_id, rating) VALUES ('$book_id', '$user_id', '$rating')";
    }
    mysqli_query($con, $query);

    // Пересчитываем среднюю оценку
    $query = "UPDATE books b
              SET average_rating = (
                  SELECT AVG(rating) FROM ratings r WHERE r.book_id = b.id
              )
              WHERE b.id = '$book_id'";
    mysqli_query($con, $query);

    header("Location: book.php?id=$book_id");
    exit();
}
?>
