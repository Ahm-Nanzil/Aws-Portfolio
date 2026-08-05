<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = ''; 
$pageName = 'blog'; 

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
          <h3 class="card-title">Blog Page Management</h3>
          
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
                            <p class="title mb-0">Blog Section</p>
                            <p class="text-muted">Edit the blog section details</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#blogModal" title="Edit Section">
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




<!-- Contact Us Section Modal -->
<div class="modal fade" id="blogModal" tabindex="-1" role="dialog" aria-labelledby="blogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="blogModalLabel">
          <i class="tim-icons icon-single-02 mr-2"></i>
          Edit blog Section
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="" method="POST">
        <div class="modal-body">
          <!-- Section Title -->
          <div class="form-group">
            <label for="contactTitle">Section Title</label>
            <input type="text" class="form-control" id="contactTitle" name="title" placeholder="Enter section title"
            value="<?php echo htmlspecialchars($sections['contact']['title'] ?? '', ENT_QUOTES); ?>">
          </div>
        </div>

        <input type="hidden" name="section" value="blog">

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