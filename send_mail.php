<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize inputs
    $rawRecipients = $_POST['recipients'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Convert newlines to commas and split recipients
    $recipients = preg_split('/[\s,]+/', str_replace(["\r\n", "\n", "\r"], ',', $rawRecipients));
    
    // Filter valid emails only
    $validRecipients = array_filter($recipients, function($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    if (empty($validRecipients)) {
        die("No valid recipient emails provided.");
    }

    // Allowed file extensions
    $allowedExtensions = ['jpg','jpeg','png','pdf','doc','docx','txt','xls','xlsx','ppt','pptx'];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    // Temporary files to attach
    $uploadedFiles = [];

    // Handle file uploads safely
    if (!empty($_FILES['attachments']) && isset($_FILES['attachments']['name'])) {
        foreach ($_FILES['attachments']['name'] as $key => $filename) {
            if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_NO_FILE) {
                // No file uploaded for this key, skip
                continue;
            }

            if ($_FILES['attachments']['error'][$key] !== UPLOAD_ERR_OK) {
                die("Error uploading file $filename");
            }

            $fileTmpPath = $_FILES['attachments']['tmp_name'][$key];
            $fileSize = $_FILES['attachments']['size'][$key];
            $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Check extension
            if (!in_array($fileExt, $allowedExtensions)) {
                die("File type not allowed: $filename");
            }

            // Check file size
            if ($fileSize > $maxFileSize) {
                die("File too large (max 5MB): $filename");
            }

            // Optional: Further checks like MIME type can be added here

            // Move to temp folder or keep temp path for attaching
            // Since PHPMailer can attach directly from temp path, no need to move
            $uploadedFiles[] = [
                'tmp_name' => $fileTmpPath,
                'name' => $filename,
            ];
        }
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();

    // SMTP Config - change these according to your SMTP provider
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = '';        // Your email here
    $mail->Password = '';             // Your SMTP app password here
    $mail->SMTPSecure = 'tls';                        
    $mail->Port = 587;

    // Sender info
    $mail->setFrom('your_email@gmail.com', 'Your Name');

    // Email format (plain text or HTML)
    $mail->isHTML(false);

    echo "<h2>Sending emails...</h2>";

    foreach ($validRecipients as $email) {
        try {
            $mail->clearAddresses();
            $mail->clearAttachments();

            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Attach files
            foreach ($uploadedFiles as $file) {
                $mail->addAttachment($file['tmp_name'], $file['name']);
            }

            $mail->send();
            echo "✔ Email sent to: <b>$email</b><br>";
        } catch (Exception $e) {
            echo "✖ Failed to send to $email: " . $mail->ErrorInfo . "<br>";
        }
    }
}
?>
