<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "quiz";

function generate_password_hash($password): string
{
    return hash('sha256', $password);
}

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Limiti per la protezione brute force
$max_attempts = 5;
$lockout_time = 60;

// Inizializza i parametri di protezione brute force
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comando'])) {
    $post_login = trim(htmlspecialchars($_POST["login"], ENT_QUOTES, "UTF-8"));
    $post_password = trim(htmlspecialchars($_POST["password"], ENT_QUOTES, "UTF-8"));

    // Protezione brute force
    $time_since_last_attempt = time() - $_SESSION['last_attempt_time'];
    if ($_SESSION['login_attempts'] >= $max_attempts && $time_since_last_attempt < $lockout_time) {
        die("Troppi tentativi falliti. Per favore, riprova tra " . ($lockout_time - $time_since_last_attempt) . " secondi.");
    }

    // Prepara la query per trovare l'utente
    $stmt = $conn->prepare("SELECT id, permesso, password FROM utente WHERE login = ?");
    $stmt->bind_param("s", $post_login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Genera hash della password fornita dall'utente
        $hashed_password = generate_password_hash($post_password);

        // Confronta l'hash generato con quello nel database
        if ($hashed_password === $row['password']) {
            session_regenerate_id(true); // Previeni il session fixation

            // Resetta i tentativi di login dopo il successo
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();

            $_SESSION["utente"] = $post_login;
            $_SESSION["idutente"] = $row["id"];
            $_SESSION["permesso"] = $row["permesso"];

            // Reindirizzamento basato sui permessi
            switch ($row["permesso"]) {
                case 0: // Studente
                    header("location: student.php");
                    break;
                case 1: // Insegnante
                    header("location: teacher/teacher.php");
                    break;
                case 2: // Amministratore
                    header("location: admin/admin.php");
                    break;
                default:
                    die("Permesso non valido.");
            }
            exit();
        }

        echo "Password non valida.";
    } else {
        echo "Login non valido.";
    }

    // Incrementa i tentativi di login falliti
    ++$_SESSION['login_attempts'];
    $_SESSION['last_attempt_time'] = time();

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | Quiz</title>
    <!-- Minified version for styling -->
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpeg" href="logo-quiz.jpg">
</head>
<body>
<div class="gradient-background"></div>
<form method="post">
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

<div>
    <p style="text-align: center; margin-top: 20px;">Login: admin Password: password<br>Login: professore Password: password<br>Login: marco Password: password</p>
</div>

</body>
</html>