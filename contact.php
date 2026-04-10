<?php
require_once 'config.php';
require_once 'auth.php';

$auth->checkSessionTimeout();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_text = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_text)) {
        // TODO: Send email
        $message = 'Thank you for your message! We will get back to you soon.';
        $messageType = 'success';
    } else {
        $message = 'Please fill all fields';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Daraz Bangladesh</title>
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

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .contact-box {
            background: white;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #333;
            margin-bottom: 15px;
        }

        h1 {
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
            grid-column: 1 / -1;
        }

        .contact-info {
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            color: #666;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-content h3 {
            color: #333;
            margin-bottom: 5px;
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

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-family: inherit;
            font-size: 14px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
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

        @media (max-width: 768px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo" onclick="window.location.href='index.php'" style="cursor: pointer;">
                <i class="fas fa-shopping-bag"></i> Daraz
            </div>
            <div>
                <a href="index.php" style="color: white; margin-left: 20px;"><i class="fas fa-home"></i> Home</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>

        <div class="content-wrapper">
            <!-- Contact Info -->
            <div class="contact-box">
                <h2>Get In Touch</h2>
                
                <div class="contact-info">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Address</h3>
                            <p>123 Business Street<br>Dhaka, Bangladesh 1000</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h3>Phone</h3>
                            <p>+88 01700-000000<br>+88 01800-000000</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>support@daraz.com.bd<br>info@daraz.com.bd</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h3>Business Hours</h3>
                            <p>Monday - Sunday<br>9:00 AM - 10:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-box">
                <h2>Send us a Message</h2>
                
                <?php if (!empty($message)): ?>
                    <div class="<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name *</label>
                        <input type="text" id="name" name="name" placeholder="Your name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" placeholder="Your email" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" placeholder="Message subject" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" placeholder="Your message" required></textarea>
                    </div>

                    <button type="submit" name="send_message">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
