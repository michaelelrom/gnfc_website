<?php
// ============================================
// Good Neighbor Fence Company - Quote Form Handler
// ============================================
// Set the email address where you want to receive quotes
$to_email = "info@fence4u.biz";

// Optional: Add a CC
// $cc_email = "sales@fence4u.biz";

// ============================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: get-in-touch.html");
    exit;
}

// Sanitize inputs
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$firstName = clean($_POST['firstName'] ?? '');
$lastName  = clean($_POST['lastName'] ?? '');
$email     = clean($_POST['email'] ?? '');
$phone     = clean($_POST['phone'] ?? '');
$company   = clean($_POST['company'] ?? '');
$service   = clean($_POST['service'] ?? '');
$location  = clean($_POST['location'] ?? '');
$message   = clean($_POST['message'] ?? '');

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($service)) {
    header("Location: get-in-touch.html?status=error&msg=missing");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: get-in-touch.html?status=error&msg=email");
    exit;
}

// Map service value to readable name
$serviceNames = [
    'temporary' => 'Temporary Fencing',
    'permanent' => 'Permanent Fencing',
    'gates'     => 'Gates & Access Control',
    'multiple'  => 'Multiple Services',
    'other'     => 'Other / Not Sure'
];
$serviceName = $serviceNames[$service] ?? $service;

// Build the HTML email
$date = date('F j, Y \a\t g:i A');

$htmlBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        .email-wrapper { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .email-header { background: #002d5b; color: white; padding: 25px 30px; }
        .email-header h1 { margin: 0; font-size: 22px; }
        .email-header p { margin: 5px 0 0; font-size: 13px; opacity: 0.8; }
        .email-body { padding: 30px; }
        .field-row { display: flex; border-bottom: 1px solid #eee; padding: 12px 0; }
        .field-label { font-weight: 600; color: #002d5b; width: 160px; min-width: 160px; font-size: 14px; }
        .field-value { color: #333; font-size: 14px; }
        .message-section { margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 6px; border-left: 4px solid #e67e22; }
        .message-section h3 { margin: 0 0 10px; color: #002d5b; font-size: 15px; }
        .message-section p { margin: 0; color: #555; font-size: 14px; line-height: 1.6; white-space: pre-wrap; }
        .email-footer { background: #f4f4f4; padding: 15px 30px; font-size: 12px; color: #999; text-align: center; }
        .badge { display: inline-block; background: #e67e22; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>🏗️ New Quote Request</h1>
            <p>Received on ' . $date . '</p>
        </div>
        <div class="email-body">
            <div style="margin-bottom: 20px;">
                <span class="badge">' . $serviceName . '</span>
            </div>
            
            <div class="field-row">
                <div class="field-label">Name</div>
                <div class="field-value">' . $firstName . ' ' . $lastName . '</div>
            </div>
            <div class="field-row">
                <div class="field-label">Email</div>
                <div class="field-value"><a href="mailto:' . $email . '" style="color: #0056b3;">' . $email . '</a></div>
            </div>
            <div class="field-row">
                <div class="field-label">Phone</div>
                <div class="field-value"><a href="tel:' . $phone . '" style="color: #0056b3;">' . $phone . '</a></div>
            </div>';

if (!empty($company)) {
    $htmlBody .= '
            <div class="field-row">
                <div class="field-label">Company</div>
                <div class="field-value">' . $company . '</div>
            </div>';
}

$htmlBody .= '
            <div class="field-row">
                <div class="field-label">Service Needed</div>
                <div class="field-value">' . $serviceName . '</div>
            </div>';

if (!empty($location)) {
    $htmlBody .= '
            <div class="field-row">
                <div class="field-label">Project Location</div>
                <div class="field-value">' . $location . '</div>
            </div>';
}

if (!empty($message)) {
    $htmlBody .= '
            <div class="message-section">
                <h3>Project Details</h3>
                <p>' . nl2br($message) . '</p>
            </div>';
}

$htmlBody .= '
        </div>
        <div class="email-footer">
            Good Neighbor Fence Company · Website Quote Form<br>
            <a href="https://www.fence4u.biz" style="color: #999;">www.fence4u.biz</a>
        </div>
    </div>
</body>
</html>';

// Email headers
$subject = "New Quote Request: $serviceName - $firstName $lastName";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Good Neighbor Fence Website <info@fence4u.biz>\r\n";
$headers .= "Reply-To: $firstName $lastName <$email>\r\n";

// Uncomment to add CC
// if (isset($cc_email)) {
//     $headers .= "Cc: $cc_email\r\n";
// }

// Send the email
$sent = @mail($to_email, $subject, $htmlBody, $headers);

// Fallback: save to local JSON file (for local testing without mail server)
if (!$sent) {
    $submission = [
        'date'      => $date,
        'name'      => "$firstName $lastName",
        'email'     => $email,
        'phone'     => $phone,
        'company'   => $company,
        'service'   => $serviceName,
        'location'  => $location,
        'message'   => $message
    ];

    $logFile = __DIR__ . '/submissions.json';
    $existing = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
    if (!is_array($existing)) $existing = [];
    $existing[] = $submission;
    file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT));

    // Still redirect as success — the submission was captured
    header("Location: get-in-touch.html?status=success");
    exit;
}

header("Location: get-in-touch.html?status=success");
exit;
?>
