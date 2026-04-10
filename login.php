<?php
if (!defined('DB_NAME')) {
    require_once 'config.php';
    require_once 'auth.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daraz Bangladesh</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }

        .logo span {
            color: #764ba2;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .saved-credentials {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .saved-credentials:hover {
            background: #efefef;
        }

        .saved-credentials input[type="radio"] {
            margin-right: 10px;
            cursor: pointer;
        }

        .saved-credentials-label {
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .remember-me label {
            margin: 0;
            cursor: pointer;
            color: #666;
            font-weight: normal;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        .signup-link a:hover {
            color: #764ba2;
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }

        .success {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3c3;
        }

        .toggle-link {
            text-align: center;
            margin-top: 15px;
        }

        .toggle-link button {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            text-decoration: underline;
            font-weight: bold;
        }

        .toggle-link button:hover {
            color: #764ba2;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .terms {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
        }

        .terms input[type="checkbox"] {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <span>Daraz</span>
        </div>

        <!-- Login Form Section -->
        <div id="loginForm" class="form-section active">
            <h2 style="text-align: center; margin-bottom: 25px; color: #333;">Login</h2>
            
            <?php
            require_once 'auth.php';

            $message = '';
            $messageType = '';

            // Check if coming from registration
            if (isset($_GET['registered'])) {
                $message = 'Registration successful! Please login.';
                $messageType = 'success';
            }

            // Check if user already logged in
            if ($auth->isLoggedIn()) {
                header('Location: index.php');
                exit;
            }

            // Check remember token
            $savedUser = $auth->checkRememberToken();
            if ($savedUser && isset($_GET['auto_login'])) {
                $_SESSION['user_id'] = $savedUser['id'];
                $_SESSION['user_name'] = $savedUser['first_name'] . ' ' . $savedUser['last_name'];
                $_SESSION['user_email'] = $savedUser['email'];
                $_SESSION['user_phone'] = $savedUser['phone'];
                $_SESSION['login_time'] = time();
                header('Location: index.php');
                exit;
            }

            // Handle login
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
                $email_or_phone = trim($_POST['email_or_phone']);
                $password = $_POST['password'];
                $remember_me = isset($_POST['remember_me']) ? true : false;

                $result = $auth->login($email_or_phone, $password, $remember_me);
                if ($result['success']) {
                    header('Location: index.php');
                    exit;
                } else {
                    $message = $result['message'];
                    $messageType = 'error';
                }
            }

            // Get saved credentials
            $savedCredentials = $auth->getSavedCredentials();
            ?>

            <?php if (!empty($message)): ?>
                <div class="<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($savedCredentials): ?>
                <div style="margin-bottom: 20px;">
                    <label style="margin-bottom: 10px; display: block; font-weight: bold;">Saved Credentials:</label>
                    <div class="saved-credentials" onclick="fillSavedCredentials('<?php echo htmlspecialchars($savedCredentials['email']); ?>')">
                        <label class="saved-credentials-label">
                            <input type="radio" name="use_saved" value="yes">
                            <span>
                                <strong><?php echo htmlspecialchars($savedCredentials['email']); ?></strong><br>
                                <small style="color: #999;">or <?php echo htmlspecialchars($savedCredentials['phone']); ?></small>
                            </span>
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email_or_phone">Email or Phone Number</label>
                    <input type="text" id="email_or_phone" name="email_or_phone" placeholder="Enter your email or phone" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember_me" name="remember_me" value="1">
                    <label for="remember_me">Remember me for 30 days</label>
                </div>

                <button type="submit" name="login" class="btn-login">Login</button>
            </form>

            <div class="toggle-link">
                <button onclick="toggleForm()">Don't have an account? Register</button>
            </div>
        </div>

        <!-- Register Form Section -->
        <div id="registerForm" class="form-section">
            <h2 style="text-align: center; margin-bottom: 25px; color: #333;">Create Account</h2>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
                $first_name = trim($_POST['first_name']);
                $last_name = trim($_POST['last_name']);
                $email = trim($_POST['email']);
                $phone = trim($_POST['phone']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                $agree_terms = isset($_POST['agree_terms']) ? true : false;

                if (!$agree_terms) {
                    $message = 'Please agree to the terms and conditions';
                    $messageType = 'error';
                } else {
                    $result = $auth->register($first_name, $last_name, $email, $phone, $password, $confirm_password);
                    if ($result['success']) {
                        header('Location: login.php?registered=1');
                        exit;
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                }
            }
            ?>

            <?php if (!empty($message) && isset($_POST['register'])): ?>
                <div class="<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name (Optional)</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name">
                </div>

                <div class="form-group">
                    <label for="reg_email">Email</label>
                    <input type="email" id="reg_email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number">
                </div>

                <div class="form-group">
                    <label for="reg_password">Password</label>
                    <input type="password" id="reg_password" name="password" placeholder="Enter your password (min 6 characters)" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>

                <div class="terms">
                    <input type="checkbox" id="agree_terms" name="agree_terms" required>
                    <label for="agree_terms" style="margin: 0;">I agree to the Terms and Conditions</label>
                </div>

                <button type="submit" name="register" class="btn-login" style="margin-top: 15px;">Create Account</button>
            </form>

            <div class="toggle-link">
                <button onclick="toggleForm()">Already have an account? Login</button>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            loginForm.classList.toggle('active');
            registerForm.classList.toggle('active');
        }

        function fillSavedCredentials(email) {
            document.getElementById('email_or_phone').value = email;
            document.getElementById('email_or_phone').focus();
            document.getElementById('password').focus();
        }
    </script>
</body>
</html>
