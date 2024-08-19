<?php
// Datenbankverbindung herstellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projektmanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Projektinformationen abrufen
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM Projekt WHERE id='$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $projekt = $result->fetch_assoc();
    } else {
        echo "Projekt nicht gefunden.";
        exit;
    }
}

// Projekt bearbeiten
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['projekt_bearbeiten'])) {
    $name = $_POST['name'];
    $startdatum = $_POST['startdatum'];
    $enddatum = $_POST['enddatum'];
    $beschreibung = $_POST['beschreibung'];
    $verantwortlich = $_POST['verantwortlich'];

    $sql = "UPDATE Projekt SET name='$name', startdatum='$startdatum', enddatum='$enddatum', beschreibung='$beschreibung', verantwortlich='$verantwortlich' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Projekt erfolgreich aktualisiert.";
        header("Location: index.php");
        exit;
    } else {
        echo "Fehler: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Projekt bearbeiten</title>
</head>
<body>

<h1>Projekt bearbeiten</h1>

<form method="POST">
    <label for="name">Name des Projekts:</label>
    <input type="text" id="name" name="name" value="<?php echo $projekt['name']; ?>" required><br><br>

    <label for="startdatum">Startdatum:</label>
    <input type="date" id="startdatum" name="startdatum" value="<?php echo $projekt['startdatum']; ?>" required><br><br>

    <label for="enddatum">Enddatum:</label>
    <input type="date" id="enddatum" name="enddatum" value="<?php echo $projekt['enddatum']; ?>" required><br><br>

    <label for="beschreibung">Beschreibung:</label>
    <textarea id="beschreibung" name="beschreibung" required><?php echo $projekt['beschreibung']; ?></textarea><br><br>

    <label for="verantwortlich">Verantwortliche Person:</label>
    <input type="text" id="verantwortlich" name="verantwortlich" value="<?php echo $projekt['verantwortlich']; ?>" required><br><br>

    <button type="submit" name="projekt_bearbeiten">Projekt speichern</button>
</form>

</body>
</html>

<?php
$conn->close();
?>
