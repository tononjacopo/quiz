<?php
session_start();
// Verifica login e permesso
if (!isset($_SESSION["utente"]) || $_SESSION["permesso"] !== 0) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studente | Quiz</title>
    <!-- Minified CSS -->
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="logo-quiz.jpg">
</head>
<body>
<p>Ciao <?php echo $_SESSION["utente"]; ?>!</p>
<button id="logout" style="margin-top: 30px; width: 150px" onclick="logout()">Logout</button>

<?php
// Mostra i quiz disponibili
if (!isset($_GET['quiz_id'])) {
    $student_id = $_SESSION["idutente"];

    echo "<p style='margin: 0;'>───────────────────────────────────────────────────</p>";
    echo "<h1>Quiz Disponibili</h1>";
    $result = $conn->query("
        SELECT q.id, q.nome, q.descrizione,
            (SELECT COUNT(*) FROM risposte r WHERE r.id_quiz = q.id AND r.id_studente = $student_id) AS completed
        FROM quiz q
    ");

    echo "<ul style='margin: 0; padding: 0; list-style: none;'>";
    while ($quiz = $result->fetch_assoc()) {
        if ($quiz['completed'] > 0) {
            // Mostra un checkmark verde se il test è completato
            echo "<li><span style='color: green; margin-right: 0.7em'>✔</span>";
            echo $quiz['nome'];
            echo "</li>";
        } else {
            // Mostra il link al quiz se non è ancora stato completato
            echo "<li><a href='student.php?quiz_id={$quiz['id']}'>{$quiz['nome']}</a>";
            echo "<p>{$quiz['descrizione']}</p></li>";
        }
    }
    echo "</ul>";
} else {
    // Mostra le domande del quiz SOLO se non è già stato completato
    $student_id = $_SESSION["idutente"];
    $quiz_id = (int)$_GET['quiz_id'];
    $completed = $conn->query("SELECT COUNT(*) AS completed FROM risposte WHERE id_quiz = $quiz_id AND id_studente = $student_id")->fetch_assoc()['completed'];

    if ($completed == 0) {  // Aggiunta controllo per verificare se il quiz è stato completato
        echo "<h1>Domande del Quiz</h1>";

        // Recupero dal database le domande del quiz
        $result = $conn->query("SELECT * FROM domande WHERE id_quiz = $quiz_id");

        if ($result && $result->num_rows > 0) {
            echo "<form method='POST' action='student.php?quiz_id=$quiz_id'>";
            while ($domanda = $result->fetch_assoc()) {
                echo "<h3>{$domanda['testo_domanda']}</h3>"; // Mostra il testo della domanda
                echo "<textarea name='risposta_{$domanda['id']}' required></textarea><br>";
            }
            echo "<input type='submit' name='submit' value='Invia Risposte'>";
            echo "</form>";
            echo "<a href=student.php>Torna alla pagina principale</a>";
        } else {
            echo "<p>Non ci sono domande disponibili per questo quiz.</p>";
        }
    } else {
        echo "<p>Hai già completato questo quiz.</p>";
        echo "<a href=student.php>Torna alla pagina principale</a>";
    }
}

// Gestisci invio delle risposte SOLO se il quiz non è già stato completato
if (isset($_POST['submit'])) {
    $student_id = $_SESSION["idutente"];
    $quiz_id = (int)$_GET['quiz_id'];

    $completed = $conn->query("SELECT COUNT(*) AS completed FROM risposte WHERE id_quiz = $quiz_id AND id_studente = $student_id")->fetch_assoc()['completed'];
    if ($completed == 0) {
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, 'risposta_')) {
                $domanda_id = (int)str_replace('risposta_', '', $key);
                $risposta = $conn->real_escape_string($value);
                $conn->query("INSERT INTO risposte (id_quiz, id_studente, risposta, datetime) VALUES ($quiz_id, $student_id, '$risposta', NOW())");
            }
        }
        echo "<p>Risposte inviate con successo!</p>";
    } else {
        echo "<p>Hai già completato questo quiz, impossibile inviare nuovamente le risposte.</p>";
    }
}
?>

<script>
    function logout() {
        fetch("../logout.php")
            .then(response => window.location.href = "index.php")
            .catch((err) => console.log("Errore:", err));
    }
</script>
</body>
</html>