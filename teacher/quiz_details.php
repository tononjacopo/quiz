<?php
/**
 * @return mysqli|void
 */
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

// Elimina quiz se viene passato il parametro dalla query string
if (isset($_GET['delete_quiz'])) {
    $delete_quiz_id = (int)$_GET['delete_quiz'];
    $delete_query = "DELETE FROM quiz WHERE id = $delete_quiz_id";
    if ($conn->query($delete_query)) {
        header("Location: teacher.php?message=Quiz eliminato con successo");
        exit();
    }

    echo "<p>Errore nell'eliminazione del quiz.</p>";
    $conn->close();
}

// Recupera l'ID del quiz dalla query string
if (empty($_GET['quiz_id'])) {
    echo "<p>ID del quiz mancante!</p>";
    exit();
}
$quiz_id = (int)$_GET['quiz_id'];

// Mostra le risposte degli studenti per il quiz specificato
$query = "
       SELECT u.nome, u.cognome, d.testo_domanda, r.risposta, r.datetime
       FROM risposte r
       JOIN domande d ON r.id_quiz = d.id_quiz
       JOIN utente u ON r.id_studente = u.id
       WHERE r.id_quiz = $quiz_id
       ORDER BY r.datetime DESC";

$risposte = $conn->query($query);
$students = [];
$domande = [];

while ($row = $risposte->fetch_assoc()) {
    $students[$row['nome'] . " " . $row['cognome']][] = [
        'domanda' => $row['testo_domanda'],
        'risposta' => $row['risposta'],
        'datetime' => $row['datetime']
    ];
    if (!in_array($row['testo_domanda'], $domande, true)) {
        $domande[] = $row['testo_domanda'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="../logo-quiz.jpg">
</head>
<body>
<h1>Risposte degli Studenti</h1>

<?php
$quiz_query = $conn->query("SELECT nome FROM quiz WHERE id = $quiz_id");
if ($quiz_query->num_rows === 0) {
    echo "<p>Quiz non trovato!</p>";
    exit();
}
$quiz_nome = $quiz_query->fetch_assoc()['nome'];

?>
<h3>Quiz: <?php echo htmlspecialchars($quiz_nome); ?></h3>

<?php
if (empty($students)) { ?>
    <p>Nessuna risposta disponibile per questo quiz.</p>
<?php } else { ?>
    <button onclick="toggleSort()">Ordina in ordine alfabetico</button>
    <table border="1" id="studentTable">
        <thead>
        <tr>
            <th>Studente</th>
            <?php foreach ($domande as $domanda) { ?>
                <th><?php echo htmlspecialchars($domanda); ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $studente => $risposte) { ?>
            <tr data-datetime="<?php echo htmlspecialchars($risposte[0]['datetime']); ?>">
                <td><?php echo htmlspecialchars($studente); ?></td>
                <?php foreach ($domande as $domanda) {
                    $found = false;
                    foreach ($risposte as $risposta_data) {
                        if ($risposta_data['domanda'] === $domanda) {
                            echo "<td>" . htmlspecialchars($risposta_data['risposta']) . "</td>";
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        echo "<td>-</td>"; // Nessuna risposta
                    }
                } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>

<a href="teacher.php">Torna alla pagina principale</a>
</body>
<script>
    let sorted = false;
    const originalOrder = [];

    document.addEventListener('DOMContentLoaded', () => {
        const rows = Array.from(document.querySelectorAll('#studentTable tbody tr'));
        originalOrder.push(...rows.map(row => row.dataset.datetime));
    });

    function toggleSort() {
        const table = document.getElementById('studentTable');
        const rows = Array.from(table.rows).slice(1); // Exclude header row
        const tbody = table.tBodies[0];

        if (!sorted) {
            rows.sort((rowA, rowB) => {
                const lastNameA = rowA.cells[0].innerText.split(' ').slice(-1)[0];
                const lastNameB = rowB.cells[0].innerText.split(' ').slice(-1)[0];
                return lastNameA.localeCompare(lastNameB);
            });
            sorted = true;
        } else {
            rows.sort((rowA, rowB) => {
                const dateA = new Date(rowA.dataset.datetime);
                const dateB = new Date(rowB.dataset.datetime);
                return dateB - dateA;
            });
            sorted = false;
        }

        rows.forEach(row => tbody.appendChild(row));
        updateButtonLabel();
    }

    function updateButtonLabel() {
        const button = document.querySelector('button');
        button.innerText = sorted ? 'Ripristina ordine originale' : 'Ordina in ordine alfabetico';
    }
</script>
</html>