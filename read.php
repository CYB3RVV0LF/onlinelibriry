<?php
require('db.php');
session_start();
include 'navbar.php';
// Проверяем, передан ли параметр ID книги в URL
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);

    // Запрос для получения данных книги по ID
    $query = "SELECT * FROM books WHERE id='$book_id'";
    $result = mysqli_query($con, $query);

    // Проверяем, вернулся ли результат
    if (mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);  // Извлекаем данные книги
        $bookFilePath = $book['link'];  // Ссылка на файл книги
    } else {
        echo "<p>Book not found</p>";
        exit();
    }
} else {
    echo "<p>Incorrect book ID</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $book['bookname']; ?> - Reading</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo $book['bookname']; ?></h1>
    <img src="uploads/<?php echo $book['image']; ?>" width="300" height="400">
    <?php
    // Проверяем тип файла (текст или PDF) и выводим содержимое
    $fileExtension = pathinfo($bookFilePath, PATHINFO_EXTENSION);

    // Если файл - текстовый, открываем его и выводим содержимое
    if ($fileExtension == 'txt') {
        $bookContent = file_get_contents($bookFilePath);
        echo "<pre>" . htmlspecialchars($bookContent) . "</pre>"; // Безопасный вывод содержимого
    } elseif ($fileExtension == 'pdf') {
        // Если это PDF, встраиваем его с помощью iframe
        echo "<iframe src='$bookFilePath' width='600' height='800'></iframe>";
    } else {
        echo "<p>Unsupported file format</p>";
    }
    ?>
</body>
</html>

