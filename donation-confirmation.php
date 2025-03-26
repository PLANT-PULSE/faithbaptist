<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if payment was successful
$payment_success = $_SESSION['payment_success'] ?? false;
$donation_data = $_SESSION['donation_data'] ?? [];
$payment_error = $_SESSION['payment_error'] ?? '';

// Clear session data
unset($_SESSION['payment_success']);
unset($_SESSION['donation_data']);
unset($_SESSION['payment_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $payment_success ? 'Thank You for Your Donation' : 'Donation Status'; ?> - Faith Baptist Church</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .donation-success {
            text-align: center;
            padding: 30px 0;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #d4edda;
            color: #28a745;
            font-size: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            margin: 0 auto 20px;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background-color: #f8d7da;
            color: #dc3545;
            font-size: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            margin: 0 auto 20px;
        }
        
        .donation-details {
            background-color: var(--gray-light);
            padding: 20px;
            border-radius: var(--border-radius);
            max-width: 500px;
            margin: 0 auto 20px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader"></div>
    </div>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Header -->
    <header id="header" class="scrolled">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="/images/logo.jpg" alt="Faith Baptist Church Logo" width="50" height="50" style="margin-right: 10px; vertical-align: middle;">
                    Faith Baptist <span>Church</span>
                </a>
            </div>
            <nav>
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#about">About</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="ministries.html">Ministries</a></li>
                    <li><a href="index.php#events">Events</a></li>
                    <li><a href="sermons.html">Sermons</a></li>
                    <li><a href="index.php#gallery">Gallery</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                    <li><a href="donate.html" class="donate-btn active">Donate</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner" style="background-image: url('images/WhatsApp Image 2025-02-25 at 08.09.51_39099767.jpg'); background-size: cover; background-position: center; height: 300px; display: flex; align-items: center; position: relative;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; color: white;">
                <h1 style="font-size: 42px; margin-bottom: 15px;"><?php echo $payment_success ? 'Thank You for Your Donation' : 'Donation Status'; ?></h1>
                <div>
                    <a href="index.php" style="color: white; transition: color 0.3s;">Home</a>
                    <span style="margin: 0 10px;">/</span>
                    <a href="donate.html" style="color: white; transition: color 0.3s;">Donate</a>
                    <span style="margin: 0 10px;">/</span>
                    <span>Confirmation</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Confirmation Section -->
    <section class="section-padding">
        <div class="container">
            <div class="donation-card">
                <?php if ($payment_success && !empty($donation_data)): ?>
                    <div class="donation-success">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h3 style="margin-bottom: 15px;">Thank You for Your Donation!</h3>
                        <p style="margin-bottom: 20px;">Your generous contribution helps us continue our mission and serve our community. A confirmation email has been sent to your registered email address with the details of your donation.</p>
                        
                        <div class="donation-details">
                            <div class="detail-item">
                                <span>Donation Amount:</span>
                                <span>$<?php echo number_format($donation_data['amount'] / 100, 2); ?></span>
                            </div>
                            <?php if ($donation_data['frequency'] !== 'one-time'): ?>
                            <div class="detail-item">
                                <span>Frequency:</span>
                                <span><?php echo ucfirst($donation_data['frequency']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="detail-item">
                                <span>Purpose:</span>
                                <span><?php echo $donation_data['purpose']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span>Transaction ID:</span>
                                <span><?php echo $donation_data['transaction_id']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span>Date:</span>
                                <span><?php echo $donation_data['payment_date']; ?></span>
                            </div>
                        </div>
                        
                        <p style="margin-bottom: 20px;">If you have any questions about your donation, please contact our church office.</p>
                        
                        <a href="index.php" class="btn btn-primary">Return to Homepage</a>
                    </div>
                <?php else: ?>
                    <div class="donation-success">
                        <div class="error-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <h3 style="margin-bottom: 15px;">Payment Not Completed</h3>
                        <p style="margin-bottom: 20px;"><?php echo !empty($payment_error) ? $payment_error : 'There was an issue processing your donation. Please try again or contact our church office for assistance.'; ?></p>
                        
                        <div style="margin-bottom: 20px;">
                            <a href="donate.html" class="btn btn-primary">Try Again</a>
                            <a href="index.php" class="btn btn-outline" style="margin-left: 10px;">Return to Homepage</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <h4>Faith Baptist Church</h4>
                    <p>A place of worship, community, and spiritual growth where everyone is welcome.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/543957330"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://www.youtube.com"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About</a></li>
                        <li><a href="index.php#services">Services</a></li>
                        <li><a href="ministries.html">Ministries</a></li>
                        <li><a href="index.php#events">Events</a></li>
                        <li><a href="sermons.html">Sermons</a></li>
                    </ul>
                </div>
                <div class="footer-services">
                    <h5>Service Times</h5>
                    <ul>
                        <li><a href="index.php#services">Sunday: 8:00 AM - 11:00 AM</a></li>
                        <li><a href="index.php#services">Wednesday: 7:00 PM - 9:00 PM</a></li>
                        <li><a href="index.php#services">Friday: 7:00 PM - 9:00 PM</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h5>Newsletter</h5>
                    <p>Subscribe to our newsletter for updates and announcements.</p>
                    <form class="newsletter-form">
                        <div class="form-group">
                            <input type="email" placeholder="Your email">
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copyright">
                    <p>&copy; 2025 Faith Baptist Church. All rights reserved.</p>
                </div>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

