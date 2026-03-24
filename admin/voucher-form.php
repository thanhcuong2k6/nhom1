<?php
/**
 * Admin Voucher Form - Th�m/s?a m� gi?m gi�
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/Auth.php';

// B?o v? - ch? admin du?c
Auth::requireAdmin();

$pageTitle = 'Voucher';
$isEdit = false;
$voucher = [
    'id' => '',
    'code' => '',
    'description' => '',
    'discount_type' => 'percentage',
    'discount_value' => '',
    'min_amount' => '0',
    'max_uses' => '',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+30 days')),
    'active' => 1
];

$db = new Database();
$conn = $db->getConnection();

// N?u c� id th� l� ch? d? edit
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $voucherId = (int)$_GET['id'];
    $query = "SELECT * FROM vouchers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $voucherId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $voucher = $result->fetch_assoc();
        $isEdit = true;
        $pageTitle = 'S?a voucher: ' . $voucher['code'];
    }
    $stmt->close();
}

// X? l� luu
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = htmlspecialchars(trim($_POST['code'] ?? ''));
    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
    $discountType = in_array($_POST['discount_type'] ?? '', ['percentage', 'fixed']) ? $_POST['discount_type'] : 'percentage';
    $discountValue = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
    $minAmount = isset($_POST['min_amount']) ? (float)$_POST['min_amount'] : 0;
    $maxUses = isset($_POST['max_uses']) && $_POST['max_uses'] !== '' ? (int)$_POST['max_uses'] : null;
    $startDate = $_POST['start_date'] ?? date('Y-m-d');
    $endDate = $_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days'));
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Validate
    if (empty($code)) {
        $errors[] = 'M� voucher kh�ng du?c d? tr?ng';
    }
    
    if ($discountValue <= 0) {
        $errors[] = 'Gi� tr? gi?m gi� ph?i l?n hon 0';
    }
    
    if ($discountType === 'percentage' && $discountValue > 100) {
        $errors[] = 'Gi?m gi� ph?n tram kh�ng th? vu?t qu� 100%';
    }
    
    if ($minAmount < 0) {
        $errors[] = '�on h�ng t?i thi?u kh�ng th? �m';
    }
    
    if (strtotime($startDate) >= strtotime($endDate)) {
        $errors[] = 'Ng�y k?t th�c ph?i sau ng�y b?t d?u';
    }
    
    // Ki?m tra code d� t?n t?i (n?u kh�ng ph?i edit)
    if (!$isEdit || $code !== $voucher['code']) {
        $checkQuery = "SELECT COUNT(*) as count FROM vouchers WHERE code = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $checkResult = $stmt->get_result();
        if ($checkResult->fetch_assoc()['count'] > 0) {
            $errors[] = 'M� voucher n�y d� t?n t?i';
        }
        $stmt->close();
    }
    
    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Update
                $query = "UPDATE vouchers SET code = ?, description = ?, discount_type = ?, discount_value = ?, min_amount = ?, max_uses = ?, start_date = ?, end_date = ?, active = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $voucherId = $voucher['id'];
                $stmt->bind_param('sssddisssi', $code, $description, $discountType, $discountValue, $minAmount, $maxUses, $startDate, $endDate, $active, $voucherId);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'C?p nh?t voucher th�nh c�ng!';
                    header('Location: ' . Config::get('APP_URL') . 'admin/vouchers.php');
                    exit;
                }
            } else {
                // Insert
                $query = "INSERT INTO vouchers (code, description, discount_type, discount_value, min_amount, max_uses, start_date, end_date, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('sssddissi', $code, $description, $discountType, $discountValue, $minAmount, $maxUses, $startDate, $endDate, $active);
                
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'Th�m voucher th�nh c�ng!';
                    header('Location: ' . Config::get('APP_URL') . 'admin/vouchers.php');
                    exit;
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = 'L?i: ' . $e->getMessage();
        }
    }
    
    // Update form values n?u c� l?i
    $voucher = [
        'id' => $_GET['id'] ?? '',
        'code' => $code,
        'description' => $description,
        'discount_type' => $discountType,
        'discount_value' => $discountValue,
        'min_amount' => $minAmount,
        'max_uses' => $maxUses,
        'start_date' => $startDate,
        'end_date' => $endDate,
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
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/users.php">?? Ngu?i d�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/products.php">?? S?n ph?m</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/categories.php">?? Danh m?c</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php" class="active">??? Voucher</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>admin/orders.php">?? �on h�ng</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>">?? Trang ch?</a></li>
                <li><a href="<?php echo Config::get('APP_URL'); ?>auth/logout.php">?? �ang xu?t</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1><?php echo $isEdit ? '?? S?a voucher' : '? Th�m voucher'; ?></h1>
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
                            <label for="code">M� voucher *</label>
                            <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($voucher['code'] ?? ''); ?>" placeholder="VD: SUMMER2024" <?php echo $isEdit ? 'readonly' : ''; ?> required>
                            <div class="info-text">M� d�ng d? �p d?ng voucher (c�c k� t? hoa, kh�ng d?u)</div>
                        </div>

                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div style="padding-top: 35px; padding-bottom: 10px;">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="active" name="active" <?php echo ($voucher['active'] ?? true) ? 'checked' : ''; ?>>
                                    <label for="active" style="margin-bottom: 0;">K�ch ho?t voucher</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="form-group">
                            <label for="description">M� t? voucher</label>
                            <textarea id="description" name="description"><?php echo htmlspecialchars($voucher['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Lo?i gi?m gi� *</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="percentage" name="discount_type" value="percentage" <?php echo ($voucher['discount_type'] ?? 'percentage') === 'percentage' ? 'checked' : ''; ?>>
                                    <label for="percentage">Ph?n tram (%)</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="fixed" name="discount_type" value="fixed" <?php echo ($voucher['discount_type'] ?? 'percentage') === 'fixed' ? 'checked' : ''; ?>>
                                    <label for="fixed">Ti?n c? d?nh (?)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="discount_value">Gi� tr? gi?m gi� *</label>
                            <input type="number" id="discount_value" name="discount_value" step="0.01" value="<?php echo $voucher['discount_value'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="min_amount">�on h�ng t?i thi?u (?)</label>
                            <input type="number" id="min_amount" name="min_amount" step="1000" value="<?php echo $voucher['min_amount'] ?? 0; ?>">
                            <div class="info-text">�on h�ng ph?i d?t s? ti?n n�y m?i du?c �p d?ng</div>
                        </div>

                        <div class="form-group">
                            <label for="max_uses">S? l?n s? d?ng t?i da</label>
                            <input type="number" id="max_uses" name="max_uses" value="<?php echo $voucher['max_uses'] ?? ''; ?>">
                            <div class="info-text">�? tr?ng d? kh�ng gi?i h?n</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Ng�y b?t d?u *</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo date('Y-m-d', strtotime($voucher['start_date'] ?? '')); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">Ng�y k?t th�c *</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo date('Y-m-d', strtotime($voucher['end_date'] ?? '')); ?>" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">?? <?php echo $isEdit ? 'C?p nh?t' : 'Th�m'; ?></button>
                        <a href="<?php echo Config::get('APP_URL'); ?>admin/vouchers.php" class="btn btn-cancel">? H?y</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

