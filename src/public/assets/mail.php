<?php
// Only process POST reqeusts.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptcha_secret_key = '6LcCUWcrAAAAAFfx_7KQvpLlmK37lfk9PwM4OL4i';
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($verify_url, false, $context);
    $json_result = json_decode($result, true);

    if (!$json_result['success']) {
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }


    // Get the form fields and remove whitespace.
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r", "\n"), array(" ", " "), $name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $number = trim($_POST["number"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Check that data was sent to the mailer.
    if (empty($name) or empty($email) or empty($number) or empty($subject) or empty($message) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Set a 400 (bad request) response code and exit.
        http_response_code(400);
        echo "Please complete the form and try again.";
        exit;
    }

    // Set the recipient email addresses.
    // Separate multiple addresses with commas.
    // You can use a mix of 'To' and 'Bcc' if needed.
    $recipients_to = "mark.fajardo@wecreate-services.com";
    $recipients_bcc = "rencielyne.fajardo@wecreate-services.com";

    // Set the email subject.
    $sender = "New contact from $name";

    //Email Header
    $head = " /// ELEGANTPIXELS \\\ ";
    // Build the email content.
    $email_content = "$head\n\n\n";

    $email_content .= "Name: $name\n";

    $email_content .= "Email: $email\n\n";

    $email_content .= "Number: $number\n\n";

    $email_content .= "Subject: $subject\n\n";

    $email_content .= "Message:\n$message\n";

    // Build the email headers.
    // Include 'From', 'Reply-To', 'To', and 'Bcc' headers.
    $email_headers = "From: $name <$email>\r\n";
    $email_headers .= "Reply-To: $email\r\n"; // Useful for direct replies
    $email_headers .= "To: $recipients_to\r\n";
    if (!empty($recipients_bcc)) {
        $email_headers .= "Bcc: $recipients_bcc\r\n";
    }
    $email_headers .= "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";


    // Send the email.
    // The first argument to mail() should be the main 'To' address.
    // Other 'To', 'Cc', 'Bcc' addresses are handled in the headers.
    if (mail($recipients_to, $sender, $email_content, $email_headers)) {
        // Set a 200 (okay) response code.
        http_response_code(200);
        echo "Thank You! Your message has been sent.";
    } else {
        // Set a 500 (internal server error) response code.
        http_response_code(500);
        echo "Oops! Something went wrong and we couldn't send your message.";
    }
} else {
    // Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptcha_secret_key = 'YOUR_SECRET_KEY'; // Replace with your Secret Key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $recaptcha_secret_key,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($verify_url, false, $context);
    $json_result = json_decode($result, true);

    if ($json_result['success']) {
        // reCAPTCHA verification successful
        // Process your form data (send email, save to database, etc.)

        $name = htmlspecialchars($_POST['user_name']);
        $email = htmlspecialchars($_POST['user_email']);
        $number = htmlspecialchars($_POST['user_number']);
        $area = htmlspecialchars($_POST['user_area']);
        $concern = htmlspecialchars($_POST['user_concern']);

        $to = "mjt.fajardo@gmail.com"; // Your email address
        $subject = "New Contact Form Submission from " . $name;
        $message = "Name: " . $name . "\n"
                 . "Email: " . $email . "\n"
                 . "Number: " . $number . "\n"
                 . "Area: " . $area . "\n"
                 . "Concern: " . $concern;

        $headers = "From: " . $email . "\r\n" .
                   "Reply-To: " . $email . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent successfully!";
            // You might redirect the user to a thank you page
            // header('Location: thank_you.html');
            // exit();
        } else {
            echo "Failed to send email.";
        }

    } else {
        // reCAPTCHA verification failed
        echo "reCAPTCHA verification failed. Please try again.";
        // You can log errors from $json_result['error-codes'] for debugging
    }
} else {
    echo "Invalid request method.";
}