<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'cc_route'; 

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
                            <p class="title mb-0">CC Route Service</p>
                            <p class="text-muted">Edit the CC Route Service section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccRouteServiceModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">What is CC Route</p>
                            <p class="text-muted">Edit the What is CC Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#whatIsCCRouteModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Optimizing Communication</p>
                            <p class="text-muted">Edit the Optimizing Communication section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#optimizingVoipModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Benefits of Using CC Route</p>
                            <p class="text-muted">Edit the Benefits of Using CC Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccRouteBenefitsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Who Benefits from the CC Route</p>
                            <p class="text-muted">Edit the Who Benefits from the CC Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccRouteWhoBenefitsModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Integrate CC Route into Your Business</p>
                            <p class="text-muted">Edit the Integrate CC Route section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccRouteIntegrateModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">How CC Route Works</p>
                            <p class="text-muted">Edit the how cc route Works details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#ccRouteHowWorksModal" title="Edit Section">
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




<!-- CC Route Service Modal -->
<div class="modal fade" id="ccRouteServiceModal" tabindex="-1" role="dialog" aria-labelledby="ccRouteServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccRouteServiceModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit CC Route Service Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="ccRouteServiceTitle">Title</label>
            <input type="text" class="form-control" id="ccRouteServiceTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['cc_route_service']['title'] ?? '', ENT_QUOTES); ?>">
          </div>
        </div>

        <input type="hidden" name="section" value="cc_route_service">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- What is CC Route Modal -->
<div class="modal fade" id="whatIsCCRouteModal" tabindex="-1" role="dialog" aria-labelledby="whatIsCCRouteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="whatIsCCRouteModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit What is CC Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="whatIsCCRouteTitle">Title</label>
            <input type="text" class="form-control" id="whatIsCCRouteTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['what_is_cc_route']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="whatIsCCRouteDescription">Description</label>
            <textarea class="form-control html-editor" id="whatIsCCRouteDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['what_is_cc_route']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="what_is_cc_route">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Optimizing Communication Modal -->
<div class="modal fade" id="optimizingVoipModal" tabindex="-1" role="dialog" aria-labelledby="optimizingVoipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="optimizingVoipModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Optimizing Communication Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="optimizingVoipTitle">Title</label>
            <input type="text" class="form-control" id="optimizingVoipTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['optimizing_voip']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="optimizingVoipDescription">Description</label>
            <textarea class="form-control html-editor" id="optimizingVoipDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['optimizing_voip']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="optimizing_voip">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Benefits of Using CC Route Modal -->
<div class="modal fade" id="ccRouteBenefitsModal" tabindex="-1" role="dialog" aria-labelledby="ccRouteBenefitsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccRouteBenefitsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Benefits of CC Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="ccRouteBenefitsTitle">Title</label>
            <input type="text" class="form-control" id="ccRouteBenefitsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['cc_route_benefits']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="ccRouteBenefitsDescription">Description</label>
            <textarea class="form-control html-editor" id="ccRouteBenefitsDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['cc_route_benefits']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="cc_route_benefits">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Who Benefits from the CC Route Modal -->
<div class="modal fade" id="ccRouteWhoBenefitsModal" tabindex="-1" role="dialog" aria-labelledby="ccRouteWhoBenefitsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccRouteWhoBenefitsModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Who Benefits from CC Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Title -->
          <div class="form-group">
            <label for="ccRouteWhoBenefitsTitle">Title</label>
            <input type="text" class="form-control" id="ccRouteWhoBenefitsTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['cc_route_who_benefits']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Description -->
          <div class="form-group">
            <label for="ccRouteWhoBenefitsDescription">Description</label>
            <textarea class="form-control html-editor" id="ccRouteWhoBenefitsDescription" name="description" rows="10" placeholder="Enter description..."><?php echo htmlspecialchars($sections['cc_route_who_benefits']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>
        </div>

        <input type="hidden" name="section" value="cc_route_who_benefits">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="tim-icons icon-check-2 mr-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Integrate CC Route into Your Business Modal -->
<div class="modal fade" id="ccRouteIntegrateModal" tabindex="-1" role="dialog" aria-labelledby="ccRouteIntegrateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccRouteIntegrateModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit Integrate CC Route Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="ccRouteIntegrateTitle">Title</label>
            <input type="text" class="form-control" id="ccRouteIntegrateTitle" name="title" placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['cc_route_integrate']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <hr>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['cc_route_integrate']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control" name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['cc_route_integrate']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['cc_route_integrate']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control" name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['cc_route_integrate']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['cc_route_integrate']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control" name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['cc_route_integrate']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group mb-0">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['cc_route_integrate']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 4 Description</label>
            <textarea class="form-control" name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['cc_route_integrate']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="cc_route_integrate">

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
<div class="modal fade" id="ccRouteHowWorksModal" tabindex="-1" role="dialog" aria-labelledby="ccRouteHowWorksModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ccRouteHowWorksModalLabel">
          <i class="tim-icons icon-settings mr-2"></i>
          Edit How CC Route Works Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">

          <!-- Section Title -->
          <div class="form-group">
            <label for="ccRouteHowWorksTitle">Title</label>
            <input type="text" class="form-control" id="ccRouteHowWorksTitle" name="title" placeholder="Enter section title"
              value="<?php echo htmlspecialchars($sections['cc_route_how_works']['title'] ?? '', ENT_QUOTES); ?>">
          </div>

          <!-- Section Description -->
          <div class="form-group">
            <label for="ccRouteHowWorksDescription">Description</label>
            <textarea class="form-control " name="description" rows="6" placeholder="Enter section description"><?php echo htmlspecialchars($sections['cc_route_how_works']['description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <hr>

          <!-- Card 1 -->
          <div class="form-group">
            <label>Card 1 Title</label>
            <input type="text" class="form-control" name="card1_title" placeholder="Card 1 title"
              value="<?php echo htmlspecialchars($sections['cc_route_how_works']['card1_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 1 Description</label>
            <textarea class="form-control " name="card1_description" rows="3" placeholder="Card 1 description"><?php echo htmlspecialchars($sections['cc_route_how_works']['card1_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 2 -->
          <div class="form-group">
            <label>Card 2 Title</label>
            <input type="text" class="form-control" name="card2_title" placeholder="Card 2 title"
              value="<?php echo htmlspecialchars($sections['cc_route_how_works']['card2_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 2 Description</label>
            <textarea class="form-control " name="card2_description" rows="3" placeholder="Card 2 description"><?php echo htmlspecialchars($sections['cc_route_how_works']['card2_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 3 -->
          <div class="form-group">
            <label>Card 3 Title</label>
            <input type="text" class="form-control" name="card3_title" placeholder="Card 3 title"
              value="<?php echo htmlspecialchars($sections['cc_route_how_works']['card3_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 3 Description</label>
            <textarea class="form-control " name="card3_description" rows="3" placeholder="Card 3 description"><?php echo htmlspecialchars($sections['cc_route_how_works']['card3_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 4 -->
          <div class="form-group">
            <label>Card 4 Title</label>
            <input type="text" class="form-control" name="card4_title" placeholder="Card 4 title"
              value="<?php echo htmlspecialchars($sections['cc_route_how_works']['card4_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group">
            <label>Card 4 Description</label>
            <textarea class="form-control " name="card4_description" rows="3" placeholder="Card 4 description"><?php echo htmlspecialchars($sections['cc_route_how_works']['card4_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

          <!-- Card 5 -->
          <div class="form-group mb-0">
            <label>Card 5 Title</label>
            <input type="text" class="form-control" name="card5_title" placeholder="Card 5 title"
              value="<?php echo htmlspecialchars($sections['cc_route_how_works']['card5_title'] ?? '', ENT_QUOTES); ?>">
          </div>
          <div class="form-group mb-0">
            <label>Card 5 Description</label>
            <textarea class="form-control " name="card5_description" rows="3" placeholder="Card 5 description"><?php echo htmlspecialchars($sections['cc_route_how_works']['card5_description'] ?? '', ENT_QUOTES); ?></textarea>
          </div>

        </div>

        <input type="hidden" name="section" value="cc_route_how_works">

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
