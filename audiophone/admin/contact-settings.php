<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$notification = '';

/**
 * Save or update a setting in key-value format
 */
function saveSetting($pdo, $key, $value) {
    $stmt = $pdo->prepare("SELECT id FROM settings WHERE `key` = ?");
    $stmt->execute([$key]);
    $existing = $stmt->fetch();

    if ($existing) {
        $update = $pdo->prepare("UPDATE settings SET `value` = ?, updated_at = NOW() WHERE id = ?");
        $update->execute([$value, $existing['id']]);
    } else {
        $insert = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)");
        $insert->execute([$key, $value]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section'])) {
    $section = $_POST['section'];
    unset($_POST['section']);

    foreach ($_POST as $key => $value) {
        saveSetting($pdo, $key, $value); 
    }

    if ($section === 'contact') {
        $notification = json_encode(['type' => 'success', 'message' => 'Contact info saved!']);
    } elseif ($section === 'links') {
        $notification = json_encode(['type' => 'success', 'message' => 'Social links saved!']);
    }
}

$settings = [];
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-category">Frontend Management</h5>
          <h3 class="card-title">Settings Page</h3>
        </div>
        <div class="card-body">
          <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">

              <div class="card card-tasks">
                <div class="card-body">
                  <div class="table-full-width table-responsive">
                    <table class="table">
                      <tbody>

                        <!-- Contact Info -->
                        <tr>
                          <td class="td-icon text-center"><i class="tim-icons icon-double-right"></i></td>
                          <td>
                            <p class="title mb-0">Contact Info</p>
                            <p class="text-muted">Edit address, email, phone, and whatsapp</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#contactModal">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <!-- Important Links -->
                        <tr>
                          <td class="td-icon text-center"><i class="tim-icons icon-double-right"></i></td>
                          <td>
                            <p class="title mb-0">Important Links</p>
                            <p class="text-muted">Edit social media links</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#linksModal">
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

<!-- Contact Info Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Contact Info</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? 'Dhaka, Bangladesh', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? 'support@nanoratech.com', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? '123456789', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>Whatsapp</label>
            <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($settings['whatsapp'] ?? '123456789', ENT_QUOTES) ?>">
          </div>
        </div>
        <input type="hidden" name="section" value="contact">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Important Links Modal -->
<div class="modal fade" id="linksModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Important Links</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label>Facebook</label>
            <input type="url" name="facebook" class="form-control" value="<?= htmlspecialchars($settings['facebook'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>Twitter</label>
            <input type="url" name="twitter" class="form-control" value="<?= htmlspecialchars($settings['twitter'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>Instagram</label>
            <input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($settings['instagram'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>LinkedIn</label>
            <input type="url" name="linkedin" class="form-control" value="<?= htmlspecialchars($settings['linkedin'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>YouTube</label>
            <input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($settings['youtube'] ?? '', ENT_QUOTES) ?>">
          </div>
        </div>
        <input type="hidden" name="section" value="links">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
