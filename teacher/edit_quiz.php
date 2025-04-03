<?php
function getConn()
{
    session_start();
    session_regenerate_id(true);
    if (!isset($_SESSION['utente']) || $_SESSION['permesso'] !== 1) {
        header("Location: index.php");
        exit();
    }

// Connessione al database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "quiz";
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
$conn = getConn();

$quiz_id = $_GET['quiz_id'] ?? 0;

// Aggiornamento quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $descrizione = $conn->real_escape_string($_POST['descrizione']);
    $conn->query("UPDATE quiz SET nome = '$nome', descrizione = '$descrizione' WHERE id = $quiz_id");

    foreach ($_POST as $key => $value) {
        if (str_starts_with($key, 'domanda_')) {
            $domanda_id = (int)str_replace('domanda_', '', $key);
            $testo_domanda = $conn->real_escape_string($value);
            $conn->query("UPDATE domande SET testo_domanda = '$testo_domanda' WHERE id = $domanda_id");
        }
    }
    echo "<p>Quiz aggiornato con successo!</p>";
}

// Recupera i dati del quiz
$quiz = $conn->query("SELECT * FROM quiz WHERE id = $quiz_id")->fetch_assoc();
$domande = $conn->query("SELECT * FROM domande WHERE id_quiz = $quiz_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="../logo-quiz.jpg">
</head>
<body>
<h1>Modifica Quiz</h1>
<form method="POST" action="">
    <label>Nome:<input type="text" name="nome" value="<?php echo htmlspecialchars($quiz['nome']); ?>" required></label><br>
    <label>Descrizione:<textarea name="descrizione" required><?php echo htmlspecialchars($quiz['descrizione']); ?></textarea></label><br>

    <?php while ($domanda = $domande->fetch_assoc()) { ?>
        Domanda: <label><textarea name="domanda_<?php echo $domanda['id']; ?>" required><?php echo htmlspecialchars($domanda['testo_domanda']); ?></textarea></label><br>
    <?php } ?>

    <input type="submit" value="Salva Modifiche">
</form>
<a href="teacher.php">Torna alla pagina principale</a>
</body>
</html>
