<?php
/**
 * About Page - Trang về chúng tôi
 */

session_start();
require_once __DIR__ . '/../config/Config.php';
Config::load();

$pageTitle = 'Về chúng tôi';

include __DIR__ . '/../includes/header.php';
?>

<div class="about-section">
    <div class="about-container">
        <!-- Hero Section -->
        <div class="about-hero">
            <h1>👗 Về FashionHub</h1>
            <p>Khám phá câu chuyện về brand thời trang của chúng tôi</p>
        </div>

        <!-- Story Section -->
        <div class="about-story">
            <div class="story-content">
                <h2>📖 Câu chuyện của chúng tôi</h2>
                <p>
                    FashionHub được thành lập vào năm 2020 với một tầm nhìn đơn giản: 
                    mang những thiết kế thời trang chất lượng cao đến với mọi người. 
                    Chúng tôi tin rằng mỗi người đều xứng đáng mặc những bộ quần áo 
                    tuyệt vời, giá cả phải chăng và phù hợp với phong cách cá nhân của họ.
                </p>
                <p>
                    Từ những ngày đầu nhỏ bé, chúng tôi đã lớn lên thành một trong những 
                    điểm đến hàng đầu cho các tín đồ thời trang trong toàn thành phố. 
                    Sự thành công của chúng tôi là nhờ vào sự tin tưởng và ủng hộ của 
                    các khách hàng tuyệt vời như bạn.
                </p>
            </div>
            <div class="story-image">
                <div class="placeholder-image">
                    <span>👚</span>
                </div>
            </div>
        </div>

        <!-- Mission Section -->
        <div class="mission-section">
            <h2>🎯 Sứ mệnh & Giá trị</h2>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">⭐</div>
                    <h3>Chất lượng hàng đầu</h3>
                    <p>
                        Chúng tôi chỉ lựa chọn những sản phẩm tốt nhất từ những nhà cung cấp 
                        uy tín, đảm bảo mỗi sản phẩm đều đạt tiêu chuẩn cao nhất.
                    </p>
                </div>

                <div class="value-card">
                    <div class="value-icon">💙</div>
                    <h3>Tâm huyết khách hàng</h3>
                    <p>
                        Khách hàng là trung tâm của tất cả những gì chúng tôi làm. 
                        Chúng tôi luôn lắng nghe, học hỏi và cải thiện dựa trên phản hồi 
                        của bạn.
                    </p>
                </div>

                <div class="value-card">
                    <div class="value-icon">♻️</div>
                    <h3>Bền vững môi trường</h3>
                    <p>
                        Chúng tôi cam kết sử dụng các vật liệu thân thiện với môi trường 
                        và giảm thiểu tác động tiêu cực đến hành tinh của chúng ta.
                    </p>
                </div>

                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Công bằng & Minh bạch</h3>
                    <p>
                        Chúng tôi tin vào việc thương mại công bằng và giá cả minh bạch. 
                        Không có ẩn số, không có biếu xén.
                    </p>
                </div>

                <div class="value-card">
                    <div class="value-icon">💡</div>
                    <h3>Đổi mới sáng tạo</h3>
                    <p>
                        Chúng tôi liên tục tìm kiếm những cách mới để cải thiện trải nghiệm 
                        mua sắm của bạn.
                    </p>
                </div>

                <div class="value-card">
                    <div class="value-icon">🌍</div>
                    <h3>Cộng đồng toàn cầu</h3>
                    <p>
                        Chúng tôi tự hào phục vụ những khách hàng từ khắp nơi trên thế giới 
                        và xây dựng một cộng đồng đầy đủ năng lượng.
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="team-section">
            <h2>👥 Đội ngũ chúng tôi</h2>
            <p class="section-subtitle">
                Được tạo bởi những người đam mê thời trang và công nghệ
            </p>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-avatar">N</div>
                    <h3>Nguyễn Minh A</h3>
                    <p class="member-role">👑 Founder & CEO</p>
                    <p class="member-bio">
                        Người sáng lập FashionHub với 10 năm kinh nghiệm trong ngành thời trang.
                    </p>
                </div>

                <div class="team-member">
                    <div class="member-avatar">T</div>
                    <h3>Trần Thị B</h3>
                    <p class="member-role">🎨 Creative Director</p>
                    <p class="member-bio">
                        Quản lý các bộ sưu tập thiết kế và đảm bảo chất lượng hình ảnh.
                    </p>
                </div>

                <div class="team-member">
                    <div class="member-avatar">P</div>
                    <h3>Phạm Văn C</h3>
                    <p class="member-role">💻 CTO</p>
                    <p class="member-bio">
                        Lãnh đạo đội ngũ công nghệ xây dựng nền tảng thương mại điện tử.
                    </p>
                </div>

                <div class="team-member">
                    <div class="member-avatar">L</div>
                    <h3>Lê Thị D</h3>
                    <p class="member-role">📊 Operations Manager</p>
                    <p class="member-bio">
                        Quản lý các hoạt động kinh doanh hàng ngày và chuỗi cung ứng.
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <h2>📈 Con số ấn tượng</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">50K+</div>
                    <p>Khách hàng hài lòng</p>
                </div>

                <div class="stat-card">
                    <div class="stat-number">5000+</div>
                    <p>Sản phẩm</p>
                </div>

                <div class="stat-card">
                    <div class="stat-number">24/7</div>
                    <p>Hỗ trợ khách hàng</p>
                </div>

                <div class="stat-card">
                    <div class="stat-number">99%</div>
                    <p>Đánh giá tích cực</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="about-cta">
            <h2>Sẵn sàng bắt đầu mua sắm?</h2>
            <p>Khám phá bộ sưu tập thời trang mới nhất của chúng tôi ngay hôm nay</p>
            <a href="<?php echo Config::get('APP_URL'); ?>pages/products.php" class="btn btn-primary">
                🛍️ Mua sắm ngay
            </a>
        </div>
    </div>
</div>

<style>
.about-section {
    padding: 40px 20px;
    background: #f8f9fa;
}

.about-container {
    max-width: 1200px;
    margin: 0 auto;
}

.about-hero {
    text-align: center;
    margin-bottom: 60px;
}

.about-hero h1 {
    font-size: 40px;
    color: #333;
    margin: 0 0 10px;
}

.about-hero p {
    font-size: 18px;
    color: #666;
    margin: 0;
}

.about-story {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 60px;
    align-items: center;
}

.story-content h2 {
    font-size: 28px;
    color: #333;
    margin: 0 0 20px;
}

.story-content p {
    color: #666;
    line-height: 1.8;
    margin: 0 0 15px;
    font-size: 16px;
}

.story-image {
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.placeholder-image {
    aspect-ratio: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 80px;
}

.mission-section h2 {
    text-align: center;
    font-size: 32px;
    color: #333;
    margin: 0 0 40px;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.value-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.value-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.value-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.value-card h3 {
    font-size: 20px;
    color: #333;
    margin: 0 0 15px;
}

.value-card p {
    color: #666;
    line-height: 1.6;
    margin: 0;
}

.team-section h2 {
    text-align: center;
    font-size: 32px;
    color: #333;
    margin: 0 0 10px;
}

.section-subtitle {
    text-align: center;
    color: #666;
    margin: 0 0 40px;
    font-size: 16px;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.team-member {
    background: white;
    padding: 30px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.team-member:hover {
    transform: translateY(-5px);
}

.member-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    color: white;
    font-weight: bold;
    margin: 0 auto 15px;
}

.team-member h3 {
    font-size: 18px;
    color: #333;
    margin: 0 0 5px;
}

.member-role {
    color: #667eea;
    font-weight: 600;
    margin: 0 0 10px;
}

.member-bio {
    color: #666;
    font-size: 14px;
    margin: 0;
    line-height: 1.5;
}

.stats-section h2 {
    text-align: center;
    font-size: 32px;
    color: #333;
    margin: 0 0 40px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-card p {
    font-size: 16px;
    margin: 0;
}

.about-cta {
    background: white;
    padding: 60px 40px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.about-cta h2 {
    font-size: 28px;
    color: #333;
    margin: 0 0 10px;
}

.about-cta p {
    color: #666;
    font-size: 16px;
    margin: 0 0 30px;
}

.btn {
    display: inline-block;
    padding: 14px 40px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

@media (max-width: 768px) {
    .about-section {
        padding: 20px 10px;
    }

    .about-hero h1 {
        font-size: 28px;
    }

    .about-story {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .values-grid,
    .team-grid {
        grid-template-columns: 1fr;
    }

    .about-cta {
        padding: 40px 20px;
    }

    .about-cta h2 {
        font-size: 24px;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
