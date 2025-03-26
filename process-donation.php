<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
require_once 'config.php';

// Process donation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $errors = [];
    
    // Required fields
    $required_fields = ['name', 'email', 'amount', 'donation_type'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    
    // Validate email
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    // Validate amount (must be numeric and greater than 0)
    if (!empty($_POST['amount']) && (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0)) {
        $errors[] = 'Please enter a valid donation amount';
    }
    
    // If no errors, proceed with payment
    if (empty($errors)) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $phone = !empty($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
        $amount = floatval($_POST['amount']) * 100; // Convert to kobo/cents
        $donation_type = htmlspecialchars($_POST['donation_type']);
        $purpose = !empty($_POST['purpose']) ? htmlspecialchars($_POST['purpose']) : 'General Fund';
        $frequency = !empty($_POST['frequency']) ? htmlspecialchars($_POST['frequency']) : 'one-time';
        $cover_fees = isset($_POST['cover_fees']) ? true : false;
        
        // Add transaction fee if selected
        if ($cover_fees) {
            $fee = $amount * 0.03;
            $amount += $fee;
        }
        
        // Generate a unique reference
        $reference = 'FBC_' . uniqid() . '_' . time();
        
        // Store transaction data in session for verification later
        $_SESSION['donation_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'amount' => $amount,
            'donation_type' => $donation_type,
            'purpose' => $purpose,
            'frequency' => $frequency,
            'cover_fees' => $cover_fees,
            'reference' => $reference,
            'status' => 'pending'
        ];
        
        // Initialize Paystack transaction
        $paystack_url = "https://api.paystack.co/transaction/initialize";
        
        $fields = [
            'email' => $email,
            'amount' => $amount,
            'reference' => $reference,
            'callback_url' => SITE_URL . '/verify-payment.php',
            'metadata' => [
                'name' => $name,
                'phone' => $phone,
                'donation_type' => $donation_type,
                'purpose' => $purpose,
                'frequency' => $frequency,
                'cover_fees' => $cover_fees ? 'yes' : 'no'
            ]
        ];
        
        $fields_string = http_build_query($fields);
        
        // Initialize cURL
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $paystack_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
            "Cache-Control: no-cache",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        
        if (curl_error($ch)) {
            // Log error
            error_log('Paystack API Error: ' . curl_error($ch));
            $errors[] = 'Payment initialization failed. Please try again.';
        } else {
            $transaction = json_decode($response, true);
            
            if ($transaction['status']) {
                // Redirect to Paystack payment page
                header('Location: ' . $transaction['data']['authorization_url']);
                exit;
            } else {
                // Log error
                error_log('Paystack Error: ' . $transaction['message']);
                $errors[] = 'Payment initialization failed: ' . $transaction['message'];
            }
        }
        
        curl_close($ch);
    }
    
    // If there are errors, store them in session and redirect back to donation page
    if (!empty($errors)) {
        $_SESSION['donation_errors'] = $errors;
        $_SESSION['donation_form_data'] = $_POST; // Store form data for repopulation
        header('Location: donate.php');
        exit;
    }
}

// If not a POST request, redirect to donation page
header('Location: donate.php');
exit;
?>

