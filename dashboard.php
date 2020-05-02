<?php

include_once("init.php");

if ($_SESSION['level'] == 0 || $_SESSION['level'] > 2) {
    die();
}
$handle = $config['dbo']->prepare('SELECT * FROM cases ORDER BY year DESC, id DESC');
$handle->execute();
$cases = $handle->fetchAll(PDO::FETCH_ASSOC);

if ($_SESSION['level'] > 1) {
    if ($_POST['action'] == 'deleteCase') {
        $handle = $config['dbo']->prepare('DELETE FROM cases WHERE id = ?');
        $handle->bindValue(1, $_POST['resource_id']);
        $handle->execute();
        header("Location: ?");
        die();
    } elseif ($_POST['action'] == 'newCase') {
        $handle = $config['dbo']->prepare('INSERT INTO cases (year, description, outcome, jboardRuling, title) VALUES (?, ?, ?, ?, ?)');
        $handle->bindValue(1, $_POST['year']);
        $handle->bindValue(2, $_POST['description']);
        $handle->bindValue(3, $_POST['outcome']);
        $handle->bindValue(4, $_POST['jboardRuling']);
        $handle->bindValue(5, $_POST['title']);
        $handle->execute();
        header("Location: ?");
        die();
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title></title>
</head>

<body>
<h1 class="mt-2">J-Board Case Log</h1>
<?php

if ($_SESSION['level'] > 1) {
    ?>
    <div class="d-flex mt-3 mb-3">
        <div class="btn-group flex-fill" role="group" aria-label="Basic example">
            <a href="?action=newCase" class="btn btn-warning">Create New Case</a>
            <a href="?action=deleteCase" class="btn btn-warning">Delete Case</a>
        </div>
    </div>
    <?php

    if ($_GET['action'] == 'newCase') {
        ?>
        <div class="pl-4 pr-4 mb-4">
            <form method="post">
                <div class="form-group">
                    <label for="newCaseTitle">Title</label>
                    <input name="title" type="text" class="form-control" id="newCaseTitle" aria-describedby="aria" placeholder="Punching A Hole In The Wall" required>
                </div>
                <div class="form-group">
                    <label for="newCaseYear">Year</label>
                    <input name="year" type="number" min="2019" step="1" class="form-control" id="newCaseYear" aria-describedby="aria" placeholder="2010" required value="<?php echo date('Y'); ?>">
                </div>
                <div class="form-group">
                    <label for="newCaseDescription">Description</label>
                    <textarea name="description" class="form-control" id="newCaseDescription" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="newCaseOutcome">Outcome</label>
                    <textarea name="outcome" class="form-control" id="newCaseOutcome" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label for="newCaseDecided">Appealed to J-Board</label>
                    <select name="jboardRuling" required id="newCaseDecided">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <input type="hidden" name="action" value="newCase">
                <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
        <?php
    } elseif ($_GET['action'] == 'deleteCase') {
        ?>
        <div class="pl-4 pr-4 mb-4">
            <form method="post">
                <div class="form-group">
                    <label for="deleteResourceResource">Case</label>
                    <select name="resource_id" required id="deleteResourceResource">
                        <?php
                        foreach ($cases as $case) {
                            echo "<option value='" . $case['id'] . "'>" . $case['year'] . ': ' . $case['title'] . "</option>";
                        } ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="deleteCase">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
        <?php
    }
}

echo "</br>";

if (count($cases) == 0) {
    echo "<h3>No Cases Found</h3>";
} else {
    ?>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th style="width: 5%;">Year</th>
                <th style="width: 15%;">Title</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 35%;">Outcome</th>
                <th style="width: 10%;">Appealed to J-Board</th>
            </tr>
        </thead>
    <?php
    foreach ($cases as $case) {
        echo "<tr>";
        echo "<td>" . $case['year'] . "</td>";
        echo "<td>" . $case['title'] . "</td>";
        echo "<td>" . nl2br($case['description']) . "</td>";
        echo "<td>" . nl2br($case['outcome']) . "</td>";
        echo "<td>" . ($case['jboardRuling'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script></body>
</html>
