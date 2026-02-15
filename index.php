<?php
session_start();
require 'db.php'; // uses $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM customer WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['PasswordHash'])) {
            // Login successful
            $_SESSION['CustomerID'] = $user['CustomerID'];
            $_SESSION['FirstName'] = $user['FirstName'];
            $_SESSION['LastName'] = $user['LastName'];
            $_SESSION['Email'] = $user['Email'];

            // Redirect to homepage
            header('Location: homepage.php');
            exit;
        } else {
            $error = "Incorrect email or password";
        }
    } else {
        $error = "Incorrect email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Governor Forbes Inn</title>
    <style>
        /* Index */

        :root {
            --gold: #d4a83f;
            --bg: #f6f4f1;
            --border: #e2e2e2;
            --text: #2b2b2b;
            --muted: #555;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            color: var(--text);
            font-size: 24px;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text);
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--gold);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--gold);
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #c4993a;
        }

        .register-link {
            margin-top: 15px;
            font-size: 14px;
            color: var(--muted);
        }

        .register-link a {
            color: var(--gold);
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Design for Mobile and Desktop */
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
                margin: 20px;
            }

            .login-container h1 {
                font-size: 20px;
            }

            .form-group input {
                padding: 10px;
                font-size: 14px;
            }

            .login-btn {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php if (!empty($error))
            echo "<p class='error-message'>$error</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <p class="register-link">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>

</html>