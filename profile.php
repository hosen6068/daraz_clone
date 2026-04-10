<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$message = '';
$messageType = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);

    if (empty($first_name)) {
        $message = 'First name is required';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ?, city = ?, district = ?, postal_code = ?, country = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $phone, $address, $city, $district, $postal_code, $country, $user_id);
        
        if ($stmt->execute()) {
            $message = 'Profile updated successfully!';
            $messageType = 'success';
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['phone'] = $phone;
            $user['address'] = $address;
            $user['city'] = $city;
            $user['district'] = $district;
            $user['postal_code'] = $postal_code;
            $user['country'] = $country;
        } else {
            $message = 'Failed to update profile';
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($old_password, $user['password'])) {
        $message = 'Current password is incorrect';
        $messageType = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'New passwords do not match';
        $messageType = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'Password must be at least 6 characters';
        $messageType = 'error';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $message = 'Password changed successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to change password';
            $messageType = 'error';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Daraz Bangladesh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .header-actions a,
        .header-actions button {
            color: white;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            margin-left: 30px;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
        }

        .sidebar {
            background: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            margin: 0 auto 15px;
        }

        .user-name {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-name h2 {
            color: #333;
            margin-bottom: 5px;
        }

        .user-name p {
            color: #999;
            font-size: 14px;
        }

        .menu-item {
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            border-left: 3px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background: #f5f5f5;
            border-left-color: #667eea;
            padding-left: 20px;
        }

        .content {
            background: white;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #667eea;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .user-avatar,
            .user-name {
                grid-column: 1 / -1;
            }

            .menu-item {
                text-align: center;
                padding: 10px;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo" onclick="window.location.href='index.php'">
                <i class="fas fa-shopping-bag"></i> Daraz
            </div>
            <div class="header-actions">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="my-orders.php"><i class="fas fa-box"></i> Orders</a>
                <button onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="sidebar">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-name">
                <h2><?php echo htmlspecialchars($user['first_name']); ?></h2>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="menu-item active" onclick="showSection('profile')">
                <i class="fas fa-user"></i> Personal Info
            </div>
            <div class="menu-item" onclick="showSection('address')">
                <i class="fas fa-map-marker-alt"></i> Address
            </div>
            <div class="menu-item" onclick="showSection('password')">
                <i class="fas fa-lock"></i> Change Password
            </div>
        </div>

        <div class="content">
            <?php if (!empty($message)): ?>
                <div class="<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Personal Info Section -->
            <div id="profile" class="section active">
                <h2><i class="fas fa-user-circle"></i> Personal Information</h2>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #f5f5f5;">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_profile"><i class="fas fa-save"></i> Save Changes</button>
                </form>
            </div>

            <!-- Address Section -->
            <div id="address" class="section">
                <h2><i class="fas fa-map-marker-alt"></i> Address Information</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <textarea id="address" name="address" placeholder="Enter your street address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" placeholder="e.g., Dhaka" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="district">District</label>
                            <input type="text" id="district" name="district" placeholder="e.g., Dhaka" value="<?php echo htmlspecialchars($user['district'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 1000" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country" placeholder="Bangladesh" value="<?php echo htmlspecialchars($user['country'] ?? 'Bangladesh'); ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_profile"><i class="fas fa-save"></i> Save Address</button>
                </form>
            </div>

            <!-- Password Section -->
            <div id="password" class="section">
                <h2><i class="fas fa-lock"></i> Change Password</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="old_password">Current Password *</label>
                        <input type="password" id="old_password" name="old_password" placeholder="Enter your current password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter your new password (min 6 characters)" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                    </div>

                    <button type="submit" name="change_password"><i class="fas fa-lock"></i> Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showSection(section) {
            // Hide all sections
            const sections = document.querySelectorAll('.section');
            sections.forEach(s => s.classList.remove('active'));
            
            // Remove active from all menu items
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(m => m.classList.remove('active'));
            
            // Show selected section
            document.getElementById(section).classList.add('active');
            event.target.closest('.menu-item').classList.add('active');
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
