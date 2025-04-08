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

// Verifica che l'ID del quiz sia presente
$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
if ($quiz_id === 0) {
    header("Location: teacher.php");
    exit();
}

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
    
    $success_message = "Quiz aggiornato con successo!";
}

// Recupera i dati del quiz
$result = $conn->query("SELECT * FROM quiz WHERE id = $quiz_id");
if ($result->num_rows === 0) {
    header("Location: teacher.php");
    exit();
}
$quiz = $result->fetch_assoc();

// Recupera le domande del quiz
$domande = $conn->query("SELECT * FROM domande WHERE id_quiz = $quiz_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
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
</head>
<body>
<h1>Modifica Quiz</h1>

<?php if (isset($success_message)): ?>
    <div class="success"><?php echo $success_message; ?></div>
<?php endif; ?>

<form method="POST" action="edit_quiz.php?quiz_id=<?php echo $quiz_id; ?>">
    <label>
        Nome:
        <input type="text" name="nome" value="<?php echo htmlspecialchars($quiz['nome']); ?>" required>
    </label><br>
    <label>
        Descrizione:
        <textarea name="descrizione" required><?php echo htmlspecialchars($quiz['descrizione']); ?></textarea>
    </label><br>

    <h2>Domande</h2>
    <?php while ($domanda = $domande->fetch_assoc()): ?>
        <div>
            <label>
                Domanda <?php echo $domanda['id']; ?>:
                <textarea name="domanda_<?php echo $domanda['id']; ?>" required><?php echo htmlspecialchars($domanda['testo_domanda']); ?></textarea>
            </label>
        </div>
    <?php endwhile; ?>

    <p>
        <input type="submit" value="Salva Modifiche">
        <a href="teacher.php">Torna alla pagina principale</a>
    </p>
</form>

<?php closeDbConnection($conn); ?>
</body>
</html>