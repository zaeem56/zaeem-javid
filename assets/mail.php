<?php
// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes (adjust paths if necessary)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Database connection (update with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "salon_zero";

// SMTP email configuration (update with your SMTP details)
$smtpHost = 'smtp.gmail.com'; // SMTP server
$smtpUsername = 'comrademr100@gmail.com'; // Your email
$smtpPassword = 'qpsx xexs niuo uqdc'; // Your email's App Password
$smtpPort = 587; // SMTP port (for TLS)

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validate the data (additional validation can be done here)
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit();
    }

    // Store form data in the database
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        $stmt->execute();

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error storing the data: ' . $e->getMessage()]);
        exit();
    }

    // Send email via SMTP
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;

        // Recipients
        $mail->setFrom($smtpUsername, 'Salon Zero Contact Form');
        $mail->addAddress('comrademr100@gmail.com'); // Your email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';

        // Attractive Email Body
        $mail->Body = "
            <div style='background-color:#f4f4f4;padding:20px;font-family:Arial,sans-serif;color:#333;'>
                <div style='max-width:600px;margin:0 auto;background-color:#fff;border-radius:10px;overflow:hidden;box-shadow:0px 0px 10px rgba(0,0,0,0.1);'>
                    <div style='background-color:#000;padding:20px;text-align:center;'>
                        
                    </div>
                    <div style='padding:20px;'>
                        <h2 style='color:#000;text-align:center;'>New Contact Form Submission</h2>
                        <p style='font-size:16px;color:#555;text-align:center;'>
                            <strong>Name:</strong> {$name}<br>
                            <strong>Email:</strong> {$email}<br>
                            <strong>Subject:</strong> {$subject}<br>
                        </p>
                        <div style='border-top:1px solid #eee;padding-top:20px;margin-top:20px;'>
                            <h3 style='color:#333;'>Message:</h3>
                            <p style='font-size:16px;color:#555;line-height:1.6;'>{$message}</p>
                        </div>
                    </div>
                    <div style='background-color:#000;padding:20px;color:#fff;text-align:center;'>
                        <p style='font-size:14px;'>Salon Zero - Where Beauty Meets Perfection</p>
                        <p style='font-size:12px;'>Visit us at <a href='https://www.salonzero.com' style='color:#fff;'>www.salonzero.com</a></p>
                    </div>
                </div>
            </div>
        ";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
