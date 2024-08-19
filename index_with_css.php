<?php
$host = 'localhost';
$dbname = 'projektmanagement';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Verbindung zur Datenbank fehlgeschlagen: ' . $conn->connect_error);
}

$edit_project = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['projekt_erstellen'])) {
        $name = $_POST['name'];
        $startdatum = $_POST['startdatum'];
        $enddatum = $_POST['enddatum'];
        $beschreibung = $_POST['beschreibung'];
        $verantwortlich = $_POST['verantwortlich'];

        $stmt = $conn->prepare("INSERT INTO Projekt (name, startdatum, enddatum, beschreibung, verantwortlich) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $startdatum, $enddatum, $beschreibung, $verantwortlich);

        if ($stmt->execute()) {
            $success_message = 'Projekt erfolgreich erstellt!';
        } else {
            $error_message = 'Fehler beim Erstellen des Projekts: ' . $conn->error;
        }

        $stmt->close();
    } elseif (isset($_POST['projekt_bearbeiten'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $startdatum = $_POST['startdatum'];
        $enddatum = $_POST['enddatum'];
        $beschreibung = $_POST['beschreibung'];
        $verantwortlich = $_POST['verantwortlich'];

        $stmt = $conn->prepare("UPDATE Projekt SET name = ?, startdatum = ?, enddatum = ?, beschreibung = ?, verantwortlich = ? WHERE id = ?");
        $stmt->bind_param("ssssss", $name, $startdatum, $enddatum, $beschreibung, $verantwortlich, $id);

        if ($stmt->execute()) {
            $success_message = 'Projekt erfolgreich aktualisiert!';
        } else {
            $error_message = 'Fehler beim Aktualisieren des Projekts: ' . $conn->error;
        }

        $stmt->close();
    }
}

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM Projekt WHERE id = '$edit_id'");
    $edit_project = $result->fetch_assoc();
}

$result = $conn->query("SELECT * FROM Projekt");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projektmanagement Tool</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        .form-container, .project-list-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
        }
        .form-container {
            margin-right: 15px;
        }
        .row-flex {
            display: flex;
            align-items: stretch;
        }
        .table-container {
            overflow-x: auto;
        }
        .table {
            table-layout: fixed;
            width: 100%;
        }
        .table th, .table td {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .btn-primary i, .btn-warning i {
            margin-right: 5px;
        }
        .form-control {
            padding-left: 35px; /* Platz für Icon */
        }
        .form-group-icon {
            position: relative;
        }
        .form-group-icon i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Projektmanagement Tool</h1>

    <div class="row-flex">
        <div class="form-container">
            <h2><?php echo $edit_project ? 'Projekt bearbeiten' : 'Neues Projekt erstellen'; ?></h2>
            <?php if(isset($success_message)) echo "<div class='alert alert-success'>$success_message</div>"; ?>
            <?php if(isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>

            <form method="POST">
                <?php if ($edit_project): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_project['id']; ?>">
                <?php endif; ?>

                <div class="mb-3 form-group-icon">
                    <label for="name" class="form-label">Name des Projekts:</label>
                    <i class="fas fa-project-diagram"></i>
                    <input type="text" id="name" name="name" class="form-control" required value="<?php echo $edit_project['name'] ?? ''; ?>">
                </div>

                <div class="mb-3 form-group-icon">
                    <label for="startdatum" class="form-label">Startdatum:</label>
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" id="startdatum" name="startdatum" class="form-control" required value="<?php echo $edit_project['startdatum'] ?? ''; ?>">
                </div>

                <div class="mb-3 form-group-icon">
                    <label for="enddatum" class="form-label">Enddatum:</label>
                    <i class="fas fa-calendar-check"></i>
                    <input type="date" id="enddatum" name="enddatum" class="form-control" required value="<?php echo $edit_project['enddatum'] ?? ''; ?>">
                </div>

                <div class="mb-3 form-group-icon">
                    <label for="beschreibung" class="form-label">Beschreibung:</label>
                    <i class="fas fa-info-circle"></i>
                    <textarea id="beschreibung" name="beschreibung" class="form-control" required><?php echo $edit_project['beschreibung'] ?? ''; ?></textarea>
                </div>

                <div class="mb-3 form-group-icon">
                    <label for="verantwortlich" class="form-label">Verantwortliche Person:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="verantwortlich" name="verantwortlich" class="form-control" required value="<?php echo $edit_project['verantwortlich'] ?? ''; ?>">
                </div>

                <button type="submit" class="btn btn-primary" name="<?php echo $edit_project ? 'projekt_bearbeiten' : 'projekt_erstellen'; ?>">
                    <i class="fas fa-save"></i>
                    <?php echo $edit_project ? 'Projekt speichern' : 'Projekt erstellen'; ?>
                </button>

                <?php if ($edit_project): ?>
                    <a href="index.php" class="btn btn-secondary">Abbrechen</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="project-list-container">
            <h2>Projektliste</h2>
            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name des Projekts</th>
                            <th>Startdatum</th>
                            <th>Enddatum</th>
                            <th>Beschreibung</th>
                            <th>Verantwortliche Person</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['startdatum'] . "</td>";
                                echo "<td>" . $row['enddatum'] . "</td>";
                                echo "<td>" . $row['beschreibung'] . "</td>";
                                echo "<td>" . $row['verantwortlich'] . "</td>";
                                echo "<td><a href='index.php?edit_id=" . $row['id'] . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Bearbeiten</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Keine Projekte gefunden</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS und Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js