<?php
session_start();
// Verifica login e permesso
if (!isset($_SESSION["utente"]) || $_SESSION["permesso"] !== 1) {
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

// Gestione creazione di un nuovo quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $descrizione = $conn->real_escape_string($_POST['descrizione']);
    $numero_domande = (int)$_POST['numero_domande'];

    // Inserimento del quiz
    $conn->query("INSERT INTO quiz (nome, descrizione, numero_domande) VALUES ('$nome', '$descrizione', $numero_domande)");
    $quiz_id = $conn->insert_id;

    // Inserimento delle domande associate
    for ($i = 1; $i <= $numero_domande; $i++) {
        if (isset($_POST["domanda_$i"])) {
            $domanda = $conn->real_escape_string($_POST["domanda_$i"]);
            $conn->query("INSERT INTO domande (id_quiz, testo_domanda) VALUES ($quiz_id, '$domanda')");
        }
    }

    // Redirezione per evitare la duplicazione
    header("Location: teacher.php"); // Reindirizza alla stessa pagina
    exit(); // Termina l'esecuzione dello script
}

// Gestione eliminazione di un quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_quiz'])) {
    $quiz_id = (int)$_POST['quiz_id'];

    // Elimina risposte, domande e quiz
    $conn->query("DELETE FROM risposte WHERE id_quiz IN (SELECT id FROM domande WHERE id_quiz = $quiz_id)");
    $conn->query("DELETE FROM domande WHERE id_quiz = $quiz_id");
    $conn->query("DELETE FROM quiz WHERE id = $quiz_id");

    echo "<p>Quiz eliminato con successo!</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insegnante | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="../logo-quiz.jpg">
    <link rel="stylesheet" href="style-teacher.css">
</head>
<body>

<p>Ciao <?php echo $_SESSION["utente"]; ?>!</p>

<button id="logout" onclick="logout()">Logout</button>

<?php
echo "<p style='margin: 0;'>───────────────────────────────────────────────────────</p>";

// Seleziona tutti gli studenti ordinati per cognome.
$result = $conn->query("SELECT cognome, nome FROM utente WHERE permesso = 0 ORDER BY cognome ASC");

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Cognome</th><th>Nome</th></tr>";
    $index = 1; // Variabile per numerare le righe
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>$index</td>"; // Stampa la numerazione
        echo "<td>{$row['cognome']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "</tr>";
        $index++; // Incrementa la numerazione
    }
    echo "</table>";
} else {
    echo "<p>Non ci sono studenti disponibili.</p>";
}
?>

<h1>Quiz Creati</h1>
<?php
$result = $conn->query("SELECT * FROM quiz");
if ($result->num_rows > 0) {
    while ($quiz = $result->fetch_assoc()) {
        echo "
            <blockquote>
            <strong>{$quiz['nome']}</strong> 
            <p>{$quiz['descrizione']}</p>
            
            <a class='button' href='quiz_details.php?quiz_id={$quiz['id']}'>Dettagli</a>
            <a class='button' href='edit_quiz.php?quiz_id={$quiz['id']}'>Modifica</a>

            <button class='button' onclick='showConfirmPopup(this, {$quiz['id']})' style='color: red;'>Elimina</button><br><br>
            </blockquote>
        ";
    }
} else {
    echo "<p>Non ci sono quiz disponibili.</p>";
}
?>
<p style='margin: 0;'>───────────────────────────────────────────────────────</p>
<h1>Crea un Nuovo Quiz</h1>
<form method="POST" action="teacher.php">
    <label>Nome Quiz:<br><input type="text" name="nome" required></label><br>
    <label>Descrizione:<textarea name="descrizione" required></textarea></label><br>
    <label for="numero_domande">Numero Domande: </label><input type="number" name="numero_domande" id="numero_domande" min="1" required><br>
    <div id="questions"></div>
    <input type="submit" name="create_quiz" value="Crea Quiz">
</form>

<script>
    document.getElementById('numero_domande').addEventListener('change', function() {
        let count = parseInt(this.value);
        let questionsDiv = document.getElementById('questions');
        questionsDiv.innerHTML = ''; // Pulisce l'area delle domande
        for (let i = 1; i <= count; i++) {
            questionsDiv.innerHTML += `Domanda ${i}: <textarea name='domanda_${i}' required></textarea><br>`;
        }
    });

    function logout() {
        fetch("../logout.php")
            .then(response => window.location.href = "../index.php")
            .catch((err) => console.log("Errore:", err));
    }

    document.addEventListener("DOMContentLoaded", function () {
        const tableRows = document.querySelectorAll("table tr");
        for (let i = 1; i < tableRows.length; i++) { // Salta l'intestazione
            tableRows[i].querySelector("td:first-child").textContent = i; // Assegna il numero della riga
        }
    });

    // Mostra il popup di conferma per eliminazione
    function showConfirmPopup(button, quizId) {
        const existingPopup = document.querySelector('.confirm-popup');
        if (existingPopup) existingPopup.remove();

        // Crea e mostra un nuovo popup
        const popup = document.createElement('div');
        popup.className = 'confirm-popup';
        popup.innerHTML = `
            <p>Sei sicuro di voler eliminare questo quiz?</p>
            <form method='POST' action='teacher.php' style='display:inline;'>
                <input type='hidden' name='quiz_id' value='${quizId}'>
                <button type='submit' name='delete_quiz'>Sì</button>
                <button type='button' onclick='removePopup()'>No</button>
            </form>
        `;

        const rect = button.getBoundingClientRect();
        popup.style.left = `${rect.left}px`;
        popup.style.top = `${rect.top + window.scrollY + button.offsetHeight}px`;

        document.body.appendChild(popup);
    }

    // Rimuove il popup
    function removePopup() {
        const popup = document.querySelector('.confirm-popup');
        if (popup) popup.remove();
    }
</script>
</body>
</html>