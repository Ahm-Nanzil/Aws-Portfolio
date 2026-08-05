<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'calling_print'; 

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
          <h3 class="card-title">About Page Management</h3>
          
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
                            <p class="title mb-0">VOIP Calling Card Printing Top Section</p>
                            <p class="text-muted">Edit the top section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipPrintingTopModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Introduction to VOIP Calling Cards</p>
                            <p class="text-muted">Edit the introduction section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipPrintingIntroModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">What is VOIP Calling Card Printing</p>
                            <p class="text-muted">Edit the what is section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipPrintingWhatIsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Steps to Print VOIP Calling Cards</p>
                            <p class="text-muted">Edit the steps section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipPrintStepsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Printing Options for VOIP Calling Cards</p>
                            <p class="text-muted">Edit the printing options section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipPrintOptionsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Key Elements of VOIP Calling Card Design</p>
                            <p class="text-muted">Edit the key elements section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipCardDesignModal" title="Edit Section">
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
</div>





<!-- VOIP Calling Card Printing Top Section Modal -->
<div class="modal fade" id="voipPrintingTopModal" tabindex="-1" role="dialog" aria-labelledby="voipPrintingTopModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipPrintingTopModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit VOIP Calling Card Printing Top Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipPrintingTopTitle">Title</label>
            <input type="text" class="form-control" id="voipPrintingTopTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_print_top']['title'] ?? '', ENT_QUOTES); ?>">
          </div>
        </div>

        <input type="hidden" name="section" value="voip_print_top">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Introduction to VOIP Calling Cards Modal -->
<div class="modal fade" id="voipPrintingIntroModal" tabindex="-1" role="dialog" aria-labelledby="voipPrintingIntroModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipPrintingIntroModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Introduction to VOIP Calling Cards
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipIntroTitle">Title</label>
            <input type="text" class="form-control" id="voipIntroTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_print_intro']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="voipIntroDescription">Description</label>
            <textarea class="form-control html-editor" id="voipIntroDescription" name="description" rows="6" placeholder="Enter section description"><?php echo htmlspecialchars($sections['voip_print_intro']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="voip_print_intro">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- What is VOIP Calling Card Printing Modal -->
<div class="modal fade" id="voipPrintingWhatIsModal" tabindex="-1" role="dialog" aria-labelledby="voipPrintingWhatIsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipPrintingWhatIsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit What is VOIP Calling Card Printing Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipPrintingTitle">Title</label>
            <input type="text" class="form-control" id="voipPrintingTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_print_what_is']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="voipPrintingDescription">Description</label>
            <textarea class="form-control html-editor" id="voipPrintingDescription" name="description" rows="6" placeholder="Enter section description"><?php echo htmlspecialchars($sections['voip_print_what_is']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="voip_print_what_is">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Steps to Print VOIP Calling Cards Modal -->
<div class="modal fade" id="voipPrintStepsModal" tabindex="-1" role="dialog" aria-labelledby="voipPrintStepsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipPrintStepsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Steps to Print VOIP Calling Cards Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipPrintStepsTitle">Title</label>
            <input type="text" class="form-control" id="voipPrintStepsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_print_steps']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="voipPrintStepsDescription">Description</label>
            <textarea class="form-control" id="voipPrintStepsDescription" name="description" rows="6" placeholder="Enter section description"><?php echo htmlspecialchars($sections['voip_print_steps']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['voip_print_steps']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" id="card1Description" name="card1_description" rows="4" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['voip_print_steps']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['voip_print_steps']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" id="card2Description" name="card2_description" rows="4" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['voip_print_steps']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['voip_print_steps']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" id="card3Description" name="card3_description" rows="4" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['voip_print_steps']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['voip_print_steps']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" id="card4Description" name="card4_description" rows="4" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['voip_print_steps']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group">
            <label for="card5Title">Card 5 Title</label>
            <input type="text" class="form-control" id="card5Title" name="card5_title" placeholder="Card 5 title"
            value="<?php echo htmlspecialchars($sections['voip_print_steps']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card5Description">Card 5 Description</label>
            <textarea class="form-control" id="card5Description" name="card5_description" rows="4" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['voip_print_steps']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="voip_print_steps">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Printing Options for VOIP Calling Cards Modal -->
<div class="modal fade" id="voipPrintOptionsModal" tabindex="-1" role="dialog" aria-labelledby="voipPrintOptionsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipPrintOptionsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Printing Options for VOIP Calling Cards Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipPrintOptionsTitle">Title</label>
            <input type="text" class="form-control" id="voipPrintOptionsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_print_options']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          

          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['voip_print_options']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" id="card1Description" name="card1_description" rows="4" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['voip_print_options']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['voip_print_options']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" id="card2Description" name="card2_description" rows="4" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['voip_print_options']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['voip_print_options']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" id="card3Description" name="card3_description" rows="4" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['voip_print_options']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['voip_print_options']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" id="card4Description" name="card4_description" rows="4" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['voip_print_options']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group">
            <label for="card5Title">Card 5 Title</label>
            <input type="text" class="form-control" id="card5Title" name="card5_title" placeholder="Card 5 title"
            value="<?php echo htmlspecialchars($sections['voip_print_options']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card5Description">Card 5 Description</label>
            <textarea class="form-control" id="card5Description" name="card5_description" rows="4" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['voip_print_options']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="voip_print_options">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Key Elements of VOIP Calling Card Design Modal -->
<div class="modal fade" id="voipCardDesignModal" tabindex="-1" role="dialog" aria-labelledby="voipCardDesignModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipCardDesignModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Key Elements of VOIP Calling Card Design
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipCardDesignTitle">Title</label>
            <input type="text" class="form-control" id="voipCardDesignTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_card_design']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['voip_card_design']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" id="card1Description" name="card1_description" rows="4" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['voip_card_design']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['voip_card_design']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" id="card2Description" name="card2_description" rows="4" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['voip_card_design']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['voip_card_design']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" id="card3Description" name="card3_description" rows="4" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['voip_card_design']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['voip_card_design']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" id="card4Description" name="card4_description" rows="4" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['voip_card_design']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group">
            <label for="card5Title">Card 5 Title</label>
            <input type="text" class="form-control" id="card5Title" name="card5_title" placeholder="Card 5 title"
            value="<?php echo htmlspecialchars($sections['voip_card_design']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card5Description">Card 5 Description</label>
            <textarea class="form-control" id="card5Description" name="card5_description" rows="4" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['voip_card_design']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="voip_card_design">

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
