<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'home'; 

/**
 * Save or update a page section
 *
 * @param PDO $pdo
 * @param string $pageName
 * @param string $section
 * @param array $data
 * @return string JSON-encoded notification
 */
function savePageSection($pdo, $pageName, $section, $data) {
    $contentData = $data;
    unset($contentData['section']); 
    $contentJson = json_encode($contentData);

    $stmt = $pdo->prepare("SELECT id FROM pages WHERE page_name = ? AND section_name = ?");
    $stmt->execute([$pageName, $section]);
    $existing = $stmt->fetch();

    if ($existing) {
        $update = $pdo->prepare("UPDATE pages SET content = ?, updated_at = NOW() WHERE id = ?");
        $update->execute([$contentJson, $existing['id']]);
        return json_encode(['type' => 'success', 'message' => ucfirst($section) . ' section updated!']);
    } else {
        $insert = $pdo->prepare("INSERT INTO pages (page_name, section_name, content) VALUES (?, ?, ?)");
        $insert->execute([$pageName, $section, $contentJson]);
        return json_encode(['type' => 'success', 'message' => ucfirst($section) . ' section saved!']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section'])) {
    $section = $_POST['section'];
    $notification = savePageSection($pdo, $pageName, $section, $_POST);
}

$sections = [];
$stmt = $pdo->prepare("SELECT section_name, content FROM pages WHERE page_name = ?");
$stmt->execute([$pageName]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $sections[$row['section_name']] = json_decode($row['content'], true);
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
          <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
              <div class="card card-tasks">
                <div class="card-body">
                  <div class="table-full-width table-responsive">
                    <table class="table">
                      <tbody>
                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Hero Section</p>
                            <p class="text-muted">Main banner with title and call-to-action</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#heroModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">VoIP Section</p>
                            <p class="text-muted">Setup Requirements</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voIPModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Services Section</p>
                            <p class="text-muted">Services overview and features</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#servicesModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">VoIP Cards Calling Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">CC Route (VOIP Solutions) Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccrouteModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How CC Route Works Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccrouteWorkModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
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
          <i class="tim-icons icon-simple-remove"></i>
        </button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="heroTitle">Title</label>
            <input type="text" class="form-control" id="heroTitle" name="title" placeholder="Enter hero title" value="<?php echo $sections['hero']['title'] ?? ''; ?>">
          </div>
          <input type="hidden" name="section" value="hero">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i>
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- voIP Section Modal -->
<div class="modal fade" id="voIPModal" tabindex="-1" role="dialog" aria-labelledby="voIPModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voIPModalLabel">
          <i class="tim-icons icon-single-02 mr-2"></i>
          Edit VoIP Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="voipTitle">Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="voipTitle" 
              name="title" 
              placeholder="Enter VoIP section title" 
              value="<?php echo htmlspecialchars($sections['voIP']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <div class="form-group">
            <label for="voipDescription">Description</label>
            <textarea 
              class="form-control html-editor" 
              id="voipDescription" 
              name="description" 
              rows="6" 
              placeholder="Enter section description"><?php echo htmlspecialchars($sections['voIP']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="voIP">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Services Section Modal -->
<div class="modal fade" id="servicesModal" tabindex="-1" role="dialog"
  aria-labelledby="servicesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="servicesModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Services Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Main Title -->
          <div class="form-group">
            <label for="servicesTitle">Main Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="servicesTitle" 
              name="title" 
              placeholder="Enter section title" 
              value="<?php echo htmlspecialchars($sections['services']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Main Description -->
          <div class="form-group">
            <label for="servicesDescription">Main Description</label>
            <textarea 
              class="form-control" 
              id="servicesDescription" 
              name="description" 
              rows="4" 
              placeholder="Enter main description"><?php echo htmlspecialchars($sections['services']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <hr>
          <h6><strong>Service Cards</strong></h6>

          <!-- Card 1 -->
          <div class="border p-3 mb-3 rounded">
            <h6>Service 1</h6>
            <div class="form-group">
              <label>Title</label>
              <input type="text" class="form-control" name="card1_title" value="<?php echo htmlspecialchars($sections['services']['card1_title'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="card1_description" rows="3"><?php echo htmlspecialchars($sections['services']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="border p-3 mb-3 rounded">
            <h6>Service 2</h6>
            <div class="form-group">
              <label>Title</label>
              <input type="text" class="form-control" name="card2_title" value="<?php echo htmlspecialchars($sections['services']['card2_title'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="card2_description" rows="3"><?php echo htmlspecialchars($sections['services']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="border p-3 mb-3 rounded">
            <h6>Service 3</h6>
            <div class="form-group">
              <label>Title</label>
              <input type="text" class="form-control" name="card3_title" value="<?php echo htmlspecialchars($sections['services']['card3_title'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="card3_description" rows="3"><?php echo htmlspecialchars($sections['services']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
          </div>

          <!-- Card 4 -->
          <div class="border p-3 mb-3 rounded">
            <h6>Service 4</h6>
            <div class="form-group">
              <label>Title</label>
              <input type="text" class="form-control" name="card4_title" value="<?php echo htmlspecialchars($sections['services']['card4_title'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="card4_description" rows="3"><?php echo htmlspecialchars($sections['services']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
          </div>

          <!-- Card 5 -->
          <div class="border p-3 mb-3 rounded">
            <h6>Service 5</h6>
            <div class="form-group">
              <label>Title</label>
              <input type="text" class="form-control" name="card5_title" value="<?php echo htmlspecialchars($sections['services']['card5_title'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="card5_description" rows="3"><?php echo htmlspecialchars($sections['services']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
          </div>

          <!-- Card 6 -->
          <div class="border p-3 mb-3 rounded">
            <h6>Service 6</h6>
            <div class="form-group">
              <label>Title</label>
              <input type="text" class="form-control" name="card6_title" value="<?php echo htmlspecialchars($sections['services']['card6_title'] ?? '', ENT_QUOTES); ?>">
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea class="form-control" name="card6_description" rows="3"><?php echo htmlspecialchars($sections['services']['card6_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
          </div>
        </div>

        <input type="hidden" name="section" value="services">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
    </div>
</div>

<!-- VoIP Calling Cards Modal -->
<div class="modal fade" id="voipModal" tabindex="-1" role="dialog" aria-labelledby="voipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipModalLabel">
          <i class="tim-icons icon-mobile mr-2"></i>
          Edit VoIP Calling Cards Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipTitle">Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="voipTitle" 
              name="title" 
              placeholder="Enter section title" 
              value="<?php echo htmlspecialchars($sections['voip_calling']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="voipDescription">Description</label>
            <textarea 
              class="form-control html-editor" 
              id="voipDescription" 
              name="description" 
              rows="6" 
              placeholder="Enter section description"><?php echo htmlspecialchars($sections['voip_calling']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="voip_calling">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CC Route (VOIP Solutions) Modal -->
<div class="modal fade" id="ccrouteModal" tabindex="-1" role="dialog" aria-labelledby="ccrouteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccrouteModalLabel">
          <i class="tim-icons icon-phone mr-2"></i>
          Edit CC Route (VOIP Solutions) Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="ccrouteTitle">Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="ccrouteTitle" 
              name="title" 
              placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['ccroute']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="ccrouteDescription">Description</label>
            <textarea 
              class="form-control html-editor" 
              id="ccrouteDescription" 
              name="description" 
              rows="8" 
              placeholder="Enter section description"><?php echo htmlspecialchars($sections['ccroute']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="ccroute">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- How CC Route Works Modal -->
<div class="modal fade" id="ccrouteWorkModal" tabindex="-1" role="dialog" aria-labelledby="ccrouteWorkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> <!-- Centered modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccrouteWorkModalLabel">
          <i class="tim-icons icon-settings-gear-63 mr-2"></i>
          Edit "How CC Route Works" Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="ccrouteWorkTitle">Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="ccrouteWorkTitle" 
              name="title" 
              placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['ccroute_work']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="ccrouteWorkDescription">Description</label>
            <textarea 
              class="form-control" 
              id="ccrouteWorkDescription" 
              name="description" 
              rows="4" 
              placeholder="Enter section description"><?php echo htmlspecialchars($sections['ccroute_work']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <hr>
          <h6 class="text-primary mb-3">Feature Cards</h6>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Clear Branding"
              value="<?php echo htmlspecialchars($sections['ccroute_work']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Enter description"><?php echo htmlspecialchars($sections['ccroute_work']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Multiple Carriers Integration"
              value="<?php echo htmlspecialchars($sections['ccroute_work']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Enter description"><?php echo htmlspecialchars($sections['ccroute_work']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Real-Time Routing Decisions"
              value="<?php echo htmlspecialchars($sections['ccroute_work']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Enter description"><?php echo htmlspecialchars($sections['ccroute_work']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Quality of Service (QoS) Monitoring"
              value="<?php echo htmlspecialchars($sections['ccroute_work']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Enter description"><?php echo htmlspecialchars($sections['ccroute_work']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="ccroute_work">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>





<?php include 'includes/footer.php'; ?>