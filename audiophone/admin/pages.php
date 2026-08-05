<?php
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/navbar.php';

// Home page specific form handling code here
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle home page form submissions
    if (isset($_POST['hero_title'])) {
        // Save hero section data
        $heroTitle = $_POST['hero_title'];
        // Your save logic here
    }
    // Add more form handling for other sections
}
?>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-category">Frontend Management</h5>
          <h3 class="card-title">Home Page Management</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Left Sidebar - Page Links (4 columns) -->
            

            <!-- Right Content Area - Sections (8 columns) -->
            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title">Home Page Sections</h4>
                  <p class="card-category">Manage your home page content sections</p>
                </div>
                <div class="card-body">
                  <div class="list-group">
                    <button class="list-group-item list-group-item-action d-flex align-items-center" data-toggle="modal" data-target="#heroModal">
                      <i class="tim-icons icon-image mr-3"></i>
                      <div>
                        <h6 class="mb-0">Hero Section</h6>
                        <small class="text-muted">Main banner with title and call-to-action</small>
                      </div>
                    </button>
                    
                    <button class="list-group-item list-group-item-action d-flex align-items-center" data-toggle="modal" data-target="#aboutModal">
                      <i class="tim-icons icon-single-02 mr-3"></i>
                      <div>
                        <h6 class="mb-0">About Section</h6>
                        <small class="text-muted">Company introduction and overview</small>
                      </div>
                    </button>
                    
                    <button class="list-group-item list-group-item-action d-flex align-items-center" data-toggle="modal" data-target="#servicesModal">
                      <i class="tim-icons icon-settings mr-3"></i>
                      <div>
                        <h6 class="mb-0">Services Section</h6>
                        <small class="text-muted">Services overview and features</small>
                      </div>
                    </button>
                    
                    <button class="list-group-item list-group-item-action d-flex align-items-center" data-toggle="modal" data-target="#testimonialModal">
                      <i class="tim-icons icon-bulb-63 mr-3"></i>
                      <div>
                        <h6 class="mb-0">Testimonial Section</h6>
                        <small class="text-muted">Customer reviews and feedback</small>
                      </div>
                    </button>
                    
                    <button class="list-group-item list-group-item-action d-flex align-items-center" data-toggle="modal" data-target="#footerModal">
                      <i class="tim-icons icon-minimal-down mr-3"></i>
                      <div>
                        <h6 class="mb-0">Footer Section</h6>
                        <small class="text-muted">Contact info and links</small>
                      </div>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Hero Section Modal -->
<div class="modal fade" id="heroModal" tabindex="-1" role="dialog" aria-labelledby="heroModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="heroModalLabel">
          <i class="tim-icons icon-image mr-2"></i>
          Edit Hero Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="index.php" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="heroTitle">Title</label>
            <input type="text" class="form-control" id="heroTitle" name="hero_title" placeholder="Enter hero title" value="<?php echo isset($heroData['title']) ? $heroData['title'] : ''; ?>">
          </div>
          <div class="form-group">
            <label for="heroSubtitle">Subtitle</label>
            <input type="text" class="form-control" id="heroSubtitle" name="hero_subtitle" placeholder="Enter hero subtitle" value="<?php echo isset($heroData['subtitle']) ? $heroData['subtitle'] : ''; ?>">
          </div>
          <div class="form-group">
            <label for="heroDescription">Description</label>
            <textarea class="form-control" id="heroDescription" name="hero_description" rows="3" placeholder="Enter hero description"><?php echo isset($heroData['description']) ? $heroData['description'] : ''; ?></textarea>
          </div>
          <div class="form-group">
            <label for="heroButtonText">Button Text</label>
            <input type="text" class="form-control" id="heroButtonText" name="hero_button_text" placeholder="Enter button text" value="<?php echo isset($heroData['button_text']) ? $heroData['button_text'] : ''; ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i>
            Save Changes
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="tim-icons icon-simple-remove mr-1"></i>
            Close
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- About Section Modal -->
<div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="aboutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aboutModalLabel">
          <i class="tim-icons icon-single-02 mr-2"></i>
          Edit About Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="home.php" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="aboutTitle">About Title</label>
            <input type="text" class="form-control" id="aboutTitle" name="about_title" placeholder="Enter about section title">
          </div>
          <div class="form-group">
            <label for="aboutContent">About Content</label>
            <textarea class="form-control" id="aboutContent" name="about_content" rows="6" placeholder="Enter about section content"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i>
            Save Changes
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="tim-icons icon-simple-remove mr-1"></i>
            Close
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add more modals for other sections... -->

<?php include 'includes/footer.php'; ?>