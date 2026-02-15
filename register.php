<?php
session_start();
require 'db.php'; // uses $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'] ?? ''; // Default to empty string to avoid undefined key warning
    $consent = isset($_POST['consent']) ? 1 : 0;

    // Validation
    if (!preg_match("/^[a-zA-Z\s\-']+$/", $firstName)) {
        $error = "First Name must contain only letters, spaces, hyphens, or apostrophes.";
    } elseif (!preg_match("/^[a-zA-Z\s\-']+$/", $lastName)) {
        $error = "Last Name must contain only letters, spaces, hyphens, or apostrophes.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match("/^09\d{9}$/", $phone)) {
        $error = "Phone number must be exactly 11 digits and start with '09'.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif ($consent != 1) {
        $error = "You must agree to data privacy policy.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM customer WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $customerID = uniqid('cust_');

            $stmt = $conn->prepare("INSERT INTO customer (CustomerID, LastName, FirstName, Email, PhoneNumber, RegistrationDate, PasswordHash, DataPrivacyConsent) 
                                    VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
            $stmt->bind_param('ssssssi', $customerID, $lastName, $firstName, $email, $phone, $hash, $consent);
            $stmt->execute();

            $_SESSION['CustomerID'] = $customerID;
            $_SESSION['FirstName'] = $firstName;
            $_SESSION['LastName'] = $lastName;
            $_SESSION['Email'] = $email;

            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Governor Forbes Inn</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- For eye icon -->
    <style>

        /*REGISTER*/

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

.register-container {
    background-color: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 40px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px; /* Increased for two-column layout on desktop */
    text-align: center;
}

.register-container h1 {
    margin-bottom: 20px;
    color: var(--text);
    font-size: 24px;
}

.error-message {
    color: red;
    margin-bottom: 20px;
}

.register-container form {
    display: grid;
    grid-template-columns: 1fr; /* Default: single column (stacked) */
    gap: 20px;
}

.form-group {
    text-align: left;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: var(--text);
    font-weight: bold;
}

.checkbox-label {
    display: flex;
    align-items: center;
    font-weight: normal;
    margin-bottom: 0;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 10px;
    width: auto;
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

.register-btn {
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

.register-btn:hover {
    background-color: #c4993a;
}

.login-link {
    margin-top: 15px;
    font-size: 14px;
    color: var(--muted);
}

.login-link a {
    color: var(--gold);
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

/* Desktop: Two-column layout for form fields */
@media (min-width: 769px) {
    .register-container form {
        grid-template-columns: 1fr 1fr; /* Two columns */
    }

    .form-group.full-width {
        grid-column: span 2; /* Checkbox and button span full width */
    }
}

/* Responsive Design for Mobile */
@media (max-width: 768px) {
    .register-container {
        padding: 20px;
        margin: 20px;
        max-width: 400px; /* Smaller on mobile */
    }

    .register-container h1 {
        font-size: 20px;
    }

    .form-group input {
        padding: 10px;
        font-size: 14px;
    }

    .register-btn {
        padding: 10px;
        font-size: 14px;
    }
}
        .password-container {
            position: relative;
        }
        .password-container input {
            padding-right: 40px; /* Space for eye icon */
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--muted);
        }
        .password-requirements {
            margin-top: 5px;
            font-size: 12px;
            color: var(--muted);
        }
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .password-requirements li {
            margin-bottom: 2px;
        }
        .password-requirements li.valid {
            color: green;
        }
        .password-requirements li::before {
            content: "✗ ";
            color: red;
        }
        .password-requirements li.valid::before {
            content: "✓ ";
            color: green;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <form method="POST">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" placeholder="e.g., John" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" placeholder="e.g., Doe" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="e.g., john.doe@example.com" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" placeholder="e.g., 09123456789" required>
            </div>
            <div class="form-group full-width">
                <label for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                </div>
                <div class="password-requirements">
                    <ul>
                        <li id="length">At least 8 characters</li>
                        <li id="lowercase">One lowercase letter</li>
                        <li id="uppercase">One uppercase letter</li>
                        <li id="number">One number</li>
                        <li id="symbol">One symbol (e.g., @, !, ?)</li>
                    </ul>
                </div>
            </div>
            <div class="form-group full-width">
                <label for="confirmPassword">Confirm Password</label>
                <div class="password-container">
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirmPassword')"></i>
                </div>
            </div>
            <div class="form-group full-width">
                <label class="checkbox-label">
                    <input type="checkbox" name="consent" required> I agree to data privacy policy
                </label>
            </div>
            <div class="form-group full-width">
                <button type="submit" class="register-btn">Register</button>
            </div>
        </form>
        <p class="login-link">Already have an account? <a href="index.php">Login</a></p>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const requirements = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                symbol: /[@$!%*?&]/.test(password)
            };
            Object.keys(requirements).forEach(req => {
                const li = document.getElementById(req);
                if (requirements[req]) {
                    li.classList.add('valid');
                } else {
                    li.classList.remove('valid');
                }
            });
        });
    </script>
</body>
</html>