<?php
require('db.php');
session_start();
include 'navbar.php';
// Проверяем, передан ли параметр ID книги в URL
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);
// Запрос для получения комментариев
    

    // Запрос для получения данных книги и средней оценки
    $query = "SELECT *, 
              (SELECT COALESCE(average_rating, 0) FROM books WHERE id = '$book_id') AS avg_rating 
              FROM books WHERE id='$book_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $book = mysqli_fetch_assoc($result);
    } else {
        echo "<p>Книга не найдена</p>";
        exit();
    }
    $comments_query = "SELECT c.comment, c.created_at, u.username 
                   FROM comments c
                   JOIN users u ON c.user_id = u.username
                   WHERE c.book_id = '$book_id'
                   ORDER BY c.created_at DESC";
    $comments_result = mysqli_query($con, $comments_query);

    $favourites_query = "SELECT u.username 
    FROM favourites f
    JOIN users u ON f.user_id = u.username
    WHERE f.book_id = '$book_id'";
    $favourites_result = mysqli_query($con, $favourites_query);
// Проверка, является ли текущий пользователь автором книги
    $is_author = ($_SESSION['username'] == $book['username'] && $_SESSION['role'] == 'author');

} else {
    echo "<p>Неизвестный книжный ID</p>";
    exit();
}
   
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo $book['bookname']; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1><?php echo $book['bookname']; ?></h1>
    <p><strong>Автор:</strong> <?php echo $book['author']; ?></p>
    <p><strong>Жанр:</strong> <?php echo $book['genre']; ?></p>
    <p><strong>Дата добавления:</strong> <?php echo $book['dateadd']; ?></p>
    <p><strong>Добавлено пользователем:</strong> <?php echo $book['username']; ?></p>
    <p><strong>Средняя оценка:</strong> <?php echo round($book['avg_rating'], 2); ?></p>
    <img src="uploads/<?php echo $book['image']; ?>" class="book-card">
    
    <form method="POST" action="add_to_favourites.php">
        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
        <button type="submit" class="button">Добавить в избранное</button>
    </form>
    <a href="read.php?id=<?php echo $book['id']; ?>" class="button">Читать</a>
    
<?php if ($_SESSION['role'] == 'reader') : ?>
    <form method="POST" action="rate_book.php">
        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
        <button type="submit" class="button">Оценить</button>
        <label for="rating">Оцените книгу:</label>
        <select name="rating" id="rating" required>
            <?php for ($i = 1; $i <= 10; $i++) : ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
        
    </form>
<?php endif; ?>

<?php if ($is_author) : ?>
    <h2>Читатели, добавившие книгу в избранное:</h2>
    <div class="favourites-section">
        <?php if (mysqli_num_rows($favourites_result) > 0) : ?>
<ul>
    <?php while ($favourite = mysqli_fetch_assoc($favourites_result)) : ?>
        <li><?php echo htmlspecialchars($favourite['username']); ?></li>
    <?php endwhile; ?>
</ul>
<?php else : ?>
<p>Никто еще не добавил эту книгу в избранное.</p>
<?php endif; ?>
</div>
<?php endif; ?>

<h2>Комментарии</h2>
<div class="comments-section">
    <?php if (mysqli_num_rows($comments_result) > 0) : ?>
        <?php while ($comment = mysqli_fetch_assoc($comments_result)) : ?>
            <div class="comment">
                <p><strong><?php echo $comment['username']; ?>:</strong></p>
                <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                <p class="comment-date"><?php echo date("d.m.Y H:i", strtotime($comment['created_at'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <p>Комментариев пока нет. Будьте первым!</p>
    <?php endif; ?>
</div>
<?php if (isset($_SESSION['username'])) : ?>
    <form method="POST" action="add_comment.php">
        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
        <label for="comment">Ваш комментарий:</label><br>
        <textarea name="comment" id="comment" rows="4" required></textarea><br>
        <button type="submit" class="button">Отправить</button>
    </form>
<?php else : ?>
    <p><a href="login.php">Войдите</a>, чтобы оставить комментарий.</p>
<?php endif; ?>

</body>
</html>

