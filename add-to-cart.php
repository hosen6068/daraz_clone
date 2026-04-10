<?php
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

$auth->checkSessionTimeout();

if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

// Check if product exists and has stock
$stmt = $conn->prepare("SELECT id, stock FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

if ($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit;
}

// Add to cart
$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE quantity = quantity + ?");
$stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
}
$stmt->close();
?>
