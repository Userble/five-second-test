<?php
// Start the session
session_start();

// Security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: no-referrer");

// IMPORTANT SETUP STEP - Enter YOUR database credentials here
$host = 'localhost';
$dbname = 'YOUR DB NAME';
$user = 'YOUR DB USERNAME';
$pass = 'YOUR DB PASSWORD';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set PDO to use prepared statements
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Create the table if it doesn't exist
    createFiveSecondTestTable($pdo);
} catch (PDOException $e) {
    // Log the error
    error_log($e->getMessage());
    echo "Database connection failed.";
    exit;
}

// Function to create the table
function createFiveSecondTestTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS five_second_test_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question1 TEXT NOT NULL,
        question2 INT NOT NULL,
        question3 TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
}

// Initialize variables
$message = '';

// Check if the user has submitted the form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['question1'])) {
    // Get the answers and sanitize input
    $question1 = htmlspecialchars(trim($_POST['question1']), ENT_QUOTES, 'UTF-8');
    $question2 = intval($_POST['question2']);
    $question3 = htmlspecialchars(trim($_POST['question3']), ENT_QUOTES, 'UTF-8');

    // Validate inputs
    if (!empty($question1) && !empty($question3) && $question2 >= 1 && $question2 <= 10) {
        // Store the answers in the database
        $stmt = $pdo->prepare("INSERT INTO five_second_test_results (question1, question2, question3) VALUES (:q1, :q2, :q3)");
        $stmt->execute([
            'q1' => $question1,
            'q2' => $question2,
            'q3' => $question3
        ]);

        $message = "Thank you for completing the test!";
        // Mark the test as completed
        $_SESSION['has_completed_test'] = true;
        // Regenerate session ID
        session_regenerate_id(true);
    } else {
        $message = "Please provide valid inputs.";
    }
}

// Check if the test is already completed
$has_completed_test = isset($_SESSION['has_completed_test']) && $_SESSION['has_completed_test'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Five Second Test</title>
    <!-- Include Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <style>
        /* Reset and basic styling */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            background-color: #f9f9f9;
            overflow-x: hidden;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
            text-align: center;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .header {
            padding: 0 0 20px 0;
        }
        .start-button, .submit-button {
            background-color: #3498db;
            color: #fff;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 20px;
        }
        .start-button:hover, .submit-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .start-button:disabled, .submit-button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
            transform: none;
        }
        .image-container {
            display: none;
            margin-top: 30px;
            max-height: calc(100vh - 150px);
            overflow: hidden;
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }
        .question-form {
            display: none;
            margin-top: 30px;
        }
        .question-form input[type="text"] {
            padding: 10px;
            width: 100%;
            max-width: 400px;
            margin-bottom: 20px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .question-form label {
            font-size: 1.1em;
            margin-bottom: 10px;
            display: block;
            color: #2c3e50;
        }
        .message {
            font-size: 1.2em;
            color: #2c3e50;
            margin-top: 30px;
        }
        /* Styles for the rating scale */
        .rating-scale {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .rating-scale input[type="radio"] {
            display: none;
        }
        .rating-scale label {
            background-color: #ccc;
            color: #333;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 1em;
            user-select: none;
        }
        .rating-scale input[type="radio"]:checked + label {
            background-color: #3498db;
            color: #fff;
        }
        .rating-scale label:hover {
            background-color: #2980b9;
            color: #fff;
        }
        .cta {
            margin-top: 20px;
            font-size: 1.1em;
            color: #2c3e50;
        }
        .cta a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .cta a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function startTest() {
            document.getElementById('start-button').style.display = 'none';
            document.getElementById('image-container').style.display = 'block';

            setTimeout(function() {
                document.getElementById('image-container').style.display = 'none';
                document.getElementById('question-form').style.display = 'block';
            }, 5000);
        }
    </script>
</head>
<body>
<div class="container">
    <?php if ($has_completed_test): ?>
        <h1>Thank you for completing the test!</h1>
        <p class="cta">Interested in getting paid to complete usability tests? <a href="https://userble.com/become-a-tester" target="_blank">Become a tester for Userble</a>.</p>
    <?php else: ?>
        <header class="header">
            <h1>Instructions:</h1>
            <p>Look at the interface for 5 seconds and remember as much as you can</p>
        </header>

        <?php if (!empty($message)) { echo "<p class='message'>" . $message . "</p>"; } ?>

        <button id="start-button" class="start-button" onclick="startTest()">I'm ready to start</button>

        <div id="image-container" class="image-container">
            <img src="five-second-test.png" alt="Test Image">
        </div>

        <form id="question-form" class="question-form" method="post">
            <div>
                <label for="question1">1. What is the website for?</label>
                <input type="text" name="question1" id="question1" required>
            </div>
            <div>
                <label>2. Rate its trustworthiness (1-10)</label>
                <div class="rating-scale" id="question2">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <input type="radio" name="question2" value="<?php echo $i; ?>" id="q2_<?php echo $i; ?>" required>
                        <label for="q2_<?php echo $i; ?>"><?php echo $i; ?></label>
                    <?php endfor; ?>
                </div>
            </div>
            <div>
                <label for="question3">3. What was the product name?</label>
                <input type="text" name="question3" id="question3" required>
            </div>
            <button type="submit" class="submit-button">Submit Answers</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
