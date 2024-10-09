# Five Second Test README

## Overview
The Five Second Test is a simple web application designed to evaluate users' memory retention and perception of a website's trustworthiness after a brief exposure. Users are shown an image for five seconds and then asked a series of questions to assess their recall and trust ratings. Created by https://www.userble.org - Open Source Usability Testing.

### Setup

**Edit Database Credentials:** Update the following variables with your MySQL credentials in the script:

$host = 'localhost';
$dbname = 'YOUR DB NAME';
$user = 'YOUR DB USERNAME';
$pass = 'YOUR DB PASSWORD';

**Database Table Creation:** The tool automatically creates a preference_test_votes table if it doesn't exist.

**Security:** This tool uses security headers to prevent common vulnerabilities.

### Usage

**Timed Image Display:** Users view an image for exactly five seconds to simulate quick information exposure.
**Interactive Rating System:** Users rate the website's trustworthiness using clickable boxes ranging from 1 to 10.
**Responsive Design:** Clean and responsive user interface that works seamlessly across devices.
**Secure Data Handling:** Utilizes PDO for secure database interactions and input sanitization to prevent SQL injection and XSS attacks.
**Session Management:** Ensures users can only submit responses once per session.
**Contributions:** Contributions are welcome! If you'd like to enhance the project or fix bugs, please submit a pull request or open an issue.

### Requirements
This application requires any web server capable of running PHP and a MySQL database (basically all shared/managed hosting), no dependencies, easy to setup and run.
PHP 7.4+
MySQL

### License
This project is free and open source, available under The ILO's Open License (https://www.theilo.org).
