# 🏦 MyBank Pro - Modern PHP Banking System

A sleek, responsive, and secure banking dashboard built with **PHP** and **MySQL**. This project features a modern "Gen Z" inspired aesthetic with full **Dark Mode** support, designed for a premium user experience.

## ✨ Key Features
- **Modern Dashboard:** A clean overview of your account balance and recent activities.
- **Dark Mode Support:** Smooth transition between Light and Dark themes using LocalStorage.
- **Transaction History:** Detailed logs for all deposits, withdrawals, and transfers with real-time status badges.
- **Secure Transactions:** Functional modules for Adding Money (Deposit), Cashing Out (Withdraw), and Sending Funds (Transfer).
- **User Authentication:** Complete Signup and Login system with session management.
- **Profile Management:** Securely update user account details.
- **Responsive Design:** Optimized for all screen sizes using Bootstrap 5.

## 🛠️ Tech Stack
- **Frontend:** HTML5, CSS3, JavaScript (ES6), Bootstrap 5
- **Backend:** PHP 8.x
- **Database:** MySQL
- **Icons:** Bootstrap Icons & Google Fonts (Inter)

## 🚀 Installation & Setup

1. **Clone the Repository:**
   ```bash
   git clone [https://github.com/rabia-irshad2/banking-system-php.git](https://github.com/rabia-irshad2/banking-system-php.git)
   
   
2. Database Configuration:
Open phpMyAdmin and create a new database (e.g., banking_db).

Import the database.sql (or your exported SQL file) into the new database.


3. Establish Connection:

Create a file named db_connect.php in the root directory.

Use the following template and add your local credentials:
<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "banking_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
?>


4. Run the Project:

Move the project folder to C:/xampp/htdocs/.

Start Apache and MySQL in XAMPP.

Open http://localhost/banking_app/login.php in your browser.


🔒 Security Note
The db_connect.php file is excluded from this repository via .gitignore to protect sensitive database credentials. Please use the template provided above to set up your local environment.

📜 License
Distributed under the MIT License. See LICENSE for more information.

Developed with ❤️ by Rabia Irshad