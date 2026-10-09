<?php
$page_title = "About Us - Brainware Computer Academy";
$active_nav = 'about';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="page-header">
  <div class="container">
    <ul class="breadcrumb">
      <li><a href="<?= url('/') ?>"><i class="fa-solid fa-house"></i> Home</a></li>
      <li><span>/</span></li>
      <li>About Us</li>
    </ul>
    <h1 class="page-title">About Brainware Computer Academy</h1>
    <p class="page-subtitle">Pioneering professional IT training, skill development, and certifications</p>
  </div>
</div>

<div class="container" style="padding-bottom: 4rem;">
  <div style="display:grid; grid-template-columns:1.2fr 0.8fr; gap:2.5rem; align-items:start;">
    
    <div>
      <div class="card">
        <div class="card-header" style="background:#f8fafc;">
          <h2 class="card-title"><i class="fa-solid fa-graduation-cap" style="color:var(--primary-red);"></i> Our Mission & Vision</h2>
        </div>
        <div class="card-body" style="font-size:0.95rem; line-height:1.7; color:var(--text-body);">
          <p style="margin-bottom:1rem;">
            <strong>Brainware Computer Academy</strong> is established with a vision to make quality IT and computer education accessible, practical, and employment-oriented for students from all backgrounds. Over the past decade, we have empowered hundreds of students to establish rewarding careers in software development, web design, financial accounting, hardware, and office automation.
          </p>
          <p style="margin-bottom:1rem;">
            Our syllabus is continually updated to meet real-world industry demands. We emphasize practical computer laboratory exercises, real-time software practice (MS Office, Tally Prime/ERP 9, C/C++, Java, Python, Web Technologies), and comprehensive semester evaluations.
          </p>
          <div style="background:var(--primary-blue-soft); border-left:4px solid var(--primary-blue); padding:1rem; border-radius:4px; margin-top:1.5rem;">
            <h4 style="color:var(--primary-blue-dark); font-size:0.95rem; font-weight:700; margin-bottom:0.35rem;"><i class="fa-solid fa-award"></i> ISO 9001:2015 Quality Assurance</h4>
            <p style="font-size:0.86rem; color:var(--text-body); margin:0;">
              Our quality management system adheres strictly to international educational and examination standards, providing verified certifications with nationwide validity.
            </p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header" style="background:#f8fafc;">
          <h2 class="card-title"><i class="fa-solid fa-handshake" style="color:var(--primary-blue);"></i> Affiliation & Center Network</h2>
        </div>
        <div class="card-body" style="font-size:0.92rem; line-height:1.65;">
          <p>
            Brainware operates a wide network of authorized study centers managed by qualified directors and faculty members. Every authorized center is equipped with high-speed internet, modern computer labs, and continuous centralized academic support from our head office in Chapra, Saran (Bihar).
          </p>
        </div>
      </div>
    </div>

    <div>
      <!-- Director's Message Card -->
      <div class="card" style="border-top:4px solid var(--primary-red);">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-user-tie" style="color:var(--primary-red);"></i> Director's Desk</h3>
        </div>
        <div class="card-body">
          <div style="font-size:0.9rem; line-height:1.65; color:var(--text-body); font-style:italic;">
            "Education is the most powerful weapon which you can use to change the world. At Brainware Computer Academy, we don't just teach software commands; we build problem-solving confidence and technical competence so our students stand out in today's competitive job market."
          </div>
          <div style="margin-top:1.25rem; padding-top:1rem; border-top:1px solid var(--border-color); display:flex; align-items:center; gap:0.75rem;">
            <div style="width:44px; height:44px; border-radius:50%; background:var(--primary-blue); color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:bold;">
              AK
            </div>
            <div>
              <div style="font-weight:700; color:var(--primary-blue-dark); font-size:0.92rem;">Er. Ajit Kumar</div>
              <div style="font-size:0.78rem; color:var(--text-muted);">Director, Brainware Computer Academy</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Highlights -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fa-solid fa-check-double" style="color:var(--success);"></i> Key Highlights</h3>
        </div>
        <div class="card-body" style="padding:0.75rem 1.25rem;">
          <ul style="list-style:none; display:flex; flex-direction:column; gap:0.75rem; font-size:0.88rem;">
            <li><i class="fa-solid fa-circle-check" style="color:var(--success); margin-right:0.5rem;"></i> 100% Practical-oriented syllabus</li>
            <li><i class="fa-solid fa-circle-check" style="color:var(--success); margin-right:0.5rem;"></i> Online QR Verification & Marksheet portal</li>
            <li><i class="fa-solid fa-circle-check" style="color:var(--success); margin-right:0.5rem;"></i> Fast-track & regular batch options</li>
            <li><i class="fa-solid fa-circle-check" style="color:var(--success); margin-right:0.5rem;"></i> Free study material & module guides</li>
            <li><i class="fa-solid fa-circle-check" style="color:var(--success); margin-right:0.5rem;"></i> Placement assistance & career counseling</li>
          </ul>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
