<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';

// Read the services data from JSON file
$servicesData = file_get_contents('services.json');
$services = json_decode($servicesData, true);

// Get the requested service ID from URL parameter
$serviceId = isset($_GET['id']) ? $_GET['id'] : 'web-design';

// Find the requested service
$currentService = null;
foreach ($services as $service) {
    if ($service['id'] === $serviceId) {
        $currentService = $service;
        break;
    }
}

// If service not found, default to the first one
if (!$currentService && count($services) > 0) {
    $currentService = $services[0];
    $serviceId = $currentService['id'];
}

// Set page title based on service
$pageTitle = $currentService ? $currentService['title'] . ' - Ahm Nanzil' : 'Service Details - Ahm Nanzil';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  
  <!-- Dynamic SEO Meta Tags -->
  <title><?php echo htmlspecialchars($currentService ? $currentService['seo']['title'] : $pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($currentService ? $currentService['seo']['description'] : 'Professional services by Ahm Nanzil'); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($currentService ? $currentService['seo']['keywords'] : 'web development, software services, portfolio'); ?>">
  
  <!-- Canonical URL to avoid duplicate content -->
  <link rel="canonical" href="https://ahmnanzil.me/service-details.php?id=<?php echo htmlspecialchars($serviceId); ?>" />
  
  <!-- Open Graph / Social Media Meta Tags -->
  <meta property="og:title" content="<?php echo htmlspecialchars($currentService ? $currentService['seo']['title'] : $pageTitle); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($currentService ? $currentService['seo']['description'] : 'Professional services by Ahm Nanzil'); ?>">
  <meta property="og:url" content="https://ahmnanzil.me/service-details.php?id=<?php echo htmlspecialchars($serviceId); ?>">
  <meta property="og:type" content="website">
  <meta property="og:image" content="https://ahmnanzil.me/assets/img/<?php echo htmlspecialchars($currentService ? $currentService['image'] : 'services.jpg'); ?>">
  <meta property="og:site_name" content="Ahm Nanzil Portfolio">
  
  <!-- Twitter Card Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($currentService ? $currentService['seo']['title'] : $pageTitle); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($currentService ? $currentService['seo']['description'] : 'Professional services by Ahm Nanzil'); ?>">
  <meta name="twitter:image" content="https://ahmnanzil.me/assets/img/<?php echo htmlspecialchars($currentService ? $currentService['image'] : 'services.jpg'); ?>">
  <meta name="twitter:site" content="@ahm_nanzil">
  
  <base href="<?= $base ?>">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/software_developer.jpg" rel="">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  
  <!-- Schema.org markup for Google -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "<?php echo htmlspecialchars($currentService ? $currentService['title'] : 'Professional Services'); ?>",
    "description": "<?php echo htmlspecialchars($currentService ? $currentService['seo']['description'] : 'Professional services by Ahm Nanzil'); ?>",
    "provider": {
      "@type": "Person",
      "name": "Ahm Nanzil",
      "url": "https://ahmnanzil.me",
      "sameAs": [
        "https://x.com/ahm_nanzil",
        "https://www.linkedin.com/in/ahmnanzil",
        "https://www.instagram.com/ahm_nanzil77"
      ]
    },
    "serviceType": "<?php echo htmlspecialchars($currentService ? $currentService['category'] : 'Web Development'); ?>",
    "areaServed": {
      "@type": "Country",
      "name": "Global"
    }
  }
  </script>
</head>

<body class="service-details-page">

  <header id="header" class="header dark-background d-flex flex-column">
    <i class="header-toggle d-xl-none bi bi-list"></i>

    <div class="profile-img">
      <img src="assets/img/mine.jpg" alt="" class="img-fluid rounded-circle">
    </div>

    <a href="#" class="logo d-flex align-items-center justify-content-center">
      <!-- Uncomment the line below if you also wish to use an image logo -->
      <!-- <img src="assets/img/logo.png" alt=""> -->
      <h1 class="sitename">Ahm Nanzil</h1>
    </a>

    <div class="social-links text-center">
      <a href="https://x.com/ahm_nanzil" class="twitter"><i class="bi bi-twitter-x"></i></a>
      <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
      <a href="https://www.instagram.com/ahm_nanzil77?igsh=MWxta3pxano1N3RrbQ==" class="instagram"><i class="bi bi-instagram"></i></a>
      <a href="#" class="google-plus"><i class="bi bi-skype"></i></a>
      <a href="https://www.linkedin.com/in/ahmnanzil" class="linkedin"><i class="bi bi-linkedin"></i></a>
    </div>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="#hero" class="active"><i class="bi bi-house navicon"></i>Home</a></li>
        <li><a href="#about"><i class="bi bi-person navicon"></i> About</a></li>
        <li><a href="#resume"><i class="bi bi-file-earmark-text navicon"></i> Resume</a></li>
        <li><a href="#portfolio"><i class="bi bi-images navicon"></i> Portfolio</a></li>
        <li class="dropdown">
          <a href="#services">
            <i class="bi bi-menu-button navicon"></i>
            <span>Services</span>
            <i class="bi bi-chevron-down toggle-dropdown"></i>
          </a>
          <ul>
            <li><a href="services/custom-web-development"><i class="bi bi-globe2"></i> Web Development</a></li>
            <li><a href="services/software-application-development"><i class="bi bi-cpu"></i> Software Apps</a></li>
            <li><a href="services/ecommerce-solutions"><i class="bi bi-cart3"></i> E-commerce</a></li>
            <li><a href="services/api-development"><i class="bi bi-share"></i> API Integration</a></li>
            <li><a href="services/database-design"><i class="bi bi-server"></i> Database Design</a></li>
            <li><a href="services/maintenance-support"><i class="bi bi-tools"></i> Support</a></li>
            <li><a href="services/bug-fixing"><i class="bi bi-bug"></i> Bug Fixing</a></li>
            <li><a href="services/coding-help"><i class="bi bi-code-slash"></i> Coding Help</a></li>
            <li><a href="services/devops-hosting"><i class="bi bi-hdd-network"></i> DevOps & Hosting</a></li>
          </ul>
        </li>
        <li><a href="#contact"><i class="bi bi-envelope navicon"></i> Contact</a></li>
      </ul>
    </nav>
  </header>


  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Service Details</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Home</a></li>
            <li class="current">Service Details</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Service Details Section -->
    <section id="service-details" class="service-details section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="services-list">
              <?php foreach ($services as $service): ?>
                <a href="services/<?php echo htmlspecialchars($service['id']); ?>" 
                   class="<?php echo $service['id'] === $serviceId ? 'active' : ''; ?>">
                  <?php echo htmlspecialchars($service['title']); ?>
                </a>
              <?php endforeach; ?>
            </div>

            <?php if ($currentService): ?>
              <h4><?php echo htmlspecialchars($currentService['short_description']); ?></h4>
              <p><?php echo htmlspecialchars($currentService['summary']); ?></p>
            <?php endif; ?>
          </div>

          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
            <?php if ($currentService): ?>
              <img src="assets/img/<?php echo htmlspecialchars($currentService['image']); ?>" alt="<?php echo htmlspecialchars($currentService['title']); ?>" class="img-fluid services-img">
              <h3><?php echo htmlspecialchars($currentService['main_heading']); ?></h3>
              <p><?php echo htmlspecialchars($currentService['description']); ?></p>
              <ul>
                <?php foreach ($currentService['features'] as $feature): ?>
                  <li><i class="bi bi-check-circle"></i> <span><?php echo htmlspecialchars($feature); ?></span></li>
                <?php endforeach; ?>
              </ul>
              <?php if (!empty($currentService['additional_info'])): ?>
                <p><?php echo htmlspecialchars($currentService['additional_info']); ?></p>
              <?php endif; ?>
              <?php if (!empty($currentService['detailed_description'])): ?>
                <p><?php echo htmlspecialchars($currentService['detailed_description']); ?></p>
              <?php endif; ?>
            <?php else: ?>
              <p>Service details not found.</p>
            <?php endif; ?>
          </div>

        </div>

      </div>

    </section><!-- /Service Details Section -->

  </main>
  <style>
    .social-connections {
      margin-top: 50px;
      position: relative;
      padding-top: 20px;
    }

    .social-connections::before {
      content: '';
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 2px;
      background: linear-gradient(to right, transparent, rgba(20, 157, 221, 0.5), transparent);
    }

    .social-connections h4 {
      font-size: 22px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #173b6c;
    }

    .social-icons {
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    .social-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: #f5f8fd;
      color: #149ddd;
      font-size: 22px;
      transition: all 0.3s ease;
      border: 1px solid rgba(20, 157, 221, 0.1);
      position: relative;
      overflow: hidden;
    }

    .social-icon::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #149ddd, #64b5f6);
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
    }

    .social-icon:hover {
      color: white;
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(20, 157, 221, 0.3);
      border-color: transparent;
    }

    .social-icon:hover::before {
      opacity: 1;
    }

    .footer .copyright {
          margin-top: 2rem;
      }

  </style>
  <footer id="footer" class="footer position-relative light-background">

    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
              <div class="social-connections">
                <h4>Connect With Me</h4>
                <div class="social-icons">
                  <a href="https://github.com/Ahm-Nanzil" target="_blank" class="social-icon"><i class="bi bi-github"></i></a>
                  <a href="https://www.linkedin.com/in/ahmnanzil" target="_blank" class="social-icon"><i class="bi bi-linkedin"></i></a>
                  <a href="https://x.com/ahm_nanzil" target="_blank" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                  <a href="https://www.instagram.com/ahm_nanzil77?igsh=MWxta3pxano1N3RrbQ==" target="_blank" class="social-icon"><i class="bi bi-instagram"></i></a>
                  <a href="https://www.upwork.com/freelancers/~0188c3e0f408323508?mp_source=share" target="_blank" class="social-icon">Up</a>
                  <a href="https://www.fiverr.com/s/qD6ZbRZ" target="_blank" class="social-icon">Fi</a>
                </div>
              </div>
        </div>
      </div>
      <div class="copyright text-center ">
          <p>&copy; <span id="currentYear"></span> All Rights Reserved | Designed with 
                  <i class="bi bi-heart-fill" style="color: #e25555;"></i> 
                  by <a href="#">Ahm Nanzil</a>
                </p>      
      </div>
     <script>
            document.getElementById("currentYear").textContent = new Date().getFullYear();
      </script>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <style>
      /*--------------------------------------------------------------
      # Beautiful Modern Preloader
      --------------------------------------------------------------*/
      #preloader {
        position: fixed;
        inset: 0;
        z-index: 999999;
        overflow: hidden;
        background: linear-gradient(135deg, var(--background-color, #1a1a2e), var(--background-color, #16213e));
        backdrop-filter: blur(10px);
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
      }

      /* Main loader container */
      .preloader-container {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }

      /* Outer ring with gradient */
      .preloader-ring {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: conic-gradient(
          from 0deg,
          transparent,
          var(--accent-color, #007bff),
          transparent 120deg
        );
        animation: rotate-ring 2s linear infinite;
        padding: 4px;
      }

      .preloader-ring::before {
        content: '';
        position: absolute;
        inset: 4px;
        background: var(--background-color, #1a1a2e);
        border-radius: 50%;
        z-index: 1;
      }

      /* Inner spinning elements */
      .preloader-inner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80px;
        height: 80px;
        z-index: 2;
      }

      .preloader-dot {
        position: absolute;
        width: 12px;
        height: 12px;
        background: var(--accent-color, #007bff);
        border-radius: 50%;
        box-shadow: 0 0 20px var(--accent-color, #007bff);
        animation: orbit 1.5s linear infinite;
      }

      .preloader-dot:nth-child(1) {
        animation-delay: 0s;
      }

      .preloader-dot:nth-child(2) {
        animation-delay: -0.5s;
      }

      .preloader-dot:nth-child(3) {
        animation-delay: -1s;
      }

      /* Pulsing center dot */
      .preloader-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        background: radial-gradient(circle, var(--accent-color, #007bff), transparent);
        border-radius: 50%;
        animation: pulse-center 2s ease-in-out infinite;
        z-index: 3;
      }

      /* Loading text */
      .preloader-text {
        margin-top: 40px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 16px;
        font-weight: 500;
        color: var(--accent-color, #007bff);
        letter-spacing: 2px;
        text-transform: uppercase;
        animation: fade-text 2s ease-in-out infinite;
      }

      /* Progress dots */
      .preloader-progress {
        display: flex;
        gap: 8px;
        margin-top: 20px;
      }

      .progress-dot {
        width: 8px;
        height: 8px;
        background: var(--accent-color, #007bff);
        border-radius: 50%;
        opacity: 0.3;
        animation: progress-wave 1.5s ease-in-out infinite;
      }

      .progress-dot:nth-child(1) { animation-delay: 0s; }
      .progress-dot:nth-child(2) { animation-delay: 0.2s; }
      .progress-dot:nth-child(3) { animation-delay: 0.4s; }
      .progress-dot:nth-child(4) { animation-delay: 0.6s; }
      .progress-dot:nth-child(5) { animation-delay: 0.8s; }

      /* Animations */
      @keyframes rotate-ring {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      @keyframes orbit {
        0% {
          transform: rotate(0deg) translateX(35px) rotate(0deg);
          opacity: 1;
          scale: 1;
        }
        50% {
          opacity: 0.7;
          scale: 1.2;
        }
        100% {
          transform: rotate(360deg) translateX(35px) rotate(-360deg);
          opacity: 1;
          scale: 1;
        }
      }

      @keyframes pulse-center {
        0%, 100% {
          transform: translate(-50%, -50%) scale(1);
          opacity: 1;
        }
        50% {
          transform: translate(-50%, -50%) scale(1.5);
          opacity: 0.6;
        }
      }

      @keyframes fade-text {
        0%, 100% {
          opacity: 0.7;
        }
        50% {
          opacity: 1;
        }
      }

      @keyframes progress-wave {
        0%, 100% {
          opacity: 0.3;
          transform: scale(1);
        }
        50% {
          opacity: 1;
          transform: scale(1.3);
        }
      }

      /* Fade out animation when loading is complete */
      #preloader.loaded {
        opacity: 0;
        visibility: hidden;
        transform: scale(1.1);
      }

      /* Alternative compact version */
      .preloader-compact {
        width: 60px;
        height: 60px;
      }

      .preloader-compact .preloader-ring {
        width: 60px;
        height: 60px;
      }

      .preloader-compact .preloader-inner {
        width: 40px;
        height: 40px;
      }

      .preloader-compact .preloader-dot {
        width: 8px;
        height: 8px;
      }

      .preloader-compact .preloader-center {
        width: 12px;
        height: 12px;
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .preloader-ring {
          width: 100px;
          height: 100px;
        }
        
        .preloader-inner {
          width: 60px;
          height: 60px;
        }
        
        .preloader-text {
          font-size: 14px;
          margin-top: 30px;
        }
      }

      /* Dark mode enhancements */
      @media (prefers-color-scheme: dark) {
        #preloader {
          background: linear-gradient(135deg, #0a0a0a, #1a1a2e);
        }
        
        .preloader-ring::before {
          background: #0a0a0a;
        }
      }
  </style>
  <!-- Preloader -->
  <div id="preloader">
    <div class="preloader-container">
      <div class="preloader-ring">
        <div class="preloader-inner">
          <div class="preloader-dot"></div>
          <div class="preloader-dot"></div>
          <div class="preloader-dot"></div>
        </div>
        <div class="preloader-center"></div>
      </div>
      <div class="preloader-text">Loading</div>
      <div class="preloader-progress">
        <div class="progress-dot"></div>
        <div class="progress-dot"></div>
        <div class="progress-dot"></div>
        <div class="progress-dot"></div>
        <div class="progress-dot"></div>
      </div>
    </div>
  </div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/typed.js/typed.umd.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>