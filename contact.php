<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $company = isset($_POST['company']) ? trim($_POST['company']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        $recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

        $secret_key = '6LfEk6wsAAAAAL_MbbSHH43XkMioqDVmoq4lbTQY';

        $verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha_response);
        $verify_data = json_decode($verify);

        if (empty($name) || empty($email) || empty($message)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (empty($recaptcha_response)) {
            $error = 'Please complete the reCAPTCHA and try again.';
        } elseif (!$verify_data->success) {
            $error = 'reCAPTCHA verification failed. Please try again.';
        } else {
            require_once dirname(__FILE__) . '/PHPMailer/src/PHPMailer.php';
            require_once dirname(__FILE__) . '/PHPMailer/src/SMTP.php';
            require_once dirname(__FILE__) . '/PHPMailer/src/Exception.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION;
            $mail->isSMTP();
            $mail->Host = '192.254.233.230';
            $mail->SMTPAuth = true;
            $mail->Username = 'website@sourceeq.com';
            $mail->Password = 'G2Z%!F#k[P=v';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('website@sourceeq.com', 'Source EQ Website');
            $mail->addAddress('jakaria@sourceeq.com');
            $mail->addReplyTo($email, $name);

            $mail->Subject = 'New Contact Form Submission from ' . htmlspecialchars($name);
            $mail->Body = "Name: " . htmlspecialchars($name) . "\n";
            $mail->Body .= "Email: " . htmlspecialchars($email) . "\n";
            if (!empty($company)) {
                $mail->Body .= "Company: " . htmlspecialchars($company) . "\n";
            }
            if (!empty($phone)) {
                $mail->Body .= "Phone: " . htmlspecialchars($phone) . "\n";
            }
            $mail->Body .= "\nMessage:\n" . htmlspecialchars($message);

            if ($mail->send()) {
                $success = 'Thank you! Your message has been sent. We will be in touch soon.';
            } else {
                $error = 'Failed to send email. Error: ' . $mail->ErrorInfo;
            }
        }
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $error = 'Sorry, there was a problem sending your message. Please try again. Error: ' . $e->getMessage();
    } catch (Throwable $e) {
        $error = 'An error occurred: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Source EQ | Contact Us</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <script src="https://www.google.com/recaptcha/api.js?render=6LfEk6wsAAAAAKrSIykCLDGeow4kIzBJDNNSPcjN" async defer></script>
    <style>
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      :root {
        --teal: #38bbe7;
        --navy: #030b14;
        --navy-2: #071525;
        --text: #e8f4fc;
        --muted: #7fa8c4;
        --white: #ffffff;
        --font-display: "Barlow Condensed", sans-serif;
        --font-body: "Barlow", sans-serif;
      }
      html { scroll-behavior: smooth; }
      body {
        background: var(--navy);
        color: var(--text);
        font-family: var(--font-body);
        font-size: 16px;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .container {
        max-width: 600px;
        width: 100%;
        padding: 3rem 2rem;
      }
      h1 {
        font-family: var(--font-display);
        font-weight: 900;
        font-size: 2.5rem;
        text-transform: uppercase;
        color: var(--white);
        margin-bottom: 2rem;
        text-align: center;
      }
      .success, .error {
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        border-radius: 4px;
        font-size: 1rem;
        text-align: center;
      }
      .success {
        background: rgba(56, 187, 231, 0.15);
        border: 1px solid var(--teal);
        color: var(--teal);
      }
      .error {
        background: rgba(231, 56, 56, 0.15);
        border: 1px solid #e73838;
        color: #e73838;
      }
      .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
      .form-group { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.5rem; }
      .form-group label {
        font-size: 0.78rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 500;
      }
      .form-group input, .form-group textarea {
        background: var(--navy-2);
        border: 1px solid rgba(56, 187, 231, 0.2);
        padding: 0.9rem 1rem;
        color: var(--text);
        font-family: var(--font-body);
        font-size: 1rem;
        transition: border-color 0.2s;
      }
      .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: var(--teal);
      }
      .form-group textarea { resize: vertical; min-height: 120px; }
      .btn-submit {
        background: var(--teal);
        color: var(--navy);
        font-family: var(--font-display);
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 1rem 2.5rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
      }
      .btn-submit:hover { background: var(--white); transform: translateY(-2px); }
      .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }
      .back-link { display: block; text-align: center; margin-top: 2rem; color: var(--teal); text-decoration: none; }
      .back-link:hover { text-decoration: underline; }
      @media (max-width: 680px) { .form-row { grid-template-columns: 1fr; } }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Contact Us</h1>
      
      <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <a href="index.html" class="back-link">← Back to Home</a>
      <?php else: ?>
        <?php if ($error): ?>
          <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" id="contactForm">
          <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
          <div class="form-row">
            <div class="form-group">
              <label for="name">Your Name *</label>
              <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="company">Organization</label>
              <input type="text" id="company" name="company" value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company']) : ''; ?>" />
            </div>
            <div class="form-group">
              <label for="phone">Phone</label>
              <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" />
            </div>
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
          </div>
          <button type="submit" class="btn-submit">Send Message →</button>
        </form>
        <a href="index.html" class="back-link">← Back to Home</a>
      <?php endif; ?>
    </div>
    <script>
      document.getElementById('contactForm').addEventListener('submit', function(e) {
        var btn = e.target.querySelector('.btn-submit');
        if (btn.disabled) return;
        
        btn.disabled = true;
        btn.textContent = 'Sending...';
        
        e.preventDefault();
        grecaptcha.ready(function() {
          grecaptcha.execute('6LfEk6wsAAAAAKrSIykCLDGeow4kIzBJDNNSPcjN', {action: 'submit'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            e.target.submit();
          }).catch(function() {
            btn.disabled = false;
            btn.textContent = 'Send Message →';
          });
        });
      });
    </script>
  </body>
</html>