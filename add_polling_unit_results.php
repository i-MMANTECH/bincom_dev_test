<?php
$servername = "sql306.infinityfree.com";
$username = "if0_39910422";
$password = "immantechwin77";
$dbname = "if0_39910422_bincom_test";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pu_id = $conn->real_escape_string($_POST['polling_unit_uniqueid']);
    $pu_name = $conn->real_escape_string($_POST['polling_unit_name']);
    $lga_id = $conn->real_escape_string($_POST['lga']);
    $ward_id = $conn->real_escape_string($_POST['ward']);

    // Check if polling unit already exists to prevent duplicate entries
    $check_sql = "SELECT uniqueid FROM polling_unit WHERE uniqueid = '$pu_id'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        $message = "Error: A polling unit with this ID already exists.";
    } else {
        // Insert into polling_unit table
        $sql_pu = "INSERT INTO polling_unit (uniqueid, polling_unit_name, lga_id, ward_id, date_entered) VALUES ('$pu_id', '$pu_name', '$lga_id', '$ward_id', NOW())";
        
        if ($conn->query($sql_pu) === TRUE) {
            $parties_sql = "SELECT partyid, partyname FROM party";
            $parties_result = $conn->query($parties_sql);
            $all_parties_inserted = true;

            if ($parties_result->num_rows > 0) {
                while($party_row = $parties_result->fetch_assoc()) {
                    $party_abbr = $party_row['partyname'];
                    if (isset($_POST[$party_abbr])) {
                        $score = $conn->real_escape_string($_POST[$party_abbr]);
                        $entered_by = "User";
                        $user_ip = $_SERVER['REMOTE_ADDR'];
                        
                        $sql_results = "INSERT INTO announced_pu_results (polling_unit_uniqueid, party_abbreviation, party_score, entered_by_user, date_entered, user_ip_address) VALUES ('$pu_id', '$party_abbr', '$score', '$entered_by', NOW(), '$user_ip')";
                        
                        if (!$conn->query($sql_results)) {
                            $message = "Error inserting results for party " . $party_abbr . ": " . $conn->error;
                            $all_parties_inserted = false;
                            break;
                        }
                    }
                }
            }
            if ($all_parties_inserted) {
                $message = "New polling unit and results added successfully!";
            }
        } else {
            $message = "Error inserting polling unit: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Polling Unit Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #49083bff; color: #fff;}
        .container { max-width: 600px; margin: auto; }
        form div { margin-bottom: 15px; }
        label { display: block; font-weight: bold; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #ff00c8ff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Polling Unit Result</h2>
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div>
            <label for="polling_unit_uniqueid">New Polling Unit ID:</label>
            <input type="text" id="polling_unit_uniqueid" name="polling_unit_uniqueid" required>
        </div>
        <div>
            <label for="polling_unit_name">Polling Unit Name:</label>
            <input type="text" id="polling_unit_name" name="polling_unit_name" required>
        </div>
        <div>
            <label for="lga">LGA ID:</label>
            <input type="text" id="lga" name="lga" required>
        </div>
        <div>
            <label for="ward">Ward ID:</label>
            <input type="text" id="ward" name="ward" required>
        </div>
        <hr>
        <h3>Party Scores:</h3>
        <?php
        $servername = "sql306.infinityfree.com";
        $username = "if0_39910422";
        $password = "immantechwin77";
        $dbname = "if0_39910422_bincom_test";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $parties_sql = "SELECT partyname FROM party";
        $parties_result = $conn->query($parties_sql);

        if ($parties_result->num_rows > 0) {
            while($row = $parties_result->fetch_assoc()) {
                $party_abbr = $row['partyname'];
                echo "<div>";
                echo "<label for='" . htmlspecialchars($party_abbr) . "'>" . htmlspecialchars($party_abbr) . " Score:</label>";
                echo "<input type='number' id='" . htmlspecialchars($party_abbr) . "' name='" . htmlspecialchars($party_abbr) . "' required>";
                echo "</div>";
            }
        }
        $conn->close();
        ?>
        <button type="submit">Submit Results</button>
    </form>
</div>

</body>
</html>