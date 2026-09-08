<!DOCTYPE html>
<html lang="te">
<head>
    <meta charset="UTF-8">
    <title>Submit Problem</title>
</head>
<body>
    <h2>Meeru edurukuntunna Problem ni Submit Cheyandi</h2>
    <form action="submit_problem.php" method="POST">
        <label>Mee Location:</label><br>
        <input type="text" name="location" required><br><br>

        <label>Mee Problem Details:</label><br>
        <textarea name="problem" rows="5" required></textarea><br><br>

        <button type="submit">Submit Problem</button>
    </form>
</body>
</html>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "community_db";

// Connection create cheyadam
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $location = $_POST['location'];
    $problem = $_POST['problem'];

    $sql = "INSERT INTO problems (location, problem_description) VALUES ('$location', '$problem')";

    if ($conn->query($sql) === TRUE) {
        echo "Problem successfully submit aindhi!";
        // Python script trigger cheyochu broadcast kosam
        exec("python3 notify_users.py");
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>
import mysql.connector
import smtplib
from email.mime.text import MIMEText

# Database Connection
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="community_db"
)

cursor = db.cursor(dictionary=True)

# 1. Fetch the latest submitted problem
cursor.execute("SELECT * FROM problems ORDER BY id DESC LIMIT 1")
latest_problem = cursor.fetchone()

# 2. Fetch emails of all registered/logged-in users
cursor.execute("SELECT email FROM users WHERE is_active = 1")
users = cursor.fetchall()

# 3. Send Emails to all active users
def send_alert(user_email, location, problem):
    msg = MIMEText(f"Krottha Problem Add Aindhi!\nLocation: {location}\nProblem: {problem}")
    msg['Subject'] = 'New Alert in Your Area'
    msg['From'] = 'your_system_email@gmail.com'
    msg['To'] = user_email

    # SMTP server configuration (e.g., Gmail SMTP)
    with smtplib.SMTP_SSL('smtp.gmail.com', 465) as server:
        server.login("your_system_email@gmail.com", "your_app_password")
        server.send_message(msg)

if latest_problem and users:
    for user in users:
        send_alert(user['email'], latest_problem['location'], latest_problem['problem_description'])
    print("Notifications sent to all active users.")
