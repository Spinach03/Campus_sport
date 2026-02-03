<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($templateParams["titolo"]); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <span class="logo-icon">🏟️</span>
                <h1 class="logo-text">Campus Sports</h1>
                <p class="logo-subtitle">Prenota i campi sportivi del campus</p>
            </div>
            
            <div class="auth-title">
                <h2>Bentornato!</h2>
                <p>Accedi al tuo account per continuare</p>
            </div>
            
            <?php if(isset($templateParams["errorelogin"])): ?>
            <div class="alert-auth alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span><?php echo htmlspecialchars($templateParams["errorelogin"]); ?></span>
            </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST" class="auth-form">
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="nome.cognome@studio.unibo.it"
                               required
                               autocomplete="email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Inserisci la tua password"
                               required
                               autocomplete="current-password">
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn-auth">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Accedi
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Non hai un account? <a href="register.php">Registrati ora</a></p>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>