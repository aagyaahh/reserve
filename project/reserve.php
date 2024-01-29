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

// Handle reservation form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $slot = $_POST['slot'];

    // Check if the selected slot is vacant
    $checkSlotSql = "SELECT * FROM reservations WHERE slot = '$slot' AND date = '$date' AND ((start_time <= '$startTime' AND end_time > '$startTime') OR (start_time < '$endTime' AND end_time >= '$endTime'))";
    $checkSlotResult = $conn->query($checkSlotSql);

    if ($checkSlotResult->num_rows == 0) {
        // The slot is vacant, proceed with the reservation
        $sql = "INSERT INTO reservations (date, start_time, end_time, slot) VALUES ('$date', '$startTime', '$endTime', '$slot')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to display.php after successful reservation
            header("Location: display.php");
            exit(); // Make sure to stop the script execution after the header is sent
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // The slot is already reserved
        echo "Error: The selected slot is already reserved for the specified time.";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Form</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <div class="center">
        <h2>Reservation Form</h2>
        <form action="reserve.php" method="post">
        <label for ="date-input" >Date:</label>
<input type="date"id="date-input" name="date" min=""/>

<script>
var today= new Date().toISOString().split('T')[0];
document.getElementById("date-input").setAttribute("min", today);
</script>

            <label for="startTime">Start Time:</label>

            
            <input type="time" id="startTime"name="startTime" oninput="calculatePayment()" required>

            <label for="startTime">End Time:</label>
            <input type="time" id="endTime"name="endTime" oninput="calculatePayment()" required>
            <p id="result"></p>

            <script>
                function calculatePayment() {
                    var startTime = document.getElementById('startTime').value;
                    var endTime = document.getElementById('endTime').value;
                    var startMinutes = parseInt(startTime.split(':')[0]) * 60 + parseInt(startTime.split(':')[1]);
                    var endMinutes = parseInt(endTime.split(':')[0]) * 60 + parseInt(endTime.split(':')[1]);

                    var timeGap = endMinutes - startMinutes;
                    var payment = timeGap * 0.167;
                    document.getElementById('result').innerHTML = 'Your Payment will be Rs.' + payment.toFixed(2);
                }
            </script>
            <label class="centered-label">esewa id:**********</label>
            <div class="dropdown">
                <button class="dropbtn">Payment Options</button>
                <div class="dropdown-content">
                    <a href="#">Cash Payment</a>
                    <a href="https://www.esewa.com.np/" target="_blank">eSewa Payment</a>
                </div>
            </div>

            <label class="center">Book your parking slot:</label>
            <div class="parking-container">
                <div class="parking-slot vacant">1</div>
                <div class="parking-slot vacant">2</div>
                <div class="parking-slot vacant">3</div>
                <div class="parking-slot vacant">4</div>
                <div class="parking-slot vacant">5</div>

                <label></label>

            </div>
            <h4>Note:*Red is already reserved</h4>
            <h4>*Green is for vaccant</h4>
            <input type="hidden" name="slot" id="selectedSlot" value="">
            <button type="submit">Reserve</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>