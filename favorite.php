<?php
require('db.php');
session_start();
include 'navbar.php';

// Проверяем, авторизован ли текущий пользователь
if (!isset($_SESSION['username'])) {
    echo "<p>Вы должны быть авторизованы, чтобы просматривать избранное.</p>";
    exit();
}

// Определяем пользователя, чьё избранное нужно отобразить
$username = isset($_GET['user']) ? mysqli_real_escape_string($con, $_GET['user']) : $_SESSION['username'];

// Проверяем, существует ли пользователь с ролью reader
$user_query = "SELECT username FROM users WHERE username = '$username' AND role = 'reader'";
$user_result = mysqli_query($con, $user_query);

if (mysqli_num_rows($user_result) == 0) {
    echo "<p>Пользователь с именем $username не найден или не является reader.</p>";
    exit();
}

// Получаем избранные книги пользователя
$favourites_query = "SELECT books.id, books.bookname, books.author, books.image 
                     FROM favourites 
                     JOIN books ON favourites.book_id = books.id 
                     WHERE favourites.user_id = '$username'";
$favourites_result = mysqli_query($con, $favourites_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Избранное <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Избранные книги пользователя <?php echo htmlspecialchars($username); ?></h1>
    <div class="book-grid">
        <?php if (mysqli_num_rows($favourites_result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($favourites_result)): ?>
                <div class="book-card">
                    <a href="book.php?id=<?php echo $row['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['bookname']); ?>" class="book-image">
                    </a>
                    <p><a href="book.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['bookname']); ?></a></p>
                    <p><strong>Автор:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Избранных книг пока нет.</p>
        <?php endif; ?>
    </div>
</body>
</html>
