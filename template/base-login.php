<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $templateParams["titolo"]; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-container">
        <div class="login-box">
            <h1>Campus Sports</h1>
            <form action="login.php" method="POST">
                <h2>Login</h2>
                <?php if(isset($templateParams["errorelogin"])): ?>
                <p class="errore"><?php echo $templateParams["errorelogin"]; ?></p>
                <?php endif; ?>
                <ul>
                    <li>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </li>
                    <li>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </li>
                    <li>
                        <input type="submit" name="submit" value="Accedi">
                    </li>
                </ul>
            </form>
        </div>
    </main>
</body>
</html>
