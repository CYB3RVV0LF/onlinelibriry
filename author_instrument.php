<?php
session_start();
require('db.php');
include 'navbar.php';
// Проверяем права доступа
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Обработка формы добавления книги
$book_file_error = $image_error = "";

if (isset($_POST['add_book'])) {
    $bookname = mysqli_real_escape_string($con, $_POST['bookname']);
    $author = mysqli_real_escape_string($con, $_POST['author']);
    $genre = mysqli_real_escape_string($con, $_POST['genre']);
    $errors = false;

    // Проверка и загрузка файла книги
    if (!empty($_FILES['book_file']['name'])) {
        $bookFile = $_FILES['book_file']['name'];
        $bookFileType = pathinfo($bookFile, PATHINFO_EXTENSION);
        $bookTarget = "uploads/books/" . basename($bookFile);

        if (!in_array($bookFileType, ['txt', 'pdf'])) {
            $book_file_error = "Допустимые форматы файла книги: .txt, .pdf";
            $errors = true;
        } elseif (!move_uploaded_file($_FILES['book_file']['tmp_name'], $bookTarget)) {
            $book_file_error = "Ошибка загрузки файла книги.";
            $errors = true;
        }
    } else {
        $book_file_error = "Файл книги обязателен для загрузки.";
        $errors = true;
    }

    // Проверка и загрузка изображения
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $imageType = pathinfo($image, PATHINFO_EXTENSION);
        $imageTarget = "uploads/" . basename($image);

        if (!in_array($imageType, ['png', 'jpg'])) {
            $image_error = "Допустимые форматы изображения: .png, .jpg";
            $errors = true;
        } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $imageTarget)) {
            $image_error = "Ошибка загрузки изображения.";
            $errors = true;
        }
    } else {
        $image_error = "Обложка обязательна для загрузки.";
        $errors = true;
    }

    // Сохранение информации о книге
    if (!$errors) {
        $query = "INSERT INTO books (bookname, author, username, image, genre, link) 
                  VALUES ('$bookname', '$author', '$username', '$image', '$genre', '$bookTarget')";
        mysqli_query($con, $query);
        
        header("Location: author_instrument.php?message=book_added");
        exit();
    }
}

// Получение списка книг
$query = "SELECT * FROM books";
if ($role === 'user') {
    $query .= " WHERE username = '$username'";
}
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Author Instrument</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .error { color: red; font-size: 0.9em; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>Добавить новую книгу</h1>
    <form action="author_instrument.php" method="POST" enctype="multipart/form-data">
        <label>Название книги:</label>
        <input type="text" name="bookname" required><br>

        <label>Автор:</label>
        <input type="text" name="author" required><br>

        <label>Жанр:</label>
        <input type="text" name="genre" required><br>

        <label>Загрузить книгу:</label>
        <input type="file" name="book_file" required><br>
        <p class="error"><?php echo $book_file_error; ?></p>

        <label>Обложка:</label>
        <input type="file" name="image" required><br>
        <p class="error"><?php echo $image_error; ?></p>

        <input type="submit" name="add_book" value="Добавить книгу">
    </form>

    <h2>Ваши книги</h2>
    <table>
        <tr>
            <th>Название книги</th>
            <th>Автор</th>
            <th>Жанр</th>
            <th>Обложка</th>
            <th>Действия</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
            <?php if ($row['username'] === $username  || $role === 'admin'): ?>
                <td><a href='book.php?id=<?php echo $row['id']; ?>'><?php echo $row['bookname']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['genre']; ?></td>
                <td><img src='uploads/<?php echo $row['image']; ?>' width='50'></td>
            <?php endif; ?>
                <td>
                    <?php if ($role === 'admin' || $row['username'] === $username): ?>
                        <a href="edit_book.php?id=<?php echo $row['id']; ?>">Редактировать</a> |
                        <a href='delete_book.php?id=<?php echo $row['id']; ?>' onclick="return confirm('Вы уверены, что хотите удалить эту книгу?');">Удалить</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

