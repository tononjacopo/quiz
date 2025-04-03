<?php /** @noinspection ALL */
session_start();
// Controllo se sono loggato
if (!isset($_SESSION["utente"])) {
    header("location: index.php");
    die();
}

// Se sono loggato mostro i dati degli utenti
$servername = "localhost";
$username = "root";
$password = "";
$database = "quiz";
$conn = new mysqli($servername, $username, $password,$database);

const DEFAULT_STRING_VALUE = "";
$id = DEFAULT_STRING_VALUE;
$nome = DEFAULT_STRING_VALUE;
$cognome = DEFAULT_STRING_VALUE;
$login = DEFAULT_STRING_VALUE;
$password = DEFAULT_STRING_VALUE;
$classe = DEFAULT_STRING_VALUE;

//controllo permessi
if ($_SESSION["permesso"] === 1) {
    $totalUsersQuery = $conn->query("SELECT COUNT(*) as total FROM utente");
    $totalUsers = $totalUsersQuery->fetch_assoc()['total'];

    echo "<h1>Total Users: $totalUsers</h1>";
    die(); // Stop further processing for permission level 1
}
if (($_SESSION["permesso"] === 2) && isset($_GET["delete"])) {
    $id = $conn->real_escape_string($_GET["delete"]);
    if ($id !== $_SESSION["idutente"]) {
        $sql = "DELETE FROM utente WHERE id=$id";
        $conn->query($sql);
    }
}

// Cancellazione dati
if (isset($_GET["delete"])) {
    $id = $conn->real_escape_string($_GET["delete"]);
    /** @noinspection ForgottenDebugOutputInspection */
    print_r($id);
    if ($id !== $_SESSION["idutente"]) {
        $sql = "delete from utente where id=$id";
        $conn -> query($sql);
    }
    $id = "";
}

// Modifica dei dati
if (isset($_GET["update"])) {
    $id = $_GET["update"];
    // Carico i dati dll'utente cliccato
    $sql = "select * from utente where id=".$id;
    $result = $conn->query($sql);
    // Inserisco i valori letti dal database nelle variabili
    if ($result->num_rows===1) {
        $row = $result->fetch_assoc();
        $nome = $row["nome"];
        $cognome = $row["cognome"];
        $login = $row["login"];
        $classe = $row["classe"];
        $password = $row["password"];
        $permesso = $row["permesso"];
    }
    // echo "$nome $cognome $login $password"; // debug
}

// Aggiunta o modifica utente
if (isset($_POST["save"])) {
    // Ottengo i dati passati tramite il form
    $id = $conn->real_escape_string($_POST["id"]);
    $nome = $conn->real_escape_string($_POST["nome"]);
    $cognome = $conn->real_escape_string($_POST["cognome"]);
    $login = $conn->real_escape_string($_POST["login"]);
    $classe = $conn->real_escape_string($_POST["classe"]);
    $password = $conn->real_escape_string($_POST["password"]);
    $permesso = $conn->real_escape_string($_POST["permesso"]);

    // Se una nuova password viene fornita, cripta la password
    if (!empty($password)) {
        $password = hash('sha256', $password); // Usa SHA-256 per crittografare la password
    } else if ($id !== "") {
        // Per evitare di sovrascrivere una password già esistente durante l'update, mantieni quella attuale
        $result = $conn->query("SELECT password FROM utente WHERE id = '$id'");
        if ($result->num_rows > 0) {
            $password_row = $result->fetch_assoc();
            $password = $password_row['password'];
        }
    } else {
        // Imposta una password predefinita per nuovi utenti (opzionale)
        $password = hash('sha256', 'password');
    }

    // Distinguo l'update dall'inserimento
    if ($id === "") {
        // Nuovo utente
        $sql = "INSERT INTO utente (nome, cognome, login, classe, password, permesso) 
                VALUES ('$nome', '$cognome', '$login', '$classe', '$password', '$permesso')";
    } else {
        // Aggiornamento di un utente esistente
        $sql = "UPDATE utente 
                SET nome = '$nome', cognome = '$cognome', login = '$login', 
                    classe = '$classe', password = '$password', permesso = '$permesso' 
                WHERE id = $id";
    }

    // Esegui la query
    if ($conn->query($sql) === TRUE) {
        echo "Utente salvato con successo.";
    } else {
        echo "Errore: " . $conn->error;
    }

    // Ripulisci le variabili
    $id = $nome = $cognome = $login = $classe = $password = $permesso = "";
}

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Quiz</title>
    <!-- Minified version -->
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="form-admin.css">
    <link rel="icon" type="image/jpeg" href="../logo-quiz.jpg">
</head>
<body>

<p>Ciao <?php echo $_SESSION["utente"]; echo ", ID " . $_SESSION["idutente"]; ?> </p>

<button id="logout" onclick="logout()">Logout</button>
<p style='margin: 0;'>───────────────────────────────────────────────────────</p>

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
                <label>Permesso</label>
                <small>0 = studente, 1 = professore, 2 = admin</small>
            </div>
            <div class="input-group">
                <input id="classe" type="text" name="classe" value="<?php echo $classe ?>" required>
                <label>Classe</label>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="submit" name="save" value="Invia">
    </div>

</form>
<p style='margin: 0;'>───────────────────────────────────────────────────────</p>

<h3>Gestione Quiz</h3>
<?php
if ($_SESSION["permesso"] === 2) {
    // Elimina un quiz se richiesto
    if (isset($_GET["delete_quiz"])) {
        $id = $conn->real_escape_string($_GET["delete_quiz"]);

        // Elimina risposte, domande e quiz
        $conn->query("DELETE FROM risposte WHERE id_quiz IN (SELECT id FROM domande WHERE id_quiz = $id)");
        $conn->query("DELETE FROM domande WHERE id_quiz = $id");
        $conn->query("DELETE FROM quiz WHERE id = $id");

        echo "<p>Quiz eliminato con successo!</p>";
    }
}


// Recupera tutti i quiz esistenti nel database
$result = $conn->query("SELECT id, nome FROM quiz");
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
?>

<h3>Lista utenti</h3>
<?php
if ($_SESSION["permesso"] === 2) {
    $result = $conn->query("SELECT * FROM utente");

    echo "<table border='1' id='tabella-utenti'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Nome</th>";
    echo "<th>Cognome</th>";
    echo "<th>Login</th>";
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
        echo "<td>".$row["permesso"]."</td>";
        echo "<td><button onclick=\"showConfirmPopup(this, 'admin.php?delete=".$row["id"]."')\">Elimina</button></td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<h2>You do not have permission to view this page.</h2>";
}
?>


<script>
    let currentSort = 2; // Permesso iniziale è 2
    let ascendingOrder = true; // Ordinamento crescente di default

    // Mostra il popup di conferma per eliminazione
    function showConfirmPopup(button, deleteUrl) {
        const existingPopup = document.querySelector('.confirm-popup');
        if (existingPopup) existingPopup.remove();

        // Crea e mostra un nuovo popup
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

    // Conferma eliminazione
    function confirmDelete(deleteUrl) {
        window.location.href = deleteUrl;
    }

    // Rimuove il popup
    function removePopup() {
        const popup = document.querySelector('.confirm-popup');
        if (popup) popup.remove();
    }

    // Ordina la tabella per permesso e login
    function ordinaTabella() {
        currentSort = (currentSort === 2) ? 1 : (currentSort === 1 ? 0 : 2); // Ciclo permessi
        ascendingOrder = !ascendingOrder; // Cambia la direzione dell'ordinamento

        const tabella = document.getElementById("tabella-utenti");
        const tbody = tabella.querySelector("tbody");
        const righe = Array.from(tbody.querySelectorAll("tr"));

        // Ordina righe per permesso e login
        righe.sort((a, b) => {
            const permessoA = parseInt(a.children[4].innerText); // Permesso
            const permessoB = parseInt(b.children[4].innerText);
            const loginA = a.children[3].innerText.toLowerCase(); // Login
            const loginB = b.children[3].innerText.toLowerCase();

            if (permessoA !== currentSort) return 1;
            if (permessoB !== currentSort) return -1;

            // Ordina per login in ordine alfabetico
            return loginA < loginB ? (ascendingOrder ? -1 : 1) : (loginA > loginB ? (ascendingOrder ? 1 : -1) : 0);
        });

        righe.forEach(riga => tbody.appendChild(riga)); // Riordina le righe nella tabella
    }

    function logout() {
        fetch("../logout.php")
            .then(response => window.location.href = "../index.php")
            .catch((err) => console.log("Errore:", err));
    }

    // Funzione per modificare un utente
    function modificaUtente(id) {
        // Reindirizza alla pagina di modifica, passando l'ID dell'utente
        window.location.href = `admin.php?update=${id}`;
    }

</script>
</body>
</html>