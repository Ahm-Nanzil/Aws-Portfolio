<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = '';
$pageName = 'download';

/**
 * Save or update a section in pages table
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

// Fetch existing sections
$sections = [];
$stmt = $pdo->prepare("SELECT section_name, content FROM pages WHERE page_name = ?");
$stmt->execute([$pageName]);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $sections[$row['section_name']] = json_decode($row['content'], true);
}
?>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-category">Frontend Management</h5>
          <h3 class="card-title">App Download </h3>
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
                          <td class="td-icon text-center"><i class="tim-icons icon-double-right"></i></td>
                          <td>
                            <p class="title mb-0">Header Section</p>
                            <p class="text-muted">Edit page title and subtitle</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#headerModal">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center"><i class="tim-icons icon-double-right"></i></td>
                          <td>
                            <p class="title mb-0">App Cards Section</p>
                            <p class="text-muted">Add, edit, or delete app cards</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#appsModal">
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

<!-- Header Modal -->
<div class="modal fade" id="headerModal" tabindex="-1" role="dialog" aria-labelledby="headerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="tim-icons icon-single-02 mr-2"></i>Edit Header Section</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label>Title</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($sections['header']['title'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Subtitle</label>
            <input type="text" class="form-control" name="subtitle" value="<?= htmlspecialchars($sections['header']['subtitle'] ?? '') ?>">
          </div>
        </div>
        <input type="hidden" name="section" value="header">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="tim-icons icon-check-2 mr-1"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Apps Modal -->
<div class="modal fade" id="appsModal" tabindex="-1" role="dialog" aria-labelledby="appsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="tim-icons icon-laptop mr-2"></i>Manage App Cards</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>

      <form method="POST" id="appsForm">
        <div class="modal-body">
          <div id="appsContainer">
            <?php
            $apps = $sections['apps']['cards'] ?? [];
            if (empty($apps)) {
                $apps = [['name' => '', 'op_code' => '', 'download_link' => '']];
            }
            foreach ($apps as $i => $app): ?>
            <div class="app-item border p-3 mb-3 rounded">
              <div class="d-flex justify-content-between">
                <h5>App <span class="app-index"><?= $i+1 ?></span></h5>
                <button type="button" class="btn btn-danger btn-sm removeApp">Remove</button>
              </div>
              <div class="form-group mt-2">
                <label>Name</label>
                <input type="text" class="form-control" name="cards[<?= $i ?>][name]" value="<?= htmlspecialchars($app['name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>OP Code</label>
                <input type="text" class="form-control" name="cards[<?= $i ?>][op_code]" value="<?= htmlspecialchars($app['op_code'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Download Link</label>
                <input type="text" class="form-control" name="cards[<?= $i ?>][download_link]" value="<?= htmlspecialchars($app['download_link'] ?? '') ?>">
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <button type="button" class="btn btn-success" id="addApp"><i class="tim-icons icon-simple-add"></i> Add New App</button>
        </div>

        <input type="hidden" name="section" value="apps">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="tim-icons icon-check-2 mr-1"></i> Save All</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const addBtn = document.getElementById('addApp');
    const container = document.getElementById('appsContainer');

    addBtn.addEventListener('click', () => {
        const index = container.querySelectorAll('.app-item').length;
        const html = `
        <div class="app-item border p-3 mb-3 rounded">
          <div class="d-flex justify-content-between">
            <h5>App <span class="app-index">${index+1}</span></h5>
            <button type="button" class="btn btn-danger btn-sm removeApp">Remove</button>
          </div>
          <div class="form-group mt-2">
            <label>Name</label>
            <input type="text" class="form-control" name="cards[${index}][name]">
          </div>
          <div class="form-group">
            <label>OP Code</label>
            <input type="text" class="form-control" name="cards[${index}][op_code]">
          </div>
          <div class="form-group">
            <label>Download Link</label>
            <input type="text" class="form-control" name="cards[${index}][download_link]">
          </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    });

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('removeApp')) {
            e.target.closest('.app-item').remove();
            // reindex
            container.querySelectorAll('.app-item').forEach((item, i) => {
                item.querySelector('.app-index').textContent = i + 1;
                item.querySelectorAll('input').forEach(input => {
                    input.name = input.name.replace(/\d+/, i);
                });
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
