<?php
session_start();

// Definisci la costante di sicurezza per l'accesso al database
define('SECURE_ACCESS', true);

// Verifica login e permesso
if (!isset($_SESSION["utente"]) || $_SESSION["permesso"] !== 1) {
    header("Location: ../../index.php");
    exit();
}

// Include il file di configurazione del database
require_once '../../config/database.php';

// Gestione creazione di un nuovo quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $classe = $_POST['classe'];
    $numero_domande = (int)$_POST['numero_domande'];

    // Inserimento del quiz usando parametri sicuri
    executeQuery(
        "INSERT INTO quiz (nome, descrizione, classe, numero_domande) VALUES (?, ?, ?, ?)",
        "sssi",
        [$nome, $descrizione, $classe, $numero_domande]
    );

    // Ottieni l'ID del quiz appena inserito
    $stmt = executeQuery("SELECT LAST_INSERT_ID() as quiz_id", "", []);
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $quiz_id = $row['quiz_id'];
    $stmt->close();

    // Inserimento delle domande (AGGIORNATO PER SALVARE GLI ID)
    $domande_ids = [];
    for ($i = 1; $i <= $numero_domande; $i++) {
        if (isset($_POST["domanda_$i"])) {
            $domanda = $_POST["domanda_$i"];
            executeQuery(
                "INSERT INTO domande (id_quiz, testo_domanda) VALUES (?, ?)",
                "is",
                [$quiz_id, $domanda]
            );

            // SALVA L'ID DELLA DOMANDA APPENA CREATA
            $stmt = executeQuery("SELECT LAST_INSERT_ID() as domanda_id", "", []);
            $domande_ids[$i] = $stmt->get_result()->fetch_assoc()['domanda_id'];
            $stmt->close();
        }
    }

    // Salva gli ID delle domande in sessione per uso futuro
    $_SESSION['domande_ids'][$quiz_id] = $domande_ids;

    // Redirezione per evitare la duplicazione
    header("Location: teacher.php"); // Reindirizza alla stessa pagina
    exit(); // Termina l'esecuzione dello script
}

// Gestione eliminazione di un quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_quiz'])) {
    $quiz_id = (int)$_POST['quiz_id'];

    // Elimina risposte, domande e quiz usando query parametrizzate
    executeQuery("DELETE FROM risposte WHERE id_quiz IN (SELECT id FROM domande WHERE id_quiz = ?)", "i", [$quiz_id]);
    executeQuery("DELETE FROM domande WHERE id_quiz = ?", "i", [$quiz_id]);
    executeQuery("DELETE FROM quiz WHERE id = ?", "i", [$quiz_id]);

    $success_message = "Quiz eliminato con successo!";
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
    <link rel="icon" type="image/ico" href="../../asset/img/logo-quiz.ico">
    <link rel="stylesheet" href="../../asset/css/style-teacher.css">
</head>
<body>
<style>
    textarea {
    width: 100%;
    min-height: 100px;
}
.success {
    color: green;
    background-color: #e8f5e9;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
}
</style>
<p>Ciao <?php echo $_SESSION["utente"]; ?>!</p>

<button id="logout" onclick="logout()">Logout</button>

<?php
echo "<p style='margin: 0;'>───────────────────────────────────────────────────────</p>";

// Query per ottenere gli studenti usando query parametrizzate
$stmt = executeQuery("SELECT cognome, nome, classe FROM utente WHERE permesso = 0 ORDER BY cognome ASC", "", []);
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Cognome</th><th>Nome</th><th>Classe</th></tr>";
    $index = 1; // Variabile per numerare le righe
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>$index</td>"; // Stampa la numerazione
        echo "<td>{$row['cognome']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>{$row['classe']}</td>";
        echo "</tr>";
        $index++; // Incrementa la numerazione
    }
    echo "</table>";
} else {
    echo "<p>Non ci sono studenti disponibili.</p>";
}
$stmt->close();
?>

<h1>Quiz Creati</h1>
<?php

if (isset($success_message)): ?>
    <div class="success"><?php echo $success_message;?></div>
<?php endif;

$stmt = executeQuery("SELECT * FROM quiz", "", []);
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($quiz = $result->fetch_assoc()) {
        echo "
            <blockquote>
            <strong>{$quiz['nome']}</strong> 
            <p>{$quiz['classe']}  {$quiz['descrizione']}</p>
            
            <a class='button' href='quiz_details.php?quiz_id={$quiz['id']}'>Dettagli</a>
            <a class='button' href='edit_quiz.php?quiz_id={$quiz['id']}'>Modifica</a>

            <button class='button' onclick='showConfirmPopup(this, {$quiz['id']})' style='color: red;'>Elimina</button><br><br>
            </blockquote>
        ";
    }
} else {
    echo "<p>Non ci sono quiz disponibili.</p>";
}
$stmt->close();
?>
<p style='margin: 0;'>───────────────────────────────────────────────────────</p>
<h1>Crea un Nuovo Quiz</h1>
<form method="POST" action="teacher.php">
    <label>Nome Quiz:<br><input type="text" name="nome" required></label><br>
    <label>Descrizione:<textarea name="descrizione" required></textarea></label><br>
    <label for="classe">Classe:</label>
    <select name="classe" id="classe" required>
        <option value="">Seleziona una classe</option>
        <?php
        $stmt = executeQuery("SELECT DISTINCT classe FROM utente WHERE permesso = 0", "", []);
        $classi_result = $stmt->get_result();
        while ($classe = $classi_result->fetch_assoc()) {
            echo "<option value='{$classe['classe']}'>{$classe['classe']}</option>";
        }
        $stmt->close();
        ?>
    </select><br>
    <label for="numero_domande">Numero Domande: </label><input type="number" name="numero_domande" id="numero_domande" min="1" required><br>
    <div id="questions"></div>
    <input type="submit" name="create_quiz" value="Crea Quiz">
</form>

<?php
// Chiudi la connessione al database
closeDbConnection($conn);
?>

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
        fetch("../../auth/logout.php")
            .then(response => window.location.href = "../../../index.php")
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