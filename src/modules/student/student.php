<?php
session_start();

define('SECURE_ACCESS', true);

if (!isset($_SESSION["utente"])) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../config/database.php';

$student_id = $_SESSION["idutente"];
$stmt = executeQuery("SELECT classe FROM utente WHERE id = ?", "i", [$student_id]);
$student_class = $stmt->get_result()->fetch_assoc()['classe'];
$stmt->close();

// Gestione invio risposte
if (isset($_POST['submit']) && isset($_GET['quiz_id'])) {
    $quiz_id = (int)$_GET['quiz_id'];

    // Verifica se già completato
    $stmt = executeQuery("SELECT COUNT(*) AS completed FROM risposte WHERE id_quiz = ? AND id_studente = ?",
                        "ii", [$quiz_id, $student_id]);
    $completed = $stmt->get_result()->fetch_assoc()['completed'];
    $stmt->close();

    if ($completed == 0) {
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, 'risposta_')) {
                $domanda_id = (int)str_replace('risposta_', '', $key);
                $risposta = $value;

                // MODIFICA LA QUERY PER INCLUDERE id_domanda
                executeQuery(
                    "INSERT INTO risposte (id_quiz, id_domanda, id_studente, risposta, datetime) VALUES (?, ?, ?, ?, NOW())",
                    "iiis",
                    [$quiz_id, $domanda_id, $student_id, $risposta]
                );
            }
        }
        $_SESSION['message'] = "Risposte inviate con successo!";
        header("Location: student.php");
        exit();
    }
}

// Verifica accesso a quiz
if (isset($_GET['quiz_id'])) {
    $quiz_id = (int)$_GET['quiz_id'];

    // Verifica se già completato
    $stmt = executeQuery("SELECT COUNT(*) AS completed FROM risposte WHERE id_quiz = ? AND id_studente = ?",
                        "ii", [$quiz_id, $student_id]);
    $completed = $stmt->get_result()->fetch_assoc()['completed'];
    $stmt->close();

    if ($completed > 0) {
        header("Location: student.php");
        exit();
    }

    // Verifica che il quiz esista e sia per la classe dello studente
    $stmt = executeQuery("SELECT nome FROM quiz WHERE id = ? AND (classe = ? OR classe IS NULL)",
                        "is", [$quiz_id, $student_class]);
    $quiz = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$quiz) {
        header("Location: student.php");
        exit();
    }

    $_SESSION['quiz_nome'] = $quiz['nome'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studente | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/ico" href="../../asset/img/logo-quiz.ico">
    <style>
        .alert { color: green; background-color: #e8f5e9; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .completed-quiz { color: green; }
        textarea { width: 100%; min-height: 100px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <p>Ciao <?php echo htmlspecialchars($_SESSION["utente"]); ?>!</p>
    <button id="logout" onclick="logout()">Logout</button>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!isset($_GET['quiz_id'])): ?>
        <p style='margin: 0;'>───────────────────────────────────────────────────────</p>
        <h1>Quiz Disponibili</h1>
        <?php
        $stmt = executeQuery("
            SELECT q.id, q.nome, q.descrizione,
                (SELECT COUNT(*) FROM risposte WHERE id_quiz = q.id AND id_studente = ?) AS completed
            FROM quiz q
            WHERE q.classe = ? OR q.classe IS NULL
            ORDER BY q.id DESC
        ", "is", [$student_id, $student_class]);

        echo "<blockquote>";
        $quizzes = $stmt->get_result();

        if ($quizzes->num_rows > 0) {
            echo "<ul style='list-style: none; padding: 0;'>";
            while ($quiz = $quizzes->fetch_assoc()) {
                if ($quiz['completed'] > 0) {
                    echo "<li><span style='color: green'>✔</span> " . htmlspecialchars($quiz['nome']) . "</li>";
                } else {
                    echo "<li><a href='student.php?quiz_id={$quiz['id']}'>" . htmlspecialchars($quiz['nome']) . "</a>";
                    echo "<p>" . htmlspecialchars($quiz['descrizione']) . "</p></li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p>Nessun quiz disponibile per la tua classe.</p>";
        }
        $stmt->close();
    else: ?>
        <h1>Quiz: <?= htmlspecialchars($_SESSION['quiz_nome']) ?></h1>
        <?php
        $stmt = executeQuery("SELECT id, testo_domanda FROM domande WHERE id_quiz = ?", "i", [(int)$_GET['quiz_id']]);
        $domande = $stmt->get_result();

        if ($domande->num_rows > 0): ?>
            <form method="POST">
                <?php while ($domanda = $domande->fetch_assoc()): ?>
                    <h3><?= htmlspecialchars($domanda['testo_domanda']) ?></h3>
                    <textarea name="risposta_<?= $domanda['id'] ?>" required></textarea>
                <?php endwhile; ?>
                <input type="submit" name="submit" value="Invia Risposte">
            </form>
        <?php else: ?>
            <p>Questo quiz non ha domande.</p>
        <?php endif;
        $stmt->close(); ?>
        <p><a href="student.php">Torna indietro</a></p>
    <?php endif; ?>

    <script>
        function logout() {
            fetch("../../auth/logout.php")
                .then(() => window.location.href = "../../../index.php")
                .catch(err => console.log("Errore:", err));
        }
    </script>
</body>
</html>