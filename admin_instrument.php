<?php
session_start();
require('db.php');
include 'navbar.php';
// Проверка авторизации и роли
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Обработка запросов изменения ролей и удаления пользователей
$message = "";

if (isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = mysqli_real_escape_string($con, $_POST['role']);
    if (in_array($new_role, ['reader', 'author', 'admin'])) {
        $query = "UPDATE users SET role = '$new_role' WHERE id = $user_id";
        if (mysqli_query($con, $query)) {
            $message = "Роль пользователя успешно обновлена.";
        } else {
            $message = "Ошибка при обновлении роли.";
        }
    }
}

if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);
    $query = "DELETE FROM users WHERE id = $user_id";
    if (mysqli_query($con, $query)) {
        $message = "Пользователь успешно удалён.";
    } else {
        $message = "Ошибка при удалении пользователя.";
    }
}

// Получение списка всех пользователей
$query = "SELECT id, username, role FROM users";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Инструмент администратора</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Инструмент администратора</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <h2>Управление пользователями</h2>
    <table>
        <tr>
            <th>Имя пользователя</th>
            <th>Текущая роль</th>
            <th>Действия</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <select name="role" required>
                            <option value="reader" <?php echo $row['role'] === 'reader' ? 'selected' : ''; ?>>Reader</option>
                            <option value="author" <?php echo $row['role'] === 'author' ? 'selected' : ''; ?>>Author</option>
                            <option value="admin" <?php echo $row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <button type="submit" name="update_role">Обновить роль</button>
                    </form>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_user" onclick="return confirm('Вы уверены, что хотите удалить пользователя?');">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
