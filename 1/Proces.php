<?php
header('Content-Type: application/json');

// Enhanced input validation and sanitization
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Age validation function
function isValidAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age >= 18 && $age <= 120;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = [];

    // Collect and sanitize form data
    $fullName = sanitizeInput($_POST['fullName']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = sanitizeInput($_POST['phone']);
    $dob = sanitizeInput($_POST['dob']);
    $gender = sanitizeInput($_POST['gender']);
    $address = sanitizeInput($_POST['address']);
    $education = sanitizeInput($_POST['education']);

    // Server-side validation
    $errors = [];

    // Full name validation
    if (empty($fullName) || !preg_match("/^[a-zA-Z\s]{2,50}$/", $fullName)) {
        $errors[] = "Invalid name format";
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Phone number validation (10-digit)
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Invalid phone number";
    }

    // Date of birth validation
    if (empty($dob) || !isValidAge($dob)) {
        $errors[] = "Invalid date of birth or age must be between 18-120";
    }

    // Gender validation
    $validGenders = ['male', 'female', 'other'];
    if (!in_array($gender, $validGenders)) {
        $errors[] = "Invalid gender selection";
    }

    // Address validation
    if (empty($address) || strlen($address) < 10 || strlen($address) > 200) {
        $errors[] = "Invalid address";
    }

    // Education validation
    $validEducations = ['high-school', 'bachelors', 'masters', 'phd'];
    if (!in_array($education, $validEducations)) {
        $errors[] = "Invalid education selection";
    }

    // Prepare response
    if (empty($errors)) {
        // Simulated successful registration
        $response = [
            'status' => 'success', 
            'data' => [ 
                'fullName' => $fullName, 
                'email' => $email, 
                'phone' => $phone, 
                'dob' => $dob, 
                'gender' => ucfirst($gender), 
                'address' => $address, 
                'education' => str_replace('-', ' ', ucwords($education)) 
            ] 
        ]; 
    } else { 
        $response = [ 
            'status' => 'error', 
            'message' => implode(", ", $errors) 
        ]; 
    } 

    echo json_encode($response); 
    exit(); 
}
?>
