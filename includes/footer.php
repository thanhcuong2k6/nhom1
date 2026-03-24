        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Về FashionHub</h3>
                    <p>FashionHub là cửa hàng thời trang trực tuyến hàng đầu, cung cấp các bộ sưu tập quần áo chất lượng cao với giá cạnh tranh.</p>
                </div>

                <div class="footer-section">
                    <h3>Liên kết nhanh</h3>
                    <ul>
                        <li><a href="<?php echo Config::get('APP_URL'); ?>">Trang chủ</a></li>
                        <li><a href="<?php echo Config::get('APP_URL'); ?>/pages/products.php">Sản phẩm</a></li>
                        <li><a href="<?php echo Config::get('APP_URL'); ?>/pages/about.php">Về chúng tôi</a></li>
                        <li><a href="<?php echo Config::get('APP_URL'); ?>/pages/contact.php">Liên hệ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Chính sách</h3>
                    <ul>
                        <li><a href="#">Điều khoản dịch vụ</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Chính sách thanh toán</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Liên hệ</h3>
                    <p>
                        <i class="fas fa-phone"></i> +84 123 456 789<br>
                        <i class="fas fa-envelope"></i> support@fashionhub.local<br>
                        <i class="fas fa-map-marker"></i> Hà Nội, Việt Nam
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024-2026 FashionHub. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo Config::get('APP_URL'); ?>/js/main.js"></script>
</body>
</html>
