<?php
// Connect to your MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "speed";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitizeInput($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Edit reservation
if (isset($_GET['edit'])) {
    $editId = sanitizeInput($_GET['edit']);
    
    // Retrieve reservation data for editing
    $editSql = "SELECT * FROM reservations WHERE id = $editId";
    $editResult = $conn->query($editSql);

    if ($editResult->num_rows == 1) {
        $editData = $editResult->fetch_assoc();
    }
}

// Update reservation
if (isset($_POST['updateReservation'])) {
    $updateId = sanitizeInput($_POST['updateId']);
    $updatedDate = sanitizeInput($_POST['updatedDate']);
    $updatedStartTime = sanitizeInput($_POST['updatedStartTime']);
    $updatedEndTime = sanitizeInput($_POST['updatedEndTime']);
    $updatedSlot = sanitizeInput($_POST['updatedSlot']);

    // Update reservation in the database
    $updateSql = "UPDATE reservations SET date='$updatedDate', start_time='$updatedStartTime', end_time='$updatedEndTime', slot='$updatedSlot' WHERE id=$updateId";

    if ($conn->query($updateSql) === TRUE) {
        echo "Reservation updated successfully!";
    } else {
        echo "Error updating reservation: " . $conn->error;
    }
}

// Delete reservation
if (isset($_GET['delete'])) {
    $deleteId = sanitizeInput($_GET['delete']);

    // Delete reservation from the database
    $deleteSql = "DELETE FROM reservations WHERE id=$deleteId";

    if ($conn->query($deleteSql) === TRUE) {
        echo "Reservation deleted successfully!";
    } else {
        echo "Error deleting reservation: " . $conn->error;
    }
}

// Retrieve all reservations from the database
$sql = "SELECT * FROM reservations";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Display</title>
    <link href="style.css" rel="stylesheet" />
    <style>
       
        body {
            font-family: Arial, sans-serif; /* Use your existing font style */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="center">
        <h2>Reservation Information</h2>

        <?php
        // Display reservation information in a table
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>Date</th>";
            echo "<th>Start Time</th>";
            echo "<th>End Time</th>";
            echo "<th>Slot</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['start_time'] . "</td>";
                echo "<td>" . $row['end_time'] . "</td>";
                echo "<td>" . $row['slot'] . "</td>";
                echo "<td><a href='display.php?edit=" . $row['id'] . "'>Edit</a> | <a href='display.php?delete=" . $row['id'] . "'>Delete</a></td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "No reservations found.";
        }
        ?>
        
        <?php
        // Display form for editing
        if (isset($editData)) {
            ?>
            <h2>Edit Reservation</h2>
            <form action="display.php" method="post">
                <input type="hidden" name="updateId" value="<?php echo $editData['id']; ?>">
                <label>Date:</label>
                <input type="date" name="updatedDate" value="<?php echo $editData['date']; ?>" required>

                <label for="updatedStartTime">Start Time:</label>
                <input type="time" id="updatedStartTime" name="updatedStartTime" value="<?php echo $editData['start_time']; ?>" required>

                <label for="updatedEndTime">End Time:</label>
                <input type="time" id="updatedEndTime" name="updatedEndTime" value="<?php echo $editData['end_time']; ?>" required>

                <label for="updatedSlot">Slot:</label>
                <input type="text" id="updatedSlot" name="updatedSlot" value="<?php echo $editData['slot']; ?>" required>

                <button type="submit" name="updateReservation">Update</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>

</html>