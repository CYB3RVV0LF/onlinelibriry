<?php
require('db.php');
session_start();

// Проверка соединения с базой данных
$db_error = "";
if (!$con) {
    $db_error = "Ошибка подключения к базе данных.";
}

// Поиск
$search_query = '';
if (isset($_GET['search']) && $con) {
    $search_query = mysqli_real_escape_string($con, $_GET['search']);
}

// Запрос на получение всех книг
$query = "SELECT id, bookname, author, image FROM books ";
if (!empty($search_query)) {
    $query .= "WHERE (bookname LIKE '%$search_query%' OR author LIKE '%$search_query%') ";
}
$query .= "ORDER BY dateadd DESC";

$books_result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог книг</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .error { color: red; font-size: 0.9em; margin-top: 5px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
    <?php if ($db_error): ?>
        <p class="error"><?php echo $db_error; ?></p>
    <?php else: ?>
        <form method="GET" action="library.php" class="search-form">
            <input type="text" name="search" placeholder="Введите название или автора" value="<?php echo htmlspecialchars($search_query); ?>" />
            <button type="submit">Поиск</button>
        </form>

        <h1>Каталог книг</h1>

        <div class="book-grid">
            <?php if ($books_result && mysqli_num_rows($books_result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($books_result)): ?>
                    <div class="book-card">
                        <a href="book.php?id=<?php echo $row['id']; ?>">
                            <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['bookname']; ?>" class="book-image">
                        </a>
                        <p><a href="book.php?id=<?php echo $row['id']; ?>"><?php echo $row['bookname']; ?></a></p>
                        <p><strong>Автор:</strong> <?php echo $row['author']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Книги не найдены.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>


