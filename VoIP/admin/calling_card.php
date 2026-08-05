<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'calling_card'; 

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
          <h3 class="card-title">Calling Card Page Management</h3>
          
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
                            <p class="title mb-0">VOIP Calling Cards Section</p>
                            <p class="text-muted">Edit the VOIP calling cards section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipCallingCardsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">What is a VOIP Calling Card Section</p>
                            <p class="text-muted">Edit the "What is a VOIP Calling Card" section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#whatIsVoipCardModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How to Choose the Right VOIP Calling Card Section</p>
                            <p class="text-muted">Edit the "How to Choose the Right VOIP Calling Card" section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#chooseVoipCardModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">What equipment do you need to set up VoIP? Section</p>
                            <p class="text-muted">Edit the "What equipment do you need to set up VoIP?" section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipEquipmentModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>
                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How VOIP Calling Cards Work Section</p>
                            <p class="text-muted">Edit the "How VOIP Calling Cards Work" section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#voipCardsWorkModal" title="Edit Section">
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


<!-- VOIP Calling Cards Section Modal -->
<div class="modal fade" id="voipCallingCardsModal" tabindex="-1" role="dialog" aria-labelledby="voipCallingCardsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipCallingCardsModalLabel">
          <i class="tim-icons icon-single-02 mr-2"></i>
          Edit VOIP Calling Cards Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipCallingCardsTitle">Section Title</label>
            <input type="text" class="form-control" id="voipCallingCardsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_calling_cards']['title'] ?? '', ENT_QUOTES); ?>">
          </div>
        </div>

        <input type="hidden" name="section" value="voip_calling_cards">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- What is a VOIP Calling Card Section Modal -->
<div class="modal fade" id="whatIsVoipCardModal" tabindex="-1" role="dialog" aria-labelledby="whatIsVoipCardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="whatIsVoipCardModalLabel">
          <i class="tim-icons icon-single-02 mr-2"></i>
          Edit "What is a VOIP Calling Card" Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="whatIsVoipCardTitle">Title</label>
            <input type="text" class="form-control" id="whatIsVoipCardTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['what_is_voip_card']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="whatIsVoipCardDescription">Description</label>
            <textarea class="form-control html-editor" id="whatIsVoipCardDescription" name="description" rows="5" placeholder="Enter section description"><?php echo htmlspecialchars($sections['what_is_voip_card']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="what_is_voip_card">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- How to Choose the Right VOIP Calling Card Section Modal -->
<div class="modal fade" id="chooseVoipCardModal" tabindex="-1" role="dialog" aria-labelledby="chooseVoipCardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chooseVoipCardModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit "How to Choose the Right VOIP Calling Card" Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="chooseVoipTitle">Title</label>
            <input type="text" class="form-control" id="chooseVoipTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['choose_voip_card']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="chooseVoipDescription">Description</label>
            <textarea class="form-control" id="chooseVoipDescription" name="description" rows="5" placeholder="Enter section description"><?php echo htmlspecialchars($sections['choose_voip_card']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['choose_voip_card']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" id="card1Description" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['choose_voip_card']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['choose_voip_card']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" id="card2Description" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['choose_voip_card']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['choose_voip_card']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" id="card3Description" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['choose_voip_card']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['choose_voip_card']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" id="card4Description" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['choose_voip_card']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group">
            <label for="card5Title">Card 5 Title</label>
            <input type="text" class="form-control" id="card5Title" name="card5_title" placeholder="Card 5 title"
            value="<?php echo htmlspecialchars($sections['choose_voip_card']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card5Description">Card 5 Description</label>
            <textarea class="form-control" id="card5Description" name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['choose_voip_card']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="choose_voip_card">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- VoIP Equipment Section Modal -->
<div class="modal fade" id="voipEquipmentModal" tabindex="-1" role="dialog" aria-labelledby="voipEquipmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipEquipmentModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit "What equipment do you need to set up VoIP?" Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipEquipmentTitle">Title</label>
            <input type="text" class="form-control" id="voipEquipmentTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_equipment']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="voipEquipmentDescription">Description</label>
            <textarea class="form-control html-editor" id="voipEquipmentDescription" name="description" rows="5" placeholder="Enter section description"><?php echo htmlspecialchars($sections['voip_equipment']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="voip_equipment">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- How VOIP Calling Cards Work Section Modal -->
<div class="modal fade" id="voipCardsWorkModal" tabindex="-1" role="dialog" aria-labelledby="voipCardsWorkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="voipCardsWorkModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit "How VOIP Calling Cards Work" Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="voipCardsWorkTitle">Title</label>
            <input type="text" class="form-control" id="voipCardsWorkTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['voip_cards_work']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          

          <!-- Card 1 -->
          <div class="form-group">
            <label for="card1Title">Card 1 Title</label>
            <input type="text" class="form-control" id="card1Title" name="card1_title" placeholder="Card 1 title"
            value="<?php echo htmlspecialchars($sections['voip_cards_work']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card1Description">Card 1 Description</label>
            <textarea class="form-control" id="card1Description" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['voip_cards_work']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label for="card2Title">Card 2 Title</label>
            <input type="text" class="form-control" id="card2Title" name="card2_title" placeholder="Card 2 title"
            value="<?php echo htmlspecialchars($sections['voip_cards_work']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card2Description">Card 2 Description</label>
            <textarea class="form-control" id="card2Description" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['voip_cards_work']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label for="card3Title">Card 3 Title</label>
            <input type="text" class="form-control" id="card3Title" name="card3_title" placeholder="Card 3 title"
            value="<?php echo htmlspecialchars($sections['voip_cards_work']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card3Description">Card 3 Description</label>
            <textarea class="form-control" id="card3Description" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['voip_cards_work']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label for="card4Title">Card 4 Title</label>
            <input type="text" class="form-control" id="card4Title" name="card4_title" placeholder="Card 4 title"
            value="<?php echo htmlspecialchars($sections['voip_cards_work']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card4Description">Card 4 Description</label>
            <textarea class="form-control" id="card4Description" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['voip_cards_work']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group">
            <label for="card5Title">Card 5 Title</label>
            <input type="text" class="form-control" id="card5Title" name="card5_title" placeholder="Card 5 title"
            value="<?php echo htmlspecialchars($sections['voip_cards_work']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label for="card5Description">Card 5 Description</label>
            <textarea class="form-control" id="card5Description" name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['voip_cards_work']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="voip_cards_work">

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