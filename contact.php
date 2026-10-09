<?php
$page_title = "Contact Us & Admission Enquiry";
$active_nav = 'contact';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$success_msg = '';
$error_msg = '';

$preset_course = isset($_GET['course']) ? clean_input($_GET['course']) : '';
$preset_type = isset($_GET['enquiry_type']) ? clean_input($_GET['enquiry_type']) : '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $e_name = clean_input($_POST['e_name'] ?? '');
    $e_mobile = clean_input($_POST['e_mobile'] ?? '');
    $e_email = clean_input($_POST['e_email'] ?? '');
    $e_question = clean_input($_POST['e_question'] ?? '');

    if (empty($e_name) || empty($e_mobile) || empty($e_question)) {
        $error_msg = "Please fill in all required fields (Name, Mobile, and Message).";
    } else {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO enquiry (e_name, e_mobile, e_email, e_question, e_datetime, status, created_by, created_at, updated_by, updated_at)
                    VALUES (?, ?, ?, ?, NOW(), 'ACTIVE', 0, NOW(), 0, NOW())
                ");
                $stmt->execute([$e_name, $e_mobile, $e_email, $e_question]);
                $success_msg = "Thank you! Your enquiry has been received successfully. Our academic counselor will contact you shortly.";
            } catch (PDOException $e) {
                $error_msg = "Failed to submit your enquiry. Please try again or call us directly.";
            }
        } else {
            $success_msg = "Thank you! Your enquiry has been recorded.";
        }
    }
}
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>Contact Us</li>
    </ul>
    <h1 class="page-title">Get In Touch With Us</h1>
    <p class="page-subtitle">Have queries regarding admission, courses, fees, or franchise affiliation? We're here to help.</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem;">
  
  <div style="display:grid; grid-template-columns: 1.2fr 0.8fr; gap:2.5rem; align-items:start;">
    
    <!-- Left: Contact Form -->
    <div>
      <div class="card" style="border-top:4px solid var(--primary-red);">
        <div class="card-header">
          <h2 class="card-title"><i class="fa-solid fa-paper-plane" style="color:var(--primary-red);"></i> Send Us An Enquiry</h2>
        </div>
        <div class="card-body" style="padding:1.75rem;">
          
          <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
              <i class="fa-solid fa-circle-check" style="margin-right:0.5rem;"></i>
              <?= htmlspecialchars($success_msg) ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger">
              <i class="fa-solid fa-triangle-exclamation" style="margin-right:0.5rem;"></i>
              <?= htmlspecialchars($error_msg) ?>
            </div>
          <?php endif; ?>

          <form action="<?= url('/contact.php') ?>" method="POST">
            <div class="form-group">
              <label class="form-label" for="e_name">Full Name <span class="req">*</span></label>
              <input type="text" id="e_name" name="e_name" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
              <div class="form-group">
                <label class="form-label" for="e_mobile">Mobile / WhatsApp Number <span class="req">*</span></label>
                <input type="tel" id="e_mobile" name="e_mobile" class="form-control" placeholder="10-digit mobile number" required>
              </div>

              <div class="form-group">
                <label class="form-label" for="e_email">Email Address</label>
                <input type="email" id="e_email" name="e_email" class="form-control" placeholder="yourname@gmail.com">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="e_question">Your Question / Course of Interest <span class="req">*</span></label>
              <textarea id="e_question" name="e_question" class="form-control" rows="4" placeholder="Mention the course name, batch timing preference, franchise interest, or any questions..." required><?= $preset_course ? "I am interested in the " . htmlspecialchars($preset_course) . " course. Please share admission details and fee structure." : ($preset_type === 'franchise' ? "I am interested in opening an authorized Brainware Computer Academy study center franchise." : "") ?></textarea>
            </div>

            <button type="submit" class="btn btn-red btn-lg" style="width:100%;">
              <i class="fa-solid fa-paper-plane"></i> Submit Enquiry
            </button>
          </form>

        </div>
      </div>
    </div>

    <!-- Right: Head Office Contact Details -->
    <div>
      <div class="card" style="border-top:4px solid var(--primary-blue);">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-building-columns" style="color:var(--primary-blue);"></i> Central Head Office</h3>
        </div>
        <div class="card-body" style="padding:1.5rem;">
          <ul style="list-style:none; display:flex; flex-direction:column; gap:1.25rem; font-size:0.9rem;">
            <li style="display:flex; gap:0.75rem;">
              <i class="fa-solid fa-location-dot" style="color:var(--primary-red); font-size:1.1rem; margin-top:0.2rem;"></i>
              <div>
                <strong style="color:var(--primary-blue-dark); display:block; margin-bottom:0.2rem;">Campus Address</strong>
                <span style="color:var(--text-body); line-height:1.5;">Madho Bihari Lane, Salempur, Chapra, Saran, Bihar - 841301</span>
              </div>
            </li>

            <li style="display:flex; gap:0.75rem;">
              <i class="fa-solid fa-phone" style="color:var(--primary-blue); font-size:1.1rem; margin-top:0.2rem;"></i>
              <div>
                <strong style="color:var(--primary-blue-dark); display:block; margin-bottom:0.2rem;">Admissions & Support Helpline</strong>
                <span style="color:var(--text-body);"><a href="tel:+919835426691">+91 9835426691</a> / <a href="tel:+919030920342">+91 9030920342</a></span>
              </div>
            </li>

            <li style="display:flex; gap:0.75rem;">
              <i class="fa-solid fa-envelope" style="color:var(--primary-red); font-size:1.1rem; margin-top:0.2rem;"></i>
              <div>
                <strong style="color:var(--primary-blue-dark); display:block; margin-bottom:0.2rem;">Official Email</strong>
                <span style="color:var(--text-body);"><a href="mailto:info@brainwarecomputer.co.in">info@brainwarecomputer.co.in</a></span>
              </div>
            </li>

            <li style="display:flex; gap:0.75rem;">
              <i class="fa-solid fa-clock" style="color:var(--success); font-size:1.1rem; margin-top:0.2rem;"></i>
              <div>
                <strong style="color:var(--primary-blue-dark); display:block; margin-bottom:0.2rem;">Office Hours</strong>
                <span style="color:var(--text-body);">Monday to Saturday: 08:00 AM – 06:00 PM<br>(Sunday Closed)</span>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Quick FAQs -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-circle-question" style="color:var(--primary-blue);"></i> Quick Help</h3>
        </div>
        <div class="card-body" style="padding:1.25rem; font-size:0.85rem; color:var(--text-body); line-height:1.6;">
          <p><strong>Are certificates verifiable online?</strong><br>
          Yes, all certificates can be instantly verified 24/7 on our <a href="<?= url('/verification.php') ?>">Verification Portal</a> using Registration No.</p>
        </div>
      </div>

    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
