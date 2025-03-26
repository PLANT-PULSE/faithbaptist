<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once 'config.php';

// Check for errors or form data from previous submission
$errors = $_SESSION['donation_errors'] ?? [];
$form_data = $_SESSION['donation_form_data'] ?? [];

// Clear session data
unset($_SESSION['donation_errors']);
unset($_SESSION['donation_form_data']);

// Get Paystack public key
$paystack_public_key = PAYSTACK_PUBLIC_KEY;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate - Faith Baptist Church</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Additional styles for donation page */
        .donation-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
        }
        
        .donation-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .tab-btn {
            flex: 1;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            font-weight: 500;
            border-bottom: 3px solid transparent;
        }
        
        .tab-btn.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .amount-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .amount-btn {
            padding: 10px;
            text-align: center;
            border: 2px solid var(--gray-light);
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .amount-btn:hover {
            border-color: var(--primary);
        }
        
        .amount-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .custom-amount {
            margin-top: 15px;
            display: none;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .payment-btn {
            padding: 15px;
            text-align: center;
            border: 2px solid var(--gray-light);
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .payment-btn:hover {
            border-color: var(--primary);
        }
        
        .payment-btn.active {
            border-color: var(--primary);
        }
        
        .payment-btn i {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .card-details {
            margin-top: 20px;
            display: none;
        }
        
        .donation-summary {
            background-color: var(--gray-light);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            padding-top: 10px;
            margin-top: 10px;
            border-top: 1px solid var(--gray);
        }
        
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
         .why-give {
            margin-bottom: 40px;
        }
        
        .giving-reason {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .reason-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(93, 59, 140, 0.1);
            color: var(--primary);
            font-size: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        .reason-content h4 {
            margin-bottom: 10px;
        }
        
        .bible-quote {
            background-color: var(--gray-light);
            padding: 30px;
            border-radius: var(--border-radius);
            margin-top: 30px;
        }
        
        .bible-quote i {
            font-size: 30px;
            color: var(--primary);
            margin-bottom: 15px;
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
                    <img src="./images/logo.jpg" alt="Faith Baptist Church Logo" width="50" height="50" style="margin-right: 10px; vertical-align: middle;">
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
                    <li><a href="donate.php" class="donate-btn active">Donate</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner" style="background-image: url('./images/'); background-size: cover; background-position: center; height: 300px; display: flex; align-items: center; position: relative;">
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; color: white;">
                <h1 style="font-size: 42px; margin-bottom: 15px;">Support Our Church</h1>
                <div>
                    <a href="index.php" style="color: white; transition: color 0.3s;">Home</a>
                    <span style="margin: 0 10px;">/</span>
                    <span>Donate</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-header text-center">
                <h6 class="reveal-top">GIVING</h6>
                <h2 class="reveal-top">Support Our Ministry</h2>
                <p class="reveal-top">Your generous donations help us continue our mission and serve our community</p>
            </div>

            <div class="row" style="display: flex; flex-wrap: wrap; gap: 30px;">
                <!-- Why Give Section -->
                <div class="why-give reveal-left" style="flex: 1; min-width: 300px;">
                    <h3 style="margin-bottom: 20px;">Why Give?</h3>
                    <p class="lead" style="margin-bottom: 20px;">Your financial support enables us to continue our mission of spreading God's love and making a difference in our community and beyond.</p>
                    <p style="margin-bottom: 20px;">When you give to Faith Baptist Church, you're supporting:</p>

                    <div class="giving-reason">
                        <div class="reason-icon">
                            <i class="fas fa-church"></i>
                        </div>
                        <div class="reason-content">
                            <h4>Church Operations</h4>
                            <p>Maintaining our facilities and supporting our staff so we can continue to provide a place of worship and community.</p>
                        </div>
                    </div>

                    <div class="giving-reason">
                        <div class="reason-icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <div class="reason-content">
                            <h4>Community Outreach</h4>
                            <p>Funding programs that serve those in need in our local community through food drives, clothing donations, and more.</p>
                        </div>
                    </div>

                    <div class="giving-reason">
                        <div class="reason-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="reason-content">
                            <h4>Missions</h4>
                            <p>Supporting missionaries and mission projects both locally and around the world to spread the Gospel.</p>
                        </div>
                    </div>

                    <div class="giving-reason">
                        <div class="reason-icon">
                            <i class="fas fa-baby"></i>
                        </div>
                        <div class="reason-content">
                            <h4>Children & Youth Programs</h4>
                            <p>Investing in the next generation through our children's and youth ministries, providing resources for their spiritual growth.</p>
                        </div>
                    </div>

                    <div class="bible-quote">
                        <i class="fas fa-quote-left"></i>
                        <p style="font-style: italic; margin-bottom: 10px;">"Each of you should give what you have decided in your heart to give, not reluctantly or under compulsion, for God loves a cheerful giver."</p>
                        <span>- 2 Corinthians 9:7</span>
                    </div>
                </div>

                <!-- Donation Form -->
                <div class="donation-form reveal-right" style="flex: 1; min-width: 300px;">
                    <div class="donation-card">
                        <h3 style="margin-bottom: 20px;">Make a Donation</h3>

                        <?php if (!empty($errors)): ?>
                            <div class="error-message">
                                <ul style="margin: 0; padding-left: 20px;">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="donation-tabs">
                            <div class="tab-btn active" data-tab="one-time">One-Time</div>
                            <div class="tab-btn" data-tab="recurring">Recurring</div>
                        </div>

                        <!-- One-Time Donation Form -->
                        <div class="tab-content active" id="one-time">
                            <form id="donationForm" action="process-donation.php" method="post">
                                <input type="hidden" name="donation_type" value="one-time">
                                
                                <div style="margin-bottom: 20px;">
                                    <h4 style="margin-bottom: 15px;">Select Amount</h4>
                                    <div class="amount-options">
                                        <div class="amount-btn active" data-amount="20">$20</div>
                                        <div class="amount-btn" data-amount="50">$50</div>
                                        <div class="amount-btn" data-amount="100">$100</div>
                                        <div class="amount-btn" data-amount="200">$200</div>
                                        <div class="amount-btn" data-amount="500">$500</div>
                                        <div class="amount-btn" data-amount="custom">Custom</div>
                                    </div>
                                    <div class="custom-amount" id="customAmount">
                                        <label for="customAmountInput">Enter Custom Amount</label>
                                        <input type="number" id="customAmountInput" name="amount" min="1" step="0.01" placeholder="Enter amount" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                    </div>
                                    <input type="hidden" id="selectedAmount" name="amount" value="20">
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label for="purpose">Purpose (Optional)</label>
                                    <select id="purpose" name="purpose" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                        <option value="General Fund">General Fund</option>
                                        <option value="Missions">Missions</option>
                                        <option value="Building Fund">Building Fund</option>
                                        <option value="Youth Ministry">Youth Ministry</option>
                                        <option value="Community Outreach">Community Outreach</option>
                                    </select>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                    <div>
                                        <label for="name">Full Name</label>
                                        <input type="text" id="name" name="name" placeholder="Your name" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;" value="<?php echo $form_data['name'] ?? ''; ?>">
                                    </div>
                                    <div>
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" name="email" placeholder="Your email" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;" value="<?php echo $form_data['email'] ?? ''; ?>">
                                    </div>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label for="phone">Phone Number (Optional)</label>
                                    <input type="tel" id="phone" name="phone" placeholder="Your phone number" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;" value="<?php echo $form_data['phone'] ?? ''; ?>">
                                </div>

                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                                    <input type="checkbox" id="coverFees" name="cover_fees" style="width: 20px; height: 20px;" <?php echo isset($form_data['cover_fees']) ? 'checked' : ''; ?>>
                                    <label for="coverFees">Cover transaction fees (3%)</label>
                                </div>

                                <div class="donation-summary">
                                    <h4 style="margin-bottom: 15px;">Donation Summary</h4>
                                    <div class="summary-item">
                                        <span>Donation Amount:</span>
                                        <span id="summaryAmount">$20.00</span>
                                    </div>
                                    <div class="summary-item" id="feesSummary" style="display: none;">
                                        <span>Transaction Fee:</span>
                                        <span id="summaryFees">$0.60</span>
                                    </div>
                                    <div class="summary-total">
                                        <span>Total Amount:</span>
                                        <span id="summaryTotal">$20.00</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;">Proceed to Payment</button>
                            </form>
                        </div>

                        <!-- Recurring Donation Form -->
                        <div class="tab-content" id="recurring">
                            <form id="recurringDonationForm" action="process-donation.php" method="post">
                                <input type="hidden" name="donation_type" value="recurring">
                                
                                <div style="margin-bottom: 20px;">
                                    <h4 style="margin-bottom: 15px;">Select Amount</h4>
                                    <div class="amount-options">
                                        <div class="amount-btn active" data-amount="20">$20</div>
                                        <div class="amount-btn" data-amount="50">$50</div>
                                        <div class="amount-btn" data-amount="100">$100</div>
                                        <div class="amount-btn" data-amount="200">$200</div>
                                        <div class="amount-btn" data-amount="500">$500</div>
                                        <div class="amount-btn" data-amount="custom">Custom</div>
                                    </div>
                                    <div class="custom-amount" id="recurringCustomAmount">
                                        <label for="recurringCustomAmountInput">Enter Custom Amount</label>
                                        <input type="number" id="recurringCustomAmountInput" name="amount" min="1" step="0.01" placeholder="Enter amount" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                    </div>
                                    <input type="hidden" id="recurringSelectedAmount" name="amount" value="20">
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label for="frequency">Frequency</label>
                                    <select id="frequency" name="frequency" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                        <option value="weekly">Weekly</option>
                                        <option value="biweekly">Bi-weekly</option>
                                        <option value="monthly" selected>Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                        <option value="annually">Annually</option>
                                    </select>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label for="recurringPurpose">Purpose (Optional)</label>
                                    <select id="recurringPurpose" name="purpose" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                        <option value="General Fund">General Fund</option>
                                        <option value="Missions">Missions</option>
                                        <option value="Building Fund">Building Fund</option>
                                        <option value="Youth Ministry">Youth Ministry</option>
                                        <option value="Community Outreach">Community Outreach</option>
                                    </select>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                    <div>
                                        <label for="recurringName">Full Name</label>
                                        <input type="text" id="recurringName" name="name" placeholder="Your name" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;" value="<?php echo $form_data['name'] ?? ''; ?>">
                                    </div>
                                    <div>
                                        <label for="recurringEmail">Email Address</label>
                                        <input type="email" id="recurringEmail" name="email" placeholder="Your email" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;" value="<?php echo $form_data['email'] ?? ''; ?>">
                                    </div>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label for="recurringPhone">Phone Number (Optional)</label>
                                    <input type="tel" id="recurringPhone" name="phone" placeholder="Your phone number" style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;" value="<?php echo $form_data['phone'] ?? ''; ?>">
                                </div>
                                        
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                                    <input type="checkbox" id="recurringCoverFees" name="cover_fees" style="width: 20px; height: 20px;" <?php echo isset($form_data['cover_fees']) ? 'checked' : ''; ?>>
                                    <label for="recurringCoverFees">Cover transaction fees (3%)</label>
                                </div>

                                <div class="donation-summary">
                                    <h4 style="margin-bottom: 15px;">Donation Summary</h4>
                                    <div class="summary-item">
                                        <span>Donation Amount:</span>
                                        <span id="recurringSummaryAmount">$20.00</span>
                                    </div>
                                    <div class="summary-item">
                                        <span>Frequency:</span>
                                        <span id="summaryFrequency">Monthly</span>
                                    </div>
                                    <div class="summary-item" id="recurringFeesSummary" style="display: none;">
                                        <span>Transaction Fee:</span>
                                        <span id="recurringSummaryFees">$0.60</span>
                                    </div>
                                    <div class="summary-total">
                                        <span>Total Amount:</span>
                                        <span id="recurringSummaryTotal">$20.00</span>
                                    </div>
                                </div>
                                         <div style="margin-bottom: 20px;">
                                    <h4 style="margin-bottom: 15px;">Payment Method</h4>
                                    <div class="payment-methods">
                                        <div class="payment-method active" data-method="card">
                                            <i class="fas fa-credit-card"></i>
                                            <div>Credit Card</div>
                                        </div>
                                        <div class="payment-method" data-method="paypal">
                                            <i class="fab fa-paypal"></i>
                                            <div>PayPal</div>
                                        </div>
                                        <div class="payment-method" data-method="bank">
                                            <i class="fas fa-university"></i>
                                            <div>Bank Transfer</div>
                                        </div>
                                    </div>

                                    <div class="payment-details active" id="cardDetails">
                                        <div style="margin-bottom: 15px;">
                                            <label for="cardNumber">Card Number</label>
                                            <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                        </div>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                            <div>
                                                <label for="expiryDate">Expiry Date</label>
                                                <input type="text" id="expiryDate" placeholder="MM/YY" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                            </div>
                                            <div>
                                                <label for="cvv">CVV</label>
                                                <input type="text" id="cvv" placeholder="123" required style="width: 100%; padding: 10px; margin-top: 10px; border: 1px solid var(--gray-light); border-radius: 5px;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="payment-details" id="paypalDetails">
                                        <p style="margin: 15px 0;">You will be redirected to PayPal to complete your donation.</p>
                                    </div>

                                    <div class="payment-details" id="bankDetails">
                                        <p style="margin: 15px 0;">Please use the following details for bank transfer:</p>
                                        <div style="background-color: var(--gray-light); padding: 15px; border-radius: 5px;">
                                            <p><strong>Bank Name:</strong> First National Bank</p>
                                            <p><strong>Account Name:</strong> Faith Baptist Church</p>
                                            <p><strong>Account Number:</strong> 1234567890</p>
                                            <p><strong>Routing Number:</strong> 987654321</p>
                                            <p><strong>Reference:</strong> Your Name</p>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%;">Set Up Recurring Donation</button>
                            </form>
                        </div>
                    </div>
                </div>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tab switching functionality
            const tabBtns = document.querySelectorAll(".tab-btn");
            const tabContents = document.querySelectorAll(".tab-content");
            
            tabBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    // Remove active class from all buttons and contents
                    tabBtns.forEach(b => b.classList.remove("active"));
                    tabContents.forEach(c => c.classList.remove("active"));
                    
                    // Add active class to clicked button and corresponding content
                    this.classList.add("active");
                    const tabId = this.getAttribute("data-tab");
                    document.getElementById(tabId).classList.add("active");
                });
            });
            
            // One-time donation amount selection
            const amountBtns = document.querySelectorAll("#one-time .amount-btn");
            const customAmountDiv = document.getElementById("customAmount");
            const customAmountInput = document.getElementById("customAmountInput");
            const selectedAmountInput = document.getElementById("selectedAmount");
            const summaryAmount = document.getElementById("summaryAmount");
            const summaryTotal = document.getElementById("summaryTotal");
            
            amountBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    // Remove active class from all buttons
                    amountBtns.forEach(b => b.classList.remove("active"));
                    
                    // Add active class to clicked button
                    this.classList.add("active");
                    
                    const amount = this.getAttribute("data-amount");
                    
                    if (amount === "custom") {
                        customAmountDiv.style.display = "block";
                        customAmountInput.focus();
                        
                        if (customAmountInput.value) {
                            const customAmount = parseFloat(customAmountInput.value).toFixed(2);
                            summaryAmount.textContent = `$${customAmount}`;
                            summaryTotal.textContent = `$${customAmount}`;
                            selectedAmountInput.value = customAmount;
                        }
                    } else {
                        customAmountDiv.style.display = "none";
                        summaryAmount.textContent = `$${parseFloat(amount).toFixed(2)}`;
                        summaryTotal.textContent = `$${parseFloat(amount).toFixed(2)}`;
                        selectedAmountInput.value = amount;
                    }
                    
                    updateFees("one-time");
                });
            });
            
            customAmountInput.addEventListener("input", function() {
                if (this.value) {
                    const customAmount = parseFloat(this.value).toFixed(2);
                    summaryAmount.textContent = `$${customAmount}`;
                    summaryTotal.textContent = `$${customAmount}`;
                    selectedAmountInput.value = customAmount;
                    
                    updateFees("one-time");
                }
            });
            
            // Recurring donation amount selection
            const recurringAmountBtns = document.querySelectorAll("#recurring .amount-btn");
            const recurringCustomAmountDiv = document.getElementById("recurringCustomAmount");
            const recurringCustomAmountInput = document.getElementById("recurringCustomAmountInput");
            const recurringSelectedAmountInput = document.getElementById("recurringSelectedAmount");
            const recurringSummaryAmount = document.getElementById("recurringSummaryAmount");
            const recurringSummaryTotal = document.getElementById("recurringSummaryTotal");
            
            recurringAmountBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    // Remove active class from all buttons
                    recurringAmountBtns.forEach(b => b.classList.remove("active"));
                    
                    // Add active class to clicked button
                    this.classList.add("active");
                    
                    const amount = this.getAttribute("data-amount");
                    
                    if (amount === "custom") {
                        recurringCustomAmountDiv.style.display = "block";
                        recurringCustomAmountInput.focus();
                        
                        if (recurringCustomAmountInput.value) {
                            const customAmount = parseFloat(recurringCustomAmountInput.value).toFixed(2);
                            recurringSummaryAmount.textContent = `$${customAmount}`;
                            recurringSummaryTotal.textContent = `$${customAmount}`;
                            recurringSelectedAmountInput.value = customAmount;
                        }
                    } else {
                        recurringCustomAmountDiv.style.display = "none";
                        recurringSummaryAmount.textContent = `$${parseFloat(amount).toFixed(2)}`;
                        recurringSummaryTotal.textContent = `$${parseFloat(amount).toFixed(2)}`;
                        recurringSelectedAmountInput.value = amount;
                    }
                    
                    updateFees("recurring");
                });
            });
            
            recurringCustomAmountInput.addEventListener("input", function() {
                if (this.value) {
                    const customAmount = parseFloat(this.value).toFixed(2);
                    recurringSummaryAmount.textContent = `$${customAmount}`;
                    recurringSummaryTotal.textContent = `$${customAmount}`;
                    recurringSelectedAmountInput.value = customAmount;
                    
                    updateFees("recurring");
                }
            });
            
            // Frequency selection
            const frequencySelect = document.getElementById("frequency");
            const summaryFrequency = document.getElementById("summaryFrequency");
            
            frequencySelect.addEventListener("change", function() {
                summaryFrequency.textContent = this.options[this.selectedIndex].text;
            });
            
            // Transaction fee checkbox functionality
            const coverFeesCheckbox = document.getElementById("coverFees");
            const feesSummary = document.getElementById("feesSummary");
            const summaryFees = document.getElementById("summaryFees");
            
            coverFeesCheckbox.addEventListener("change", function() {
                updateFees("one-time");
            });
            
            const recurringCoverFeesCheckbox = document.getElementById("recurringCoverFees");
            const recurringFeesSummary = document.getElementById("recurringFeesSummary");
            const recurringSummaryFees = document.getElementById("recurringSummaryFees");
            
            recurringCoverFeesCheckbox.addEventListener("change", function() {
                updateFees("recurring");
            });
            
            // Function to update fees and total
            function updateFees(type) {
                if (type === "one-time") {
                    const amountText = summaryAmount.textContent.replace("$", "");
                    const amount = parseFloat(amountText);
                    
                    if (coverFeesCheckbox.checked) {
                        feesSummary.style.display = "flex";
                        const fee = (amount * 0.03).toFixed(2);
                        summaryFees.textContent = `$${fee}`;
                        summaryTotal.textContent = `$${(amount + parseFloat(fee)).toFixed(2)}`;
                    } else {
                        feesSummary.style.display = "none";
                        summaryTotal.textContent = `$${amount.toFixed(2)}`;
                    }
                } else {
                    const amountText = recurringSummaryAmount.textContent.replace("$", "");
                    const amount = parseFloat(amountText);
                    
                    if (recurringCoverFeesCheckbox.checked) {
                        recurringFeesSummary.style.display = "flex";
                        const fee = (amount * 0.03).toFixed(2);
                        recurringSummaryFees.textContent = `$${fee}`;
                        recurringSummaryTotal.textContent = `$${(amount + parseFloat(fee)).toFixed(2)}`;
                    } else {
                        recurringFeesSummary.style.display = "none";
                        recurringSummaryTotal.textContent = `$${amount.toFixed(2)}`;
                    }
                }
            }
        });
    </script>
</body>
</html>

