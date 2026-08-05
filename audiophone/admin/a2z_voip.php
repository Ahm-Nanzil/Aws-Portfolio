<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'a2z_voip'; 

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
          <h3 class="card-title">A2Z voIP Page Management</h3>
          
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
                            <p class="title mb-0">A2Z VOIP Route Section</p>
                            <p class="text-muted">Edit the A2Z VOIP route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#a2zVoipModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">A2Z VOIP Route Description</p>
                            <p class="text-muted">Edit the A2Z VOIP route description details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#a2zVoipDescriptionModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How A2Z VOIP Route Works</p>
                            <p class="text-muted">Edit the how A2Z VOIP route works section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#a2zVoipWorksModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Benefits of Using A2Z VOIP Route</p>
                            <p class="text-muted">Edit the benefits of using A2Z VOIP route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#a2zVoipBenefitsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Power of A2Z VoIP Routes</p>
                            <p class="text-muted">Edit the power of A2Z VoIP routes section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#powerA2ZModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Why Choose the A2Z VOIP Route</p>
                            <p class="text-muted">Edit the why choose the A2Z VOIP route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#chooseA2ZModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Why Choose the A2Z VOIP Route2</p>
                            <p class="text-muted">Edit the Why Choose the A2Z VOIP Route2 details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#chooseA2ZCardsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Intregate A2Z VOIP Route Section</p>
                            <p class="text-muted">Edit the Choose A2Z VOIP Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#integrateA2ZModal" title="Edit Section">
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

<!-- A2Z VOIP Route Modal -->
<div class="modal fade" id="a2zVoipModal" tabindex="-1" role="dialog" aria-labelledby="a2zVoipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="a2zVoipModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit A2Z VOIP Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="a2zVoipTitle">Title</label>
            <input type="text" class="form-control" id="a2zVoipTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['a2z_voip']['title'] ?? '', ENT_QUOTES); ?>">
          </div>
        </div>

        <input type="hidden" name="section" value="a2z_voip">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- A2Z VOIP Route Description Modal -->
<div class="modal fade" id="a2zVoipDescriptionModal" tabindex="-1" role="dialog" aria-labelledby="a2zVoipDescriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="a2zVoipDescriptionModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit A2Z VOIP Route Description
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="a2zVoipTitleDesc">Title</label>
            <input type="text" class="form-control" id="a2zVoipTitleDesc" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['what_a2z_voip']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="a2zVoipDescription">Description</label>
            <textarea class="form-control html-editor" id="a2zVoipDescription" name="description" rows="6" placeholder="Enter section description"><?php echo htmlspecialchars($sections['what_a2z_voip']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="what_a2z_voip">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- How A2Z VOIP Route Works Modal -->
<div class="modal fade" id="a2zVoipWorksModal" tabindex="-1" role="dialog" aria-labelledby="a2zVoipWorksModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="a2zVoipWorksModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit How A2Z VOIP Route Works
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="a2zWorksTitle">Title</label>
            <input type="text" class="form-control" id="a2zWorksTitle" name="title" placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['a2z_voip_works']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="a2zWorksDescription">Description</label>
            <textarea class="form-control" id="a2zWorksDescription" name="description" rows="5" placeholder="Enter section description"><?php echo htmlspecialchars($sections['a2z_voip_works']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <hr>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['a2z_voip_works']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['a2z_voip_works']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['a2z_voip_works']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['a2z_voip_works']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['a2z_voip_works']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['a2z_voip_works']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['a2z_voip_works']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['a2z_voip_works']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group mb-0">
            <label>Card 5 Title</label>
            <input type="text" class="form-control" name="card5_title" placeholder="Card 5 title"
              value="<?php echo htmlspecialchars($sections['a2z_voip_works']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 5 Description</label>
            <textarea class="form-control" name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['a2z_voip_works']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="a2z_voip_works">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Benefits of Using A2Z VOIP Route Modal -->
<div class="modal fade" id="a2zVoipBenefitsModal" tabindex="-1" role="dialog" aria-labelledby="a2zVoipBenefitsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="a2zVoipBenefitsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Benefits of Using A2Z VOIP Route
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="a2zBenefitsTitle">Title</label>
            <input type="text" class="form-control" id="a2zBenefitsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['a2z_voip_benefits']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description (HTML Editor) -->
          <div class="form-group">
            <label for="a2zBenefitsDescription">Description</label>
            <textarea class="form-control html-editor" name="description" rows="10" placeholder="Enter description..."></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="a2z_voip_benefits">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Power of A2Z VoIP Routes Modal -->
<div class="modal fade" id="powerA2ZModal" tabindex="-1" role="dialog" aria-labelledby="powerA2ZModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="powerA2ZModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Power of A2Z VoIP Routes Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="powerA2ZTitle">Title</label>
            <input type="text" class="form-control" id="powerA2ZTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['power_a2z']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description with HTML editor -->
          <div class="form-group">
            <label for="powerA2ZDescription">Description</label>
            <textarea class="form-control html-editor" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['power_a2z']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="power_a2z">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Why Choose the A2Z VOIP Route Modal -->
<div class="modal fade" id="chooseA2ZModal" tabindex="-1" role="dialog" aria-labelledby="chooseA2ZModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chooseA2ZModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Why Choose the A2Z VOIP Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="chooseA2ZTitle">Title</label>
            <input type="text" class="form-control" id="chooseA2ZTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['choose_a2z']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description with HTML editor -->
          <div class="form-group">
            <label for="chooseA2ZDescription">Description</label>
            <textarea class="form-control html-editor" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['choose_a2z']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="choose_a2z">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Why Choose the A2Z VOIP Route Cards Modal -->
<div class="modal fade" id="chooseA2ZCardsModal" tabindex="-1" role="dialog" aria-labelledby="chooseA2ZCardsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chooseA2ZCardsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Why Choose the A2Z VOIP Route Section (Cards)
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="chooseA2ZCardsTitle">Title</label>
            <input type="text" class="form-control" id="chooseA2ZCardsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['choose_a2z_cards']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

         
          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['choose_a2z_cards']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="4" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['choose_a2z_cards']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['choose_a2z_cards']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="4" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['choose_a2z_cards']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['choose_a2z_cards']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="4" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['choose_a2z_cards']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['choose_a2z_cards']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="4" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['choose_a2z_cards']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="choose_a2z_cards">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Integrate A2Z VOIP Route Modal -->
<div class="modal fade" id="integrateA2ZModal" tabindex="-1" role="dialog" aria-labelledby="integrateA2ZModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="integrateA2ZModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit How to Integrate A2Z VOIP Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="integrateA2ZTitle">Title</label>
            <input type="text" class="form-control" id="integrateA2ZTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['integrate_a2z']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group"> 
            <label for="integrateA2ZDescription">Description</label>
            <textarea class="form-control html-editor" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['integrate_a2z']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="integrate_a2z">

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
