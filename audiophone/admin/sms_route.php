<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'sms_route'; 

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
                            <p class="title mb-0">SMS Route Service</p>
                            <p class="text-muted">Edit the SMS Route Service section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsRouteServiceModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">What is an SMS Route</p>
                            <p class="text-muted">Edit the What is an SMS Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsRouteModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Optimizing Messaging</p>
                            <p class="text-muted">Edit the Optimizing Messaging section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsOptimizingModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How SMS Route Works</p>
                            <p class="text-muted">Edit the How SMS Route Works section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsRouteWorksModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Benefits of Using SMS Route</p>
                            <p class="text-muted">Edit the Benefits of Using SMS Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsRouteBenefitsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How to Integrate SMS Route</p>
                            <p class="text-muted">Edit the How to Integrate SMS Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsRouteIntegrationModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>
                        

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">SMS Route FAQ</p>
                            <p class="text-muted">Edit the SMS Route FAQ section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smsRouteCombinedModal" title="Edit Section">
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





<!-- SMS Route Service Modal -->
<div class="modal fade" id="smsRouteServiceModal" tabindex="-1" role="dialog" aria-labelledby="smsRouteServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsRouteServiceModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit SMS Route Service Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="smsRouteServiceTitle">Title</label>
            <input type="text" class="form-control" id="smsRouteServiceTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['sms_route_service']['title'] ?? '', ENT_QUOTES); ?>">
          </div>
        </div>

        <input type="hidden" name="section" value="sms_route_service">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- What is an SMS Route Modal -->
<div class="modal fade" id="smsRouteModal" tabindex="-1" role="dialog" aria-labelledby="smsRouteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsRouteModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit What is SMS Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="smsRouteTitle">Title</label>
            <input type="text" class="form-control" id="smsRouteTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['sms_route']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="smsRouteDescription">Description</label>
            <textarea class="form-control html-editor" id="smsRouteDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['sms_route']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="sms_route">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Optimizing Messaging Modal -->
<div class="modal fade" id="smsOptimizingModal" tabindex="-1" role="dialog" aria-labelledby="smsOptimizingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsOptimizingModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Optimizing Messaging Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="smsOptimizingTitle">Title</label>
            <input type="text" class="form-control" id="smsOptimizingTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['sms_optimizing']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="smsOptimizingDescription">Description</label>
            <textarea class="form-control html-editor" id="smsOptimizingDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['sms_optimizing']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="sms_optimizing">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- How SMS Route Works Modal -->
<div class="modal fade" id="smsRouteWorksModal" tabindex="-1" role="dialog" aria-labelledby="smsRouteWorksModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsRouteWorksModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit How SMS Route Works Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="smsRouteWorksTitle">Title</label>
            <input type="text" class="form-control" id="smsRouteWorksTitle" name="title" placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['sms_route_works']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="smsRouteWorksDescription">Description</label>
            <textarea class="form-control" name="description" rows="6" placeholder="Enter section description"><?php echo htmlspecialchars($sections['sms_route_works']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <hr>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['sms_route_works']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['sms_route_works']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['sms_route_works']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['sms_route_works']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['sms_route_works']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['sms_route_works']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['sms_route_works']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['sms_route_works']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group mb-0">
            <label>Card 5 Title</label>
            <input type="text" class="form-control" name="card5_title" placeholder="Card 5 title"
              value="<?php echo htmlspecialchars($sections['sms_route_works']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 5 Description</label>
            <textarea class="form-control" name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['sms_route_works']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="sms_route_works">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Benefits of Using SMS Route Modal -->
<div class="modal fade" id="smsRouteBenefitsModal" tabindex="-1" role="dialog" aria-labelledby="smsRouteBenefitsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsRouteBenefitsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Benefits of Using SMS Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="smsRouteBenefitsTitle">Title</label>
            <input type="text" class="form-control" id="smsRouteBenefitsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['sms_route_benefits']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="smsRouteBenefitsDescription">Description</label>
            <textarea class="form-control html-editor" id="smsRouteBenefitsDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['sms_route_benefits']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="sms_route_benefits">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Integrate SMS Route Modal -->
<div class="modal fade" id="smsRouteIntegrationModal" tabindex="-1" role="dialog" aria-labelledby="smsRouteIntegrationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsRouteIntegrationModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit How to Integrate SMS Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="smsRouteIntegrationTitle">Title</label>
            <input type="text" class="form-control" id="smsRouteIntegrationTitle" name="title" placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['sms_route_integration']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <hr>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['sms_route_integration']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['sms_route_integration']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['sms_route_integration']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['sms_route_integration']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['sms_route_integration']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['sms_route_integration']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['sms_route_integration']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['sms_route_integration']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group mb-0">
            <label>Card 5 Title</label>
            <input type="text" class="form-control" name="card5_title" placeholder="Card 5 title"
              value="<?php echo htmlspecialchars($sections['sms_route_integration']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 5 Description</label>
            <textarea class="form-control" name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['sms_route_integration']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="sms_route_integration">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- SMS Route Combined Modal -->
<div class="modal fade" id="smsRouteCombinedModal" tabindex="-1" role="dialog" aria-labelledby="smsRouteCombinedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smsRouteCombinedModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit SMS Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="smsRouteCombinedTitle">Title</label>
            <input type="text" class="form-control" id="smsRouteCombinedTitle" name="title" placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['sms_route_combined']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="smsRouteCombinedDescription">Description</label>
            <textarea class="form-control html-editor" id="smsRouteCombinedDescription" name="description" rows="6" placeholder="Enter section description..."><?php echo htmlspecialchars($sections['sms_route_combined']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <hr>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['sms_route_combined']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['sms_route_combined']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['sms_route_combined']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['sms_route_combined']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['sms_route_combined']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['sms_route_combined']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['sms_route_combined']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['sms_route_combined']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="sms_route_combined">

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
