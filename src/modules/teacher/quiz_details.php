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

// Recupera l'ID del quiz dalla query string
if (empty($_GET['quiz_id'])) {
    echo "<p>ID del quiz mancante!</p>";
    exit();
}
$quiz_id = (int)$_GET['quiz_id'];

// Verifica se il quiz esiste
$stmt = executeQuery("SELECT nome FROM quiz WHERE id = ?", "i", [$quiz_id]);
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<p>Quiz non trovato!</p>";
    $stmt->close();
    closeDbConnection($conn);
    exit();
}
$quiz_nome = $result->fetch_assoc()['nome'];
$stmt->close();

// Mostra le risposte degli studenti per il quiz specificato
$stmt = executeQuery("
    SELECT u.nome, u.cognome, d.testo_domanda, r.risposta, r.datetime
    FROM risposte r
    JOIN domande d ON r.id_domanda = d.id
    JOIN utente u ON r.id_studente = u.id
    WHERE r.id_quiz = ?
    ORDER BY r.datetime DESC", 
    "i", [$quiz_id]
);
$risposte = $stmt->get_result();

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
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="icon" type="image/ico" href="../../asset/img/logo-quiz.ico">
    <link rel="stylesheet" href="../../asset/css/style-teacher.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        #button-csv {
            display: inline-block;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #button-csv:hover {
            background-color:rgb(0, 196, 10);
        }
    </style>
</head>
<body>
<h1>Risposte degli Studenti</h1>

<h3>Quiz: <?php echo htmlspecialchars($quiz_nome); ?></h3>

<?php if (empty($students)) { ?>
    <p>Nessuna risposta disponibile per questo quiz.</p>
<?php } else { ?>
    <button class="button" onclick="toggleSort()" id="sortButton">Ordina in ordine alfabetico</button>
    <button id="button-csv" onclick="exportToCSV()">Esporta CSV</button>
    
    <div style="overflow-x: auto;">
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
    </div>
<?php } 


// Chiudi la connessione al database
closeDbConnection($conn);
?>


<div class="actions-container">
    <a class="button secondary" href="teacher.php">Torna alla pagina principale</a>

</div>

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
            // Ordina per cognome
            rows.sort((rowA, rowB) => {
                const lastNameA = rowA.cells[0].innerText.split(' ').slice(-1)[0];
                const lastNameB = rowB.cells[0].innerText.split(' ').slice(-1)[0];
                return lastNameA.localeCompare(lastNameB);
            });
            sorted = true;
        } else {
            // Ripristina l'ordine cronologico originale
            rows.sort((rowA, rowB) => {
                const dateA = new Date(rowA.dataset.datetime);
                const dateB = new Date(rowB.dataset.datetime);
                return dateB - dateA; // Ordine decrescente (più recente prima)
            });
            sorted = false;
        }

        // Aggiorna la tabella
        rows.forEach(row => tbody.appendChild(row));
        updateButtonLabel();
    }

    function updateButtonLabel() {
        const button = document.getElementById('sortButton');
        button.innerText = sorted ? 'Ripristina ordine cronologico' : 'Ordina in ordine alfabetico';
    }
    
    function exportToCSV() {
        const table = document.getElementById('studentTable');
        let csv = [];
        
        // Ottieni l'intestazione
        const headers = Array.from(table.querySelectorAll('th')).map(th => `"${th.innerText.replace(/"/g, '""')}"`);
        csv.push(headers.join(','));
        
        // Ottieni le righe dei dati
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        rows.forEach(row => {
            const rowData = Array.from(row.querySelectorAll('td')).map(td => `"${td.innerText.replace(/"/g, '""')}"`);
            csv.push(rowData.join(','));
        });
        
        // Crea il file CSV
        const csvString = csv.join('\n');
        const filename = 'quiz_<?php echo $quiz_nome; ?>_risposte.csv';
        
        // Crea un elemento di download
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        // Crea un URL per il blob
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        
        // Aggiungi il link al DOM, clicca e rimuovilo
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
</body>
</html>