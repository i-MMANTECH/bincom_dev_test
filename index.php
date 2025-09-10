<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling Unit Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #49083bff; color: #fff;}
        .container { max-width: 600px; margin: auto; }
        select, button { padding: 10px; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #c4c1c1ff; padding: 10px; text-align: left; }
    </style>
</head>
<body>

<div class="container">
    <h2>View Polling Unit Result</h2>
    <form action="" method="GET">
        <label for="polling_unit_id">Select Polling Unit:</label>
        <select name="polling_unit_id" id="polling_unit_id">
            <option value="">-- Select a Polling Unit --</option>
            <?php
            $servername = "sql306.infinityfree.com";
            $username = "if0_39910422";
            $password = "immantechwin77";
            $dbname = "if0_39910422_bincom_test";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT uniqueid, polling_unit_name FROM polling_unit";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['uniqueid'] . "'>" . htmlspecialchars($row['polling_unit_name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Get Results</button><br>
        <a href="lga_results.php" style="color: #ff00c8ff; font-size: 17px; font-style: italic;">Check For Local Government Results</a><br>
        <a href="add_polling_unit_results.php" style="color: #ff00c8ff; font-size: 17px; font-style: italic;">Add Polling Unit Results</a>
    </form>

    <?php
    if (isset($_GET['polling_unit_id']) && !empty($_GET['polling_unit_id'])) {
        $pu_id = $conn->real_escape_string($_GET['polling_unit_id']);

        $sql = "SELECT party_abbreviation, party_score FROM announced_pu_results WHERE polling_unit_uniqueid = '$pu_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<h3>Results for Polling Unit ID: " . htmlspecialchars($pu_id) . "</h3>";
            echo "<table>";
            echo "<tr><th>Party</th><th>Score</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['party_abbreviation']) . "</td><td>" . htmlspecialchars($row['party_score']) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No results found for this polling unit.</p>";
        }
    }
    $conn->close();
    ?>
</div>

</body>
</html>