<?php
  $current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
/* Force sidebar scrolling */
.sidebar,
.sidebar-wrapper,
.sidebar-wrapper .nav {
  height: 100vh !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
}

/* Hide scrollbar but keep scroll functionality */
.sidebar-wrapper::-webkit-scrollbar {
  width: 0px;
  background: transparent; /* optional, just to be safe */
}

.sidebar-wrapper {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;     /* Firefox */
}

/* Sidebar fixed position */
.sidebar {
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  bottom: 0 !important;
}

.sidebar-wrapper .nav {
  padding-bottom: 50px !important;
}

</style>
<div class="sidebar" data-color="blue">
  <div class="sidebar-wrapper">
    <div class="logo">
      <!-- <a href="javascript:void(0)" class="simple-text logo-mini">
        CT
      </a>
      <a href="javascript:void(0)" class="simple-text logo-normal">
        Creative Tim
      </a> -->
    </div>

    <ul class="nav">
      <!-- 🏠 Dashboard -->
      <li class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
        <a href="index.php">
          <i class="tim-icons icon-chart-pie-36"></i>
          <p>Dashboard</p>
        </a>
      </li>

      <!-- 🟦 Pages Section -->
      <style>
        .section-title {
          position: relative;
          padding: 0.5rem;
          margin-left: 20px;
          z-index: 4;
        }
      </style>
      <li class="nav-section">
        <h5 class="text-uppercase section-title">Pages</h5>
        <hr class="my-2">
      </li>

      <!-- 🏠 Page Links -->
      <li class="<?= ($current_page == 'home.php') ? 'active' : '' ?>">
        <a href="home.php">
          <i class="tim-icons icon-bank"></i>
          <p>Home</p>
        </a>
      </li>

      <li class="<?= ($current_page == 'about.php') ? 'active' : '' ?>">
        <a href="about.php">
          <i class="tim-icons icon-single-copy-04"></i>
          <p>About</p>
        </a>
      </li>

      <li class="<?= ($current_page == 'contactus.php') ? 'active' : '' ?>">
        <a href="contactus.php">
          <i class="tim-icons icon-email-85"></i>
          <p>Contact</p>
        </a>
      </li>

      <!-- ⚙️ Expandable Services Submenu -->
      <?php
        // List of subpages under Services
        $services_pages = [
          'reseller.php', 'calling_card.php', 'calling_print.php',
          'a2z_voip.php', 'cc_route.php', 'sms_route.php'
        ];
        $services_active = in_array($current_page, $services_pages) ? 'show' : '';
      ?>
      <li class="submenu <?= $services_active ? 'active' : '' ?>">
        <a data-toggle="collapse" href="#servicesMenu" 
           class="<?= $services_active ? '' : 'collapsed' ?>" 
           aria-expanded="<?= $services_active ? 'true' : 'false' ?>">
          <i class="tim-icons icon-settings"></i>
          <p>Services <b class="caret"></b></p>
        </a>
        <div class="collapse <?= $services_active ?>" id="servicesMenu">
          <ul class="nav">
            <!-- <li class="<?= ($current_page == 'reseller.php') ? 'active' : '' ?>">
              <a href="reseller.php">
                <i class="tim-icons icon-app"></i>
                <span class="sidebar-normal">Reseller</span>
              </a>
            </li> -->
            <li class="<?= ($current_page == 'calling_card.php') ? 'active' : '' ?>">
              <a href="calling_card.php">
                <i class="tim-icons icon-credit-card"></i>
                <span class="sidebar-normal">Calling Card</span>
              </a>
            </li>
            <li class="<?= ($current_page == 'calling_print.php') ? 'active' : '' ?>">
              <a href="calling_print.php">
                <i class="tim-icons icon-badge"></i>
                <span class="sidebar-normal">Calling Card Print</span>
              </a>
            </li>
            <li class="<?= ($current_page == 'a2z_voip.php') ? 'active' : '' ?>">
              <a href="a2z_voip.php">
                <i class="tim-icons icon-headphones"></i>
                <span class="sidebar-normal">A2Z Voip Route</span>
              </a>
            </li>
            <li class="<?= ($current_page == 'cc_route.php') ? 'active' : '' ?>">
              <a href="cc_route.php">
                <i class="tim-icons icon-compass-05"></i>
                <span class="sidebar-normal">CC Route</span>
              </a>
            </li>
            <li class="<?= ($current_page == 'sms_route.php') ? 'active' : '' ?>">
              <a href="sms_route.php">
                <i class="tim-icons icon-chat-33"></i>
                <span class="sidebar-normal">SMS Route</span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      <!-- 📰 Blog -->
      <li class="<?= ($current_page == 'blog.php') ? 'active' : '' ?>">
        <a href="blog.php">
          <i class="tim-icons icon-paper"></i>
          <p>Blog</p>
        </a>
      </li>
      <li class="<?= ($current_page == 'download.php') ? 'active' : '' ?>">
        <a href="download.php">
          <i class="tim-icons icon-cloud-download-93"></i>
          <p>Download</p>
        </a>
      </li>
      <li class="<?= ($current_page == 'rates.php') ? 'active' : '' ?>">
        <a href="rates.php">
          <i class="tim-icons icon-tap-02"></i>
          <p>Rates</p>
        </a>
      </li>
      <li class="nav-section">
        <h5 class="text-uppercase section-title">Settings</h5>
        <hr class="my-2">
      </li>
      <li class="<?= ($current_page == 'contact-settings.php') ? 'active' : '' ?>">
        <a href="contact-settings.php">
          <i class="tim-icons icon-link-72"></i>
          <p>Contact</p>
        </a>
      </li>
      <li class="<?= ($current_page == 'system.php') ? 'active' : '' ?>">
        <a href="system.php">
          <i class="tim-icons icon-molecule-40"></i>
          <p>System</p>
        </a>
      </li>
      <li class="<?= ($current_page == 'api.php') ? 'active' : '' ?>">
        <a href="api.php">
          <i class="tim-icons icon-globe-2"></i>
          <p>API</p>
        </a>
      </li>

    </ul>
  </div>
</div>
