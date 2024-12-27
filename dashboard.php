<?php
require('db.php');
session_start();
$is_logged_in = isset($_SESSION['username']);
$role = $_SESSION['role'] ?? null;

// Проверка соединения с базой данных
$db_error = "";
if (!$con) {
    $db_error = "Ошибка подключения к базе данных.";
}

// Инициализация переменной поиска
$search_query = '';
$search_results = true; // Переменная для проверки наличия результатов

if (isset($_GET['search']) && $con) {
    $search_query = mysqli_real_escape_string($con, $_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
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
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Введите полное название или автора" value="<?php echo htmlspecialchars($search_query); ?>" />
            <button type="submit">Поиск</button>
        </form>

        <h1>Последние добавленные книги</h1>

        <div class="book-grid">
            <?php
            // Выполнение запроса только если подключение успешно
            if ($con) {
                // Запрос с фильтром поиска, если введён запрос
                $query = "SELECT id, bookname, author, image FROM books ";
                if (!empty($search_query)) {
                    $query .= "WHERE (bookname = '$search_query' OR author = '$search_query') ";
                }
                $query .= "ORDER BY dateadd DESC LIMIT 6";
                
                $result = mysqli_query($con, $query);

                // Проверка наличия результатов
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class='book-card'>
                            <p><a href='book.php?id=<?php echo $row['id']; ?>'><?php echo $row['bookname']; ?></a></p>
                            <p><strong>Автор:</strong> <?php echo $row['author']; ?></p>
                           <img src='uploads/<?php echo $row['image']; ?>' width='200' alt='book image' class="book-image">
                        </div>
                    <?php endwhile;
                } else {
                    // Вывод сообщения, если книги не найдены
                    echo "<p>Искомая книга не найдена</p>";
                }
            } else {
                echo "<p>Не удалось загрузить книги из-за ошибки базы данных.</p>";
            }
            ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
