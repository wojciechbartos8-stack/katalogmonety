<?php
session_start();
$user_role = $_SESSION['role'] ?? 'guest';
?>

<footer style="
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #1a1a1a;
    color: #f1f1f1;
    text-align: center;
    padding: 10px 0;
    font-family: Arial, sans-serif;
    border-top: 2px solid #444;
    z-index: 1000;
">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="font-weight: bold;">
            &copy; <?php echo date('Y'); ?> Katalog Monet
        </div>
        <div>
            <a href="index.php" style="color: #f1f1f1; text-decoration: none; margin: 0 10px;">Strona g³ówna</a> |
            <a href="aboutme.php" style="color: #f1f1f1; text-decoration: none; margin: 0 10px;">O mnie</a> |
            <a href="contact.php" style="color: #f1f1f1; text-decoration: none; margin: 0 10px;">Kontakt</a> |

            <?php if ($user_role === 'admin'): ?>
                <a href="admin_panel.php" style="color: #00ff7f; text-decoration: none; margin: 0 10px;">Panel administratora</a> |
                <a href="logout.php" style="color: #ff6347; text-decoration: none; margin: 0 10px;">Wyloguj</a>
            <?php elseif ($user_role === 'user'): ?>
                <a href="user_panel.php" style="color: #00bfff; text-decoration: none; margin: 0 10px;">Panel u¿ytkownika</a> |
                <a href="logout.php" style="color: #ff6347; text-decoration: none; margin: 0 10px;">Wyloguj</a>
            <?php else: ?>
                <a href="login.php" style="color: #f1f1f1; text-decoration: none; margin: 0 10px;">Zaloguj</a> |
                <a href="register.php" style="color: #f1f1f1; text-decoration: none; margin: 0 10px;">Zarejestruj</a>
            <?php endif; ?>
        </div>
    </div>
</footer>
