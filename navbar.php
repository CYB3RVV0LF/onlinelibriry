<div class="navbar">
    <?php if (isset($_SESSION['username'])): ?>
        <p>Добро пожаловать, <?php echo $_SESSION['username']; ?>!</p>
    <?php else: ?>
        <p>Пожалуйста авторизуйтесь для полного доступа к сайту</p>
    <?php endif; ?>
</div>

<div class="sidebar">
    
        <a href="dashboard.php">Главная</a>
        <a href="library.php">Каталог книг</a>
        <a href="readers.php">Читатели</a>
        <?php if (isset($_SESSION['username'])): ?>
        
            <?php if ($_SESSION['role'] === 'author'): ?>
                <a href="author_instrument.php">Инструмент автора</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'reader'): ?>
                <a href="favorite.php">Избранное</a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_instrument.php">Инструмент администратора</a>
                <a href="author_instrument.php">Инструмент автора</a>
            <?php endif; ?>
            <a href="logout.php">Выход</a>  
        <?php else: ?>
        <a href="login.php">Авторизация</a>
        <a href="registration.php">Регистрация</a>
        <?php endif; ?>
        
    
</div>
