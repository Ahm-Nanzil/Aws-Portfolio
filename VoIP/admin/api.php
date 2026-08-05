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

    if ($section === 'smtp') {
        $notification = json_encode(['type' => 'success', 'message' => 'smtp info saved!']);
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
          <h5 class="card-category">System API</h5>
          <h3 class="card-title">System API Management</h3>
          
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">

              <div class="card card-tasks">
                <div class="card-body">
                  <div class="table-full-width table-responsive">
                    <table class="table">
                      <tbody>

                        <!-- SMTP/API Settings -->
                                <tr>
                                <td class="td-icon text-center"><i class="tim-icons icon-double-right"></i></td>
                                <td>
                                    <p class="title mb-0">SMTP / Email API</p>
                                    <p class="text-muted">Configure your SMTP credentials to send emails from your website</p>
                                </td>
                                <td class="td-actions text-right">
                                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#smtpModal">
                                    <i class="tim-icons icon-pencil"></i>
                                    </button>
                                </td>
                                </tr>

                                <!-- test smtp -->

                                <tr>
                                    <td class="td-icon text-center"><i class="tim-icons icon-double-right"></i></td>
                                    <td>
                                        <p class="title mb-0">Test SMTP / Email API</p>
                                        <p class="text-muted">Test your SMTP credentials </p>
                                    </td>
                                    <td class="td-actions text-right">
                                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#testSmtpModal">
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

<!-- SMTP / Email API Modal -->
<div class="modal fade" id="smtpModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit SMTP / API Settings</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label>SMTP Host</label>
            <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>SMTP Port</label>
            <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>SMTP Username</label>
            <input type="email" name="smtp_user" class="form-control" value="<?= htmlspecialchars($settings['smtp_user'] ?? 'ahmnanzil33@gmail.com', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>SMTP Password</label>
            <input type="text" name="smtp_pass" class="form-control" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>From Name</label>
            <input type="text" name="smtp_from_name" class="form-control" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? 'Audiophone Website', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label>From Email</label>
            <input type="email" name="smtp_from_email" class="form-control" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? 'ahmnanzil33@gmail.com', ENT_QUOTES) ?>">
          </div>
        </div>
        <input type="hidden" name="section" value="smtp">
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Test SMTP Modal -->
<div class="modal fade" id="testSmtpModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Test SMTP Settings</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <p>Click the button below to send a test email using your current SMTP settings.</p>
        <button type="button" class="btn btn-success" id="runSmtpTest">Send Test Email</button>
        <div id="smtpTestResult" style="margin-top: 15px;"></div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('runSmtpTest').addEventListener('click', function() {
    fetch('../email.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'test_smtp=1'
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('smtpTestResult').innerHTML = 
            '<div class="alert alert-info">' + data + '</div>';
    })
    .catch(err => {
        document.getElementById('smtpTestResult').innerHTML = 
            '<div class="alert alert-danger">Error: ' + err + '</div>';
    });
});
</script>






<?php include 'includes/footer.php'; ?>
