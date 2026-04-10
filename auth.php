<?php
if (!defined('DB_NAME')) {
    require_once 'config.php';
}

class Auth {
    private $conn;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    
    /**
     * Register a new user
     */
    public function register($first_name, $last_name, $email, $phone, $password, $confirm_password) {
        // Validate input
        if (empty($first_name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Please fill all required fields'];
        }
        
        if ($password !== $confirm_password) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        $stmt->close();
        
        // Check if phone already exists (if provided)
        if (!empty($phone)) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                return ['success' => false, 'message' => 'Phone number already registered'];
            }
            $stmt->close();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert new user
        $stmt = $this->conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $hashed_password);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Registration successful! Please login.', 'user_id' => $user_id];
        } else {
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Login user
     */
    public function login($email_or_phone, $password, $remember_me = false) {
        // Check if user exists
        $stmt = $this->conn->prepare("SELECT id, first_name, last_name, email, phone, password FROM users WHERE (email = ? OR phone = ?) AND status = 'active'");
        $stmt->bind_param("ss", $email_or_phone, $email_or_phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid email/phone or password'];
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email/phone or password'];
        }
        
        // Create session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['login_time'] = time();
        
        // Handle remember me
        if ($remember_me) {
            $token = bin2hex(random_bytes(32));
            $hashed_token = hash('sha256', $token);
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $stmt = $this->conn->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['id'], $hashed_token, $expires_at);
            $stmt->execute();
            $stmt->close();
            
            // Set cookie
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            setcookie('remember_email', $user['email'], time() + (30 * 24 * 60 * 60), '/');
        }
        
        return ['success' => true, 'message' => 'Login successful', 'user_id' => $user['id']];
    }
    
    /**
     * Check remember me token
     */
    public function checkRememberToken() {
        if (!isset($_COOKIE['remember_token'])) {
            return null;
        }
        
        $token = $_COOKIE['remember_token'];
        $hashed_token = hash('sha256', $token);
        
        // Check if token exists and is not expired
        $stmt = $this->conn->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $hashed_token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Delete expired cookie
            setcookie('remember_token', '', time() - 3600, '/');
            setcookie('remember_email', '', time() - 3600, '/');
            $stmt->close();
            return null;
        }
        
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $stmt->close();
        
        // Get user details
        $stmt = $this->conn->prepare("SELECT id, first_name, last_name, email, phone FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $user;
    }
    
    /**
     * Get saved credentials for remember me
     */
    public function getSavedCredentials() {
        if (isset($_COOKIE['remember_email'])) {
            $email = $_COOKIE['remember_email'];
            
            // Get user details
            $stmt = $this->conn->prepare("SELECT email, phone FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $user;
        }
        
        return null;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Delete remember token
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Delete cookies
        setcookie('remember_token', '', time() - 3600, '/');
        setcookie('remember_email', '', time() - 3600, '/');
        
        // Destroy session
        session_destroy();
        return true;
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'phone' => $_SESSION['user_phone']
        ];
    }
    
    /**
     * Check session timeout
     */
    public function checkSessionTimeout() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }
        
        // Update session time
        $_SESSION['login_time'] = time();
        return true;
    }
}

$auth = new Auth($conn);

// Check session timeout
if ($auth->isLoggedIn()) {
    $auth->checkSessionTimeout();
}
?>
