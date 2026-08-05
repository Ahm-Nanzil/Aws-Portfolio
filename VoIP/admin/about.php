<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'about'; 

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
                            <p class="title mb-0">About Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#aboutModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Our Mission Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#missionModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Why Choose Asian Telecom Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#whyChooseModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">What We Offer Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#whatWeOfferModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Our Values Section</p>
                            <p class="text-muted">Describe</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ourValuesModal" title="Edit Section">
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




<!-- About Section Modal -->
<div class="modal fade" id="aboutModal" tabindex="-1" role="dialog" aria-labelledby="aboutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"> <!-- Same as Hero modal -->
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

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="aboutTitle">Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="aboutTitle" 
              name="title" 
              placeholder="Enter about section title"
              value="<?php echo htmlspecialchars($sections['about']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="aboutDescription">Description</label>
            <textarea 
              class="form-control html-editor" 
              id="aboutDescription" 
              name="description" 
              rows="6" 
              placeholder="Enter about section description"><?php echo htmlspecialchars($sections['about']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="about">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Our Mission Section Modal -->
<div class="modal fade" id="missionModal" tabindex="-1" role="dialog" aria-labelledby="missionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document"> <!-- Top-aligned like Hero modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="missionModalLabel">
          <i class="tim-icons icon-bulb-63 mr-2"></i>
          Edit Our Mission Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="missionTitle">Title</label>
            <input 
              type="text" 
              class="form-control" 
              id="missionTitle" 
              name="title" 
              placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['mission']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="missionDescription">Description</label>
            <textarea 
              class="form-control html-editor" 
              id="missionDescription" 
              name="description" 
              rows="6" 
              placeholder="Enter section description"><?php echo htmlspecialchars($sections['mission']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="mission">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Why Choose Asian Telecom Section Modal -->
<div class="modal fade" id="whyChooseModal" tabindex="-1" role="dialog" aria-labelledby="whyChooseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="whyChooseModalLabel">
          <i class="tim-icons icon-check-2 mr-2"></i>
          Edit Why Choose Asian Telecom Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Main Section Title -->
          <div class="form-group">
            <label for="whyChooseMainTitle">Main Title</label>
            <input type="text" class="form-control" id="whyChooseMainTitle" name="title" placeholder="Enter section main title"
              value="<?php echo htmlspecialchars($sections['why_choose']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <hr>
          <h6 class="text-primary mb-3">Feature Cards</h6>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['why_choose']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['why_choose']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['why_choose']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['why_choose']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['why_choose']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['why_choose']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['why_choose']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['why_choose']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group mb-0">
            <label>Card 5 Title</label>
            <input type="text" class="form-control" name="card5_title" placeholder="Card 5 title"
              value="<?php echo htmlspecialchars($sections['why_choose']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 5 Description</label>
            <textarea class="form-control" name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['why_choose']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="why_choose">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- What We Offer Section Modal -->
<div class="modal fade" id="whatWeOfferModal" tabindex="-1" role="dialog" aria-labelledby="whatWeOfferModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="whatWeOfferModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit What We Offer Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Main Title -->
          <div class="form-group">
            <label for="offerMainTitle">Main Title</label>
            <input type="text" class="form-control" id="offerMainTitle" name="title" placeholder="Enter section main title"
            value="<?php echo htmlspecialchars($sections['what_we_offer']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['what_we_offer']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" id="card1Description" name="card1_description" rows="4" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['what_we_offer']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['what_we_offer']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" id="card2Description" name="card2_description" rows="4" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['what_we_offer']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['what_we_offer']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" id="card3Description" name="card3_description" rows="4" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['what_we_offer']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['what_we_offer']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" id="card4Description" name="card4_description" rows="4" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['what_we_offer']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="what_we_offer">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Our Values Section Modal -->
<div class="modal fade" id="ourValuesModal" tabindex="-1" role="dialog" aria-labelledby="ourValuesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ourValuesModalLabel">
          <i class="tim-icons icon-single-02 mr-2"></i>
          Edit Our Values Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="valuesTitle">Section Title</label>
            <input type="text" class="form-control" id="valuesTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['our_values']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Quote 1 -->
          <div class="form-group">
            <label for="quote1">Quote 1</label>
            <textarea class="form-control html-editor" id="quote1" name="quote1" rows="3" placeholder="Enter first quote"><?php echo htmlspecialchars($sections['our_values']['quote1'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Quote 2 -->
          <div class="form-group">
            <label for="quote2">Quote 2</label>
            <textarea class="form-control html-editor" id="quote2" name="quote2" rows="3" placeholder="Enter second quote"><?php echo htmlspecialchars($sections['our_values']['quote2'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Quote 3 -->
          <div class="form-group">
            <label for="quote3">Quote 3</label>
            <textarea class="form-control html-editor" id="quote3" name="quote3" rows="3" placeholder="Enter third quote"><?php echo htmlspecialchars($sections['our_values']['quote3'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="our_values">

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