<?php
/**
 * Contact Page - Trang liên hệ
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();

$pageTitle = 'Liên hệ chúng tôi';

// Xử lý form
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // Validate
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Vui lòng nhập tên';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vui lòng nhập email hợp lệ';
    }

    if (empty($subject)) {
        $errors[] = 'Vui lòng nhập tiêu đề';
    }

    if (empty($content)) {
        $errors[] = 'Vui lòng nhập nội dung tin nhắn';
    }

    if (empty($errors)) {
        // Gửi email (tạm thời chỉ lưu thông báo)
        $message = '✅ Cảm ơn bạn đã liên hệ! Chúng tôi sẽ trả lời trong vòng 24 giờ.';
        
        // TODO: Gửi email thực tế
        // $to = 'admin@fashionhub.local';
        // $headers = "From: " . $email . "\r\n";
        // mail($to, $subject, $content, $headers);
        
    } else {
        $error = implode('<br>', $errors);
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="contact-section">
    <div class="contact-container">
        <div class="contact-header">
            <h1>📞 Liên hệ chúng tôi</h1>
            <p>Có câu hỏi hay ý kiến? Hãy liên hệ với chúng tôi</p>
        </div>

        <div class="contact-content">
            <!-- Thông tin liên hệ -->
            <div class="contact-info">
                <div class="info-card">
                    <div class="info-icon">📍</div>
                    <h3>Địa chỉ</h3>
                    <p>
                        FashionHub Shop<br>
                        123 Đường Nguyễn Huệ<br>
                        Quận 1, TP. Hồ Chí Minh
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-icon">📱</div>
                    <h3>Điện thoại</h3>
                    <p>
                        <a href="tel:+84123456789">+84 (123) 456 789</a><br>
                        <a href="tel:+84987654321">+84 (987) 654 321</a>
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-icon">✉️</div>
                    <h3>Email</h3>
                    <p>
                        <a href="mailto:info@fashionhub.com">info@fashionhub.com</a><br>
                        <a href="mailto:support@fashionhub.com">support@fashionhub.com</a>
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-icon">🕐</div>
                    <h3>Giờ làm việc</h3>
                    <p>
                        Thứ 2 - Thứ 6: 09:00 - 21:00<br>
                        Thứ 7: 10:00 - 20:00<br>
                        Chủ nhật: 10:00 - 18:00
                    </p>
                </div>

                <div class="social-links">
                    <a href="https://facebook.com" target="_blank" title="Facebook">
                        <span>f</span>
                    </a>
                    <a href="https://instagram.com" target="_blank" title="Instagram">
                        <span>📷</span>
                    </a>
                    <a href="https://twitter.com" target="_blank" title="Twitter">
                        <span>𝕏</span>
                    </a>
                    <a href="https://youtube.com" target="_blank" title="YouTube">
                        <span>▶️</span>
                    </a>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div class="contact-form-wrapper">
                <div class="form-card">
                    <h2>📧 Gửi tin nhắn cho chúng tôi</h2>

                    <?php if ($message): ?>
                        <div class="success-message">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="error-message">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="contact-form">
                        <div class="form-group">
                            <label for="name">Tên của bạn *</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                placeholder="Nhập tên của bạn"
                                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    placeholder="example@email.com"
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="phone">Điện thoại</label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    placeholder="0123456789"
                                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Tiêu đề *</label>
                            <input 
                                type="text" 
                                id="subject" 
                                name="subject" 
                                placeholder="Tiêu đề tin nhắn"
                                value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="content">Nội dung *</label>
                            <textarea 
                                id="content" 
                                name="content" 
                                rows="6" 
                                placeholder="Nhập nội dung tin nhắn của bạn..."
                                required
                            ><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-submit">
                            ✉️ Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="contact-map">
            <h2>📍 Vị trí của chúng tôi</h2>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4759652369566!2d106.71827!3d10.7769!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3ec1234567%3A0x1234567890abcdef!2sFashionHub!5e0!3m2!1svi!2svn!4v1234567890" 
                width="100%" 
                height="400" 
                style="border:0; border-radius: 10px;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>❓ Câu hỏi thường gặp</h2>
            
            <div class="faq-grid">
                <div class="faq-item">
                    <h3>Bao lâu tôi sẽ nhận được hàng?</h3>
                    <p>Thông thường đơn hàng đến trong 3-7 ngày làm việc. Chúng tôi sẽ gửi mã vận đơn để bạn theo dõi.</p>
                </div>

                <div class="faq-item">
                    <h3>Có miễn phí vận chuyển không?</h3>
                    <p>Có miễn phí vận chuyển cho đơn hàng từ 500.000đ trở lên trong nội thành Hồ Chí Minh.</p>
                </div>

                <div class="faq-item">
                    <h3>Làm sao để trả hàng?</h3>
                    <p>Bạn có thể trả hàng trong vòng 30 ngày kể từ ngày nhận. Hàng phải còn nguyên vẹn và chưa qua sử dụng.</p>
                </div>

                <div class="faq-item">
                    <h3>Có hỗ trợ thanh toán nào?</h3>
                    <p>Chúng tôi chấp nhận thanh toán bằng thẻ tín dụng, ví điện tử, chuyển khoản ngân hàng và COD.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-section {
    padding: 40px 20px;
    background: #f8f9fa;
}

.contact-container {
    max-width: 1200px;
    margin: 0 auto;
}

.contact-header {
    text-align: center;
    margin-bottom: 50px;
}

.contact-header h1 {
    font-size: 36px;
    color: #333;
    margin: 0 0 10px;
}

.contact-header p {
    color: #666;
    font-size: 18px;
    margin: 0;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 50px;
}

.contact-info {
    display: grid;
    gap: 20px;
}

.info-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.info-icon {
    font-size: 32px;
    margin-bottom: 15px;
}

.info-card h3 {
    font-size: 18px;
    color: #333;
    margin: 0 0 10px;
}

.info-card p {
    color: #666;
    margin: 0;
    line-height: 1.6;
}

.info-card a {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}

.info-card a:hover {
    text-decoration: underline;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 10px;
    justify-content: center;
}

.social-links a {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-links a:hover {
    transform: scale(1.1);
}

.contact-form-wrapper {
    display: flex;
    flex-direction: column;
}

.form-card {
    background: white;
    padding: 35px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-card h2 {
    font-size: 24px;
    color: #333;
    margin: 0 0 30px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group input,
.form-group textarea {
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 14px 30px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.success-message {
    background: #d1fae5;
    border: 1px solid #6ee7b7;
    color: #065f46;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 600;
}

.error-message {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.contact-map {
    background: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 50px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.contact-map h2 {
    font-size: 24px;
    color: #333;
    margin: 0 0 20px;
}

.faq-section {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.faq-section h2 {
    font-size: 28px;
    color: #333;
    margin: 0 0 30px;
    text-align: center;
}

.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.faq-item {
    padding: 25px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.faq-item h3 {
    font-size: 16px;
    color: #333;
    margin: 0 0 10px;
}

.faq-item p {
    color: #666;
    margin: 0;
    line-height: 1.6;
    font-size: 14px;
}

@media (max-width: 768px) {
    .contact-section {
        padding: 20px 10px;
    }

    .contact-header h1 {
        font-size: 24px;
    }

    .contact-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .faq-grid {
        grid-template-columns: 1fr;
    }

    .contact-map iframe {
        height: 300px;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
