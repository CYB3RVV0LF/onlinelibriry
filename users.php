<?php
require('db.php');
session_start();



$readers_query = "SELECT username FROM users WHERE role = 'renter'";
$readers_result = mysqli_query($con, $readers_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
    <h1>Список пользователей</h1>

    <ul class="readers-list">
        <?php if (mysqli_num_rows($readers_result) > 0) : ?>
            <?php while ($reader = mysqli_fetch_assoc($readers_result)) : ?>
                <li>
                    <a href="favorite.php?user=<?php echo htmlspecialchars($reader['username']); ?>">
                        <?php echo htmlspecialchars($reader['username']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Читателей пока нет.</p>
        <?php endif; ?>
    </ul>
</body>
</html>
