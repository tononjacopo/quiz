<?php
session_start();

// Definisci la costante di sicurezza per l'accesso al database
define('SECURE_ACCESS', true);

// Check if user is logged in
if (!isset($_SESSION["utente"])) {
    header("location: ../../index.php");
    exit();
}

// Include il file di configurazione del database
require_once '../../config/database.php';

const DEFAULT_STRING_VALUE = "";
$id = DEFAULT_STRING_VALUE;
$nome = DEFAULT_STRING_VALUE;
$cognome = DEFAULT_STRING_VALUE;
$login = DEFAULT_STRING_VALUE;
$password = DEFAULT_STRING_VALUE;
$classe = DEFAULT_STRING_VALUE;
$permesso = "";

// Delete user (con protezione per admin)
if ($_SESSION["permesso"] === 2 && isset($_GET["delete"])) {
    $id = $_GET["delete"];
    
    // Verifica se l'utente da eliminare è un admin
    $stmt = executeQuery("SELECT permesso FROM utente WHERE id = ?", "i", [$id]);
    $result = $stmt->get_result();
    $user_to_delete = $result->fetch_assoc();
    $stmt->close();
    
    // Verifica che non sia l'admin stesso e che l'utente da eliminare non sia admin (permesso != 2)
    if ($id !== $_SESSION["idutente"] && (!$user_to_delete || $user_to_delete['permesso'] != 2)) {
        executeQuery("DELETE FROM utente WHERE id = ?", "i", [$id]);
        $success_delete_message = "Utente #$id eliminato con successo.";
    } else {
        $error_message = "Non puoi eliminare un amministratore o il tuo account!";
    }
    header("Location: admin.php");
    exit();
}

// Update user data
if (isset($_GET["update"])) {
    $id = $_GET["update"];
    $stmt = executeQuery("SELECT * FROM utente WHERE id = ?", "i", [$id]);
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $nome = $row["nome"];
        $cognome = $row["cognome"];
        $login = $row["login"];
        $classe = $row["classe"];
        $password = $row["password"];
        $permesso = $row["permesso"];
    }
    $stmt->close();
}

// Add or update user
if (isset($_POST["save"])) {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $login = $_POST["login"];
    $classe = $_POST["classe"];
    $password = $_POST["password"];
    $permesso = $_POST["permesso"];

    if (!empty($password)) {
        $password = generate_password_hash($password);
    } else if ($id !== "") {
        $stmt = executeQuery("SELECT password FROM utente WHERE id = ?", "i", [$id]);
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $password_row = $result->fetch_assoc();
            $password = $password_row['password'];
        }
        $stmt->close();
    } else {
        $password = generate_password_hash('password');
    }

    if ($id === "") {
        executeQuery(
            "INSERT INTO utente (nome, cognome, login, classe, password, permesso) VALUES (?, ?, ?, ?, ?, ?)", 
            "sssssi", 
            [$nome, $cognome, $login, $classe, $password, $permesso]
        );
        $new_user_id = $conn->insert_id;
        $success_message = "Utente #$new_user_id creato con successo.";
    } else {
        executeQuery(
            "UPDATE utente SET nome = ?, cognome = ?, login = ?, classe = ?, password = ?, permesso = ? WHERE id = ?", 
            "sssssii", 
            [$nome, $cognome, $login, $classe, $password, $permesso, $id]
        );
        $success_message = "Utente #$id modificato con successo.";
    }

    header("Location: admin.php");
    exit();
}

// Gestione eliminazione quiz
if ($_SESSION["permesso"] === 2 && isset($_GET["delete_quiz"])) {
    $quiz_id = $_GET["delete_quiz"];
    
    // Recupera il nome del quiz prima di eliminarlo
    $stmt = executeQuery("SELECT nome FROM quiz WHERE id = ?", "i", [$quiz_id]);
    $quiz_nome = $stmt->get_result()->fetch_assoc()['nome'];
    $stmt->close();
    
    executeQuery("DELETE FROM risposte WHERE id_quiz IN (SELECT id FROM domande WHERE id_quiz = ?)", "i", [$quiz_id]);
    executeQuery("DELETE FROM domande WHERE id_quiz = ?", "i", [$quiz_id]);
    executeQuery("DELETE FROM quiz WHERE id = ?", "i", [$quiz_id]);
    
    $success_quiz_message = "Quiz \"$quiz_nome\" eliminato con successo!";

    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Quiz</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../asset/css/admin-style.css">
    <link rel="icon" type="image/ico" href="../../asset/img/logo-quiz.ico">
    
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

<p>Ciao <?php echo $_SESSION["utente"]; echo ", ID " . $_SESSION["idutente"]; ?> </p>
<button id="logout" onclick="logout()">Logout</button>


<p style='margin: 0;'>───────────────────────────────────────────────────────</p>

<?php if (isset($success_message)): ?>
    <div class="success"><?php echo $success_message;?></div>
<?php endif; ?>

<h3>Nuovo Utente/Modifica</h3>
<form method="post" action="admin.php">
    <div class="form-group">
        <div class="group-by-2">
            <div class="input-group">
                <input type="text" name="login" value="<?php echo $login ?>" required>
                <label>Login</label>
            </div>
            <div class="input-group">
                <input type="text" name="password" value="" placeholder="password">
                <label>Password</label>
            </div>
        </div>
        <div class="group-by-2">
            <div class="input-group">
                <input type="text" name="nome" value="<?php echo $nome ?>" required>
                <label>Nome</label>
            </div>
            <div class="input-group">
                <input type="text" name="cognome" value="<?php echo $cognome ?>" required>
                <label>Cognome</label>
            </div>
        </div>
        <div class="group-by-2">
            <div class="input-group" id="permit">
                <input type="number" name="permesso" value="<?php echo $permesso ?>" max="2" min="0">
                <label>Permesso</label><br>
                <small>0 = studente, 1 = professore, 2 = admin</small>
            </div>
            <div class="input-group">
                <input type="text" id="classe" name="classe" value="<?php echo $classe ?>" >
                <label for="classe">Classe</label>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="submit" name="save" value="Salva">
    </div>
</form>
<p style='margin: 0;'>───────────────────────────────────────────────────────</p>
<?php if (isset($success_quiz_message)): ?>
    <div class="success"><?php echo htmlspecialchars($success_quiz_message); ?></div>
<?php endif; ?>
<h3>Gestione Quiz</h3>
<?php
$stmt = executeQuery("SELECT id, nome FROM quiz", "", []);
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Nome del Quiz</th><th>Azione</th></tr>";
    while ($quiz = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($quiz["nome"]) . "</td>";
        echo "<td><a href='admin.php?delete_quiz=" . $quiz["id"] . "' onclick='return confirm(\"Sei sicuro di voler eliminare questo quiz?\")'>Elimina</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Non ci sono quiz disponibili.</p>";
}
$stmt->close();
?>

<p style='margin: 0;'>───────────────────────────────────────────────────────</p>

<?php if (isset($success_delete_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($success_delete_message); ?></div>
    <?php endif; ?>
<?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
<h3>Lista utenti</h3>
<?php
if ($_SESSION["permesso"] === 2) {
    $stmt = executeQuery("SELECT * FROM utente", "", []);
    $result = $stmt->get_result();

    echo "<table border='1' id='tabella-utenti'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Nome</th>";
    echo "<th>Cognome</th>";
    echo "<th>Login</th>";
    echo "<th>Classe<button onclick='ordinaClasse()'>▲▼</button></th>";
    echo "<th>Permesso <button onclick='ordinaTabella()'>▲▼</button></th>";
    echo "<th>Actions</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><a href='#' onclick='modificaUtente({$row["id"]})'>".$row["id"]."</a></td>";
        echo "<td>".$row["nome"]."</td>";
        echo "<td>".$row["cognome"]."</td>";
        echo "<td>".$row["login"]."</td>";
        echo "<td>".$row["classe"]."</td>";
        echo "<td>".$row["permesso"]."</td>";
        
        // Non mostra il pulsante elimina per gli amministratori
        if ($row["permesso"] != 2) {
            echo "<td><button onclick=\"showConfirmPopup(this, 'admin.php?delete=".$row["id"]."')\">Elimina</button></td>";
        } else {
            echo "<td><button disabled title='Non puoi eliminare un amministratore'>Elimina</button></td>";
        }
        
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    
    $stmt->close();
} else {
    echo "<h2>Non hai i permessi per visualizzare questa pagina.</h2>";
}

// Chiudi la connessione al database alla fine del file
closeDbConnection($conn);
?>

<script>
    // [Tutti gli script JavaScript rimangono invariati]
    let currentSort = 2;
    let ascendingOrder = true;

    function showConfirmPopup(button, deleteUrl) {
        const existingPopup = document.querySelector('.confirm-popup');
        if (existingPopup) existingPopup.remove();

        const popup = document.createElement('div');
        popup.className = 'confirm-popup';
        popup.innerHTML = `
            <p>Sei sicuro?</p>
            <button onclick="confirmDelete('${deleteUrl}')">Sì</button>
            <button onclick="removePopup()">No</button>
        `;

        const rect = button.getBoundingClientRect();
        popup.style.position = 'absolute';
        popup.style.left = `${rect.left}px`;
        popup.style.top = `${rect.top + window.scrollY + button.offsetHeight}px`;

        document.body.appendChild(popup);
    }

    function confirmDelete(deleteUrl) {
        window.location.href = deleteUrl;
    }

    function removePopup() {
        const popup = document.querySelector('.confirm-popup');
        if (popup) popup.remove();
    }

    function ordinaTabella() {
        currentSort = (currentSort === 2) ? 1 : (currentSort === 1 ? 0 : 2);
        ascendingOrder = !ascendingOrder;

        const tabella = document.getElementById("tabella-utenti");
        const tbody = tabella.querySelector("tbody");
        const righe = Array.from(tbody.querySelectorAll("tr"));

        righe.sort((a, b) => {
            const permessoA = parseInt(a.children[5].innerText);
            const permessoB = parseInt(b.children[5].innerText);
            const loginA = a.children[3].innerText.toLowerCase();
            const loginB = b.children[3].innerText.toLowerCase();

            if (permessoA !== currentSort) return 1;
            if (permessoB !== currentSort) return -1;

            return loginA < loginB ? (ascendingOrder ? -1 : 1) : (loginA > loginB ? (ascendingOrder ? 1 : -1) : 0);
        });

        righe.forEach(riga => tbody.appendChild(riga));
    }

    let lastSortedClass = "";

    function ordinaClasse() {
        const tabella = document.getElementById("tabella-utenti");
        const tbody = tabella.querySelector("tbody");
        const righe = Array.from(tbody.querySelectorAll("tr"));

        // Extract all unique classes
        const classes = [...new Set(righe.map(riga => riga.children[4].innerText.toLowerCase()))].sort();

        // Find the next class to sort by
        let nextClassIndex = classes.indexOf(lastSortedClass) + 1;
        if (nextClassIndex >= classes.length) {
            nextClassIndex = 0;
        }
        const nextClass = classes[nextClassIndex];
        lastSortedClass = nextClass;

        // Sort rows by the next class
        righe.sort((a, b) => {
            const classeA = a.children[4].innerText.toLowerCase();
            const classeB = b.children[4].innerText.toLowerCase();

            if (classeA === nextClass && classeB !== nextClass) return -1;
            if (classeA !== nextClass && classeB === nextClass) return 1;
            return classeA < classeB ? -1 : (classeA > classeB ? 1 : 0);
        });

        righe.forEach(riga => tbody.appendChild(riga)); // Reorder rows in the table
    }

    function logout() {
        fetch("../../auth/logout.php")
            .then(response => window.location.href = "../../../index.php")
            .catch(err => console.log("Errore:", err));
    }

    function modificaUtente(id) {
        window.location.href = `admin.php?update=${id}`;
    }
</script>
</body>
</html>