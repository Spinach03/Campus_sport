<?php
require_once 'bootstrap.php';

// Se gia loggato, redirect
if(isUserLoggedIn()){
    if(isAdmin()){
        header("Location: admin/index.php");
    } else {
        header("Location: utente/index.php");
    }
    exit;
}

$errori = [];
$successo = false;

/**
 * Normalizza una stringa per il confronto con l'email
 * Rimuove accenti, converte in minuscolo, rimuove spazi extra
 */
function normalizzaPerEmail($stringa) {
    $stringa = mb_strtolower(trim($stringa), 'UTF-8');
    
    // Usa iconv per rimuovere accenti
    $stringa = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $stringa);
    
    // Rimuovi tutto tranne lettere minuscole
    $stringa = preg_replace('/[^a-z]/', '', $stringa);
    
    return $stringa;
}

/**
 * Costruisce email attesa da nome e cognome
 */
function costruisciEmailAttesa($nome, $cognome) {
    $nomeNorm = normalizzaPerEmail($nome);
    $cognomeNorm = normalizzaPerEmail($cognome);
    return $nomeNorm . '.' . $cognomeNorm . '@studio.unibo.it';
}

// Gestione della registrazione
if(isset($_POST['submit'])){
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confermaPassword = $_POST['conferma_password'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $dataNascita = $_POST['data_nascita'] ?? '';
    $corsoLaureaId = intval($_POST['corso_laurea_id'] ?? 0);
    $annoIscrizione = intval($_POST['anno_iscrizione'] ?? date('Y'));
    
    // VALIDAZIONI
    
    if(empty($nome)){
        $errori[] = "Il nome e obbligatorio";
    } elseif(strlen($nome) < 2 || strlen($nome) > 50){
        $errori[] = "Il nome deve essere tra 2 e 50 caratteri";
    }
    
    if(empty($cognome)){
        $errori[] = "Il cognome e obbligatorio";
    } elseif(strlen($cognome) < 2 || strlen($cognome) > 50){
        $errori[] = "Il cognome deve essere tra 2 e 50 caratteri";
    }
    
    if(empty($email)){
        $errori[] = "L'email e obbligatoria";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errori[] = "Formato email non valido";
    } elseif(!preg_match('/@studio\.unibo\.it$/i', $email)){
        $errori[] = "Devi usare la tua email universitaria (@studio.unibo.it)";
    } else {
        $emailAttesa = costruisciEmailAttesa($nome, $cognome);
        
        if($email !== $emailAttesa){
            $errori[] = "L'email deve corrispondere al tuo nome e cognome. Per " . $nome . " " . $cognome . " l'email deve essere: " . $emailAttesa;
        } else {
            if($dbh->emailExists($email)){
                $errori[] = "Questa email e gia registrata. Hai gia un account?";
            }
        }
    }
    
    if(empty($password)){
        $errori[] = "La password e obbligatoria";
    } elseif(strlen($password) < 8){
        $errori[] = "La password deve essere di almeno 8 caratteri";
    } elseif(!preg_match('/[A-Z]/', $password)){
        $errori[] = "La password deve contenere almeno una lettera maiuscola";
    } elseif(!preg_match('/[a-z]/', $password)){
        $errori[] = "La password deve contenere almeno una lettera minuscola";
    } elseif(!preg_match('/[0-9]/', $password)){
        $errori[] = "La password deve contenere almeno un numero";
    }
    
    if($password !== $confermaPassword){
        $errori[] = "Le password non coincidono";
    }
    
    // Data di nascita - OBBLIGATORIA e deve essere maggiorenne
    if(empty($dataNascita)){
        $errori[] = "La data di nascita e obbligatoria";
    } else {
        $dataNascitaObj = DateTime::createFromFormat('Y-m-d', $dataNascita);
        if(!$dataNascitaObj){
            $errori[] = "Formato data di nascita non valido";
        } else {
            $oggi = new DateTime();
            $eta = $oggi->diff($dataNascitaObj)->y;
            
            // Deve essere maggiorenne (almeno 18 anni)
            if($eta < 18){
                $errori[] = "Devi essere maggiorenne (almeno 18 anni) per registrarti";
            } elseif($eta > 100){
                $errori[] = "Data di nascita non valida";
            }
            
            // Verifica che la data non sia nel futuro
            if($dataNascitaObj > $oggi){
                $errori[] = "La data di nascita non puo essere nel futuro";
            }
        }
    }
    
    if($corsoLaureaId > 0){
        $corsoValido = $dbh->getCorsoLaureaById($corsoLaureaId);
        if(!$corsoValido){
            $errori[] = "Corso di laurea non valido";
        }
    }
    
    $annoCorrente = intval(date('Y'));
    if($annoIscrizione < 2000 || $annoIscrizione > $annoCorrente){
        $errori[] = "Anno di iscrizione non valido";
    }
    
    // REGISTRAZIONE
    if(empty($errori)){
        $datiUtente = array(
            'nome' => $nome,
            'cognome' => $cognome,
            'email' => $email,
            'password' => $password,
            'telefono' => !empty($telefono) ? $telefono : null,
            'data_nascita' => $dataNascita,
            'corso_laurea_id' => $corsoLaureaId > 0 ? $corsoLaureaId : null,
            'anno_iscrizione' => $annoIscrizione
        );
        
        $risultato = $dbh->registerUser($datiUtente);
        
        if($risultato['success']){
            $successo = true;
        } else {
            $errori[] = isset($risultato['error']) ? $risultato['error'] : "Errore durante la registrazione. Riprova.";
        }
    }
}

$corsiLaurea = $dbh->getCorsiLaurea();

$templateParams["titolo"] = "Campus Sports - Registrazione";
$templateParams["errori"] = $errori;
$templateParams["successo"] = $successo;
$templateParams["corsi_laurea"] = $corsiLaurea;

$templateParams["old"] = array(
    'nome' => isset($_POST['nome']) ? $_POST['nome'] : '',
    'cognome' => isset($_POST['cognome']) ? $_POST['cognome'] : '',
    'email' => isset($_POST['email']) ? $_POST['email'] : '',
    'telefono' => isset($_POST['telefono']) ? $_POST['telefono'] : '',
    'data_nascita' => isset($_POST['data_nascita']) ? $_POST['data_nascita'] : '',
    'corso_laurea_id' => isset($_POST['corso_laurea_id']) ? $_POST['corso_laurea_id'] : '',
    'anno_iscrizione' => isset($_POST['anno_iscrizione']) ? $_POST['anno_iscrizione'] : date('Y')
);

require 'template/base-register.php';
?>