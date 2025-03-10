<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email == "demo@example.com" && $password == "password") {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = "Demo User";
        $isLoggedIn = true;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $loginError = "Email atau password salah";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;
    
    if ($password != $confirm_password) {
        $registerError = "Password tidak cocok";
    } elseif (!$terms) {
        $registerError = "Anda harus menyetujui syarat dan ketentuan";
    } else {
        $_SESSION['user_id'] = 2;
        $_SESSION['user_name'] = $name;
        $isLoggedIn = true;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['forgot'])) {
    $email = $_POST['email'] ?? '';
    
    $resetSuccess = "Email reset password telah dikirim ke $email";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $rating = $_POST['rating'] ?? 0;
    $review_text = $_POST['review_text'] ?? '';
    
    $reviewSuccess = "Terima kasih atas ulasan Anda!";
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$popularVehicles = [
    [
        'image' => 'images/1 White.png',
        'name' => 'Rush GR Sport',
        'rating' => 4.5,
        'reviews' => 196,
        'price' => 950000
    ],
    [
        'image' => 'images/faz-pink.png',
        'name' => 'Yamaha Fazzio',
        'rating' => 4.5,
        'reviews' => 131,
        'price' => 120000
    ],
    [
        'image' => 'images/wulingpink.png',
        'name' => 'Wuling Air EV',
        'rating' => 4.5,
        'reviews' => 326,
        'price' => 800000
    ]
];

$customerReviews = [
    [
        'rating' => 5,
        'text' => 'Pelayanan sangat memuaskan, kendaraan bersih dan terawat. Proses pemesanan juga mudah dan cepat.',
        'name' => 'Timora L.',
        'occupation' => 'Mahasiswa'
    ],
    [
        'rating' => 4,
        'text' => 'Mobil yang disewakan dalam kondisi prima, harga sewa juga terjangkau. Recommended!',
        'name' => 'Dalila N.',
        'occupation' => 'Pengusaha'
    ]
];

$teamMembers = [
    [
        'name' => 'Aqilah Azzahra Khoirunnisa',
        'nim' => '2311103129',
        'job' => 'Back End'
    ],
    [
        'name' => 'Halimah Ummulizah',
        'nim' => '2311103150',
        'job' => 'Database & Testing'
    ],
    [
        'name' => 'Made Putri Viona',
        'nim' => '2311103109',
        'job' => 'Front End'
    ],
    [
        'name' => 'Timora Lestenia',
        'nim' => '2311103040',
        'job' => 'Server & Testing'
    ]
];

function formatPrice($price) {
    return 'Rp' . number_format($price, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickRent - Rental Mobil & Motor Purwokerto</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-section:target .auth-content {
            animation: fadeInUp 0.5s;
        }
        
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            z-index: 1000;
        }
        .popup button {
            margin-top: 10px;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>Quick<span>Rent</span></h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="about.php">Tentang Kami</a></li>
                <li><a href="#reviews">Ulasan</a></li>
                <li><a href="#contact">Kontak</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="#">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
                    <li><a href="?logout=1">Keluar</a></li>
                <?php else: ?>
                    <li><a href="#login-section" class="auth-link">Masuk</a></li>
                    <li><a href="#register-section" class="auth-link">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Temukan solusi transportasi terbaik untuk kebutuhan Anda di Purwokerto dengan harga terjangkau dan pelayanan prima</h1>
            <div class="filter-box">
                <div class="filter-tabs">
                    <button class="tab-button active" onclick="showFilter('mobil')">🚗 Rental Mobil</button>
                    <button class="tab-button" onclick="showFilter('motor')">🛵 Rental Motor</button>
                </div>
                <div id="filter-mobil" class="filter-content active">
                </div>
                
                <div id="filter-motor" class="filter-content">
                </div>
                <div class="filter-options">
                    <select><option>🚗 Semua Brand</option></select>
                    <select><option>⚙️ Semua Transmisi</option></select>
                    <select><option>📆 2025</option></select>
                    <select><option>🧑‍✈️ Dengan pengemudi</option></select>
                </div>
                <button class="search-button">Cari sekarang</button>
            </div>
        </div>
    </section>

    <section class="about-section">
        <h2>Rental Mobil dan Motor Terbaik di Purwokerto - QuickRent</h2>
        <p>Selamat datang di QuickRent, aplikasi penyewaan mobil dan motor terdepan di Purwokerto yang dirancang untuk memberikan kemudahan dan 
            fleksibilitas dalam memenuhi kebutuhan transportasi Anda. Dengan QuickRent, Anda dapat dengan mudah menemukan dan menyewa berbagai jenis kendaraan, 
            mulai dari mobil keluarga yang nyaman, SUV tangguh, hingga motor lincah untuk mobilitas perkotaan. </p>
        <p>Kami memahami bahwa setiap perjalanan memiliki kebutuhan yang berbeda. Oleh karena itu, QuickRent menawarkan beragam 
            pilihan kendaraan terbaru dengan kondisi prima, yang selalu kami jaga melalui perawatan berkala. Baik Anda membutuhkan kendaraan 
            untuk perjalanan wisata, urusan bisnis, atau sekadar mobilitas sehari-hari, QuickRent hadir dengan solusi yang tepat.</p>
    </section>
    
    <section id="search" class="search-section">
        <div class="section-title">
            <h2>Kendaraan Populer 🔥</h2>
        </div>
        <center>
        <div class="team-buttons">
            <?php foreach ($teamMembers as $index => $member): ?>
                <button onclick="showPopup('popup<?php echo $index + 1; ?>')">Lihat Identitas Pembuat <?php echo $index + 1; ?></button>
            <?php endforeach; ?>
        </div>

        <?php foreach ($teamMembers as $index => $member): ?>
            <div id="popup<?php echo $index + 1; ?>" class="popup">
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($member['name']); ?></p>
                <p><strong>NIM:</strong> <?php echo htmlspecialchars($member['nim']); ?></p>
                <p><strong>Jobs:</strong> <?php echo htmlspecialchars($member['job']); ?></p>
                <button onclick="closePopup('popup<?php echo $index + 1; ?>')">Tutup</button>
            </div>
        <?php endforeach; ?>
        </center>
        
        <div class="vehicle-grid" id="vehicle-list">
            <?php foreach ($popularVehicles as $vehicle): ?>
                <div class="vehicle-card">
                    <img src="<?php echo htmlspecialchars($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
                    <div class="vehicle-info">
                        <div class="rating">
                            <?php
                            $fullStars = floor($vehicle['rating']);
                            $halfStar = $vehicle['rating'] - $fullStars >= 0.5;
                            
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $fullStars) {
                                    echo '<span class="star filled">★</span>';
                                } elseif ($i == $fullStars + 1 && $halfStar) {
                                    echo '<span class="star half-filled">★</span>';
                                } else {
                                    echo '<span class="star">★</span>';
                                }
                            }
                            ?>
                            <span class="review-count">(<?php echo $vehicle['reviews']; ?>)</span>
                        </div>
                        <h3><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                        <hr>
                        <p class="price"><?php echo formatPrice($vehicle['price']); ?> <span class="per-day">/Per Hari</span></p>
                        <a href="booking.php?vehicle=<?php echo urlencode($vehicle['name']); ?>" class="sewa-sekarang">Sewa Sekarang <span class="arrow">›</span></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
        
    <section id="reviews" class="reviews-section">
        <div class="section-title">
            <h2>Testimoni Pelanggan Kami💖</h2>
        </div>
        <div class="reviews-container">
            <?php foreach ($customerReviews as $review): ?>
                <div class="review-card">
                    <div class="rating">
                        <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?>
                    </div>
                    <p class="review-text"><?php echo htmlspecialchars($review['text']); ?></p>
                    <p class="reviewer"><?php echo htmlspecialchars($review['name']); ?> <span>- <?php echo htmlspecialchars($review['occupation']); ?></span></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (isset($reviewSuccess)): ?>
            <div class="alert alert-success"><?php echo $reviewSuccess; ?></div>
        <?php endif; ?>
        
        <form class="review-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>#reviews">
            <h3>Berikan Ulasan Anda</h3>
            <div class="rating-input">
                <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
            </div>
            <textarea name="review_text" placeholder="Tulis ulasan Anda di sini"></textarea>
            <button type="submit" name="submit_review" class="form-submit">Kirim Ulasan</button>
        </form>
    </section>

    <section id="login-section" class="auth-section">
        <div class="auth-container">
            <div class="auth-content">
                <a href="#" class="close-button">&times;</a>
                <h2>Masuk</h2>
                <?php if (isset($loginError)): ?>
                    <div class="alert alert-danger"><?php echo $loginError; ?></div>
                <?php endif; ?>
                <form class="auth-form" action="<?php echo $_SERVER['PHP_SELF']; ?>#login-section" method="POST">
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <div class="form-group remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ingat Saya</label>
                    </div>
                    <button type="submit" name="login" class="auth-button">Masuk</button>
                    <p class="forgot-password"><a href="#forgot-section">Lupa password?</a></p>
                    <p class="switch-form">Belum punya akun? <a href="#register-section">Daftar disini</a></p>
                </form>
            </div>
        </div>
    </section>

    <section id="register-section" class="auth-section">
        <div class="auth-container">
            <div class="auth-content">
                <a href="#" class="close-button">&times;</a>
                <h2>Daftar</h2>
                <?php if (isset($registerError)): ?>
                    <div class="alert alert-danger"><?php echo $registerError; ?></div>
                <?php endif; ?>
                <form class="auth-form" action="<?php echo $_SERVER['PHP_SELF']; ?>#register-section" method="POST">
                    <div class="form-group">
                        <label for="register-name">Nama Lengkap</label>
                        <input type="text" id="register-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-phone">No. HP</label>
                        <input type="tel" id="register-phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="register-confirm-password">Konfirmasi Password</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" required>
                    </div>
                    <div class="form-group terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">Saya menyetujui <a href="#">Syarat dan Ketentuan</a></label>
                    </div>
                    <button type="submit" name="register" class="auth-button">Daftar</button>
                    <p class="switch-form">Sudah punya akun? <a href="#login-section">Masuk disini</a></p>
                </form>
            </div>
        </div>
    </section>

    <section id="forgot-section" class="auth-section">
        <div class="auth-container">
            <div class="auth-content">
                <a href="#" class="close-button">&times;</a>
                <h2>Lupa Password</h2>
                <?php if (isset($resetSuccess)): ?>
                    <div class="alert alert-success"><?php echo $resetSuccess; ?></div>
                <?php endif; ?>
                <form class="auth-form" action="<?php echo $_SERVER['PHP_SELF']; ?>#forgot-section" method="POST">
                    <div class="form-group">
                        <label for="forgot-email">Email</label>
                        <input type="email" id="forgot-email" name="email" required>
                    </div>
                    <button type="submit" name="forgot" class="auth-button">Reset Password</button>
                    <p class="switch-form"><a href="#login-section">Kembali ke halaman login</a></p>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>QuickRent</h3>
                <p>Kami melayani sewa sesuai dengan opsi kebutuhan Anda yaitu per 12 jam, 24 jam, harian, mingguan, dan bulanan.</p>
                <p>Menyediakan layanan rental mobil dan motor berkualitas dengan pilihan kendaraan terlengkap, harga terjangkau, dan proses pemesanan yang mudah untuk memenuhi kebutuhan transportasi Anda selama di kota ini, kami ahlinya.</p>
            </div>
            <div class="footer-section">
                <h3>Kontak Kami</h3>
                <p>Alamat: Jl. Kauman Lama No.26, Purwokerto Barat, Jawa Tengah</p>
                <p>Telp: 0812-9283-9982</p>
                <p>Email: vionasptrsduasa@quickrent.com</p>
                <h4>Jam Operasional</h4>
                <p>Senin - Minggu: 08.00 - 22.00</p>
                <h4>Sosial Media</h4>
                <div class="social-icons">
                    <a href="https://wa.me/6289508847208" target="_blank">
                        <img src ="images/whatsapp-icon.png">
                    </a>
                    <a href="https://www.instagram.com/quickrent__?igsh=bnUxNDlsZWF3eGdq" target="_blank">
                        <img src="images/instagram-icon.png">
                    </a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Alamat</h3>
                <img src="images/map-placeholder.png" alt="Peta Lokasi" class="footer-map">
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> QuickRent. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function showPopup(id) {
            document.getElementById(id).style.display = "block";
        }
        function closePopup(id) {
            document.getElementById(id).style.display = "none";
        }
        
        function showFilter(type) {
            const contents = document.getElementsByClassName('filter-content');
            for (let i = 0; i < contents.length; i++) {
                contents[i].classList.remove('active');
            }
            
            const buttons = document.getElementsByClassName('tab-button');
            for (let i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }
            
            document.getElementById('filter-' + type).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
