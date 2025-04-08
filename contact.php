<?php
    /*
    Collect form submissions for first name, last name, phone, email, and message.
    Send the data via email to the specified recipient.
    */

    // Only process POST requests.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and sanitize inputs.
        $first_name = strip_tags(trim($_POST["first_name"]));
        $first_name = str_replace(array("\r", "\n"), array(" ", " "), $first_name);
        $last_name = strip_tags(trim($_POST["last_name"]));
        $last_name = str_replace(array("\r", "\n"), array(" ", " "), $last_name);
        $phone = strip_tags(trim($_POST["phone"]));
        $phone = str_replace(array("\r", "\n"), array(" ", " "), $phone);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["message"]);

        // Validate the inputs.
        if (empty($first_name) || empty($last_name) || empty($phone) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Please fill out all required fields with valid information.";
            exit;
        }

        // Additional phone number validation (basic format check).
        if (!preg_match("/^[0-9\s\-\+\(\)]{7,15}$/", $phone)) {
            http_response_code(400);
            echo "Please enter a valid phone number.";
            exit;
        }

        // Combine first and last name for the email.
        $full_name = $first_name . " " . $last_name;

        // Set the recipient email address.
        // TODO: Update this to your desired email address.
        $recipient = "labyrinthwebstudio@gmail.com"; // Replace with your email address

        // Set the email subject.
        $subject = "New Contact Form Submission from $full_name";

        // Build the email content.
        $email_content = "First Name: $first_name\n";
        $email_content .= "Last Name: $last_name\n";
        $email_content .= "Phone Number: $phone\n";
        $email_content .= "Email: $email\n";
        $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: $full_name <$email>\r\n";
        $email_headers .= "Reply-To: $email\r\n";
        $email_headers .= "MIME-Version: 1.0\r\n";
        $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong, and we couldn't send your message.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }
?>