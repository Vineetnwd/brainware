<?php
$page_title = "Home - Premier Computer Education & Certification";
$active_nav = 'home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch quick stats from database
$total_students = 680;
$total_centers = 5;
$total_courses = 17;

if ($pdo) {
  try {
    $total_students = $pdo->query("SELECT COUNT(*) FROM student WHERE status = 'ACTIVE'")->fetchColumn() ?: $total_students;
    $total_centers = $pdo->query("SELECT COUNT(*) FROM center_details WHERE status = 'ACTIVE'")->fetchColumn() ?: $total_centers;
    $total_courses = $pdo->query("SELECT COUNT(*) FROM course_details WHERE status = 'ACTIVE'")->fetchColumn() ?: $total_courses;

    // Fetch top courses
    $courses_stmt = $pdo->query("SELECT * FROM course_details WHERE status = 'ACTIVE' LIMIT 6");
    $popular_courses = $courses_stmt->fetchAll();

    // Fetch centers
    $centers_stmt = $pdo->query("SELECT * FROM center_details WHERE status = 'ACTIVE' LIMIT 4");
    $featured_centers = $centers_stmt->fetchAll();

    // Fetch gallery images (exclude hero sliders)
    $gallery_stmt = $pdo->query("SELECT * FROM gallery WHERE status = 'ACTIVE' AND (file_type != 'slider' OR file_type IS NULL) AND image_url != '' ORDER BY id DESC LIMIT 4");
    $gallery_items = $gallery_stmt->fetchAll();

    // Fetch hero slider images
    $hero_stmt = $pdo->query("SELECT * FROM gallery WHERE status = 'ACTIVE' AND file_type = 'slider' AND image_url != '' ORDER BY id DESC LIMIT 5");
    $hero_items = $hero_stmt->fetchAll();
  } catch (PDOException $e) {
    $popular_courses = [];
    $featured_centers = [];
    $gallery_items = [];
    $hero_items = [];
  }
} else {
  $popular_courses = [];
  $featured_centers = [];
  $gallery_items = [];
  $hero_items = [];
}
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-pattern"></div>
  <div class="hero-glow"></div>
  <div class="container">
    <div class="hero-grid">

      <!-- Hero Left Content -->
      <div>
        <div class="hero-badge">
          <i class="fa-solid fa-star"></i> ISO 9001:2015 Certified IT Institute
        </div>
        <h1 class="hero-title">
          Empowering Your Future with <span class="highlight">Industry-Standard</span> Computer Education
        </h1>
        <p class="hero-desc">
          Join thousands of successful graduates. We offer premier Diploma, Programming, Office Automation, Tally
          Accounting, and Web Development certifications with 100% online verification support.
        </p>
        <div class="hero-actions">
          <a href="<?= url('/courses.php') ?>" class="btn btn-danger btn-lg">
            <i class="fa-solid fa-book-open-reader"></i> Explore Courses
          </a>
          <a href="<?= url('/centers.php') ?>" class="btn btn-outline-primary btn-lg"
            style="color:#ffffff !important; border-color:rgba(255,255,255,0.4);">
            <i class="fa-solid fa-map-location-dot"></i> Find Study Center
          </a>
        </div>
      </div>

      <!-- Hero Right: Quick Verification Card -->
      <div>
        <div class="hero-slider-card">
          <div class="hero-slider-container" id="heroSlider">
            <?php if (!empty($hero_items)): ?>
              <?php foreach ($hero_items as $index => $item): ?>
                <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>">
                  <img src="<?= url('/uploads/gallery/' . htmlspecialchars($item['image_url'] ?? '')) ?>" alt="Hero Slide">
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="hero-slide active">
                <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800" alt="Hero Default 1">
              </div>
              <div class="hero-slide">
                <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&q=80&w=800" alt="Hero Default 2">
              </div>
            <?php endif; ?>
          </div>
          <div class="hero-slider-controls">
            <button onclick="changeHeroSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button onclick="changeHeroSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<style>
.hero-slider-card {
  position: relative;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  aspect-ratio: 4/3;
  width: 100%;
}
.hero-slider-container {
  position: relative;
  width: 100%;
  height: 100%;
}
.hero-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.6s ease-in-out;
}
.hero-slide.active {
  opacity: 1;
  z-index: 2;
}
.hero-slide img {
  width: 100%;
  height: 100%;
  object-fit: fill;
}
.hero-slider-controls {
  position: absolute;
  bottom: 15px;
  right: 15px;
  z-index: 10;
  display: flex;
  gap: 10px;
}
.hero-slider-controls button {
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.2);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.hero-slider-controls button:hover {
  background: var(--primary-blue);
  border-color: var(--primary-blue);
}
</style>

<script>
let currentHeroSlide = 0;
function changeHeroSlide(direction) {
  const slides = document.querySelectorAll('.hero-slide');
  if (slides.length === 0) return;
  slides[currentHeroSlide].classList.remove('active');
  currentHeroSlide = (currentHeroSlide + direction + slides.length) % slides.length;
  slides[currentHeroSlide].classList.add('active');
}

// Auto-advance every 4 seconds
setInterval(() => changeHeroSlide(1), 4000);
</script>



<!-- Stats Counter Strip -->
<div class="container">
  <div class="stats-strip">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-number red"><?= number_format($total_students) ?>+</div>
        <div class="stat-label">Certified Students</div>
      </div>
      <div class="stat-item">
        <div class="stat-number"><?= $total_centers ?>+</div>
        <div class="stat-label">Authorized Centers</div>
      </div>
      <div class="stat-item">
        <div class="stat-number red"><?= $total_courses ?>+</div>
        <div class="stat-label">Career Courses</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">10+</div>
        <div class="stat-label">Years of Excellence</div>
      </div>
    </div>
  </div>
</div>

<!-- Features Section -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-subtitle">Why Brainware</span>
      <h2 class="section-title">Built for Modern Career Success</h2>
      <p class="section-description">
        We bridge the gap between academic education and industry requirements through practical hands-on IT training.
      </p>
    </div>

    <div class="feature-grid">
      <div class="feature-card">
        <div class="feature-icon red"><i class="fa-solid fa-certificate"></i></div>
        <h3 class="feature-title">Recognized Certification</h3>
        <p class="feature-text">Government-aligned curriculum and ISO 9001:2015 certified diplomas valid across private
          and public sectors.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon blue"><i class="fa-solid fa-laptop-code"></i></div>
        <h3 class="feature-title">Hands-on Practical Labs</h3>
        <p class="feature-text">High-spec computer labs with 1:1 student-to-PC ratio ensuring extensive practical
          exposure on real-world projects.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon red"><i class="fa-solid fa-qrcode"></i></div>
        <h3 class="feature-title">Online Student Verification</h3>
        <p class="feature-text">Instant online QR and Registration verification for certificates and semester marksheets
          24/7.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon blue"><i class="fa-solid fa-chalkboard-user"></i></div>
        <h3 class="feature-title">Expert Faculty</h3>
        <p class="feature-text">Experienced certified trainers providing individualized mentoring and fast-track
          guidance.</p>
      </div>
    </div>
  </div>
</section>

<!-- Featured Courses Section -->
<section class="section"
  style="background-color:#ffffff; border-top:1px solid var(--border-color); border-bottom:1px solid var(--border-color);">
  <div class="container">
    <div class="section-header">
      <span class="section-subtitle">Academic Programs</span>
      <h2 class="section-title">Popular Career Courses</h2>
      <p class="section-description">Choose from our top diploma and certificate programs designed for immediate
        employment.</p>
    </div>

    <div class="course-grid">
      <?php if (!empty($popular_courses)): ?>
        <?php foreach ($popular_courses as $c): ?>
          <div class="course-card">
            <div class="course-card-header">
              <span class="course-badge"><?= $c['no_of_semester'] ?>
                <?= ($c['no_of_semester'] > 1) ? 'Semesters' : 'Semester' ?></span>
              <div class="course-code"><?= htmlspecialchars($c['course_code']) ?></div>
              <h3 class="course-title"><?= htmlspecialchars($c['course_name']) ?></h3>
            </div>
            <div class="course-card-body">
              <div class="course-meta-row">
                <span class="course-meta-label"><i class="fa-regular fa-clock"></i> Duration:</span>
                <span class="course-meta-val"><?= htmlspecialchars($c['course_duration']) ?></span>
              </div>
              <div class="course-syllabus-preview">
                <strong><i class="fa-solid fa-list-check"></i> Modules Covered:</strong>
                <p style="margin-top:0.35rem; color:var(--text-muted); font-size:0.83rem;">
                  Comprehensive syllabus including Practical Software Training & Assessment.
                </p>
              </div>
              <div class="course-card-footer">
                <a href="<?= url('/courses.php#course-' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fa-solid fa-eye"></i> View Syllabus
                </a>
                <a href="<?= url('/contact.php?course=' . urlencode($c['course_name'])) ?>" class="btn btn-sm btn-danger">
                  <i class="fa-solid fa-paper-plane"></i> Enquire Now
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center; grid-column:1/-1;">No courses available at the moment.</p>
      <?php endif; ?>
    </div>

    <div style="text-align:center; margin-top:2.5rem;">
      <a href="<?= url('/courses.php') ?>" class="btn btn-primary btn-lg">
        <i class="fa-solid fa-layer-group"></i> View All Available Courses &rarr;
      </a>
    </div>
  </div>
</section>

<!-- Campus Gallery Slider -->
<section class="section" style="background-color:#f8fafc; overflow:hidden;">
  <div class="container">
    <div class="section-header">
      <span class="section-subtitle">Campus Life</span>
      <h2 class="section-title">Photo Gallery</h2>
      <p class="section-description">Glimpses of our vibrant campus, practical labs, and successful events.</p>
    </div>

    <div class="gallery-slider-wrapper">
      <button class="slider-btn prev" onclick="moveSlider(-1)"><i class="fa-solid fa-chevron-left"></i></button>
      <div class="gallery-slider-container" id="gallerySlider">
        <?php if (!empty($gallery_items)): ?>
          <?php foreach ($gallery_items as $item): ?>
            <div class="gallery-slide">
              <img src="<?= url('/uploads/gallery/' . htmlspecialchars($item['image_url'] ?? '')) ?>"
                alt="<?= htmlspecialchars($item['image_title'] ?? 'Gallery Image') ?>">
              <div class="slide-caption"><?= htmlspecialchars($item['image_title'] ?? '') ?></div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Placeholder Slides when Gallery is Empty -->
          <div class="gallery-slide">
            <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800"
              alt="Computer Lab">
            <div class="slide-caption">Modern Computer Labs</div>
          </div>
          <div class="gallery-slide">
            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&q=80&w=800"
              alt="Training">
            <div class="slide-caption">Expert Faculty Training</div>
          </div>
          <div class="gallery-slide">
            <img src="https://images.unsplash.com/photo-1523580494112-071d31172315?auto=format&fit=crop&q=80&w=800"
              alt="Students">
            <div class="slide-caption">Student Interactions</div>
          </div>
        <?php endif; ?>
      </div>
      <button class="slider-btn next" onclick="moveSlider(1)"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
  </div>
</section>

<style>
  .gallery-slider-wrapper {
    position: relative;
    max-width: 1000px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .gallery-slider-container {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 1rem;
  }

  .gallery-slider-container::-webkit-scrollbar {
    display: none;
  }

  .gallery-slide {
    flex: 0 0 calc(33.333% - 1rem);
    scroll-snap-align: start;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    aspect-ratio: 4/3;
    background: #000;
  }

  .gallery-slide img {
    width: 100%;
    height: 100%;
    object-fit: fill;
    transition: transform 0.4s ease;
  }

  .gallery-slide:hover img {
    transform: scale(1.05);
  }

  .slide-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    text-align: center;
  }

  .slider-btn {
    background: #fff;
    border: 1px solid #e2e8f0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-blue);
    font-size: 1.2rem;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
    flex-shrink: 0;
    z-index: 10;
  }

  .slider-btn:hover {
    background: var(--primary-blue);
    color: #fff;
    border-color: var(--primary-blue);
  }

  @media (max-width: 768px) {
    .gallery-slide {
      flex: 0 0 calc(50% - 0.75rem);
    }
  }

  @media (max-width: 480px) {
    .gallery-slide {
      flex: 0 0 100%;
    }
  }
</style>

<script>
  function moveSlider(dir) {
    const container = document.getElementById('gallerySlider');
    const slideWidth = container.querySelector('.gallery-slide').offsetWidth + 24; // Including gap
    container.scrollBy({ left: dir * slideWidth, behavior: 'smooth' });
  }
</script>

<!-- Study Center Network -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-subtitle">Authorized Network</span>
      <h2 class="section-title">Our Study Centers</h2>
      <p class="section-description">Locate an authorized Brainware Computer Academy branch near you.</p>
    </div>

    <div class="center-grid">
      <?php if (!empty($featured_centers)): ?>
        <?php foreach ($featured_centers as $center): ?>
          <div class="center-card">
            <span class="center-code-pill"><i class="fa-solid fa-hashtag"></i>
              <?= htmlspecialchars($center['center_code']) ?></span>
            <h3 class="center-name"><?= htmlspecialchars($center['center_name']) ?></h3>
            <ul class="center-info-list">
              <li>
                <strong><i class="fa-solid fa-location-dot" style="color:var(--text-muted);"></i> Address:</strong>
                <span><?= htmlspecialchars($center['center_address']) ?></span>
              </li>
            </ul>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div style="text-align:center; margin-top:2rem;">
      <a href="<?= url('/centers.php') ?>" class="btn btn-outline-danger">
        <i class="fa-solid fa-building"></i> View All Branches & Details
      </a>
    </div>
  </div>
</section>

<!-- Quick Enquiry Banner -->
<section class="section"
  style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color:#ffffff; border-top: 3px solid var(--primary-red);">
  <div class="container">
    <div style="display:grid; grid-template-columns:1.2fr 0.8fr; gap:2.5rem; align-items:center;">
      <div>
        <h2 style="font-size:2rem; font-weight:800; margin-bottom:0.75rem;">Looking to Start a Career in IT or Open a
          Franchise?</h2>
        <p style="color:#cbd5e1; font-size:1rem; line-height:1.6;">
          Contact our admissions team today for free counseling on course selection, scholarship opportunities, or
          center affiliation partnerships.
        </p>
      </div>
      <div style="text-align:right;">
        <a href="<?= url('/contact.php') ?>" class="btn btn-danger btn-lg"
          style="padding:0.9rem 2rem; font-size:1.05rem;">
          <i class="fa-solid fa-envelope-open-text"></i> Submit Online Enquiry
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>