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
    <main class="auth-container register">
        <div class="auth-card">
            <div class="auth-logo">
                <span class="logo-icon">🏟️</span>
                <h1 class="logo-text">Campus Sports</h1>
                <p class="logo-subtitle">Prenota i campi sportivi del campus</p>
            </div>
            
            <?php if($templateParams["successo"]): ?>
            <div class="auth-success">
                <span class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </span>
                <h2 class="success-title">Registrazione completata!</h2>
                <p class="success-text">
                    Il tuo account e stato creato con successo.<br>
                    Ora puoi accedere e iniziare a prenotare i campi sportivi.
                </p>
                <a href="login.php" class="btn-success-action">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Vai al Login
                </a>
            </div>
            <?php else: ?>
            <div class="auth-title">
                <h2>Crea il tuo account</h2>
                <p>Registrati per prenotare i campi sportivi</p>
            </div>
            
            <?php if(!empty($templateParams["errori"])): ?>
            <div class="alert-auth alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    <ul>
                        <?php foreach($templateParams["errori"] as $errore): ?>
                        <li><?php echo htmlspecialchars($errore); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <form action="register.php" method="POST" class="auth-form" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nome">
                            Nome <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               placeholder="Mario"
                               value="<?php echo htmlspecialchars($templateParams['old']['nome']); ?>"
                               required minlength="2" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cognome">
                            Cognome <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="cognome" name="cognome" 
                               placeholder="Rossi"
                               value="<?php echo htmlspecialchars($templateParams['old']['cognome']); ?>"
                               required minlength="2" maxlength="50">
                    </div>
                </div>
                
                <div class="email-preview" id="emailPreview" style="display: none;">
                    <div class="email-preview-label">La tua email universitaria deve essere:</div>
                    <div class="email-preview-value" id="emailAttesa"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">
                        Email Universitaria <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="nome.cognome@studio.unibo.it"
                               value="<?php echo htmlspecialchars($templateParams['old']['email']); ?>"
                               required>
                    </div>
                    <div class="form-hint">
                        <i class="bi bi-info-circle"></i>
                        <span>L'email deve corrispondere esattamente al tuo nome e cognome</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">
                        Password <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Crea una password sicura"
                               required minlength="8">
                    </div>
                    <div class="password-requirements">
                        <strong>La password deve contenere:</strong>
                        <ul>
                            <li>Almeno 8 caratteri</li>
                            <li>Una lettera maiuscola</li>
                            <li>Una lettera minuscola</li>
                            <li>Almeno un numero</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="conferma_password">
                        Conferma Password <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" class="form-control" id="conferma_password" 
                               name="conferma_password" placeholder="Ripeti la password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="data_nascita">
                        Data di Nascita <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-calendar input-icon"></i>
                        <input type="date" class="form-control" id="data_nascita" name="data_nascita"
                               value="<?php echo htmlspecialchars($templateParams['old']['data_nascita']); ?>"
                               required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                    </div>
                    <div class="form-hint">
                        <i class="bi bi-shield-check"></i>
                        <span>Devi essere maggiorenne (almeno 18 anni) per registrarti</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="telefono">
                        Telefono <span class="optional">(opzionale)</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="bi bi-phone input-icon"></i>
                        <input type="tel" class="form-control" id="telefono" name="telefono" 
                               placeholder="+39 333 1234567"
                               value="<?php echo htmlspecialchars($templateParams['old']['telefono']); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="corso_laurea_id">
                            Corso di Laurea <span class="optional">(opzionale)</span>
                        </label>
                        <select class="form-select" id="corso_laurea_id" name="corso_laurea_id">
                            <option value="">-- Seleziona --</option>
                            <?php foreach($templateParams['corsi_laurea'] as $corso): ?>
                            <option value="<?php echo $corso['corso_id']; ?>"
                                    <?php echo ($templateParams['old']['corso_laurea_id'] == $corso['corso_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($corso['nome']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="anno_iscrizione">
                            Anno Iscrizione
                        </label>
                        <select class="form-select" id="anno_iscrizione" name="anno_iscrizione">
                            <?php 
                            $annoCorrente = intval(date('Y'));
                            for($anno = $annoCorrente; $anno >= 2015; $anno--): 
                            ?>
                            <option value="<?php echo $anno; ?>"
                                    <?php echo ($templateParams['old']['anno_iscrizione'] == $anno) ? 'selected' : ''; ?>>
                                <?php echo $anno; ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn-auth">
                    <i class="bi bi-person-plus"></i>
                    Registrati
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Hai gia un account? <a href="login.php">Accedi</a></p>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function normalizzaPerEmail(str) {
            if (!str) return '';
            str = str.toLowerCase().trim();
            str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            str = str.replace(/[^a-z]/g, '');
            return str;
        }
        
        function costruisciEmailAttesa(nome, cognome) {
            const nomeNorm = normalizzaPerEmail(nome);
            const cognomeNorm = normalizzaPerEmail(cognome);
            if (!nomeNorm || !cognomeNorm) return '';
            return nomeNorm + '.' + cognomeNorm + '@studio.unibo.it';
        }
        
        function aggiornaEmailPreview() {
            const nome = document.getElementById('nome').value;
            const cognome = document.getElementById('cognome').value;
            const emailInput = document.getElementById('email');
            const emailPreview = document.getElementById('emailPreview');
            const emailAttesa = document.getElementById('emailAttesa');
            
            const emailCalcolata = costruisciEmailAttesa(nome, cognome);
            
            if (emailCalcolata) {
                emailPreview.style.display = 'block';
                emailAttesa.textContent = emailCalcolata;
                
                const emailInserita = emailInput.value.toLowerCase().trim();
                
                if (emailInserita) {
                    if (emailInserita === emailCalcolata) {
                        emailPreview.className = 'email-preview valid';
                        emailInput.classList.remove('is-invalid');
                        emailInput.classList.add('is-valid');
                        emailAttesa.innerHTML = emailCalcolata + ' <i class="bi bi-check-circle-fill"></i>';
                    } else {
                        emailPreview.className = 'email-preview invalid';
                        emailInput.classList.remove('is-valid');
                        emailInput.classList.add('is-invalid');
                        emailAttesa.innerHTML = emailCalcolata + ' <i class="bi bi-x-circle-fill"></i>';
                    }
                } else {
                    emailPreview.className = 'email-preview';
                    emailInput.classList.remove('is-valid', 'is-invalid');
                    emailAttesa.textContent = emailCalcolata;
                }
            } else {
                emailPreview.style.display = 'none';
                emailInput.classList.remove('is-valid', 'is-invalid');
            }
        }
        
        document.getElementById('nome').addEventListener('input', aggiornaEmailPreview);
        document.getElementById('cognome').addEventListener('input', aggiornaEmailPreview);
        document.getElementById('email').addEventListener('input', aggiornaEmailPreview);
        aggiornaEmailPreview();
        
        document.getElementById('registerForm')?.addEventListener('submit', function(e) {
            const nome = document.getElementById('nome').value;
            const cognome = document.getElementById('cognome').value;
            const email = document.getElementById('email').value.toLowerCase().trim();
            const password = document.getElementById('password').value;
            const conferma = document.getElementById('conferma_password').value;
            const dataNascita = document.getElementById('data_nascita').value;
            const emailAttesa = costruisciEmailAttesa(nome, cognome);
            
            if (email !== emailAttesa) {
                e.preventDefault();
                alert('L\'email deve corrispondere al tuo nome e cognome.\n\nPer "' + nome + ' ' + cognome + '" l\'email deve essere:\n' + emailAttesa);
                return false;
            }
            
            if (!dataNascita) {
                e.preventDefault();
                alert('La data di nascita e obbligatoria');
                return false;
            }
            
            const oggi = new Date();
            const nascita = new Date(dataNascita);
            let eta = oggi.getFullYear() - nascita.getFullYear();
            const m = oggi.getMonth() - nascita.getMonth();
            if (m < 0 || (m === 0 && oggi.getDate() < nascita.getDate())) eta--;
            
            if (eta < 18) {
                e.preventDefault();
                alert('Devi essere maggiorenne (almeno 18 anni) per registrarti');
                return false;
            }
            
            if (password.length < 8) { e.preventDefault(); alert('La password deve essere di almeno 8 caratteri'); return false; }
            if (!/[A-Z]/.test(password)) { e.preventDefault(); alert('La password deve contenere almeno una lettera maiuscola'); return false; }
            if (!/[a-z]/.test(password)) { e.preventDefault(); alert('La password deve contenere almeno una lettera minuscola'); return false; }
            if (!/[0-9]/.test(password)) { e.preventDefault(); alert('La password deve contenere almeno un numero'); return false; }
            if (password !== conferma) { e.preventDefault(); alert('Le password non coincidono'); return false; }
            
            return true;
        });
    </script>
</body>
</html>