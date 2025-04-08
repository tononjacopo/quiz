<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Quiz</title>
    <!-- Minified version for styling -->
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link rel="stylesheet" href="src/asset/css/style.css">
    <link rel="icon" type="image/ico" href="src/asset/img/logo-quiz.ico">
</head>
<body>
<div class="gradient-background"></div>

<?php
// Mostra eventuali messaggi di errore dalla sessione
session_start();
if (isset($_SESSION['login_error'])) {
    echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
    unset($_SESSION['login_error']); // Rimuovi il messaggio dopo averlo mostrato
}
?>

<form method="post" action="src/auth/login.php">
    <div class="input-group">
        <input type="text" name="login" id="login" placeholder=" " required>
        <label for="login">Login</label>
    </div>
    <div class="input-group">
        <input type="password" name="password" id="password" placeholder=" " required>
        <label for="password">Password</label>
    </div>
    <div>
        <input id="custom-submit" type="submit" name="comando" value="Login">
    </div>
</form>

</body>
</html>