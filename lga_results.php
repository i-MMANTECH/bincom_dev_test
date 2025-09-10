<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGA Total Results</title>
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
    <h2>LGA Total Results</h2>
    <form action="" method="GET">
        <label for="lga_id">Select LGA:</label>
        <select name="lga_id" id="lga_id">
            <option value="">-- Select an LGA --</option>
            <?php
            $servername = "sql306.infinityfree.com";
            $username = "if0_39910422";
            $password = "immantechwin77";
            $dbname = "if0_39910422_bincom_test";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT lga_id, lga_name FROM lga ORDER BY lga_name";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['lga_id'] . "'>" . htmlspecialchars($row['lga_name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Get Results</button>
    </form>

    <?php
    if (isset($_GET['lga_id']) && !empty($_GET['lga_id'])) {
        $lga_id = $conn->real_escape_string($_GET['lga_id']);
        
        $sql = "
            SELECT
                t2.party_abbreviation,
                SUM(t2.party_score) AS total_score
            FROM
                polling_unit AS t1
            JOIN
                announced_pu_results AS t2 ON t1.uniqueid = t2.polling_unit_uniqueid
            WHERE
                t1.lga_id = '$lga_id'
            GROUP BY
                t2.party_abbreviation
            ORDER BY
                total_score DESC;
        ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $lga_name_sql = "SELECT lga_name FROM lga WHERE lga_id = '$lga_id'";
            $lga_name_result = $conn->query($lga_name_sql);
            $lga_name = $lga_name_result->fetch_assoc()['lga_name'];
            
            echo "<h3>Total Results for " . htmlspecialchars($lga_name) . "</h3>";
            echo "<table>";
            echo "<tr><th>Party</th><th>Total Score</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['party_abbreviation']) . "</td><td>" . htmlspecialchars($row['total_score']) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No results found for this LGA.</p>";
        }
    }
    $conn->close();
    ?>
</div>

</body>
</html>