<?php
/**
 * Admin User Form - S?a th�ng tin ngu?i d�ng
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'S?a ngu?i d�ng';

$user = [
    'id' => '',
    'name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'country' => '',
    'postal_code' => '',
    'role' => 'customer',
    'active' => 1
];

$db = new Database();
$conn = $db->getConnection();

// N?u c� id th� load d? li?u ngu?i d�ng
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = (int)$_GET['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $pageTitle = 'S?a ngu?i d�ng: ' . $user['name'];
    } else {
        $_SESSION['error_message'] = 'Kh�ng t�m th?y ngu?i d�ng!';
        header('Location: ' . Config::get('APP_URL') . 'admin/users.php');
        exit;
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = 'ID ngu?i d�ng kh�ng h?p l?!';
    header('Location: ' . Config::get('APP_URL') . 'admin/users.php');
    exit;
}

// X? l� luu
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $city = htmlspecialchars(trim($_POST['city'] ?? ''));
    $country = htmlspecialchars(trim($_POST['country'] ?? ''));
    $postal_code = htmlspecialchars(trim($_POST['postal_code'] ?? ''));
    $role = in_array($_POST['role'] ?? '', ['customer', 'admin']) ? $_POST['role'] : 'customer';
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validate
    if (empty($name)) {
        $errors[] = 'T�n ngu?i d�ng kh�ng du?c d? tr?ng';
    }
    
    if (empty($email)) {
        $errors[] = 'Email kh�ng du?c d? tr?ng';
    }
    
    // Ki?m tra email d� t?n t?i
    if ($email !== $user['email']) {
        $checkQuery = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        if ($checkResult->fetch_assoc()['count'] > 0) {
            $errors[] = 'Email n�y d� du?c s? d?ng';
        }
        $stmt->close();
    }
    
    if (empty($errors)) {
        try {
            $query = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, city = ?, country = ?, postal_code = ?, role = ?, active = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $userId = $user['id'];
            $stmt->bind_param('sssssssii', $name, $email, $phone, $address, $city, $country, $postal_code, $role, $active, $userId);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'C?p nh?t ngu?i d�ng th�nh c�ng!';
                header('Location: ' . Config::get('APP_URL') . 'admin/users.php');
                exit;
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = 'L?i: ' . $e->getMessage();
        }
    }
    
    // Update form values n?u c� l?i
    $user = [
        'id' => $_GET['id'] ?? '',
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'city' => $city,
        'country' => $country,
        'postal_code' => $postal_code,
        'role' => $role,
        'active' => $active
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::get('APP_URL'); ?>admin/admin-style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>

            <ul class="sidebar-menu">
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/">?? Dashboard</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/reports.php">?? B�o c�o</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/users.php" class="active">?? Ngu?i d�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php">??? Voucher</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>?? <?php echo $pageTitle; ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">T�n ngu?i d�ng *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">�i?n tho?i</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="role">Vai tr�</label>
                            <select id="role" name="role">
                                <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Kh�ch h�ng</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="address">�?a ch?</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Th�nh ph?</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="country">Qu?c gia</label>
                            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postal_code">M� buu ch�nh</label>
                            <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="active" style="margin-bottom: 0;">Tr?ng th�i</label>
                            <div class="checkbox-group" style="margin-top: 15px;">
                                <input type="checkbox" id="active" name="active" <?php echo ($user['active'] ?? true) ? 'checked' : ''; ?>>
                                <label for="active" style="margin-bottom: 0;">K�ch ho?t ngu?i d�ng</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">?? Luu thay d?i</button>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/users.php" class="btn btn-cancel">? H?y</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

