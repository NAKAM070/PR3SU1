<?php
// Datenbankverbindung herstellen
$servername = "localhost";
$username = "root"; // Standard-Benutzername für MySQL
$password = ""; // Standard-Passwort für MySQL, ggf. anpassen
$dbname = "projektmanagement";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Projekt erstellen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['projekt_erstellen'])) {
    $id = uniqid();
    $name = $_POST['name'];
    $startdatum = $_POST['startdatum'];
    $enddatum = $_POST['enddatum'];
    $beschreibung = $_POST['beschreibung'];
    $verantwortlich = $_POST['verantwortlich'];

    $sql = "INSERT INTO Projekt (id, name, startdatum, enddatum, beschreibung, verantwortlich)
            VALUES ('$id', '$name', '$startdatum', '$enddatum', '$beschreibung', '$verantwortlich')";

    if ($conn->query($sql) === TRUE) {
        echo "Neues Projekt erfolgreich erstellt.";
    } else {
        echo "Fehler: " . $sql . "<br>" . $conn->error;
    }
}

// Projekte abrufen
$sql = "SELECT * FROM Projekt";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Projektmanagement Tool</title>
</head>
<body>

<h1>Projektmanagement Tool</h1>

<h2>Neues Projekt erstellen</h2>
<form method="POST">
    <label for="name">Name des Projekts:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="startdatum">Startdatum:</label>
    <input type="date" id="startdatum" name="startdatum" required><br><br>

    <label for="enddatum">Enddatum:</label>
    <input type="date" id="enddatum" name="enddatum" required><br><br>

    <label for="beschreibung">Beschreibung:</label>
    <textarea id="beschreibung" name="beschreibung" required></textarea><br><br>

    <label for="verantwortlich">Verantwortliche Person:</label>
    <input type="text" id="verantwortlich" name="verantwortlich" required><br><br>

    <button type="submit" name="projekt_erstellen">Projekt speichern</button>
</form>

<h2>Projektliste</h2>
<table border="1">
    <tr>
        <th>Name des Projekts</th>
        <th>Startdatum</th>
        <th>Enddatum</th>
        <th>Beschreibung</th>
        <th>Verantwortliche Person</th>
        <th>Aktionen</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['startdatum'] . "</td>";
            echo "<td>" . $row['enddatum'] . "</td>";
            echo "<td>" . $row['beschreibung'] . "</td>";
            echo "<td>" . $row['verantwortlich'] . "</td>";
            echo "<td><a href='edit.php?id=" . $row['id'] . "'>Bearbeiten</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Keine Projekte gefunden</td></tr>";
    }
    ?>
</table>

<p>Anzahl der Projekte: <?php echo $result->num_rows; ?></p>

</body>
</html>

<?php
$conn->close();
?>
