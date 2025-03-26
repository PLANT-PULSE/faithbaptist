<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
require_once 'config.php';

// Check if reference is provided
if (isset($_GET['reference']) && !empty($_GET['reference'])) {
    $reference = htmlspecialchars($_GET['reference']);
    
    // Verify the transaction
    $paystack_url = "https://api.paystack.co/transaction/verify/" . $reference;
    
    // Initialize cURL
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $paystack_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Cache-Control: no-cache",
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    
    if (curl_error($ch)) {
        // Log error
        error_log('Paystack API Error: ' . curl_error($ch));
        $_SESSION['payment_error'] = 'Payment verification failed. Please contact support.';
    } else {
        $transaction = json_decode($response, true);
        
        if ($transaction['status'] && $transaction['data']['status'] === 'success') {
            // Payment was successful
            
            // Get donation data from session
            $donation_data = $_SESSION['donation_data'] ?? [];
            
            if (!empty($donation_data) && $donation_data['reference'] === $reference) {
                // Update donation status
                $donation_data['status'] = 'completed';
                $donation_data['transaction_id'] = $transaction['data']['id'];
                $donation_data['payment_date'] = date('Y-m-d H:i:s');
                
                // Save donation to database
                $conn = db_connect();
                
                $stmt = $conn->prepare("INSERT INTO donations (name, email, phone, amount, donation_type, purpose, frequency, cover_fees, reference, transaction_id, status, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $cover_fees_db = $donation_data['cover_fees'] ? 1 : 0;
                
                $stmt->bind_param("sssdsssissss", 
                    $donation_data['name'],
                    $donation_data['email'],
                    $donation_data['phone'],
                    $donation_data['amount'] / 100, // Convert back from kobo/cents
                    $donation_data['donation_type'],
                    $donation_data['purpose'],
                    $donation_data['frequency'],
                    $cover_fees_db,
                    $donation_data['reference'],
                    $donation_data['transaction_id'],
                    $donation_data['status'],
                    $donation_data['payment_date']
                );
                
                if ($stmt->execute()) {
                    // Send thank you email
                    send_thank_you_email($donation_data);
                    
                    // Set success message
                    $_SESSION['payment_success'] = true;
                    $_SESSION['donation_data'] = $donation_data;
                } else {
                    // Log database error
                    error_log('Database Error: ' . $stmt->error);
                    $_SESSION['payment_error'] = 'There was an error saving your donation. Please contact support.';
                }
                
                $stmt->close();
                $conn->close();
            } else {
                // Invalid reference or session expired
                $_SESSION['payment_error'] = 'Invalid transaction reference.';
            }
        } else {
            // Payment failed
            $_SESSION['payment_error'] = 'Payment was not successful: ' . ($transaction['data']['gateway_response'] ?? 'Unknown error');
        }
    }
    
    curl_close($ch);
    
    // Redirect to thank you page
    header('Location: donation-confirmation.php');
    exit;
} else {
    // No reference provided
    $_SESSION['payment_error'] = 'No transaction reference provided.';
    header('Location: donate.php');
    exit;
}

// Function to send thank you email
function send_thank_you_email($donation_data) {
    $to = $donation_data['email'];
    $subject = 'Thank You for Your Donation - Faith Baptist Church';
    
    $amount = number_format($donation_data['amount'] / 100, 2);
    $frequency = $donation_data['frequency'] === 'one-time' ? 'One-time' : ucfirst($donation_data['frequency']);
    
    $message = "
    <html>
    <head>
        <title>Thank You for Your Donation</title>
    </head>
    <body>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <img src='" . SITE_URL . "/images/logo.jpg' alt='Faith Baptist Church Logo' style='max-width: 100px;'>
                <h1 style='color: #5d3b8c;'>Faith Baptist Church</h1>
            </div>
            
            <p>Dear {$donation_data['name']},</p>
            
            <p>Thank you for your generous donation to Faith Baptist Church. Your contribution helps us continue our mission and serve our community.</p>
            
            <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <h3 style='margin-top: 0;'>Donation Details:</h3>
                <p><strong>Amount:</strong> $" . $amount . "</p>
                <p><strong>Donation Type:</strong> " . $frequency . "</p>
                <p><strong>Purpose:</strong> {$donation_data['purpose']}</p>
                <p><strong>Reference:</strong> {$donation_data['reference']}</p>
                <p><strong>Date:</strong> {$donation_data['payment_date']}</p>
            </div>
            
            <p>If you have any questions about your donation, please contact our church office at <a href='mailto:info@faithbaptistchurch.com'>info@faithbaptistchurch.com</a> or call us at +233 543 957 330.</p>
            
            <p>May God bless you abundantly for your generosity.</p>
            
            <p>Sincerely,<br>Pastor Solomon Kwesi Ackon<br>Faith Baptist Church</p>
            
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #777; font-size: 12px;'>
                <p>Faith Baptist Church<br>Ayensudo-Abeyee, Central Region, Ghana, West Africa</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Set content-type header for sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Faith Baptist Church <info@faithbaptistchurch.com>" . "\r\n";
    
    // Send email
    mail($to, $subject, $message, $headers);
}
?>

