<?php
// Подключение к базе данных
require('db.php');
session_start();
include 'navbar.php';
$book_file_error = $image_error = ""; // Переменные для ошибок

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // Получаем роль текущего пользователя

// Проверяем, есть ли ID книги
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);

    // Проверяем права доступа
    if ($role === 'moderator' || $role === 'admin') {
        $query = "SELECT * FROM books WHERE id='$book_id'";
    } else {
        $query = "SELECT * FROM books WHERE id='$book_id' AND username='$username'";
    }

    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);
    } else {
        echo "<p>У вас нет прав на редактирование этой книги.</p>";
        exit();
    }
}

if (isset($_POST['update_book'])) {
    $bookname = mysqli_real_escape_string($con, $_POST['bookname']);
    $author = mysqli_real_escape_string($con, $_POST['author']);
    $genre = mysqli_real_escape_string($con, $_POST['genre']);
    $errors = false;

    // Проверка формата файла книги
    if (!empty($_FILES['book_file']['name'])) {
        $bookFile = $_FILES['book_file']['name'];
        $bookFileType = pathinfo($bookFile, PATHINFO_EXTENSION);
        $bookTarget = "uploads/books/" . basename($bookFile);

        if (!in_array($bookFileType, ['txt', 'pdf'])) {
            $book_file_error = "Допустимые форматы файла книги: .txt, .pdf";
            $errors = true;
        } elseif (!move_uploaded_file($_FILES['book_file']['tmp_name'], $bookTarget)) {
            $book_file_error = "Ошибка загрузки файла книги. Проверьте доступность ресурса.";
            $errors = true;
        } else {
            $query = "UPDATE books SET bookname='$bookname', author='$author', genre='$genre', link='$bookTarget' WHERE id='$book_id'";
        }
    } else {
        $query = "UPDATE books SET bookname='$bookname', author='$author', genre='$genre' WHERE id='$book_id'";
    }

    // Проверка формата файла изображения
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $imageType = pathinfo($image, PATHINFO_EXTENSION);
        $imageTarget = "uploads/" . basename($image);

        if (!in_array($imageType, ['png', 'jpg'])) {
            $image_error = "Допустимые форматы изображения: .png, .jpg";
            $errors = true;
        } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $imageTarget)) {
            $image_error = "Ошибка загрузки изображения. Проверьте доступность ресурса.";
            $errors = true;
        } else {
            $query = "UPDATE books SET image='$image' WHERE id='$book_id'";
        }
    }

    // Выполнение запроса на обновление
    if (!$errors && mysqli_query($con, $query)) {
        header("Location: author_instrument.php?message=book_updated");
        exit();
    } elseif (!$errors) {
        echo "<p>Не удалось обновить информацию о книге.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать книгу</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .error { color: red; font-size: 0.9em; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>Редактирование книги</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Название книги:</label>
        <input type="text" name="bookname" value="<?php echo $book['bookname']; ?>" required><br>

        <label>Автор:</label>
        <input type="text" name="author" value="<?php echo $book['author']; ?>" required><br>

        <label>Жанр:</label>
        <input type="text" name="genre" value="<?php echo $book['genre']; ?>" required><br>

        <label>Обновить файл книги:</label>
        <input type="file" name="book_file"><br>
        <p class="error"><?php echo $book_file_error; ?></p>

        <label>Обновить обложку:</label>
        <input type="file" name="image"><br>
        <p class="error"><?php echo $image_error; ?></p>

        <input type="submit" name="update_book" value="Сохранить изменения">
    </form>
</body>
</html>
