<?php
include 'includes/header.php';

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
        return true;
    } else {
        $insert = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)");
        $insert->execute([$key, $value]);
        return true;
    }
}

/**
 * Handle file upload
 */
function handleFileUpload($fileKey, $targetDir = 'assets/img/logo/') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $fileTmp  = $_FILES[$fileKey]['tmp_name'];
    $fileName = basename($_FILES[$fileKey]['name']);
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allow only image extensions
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'];
    if (!in_array($fileExt, $allowed)) {
        return null;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $newName = uniqid('logo_', true) . '.' . $fileExt;
    $targetPath = $targetDir . $newName;

    if (move_uploaded_file($fileTmp, $targetPath)) {
        return $targetPath;
    }

    return null;
}

// Handle POST submission BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['section'])) {
    $section = $_POST['section'];
    unset($_POST['section']);

    if ($section === 'system_logo') {
        // Handle logo uploads
        $uploadedCount = 0;
        $uploadedItems = [];
        
        $headerLogo = handleFileUpload('header_logo');
        if ($headerLogo) {
            saveSetting($pdo, 'header_logo', $headerLogo);
            $uploadedCount++;
            $uploadedItems[] = 'Header Logo';
        }
        
        $favicon = handleFileUpload('favicon');
        if ($favicon) {
            saveSetting($pdo, 'favicon', $favicon);
            $uploadedCount++;
            $uploadedItems[] = 'Favicon';
        }
        
        $footerLogo = handleFileUpload('footer_logo');
        if ($footerLogo) {
            saveSetting($pdo, 'footer_logo', $footerLogo);
            $uploadedCount++;
            $uploadedItems[] = 'Footer Logo';
        }

        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=logo_updated&count=' . $uploadedCount);
        exit;
        
    } elseif ($section === 'brand_info') {
        // Handle text fields
        $updatedCount = 0;
        foreach ($_POST as $key => $value) {
            if (!empty($value)) {
                saveSetting($pdo, $key, $value);
                $updatedCount++;
            }
        }
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=brand_updated&count=' . $updatedCount);
        exit;
    }
}

include 'includes/sidebar.php';
include 'includes/navbar.php';

// Show notification based on URL parameter
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'logo_updated') {
        $count = isset($_GET['count']) ? intval($_GET['count']) : 0;
        if ($count > 0) {
            $message = $count === 1 ? '1 logo uploaded successfully!' : 'All ' . $count . ' logos uploaded successfully!';
            $notification = json_encode(['type' => 'success', 'message' => $message]);
        } else {
            $notification = json_encode(['type' => 'warning', 'message' => 'No logos were selected for upload.']);
        }
    } elseif ($_GET['status'] === 'brand_updated') {
        $count = isset($_GET['count']) ? intval($_GET['count']) : 0;
        if ($count > 0) {
            $notification = json_encode(['type' => 'success', 'message' => 'Brand information updated successfully!']);
        } else {
            $notification = json_encode(['type' => 'warning', 'message' => 'No changes were made.']);
        }
    }
}


?>

<style>
.preview-container {
    margin-top: 10px;
    position: relative;
}
.preview-image {
    max-width: 200px;
    max-height: 200px;
    border: 2px dashed #ddd;
    border-radius: 5px;
    padding: 5px;
    display: none;
}
.preview-image.active {
    display: block;
    border-color: #28a745;
}
.file-name {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    font-style: italic;
}
.current-image-label {
    font-size: 11px;
    color: #999;
    margin-top: 5px;
}

/* Upload Area Styling */
.upload-area {
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background-color: #f7fafc;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    margin-bottom: 15px;
}

.upload-area:hover {
    border-color: #4299e1;
    background-color: #ebf8ff;
}

.upload-area.active {
    border-color: #28a745;
    background-color: #f0fff4;
}

.upload-area.drag-over {
    border-color: #4299e1;
    background-color: #ebf8ff;
    transform: scale(1.02);
}

.upload-icon {
    font-size: 48px;
    color: #a0aec0;
    margin-bottom: 10px;
    pointer-events: none;
}

.upload-area:hover .upload-icon {
    color: #4299e1;
}

.upload-area.active .upload-icon {
    color: #28a745;
}

.upload-text {
    font-size: 14px;
    color: #718096;
    margin-bottom: 5px;
    pointer-events: none;
}

.upload-hint {
    font-size: 12px;
    color: #a0aec0;
    pointer-events: none;
}

.upload-area input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.image-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.image-section:last-child {
    border-bottom: none;
}

.section-label {
    font-weight: 600;
    font-size: 15px;
    color: #2d3748;
    margin-bottom: 10px;
    display: block;
}
</style>



<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-category">Frontend Page</h5>
          <h3 class="card-title">System Management</h3>
          
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
                            <p class="title mb-0">System Logo Settings</p>
                            <p class="text-muted">Manage system logos</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#systemLogoModal" title="Edit Section">
                              <i class="tim-icons icon-pencil"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td class="td-icon text-center">
                            <i class="tim-icons icon-double-right"></i>
                          </td>
                          <td>
                            <p class="title mb-0">Brand Information Settings</p>
                            <p class="text-muted">Manage brand information</p>
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#brandInfoModal" title="Edit Section">
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


<!-- 🧩 System Logo Modal -->
<div class="modal fade" id="systemLogoModal" tabindex="-1" role="dialog" aria-labelledby="systemLogoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">System Logo Settings</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="section" value="system_logo">

          <!-- Header Logo Section -->
          <div class="image-section">
            <label class="section-label">📌 Header Logo</label>
            <div class="upload-area" id="header_logo_area" data-input="header_logo">
              <div class="upload-icon">📤</div>
              <div class="upload-text">Click or drag to upload Header Logo</div>
              <div class="upload-hint">Supported: JPG, PNG, GIF, SVG, WEBP</div>
              <input type="file" class="image-upload" id="header_logo" name="header_logo" accept="image/*">
            </div>
            <div class="preview-container">
              <img id="header_logo_preview" class="preview-image" alt="Preview">
              <div id="header_logo_filename" class="file-name"></div>
            </div>
            <?php if (setting('header_logo')): ?>
              <div class="current-image-label">Current Image:</div>
              <img src="<?= htmlspecialchars(setting('header_logo')) ?>" alt="Header Logo" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
          </div>

          <!-- Favicon Section -->
          <div class="image-section">
            <label class="section-label">⭐ Favicon</label>
            <div class="upload-area" id="favicon_area" data-input="favicon">
              <div class="upload-icon">📤</div>
              <div class="upload-text">Click or drag to upload Favicon</div>
              <div class="upload-hint">Supported: JPG, PNG, GIF, SVG, WEBP, ICO</div>
              <input type="file" class="image-upload" id="favicon" name="favicon" accept="image/*">
            </div>
            <div class="preview-container">
              <img id="favicon_preview" class="preview-image" alt="Preview">
              <div id="favicon_filename" class="file-name"></div>
            </div>
            <?php if (setting('favicon')): ?>
              <div class="current-image-label">Current Image:</div>
              <img src="<?= htmlspecialchars(setting('favicon')) ?>" alt="Favicon" class="img-thumbnail mt-2" width="50">
            <?php endif; ?>
          </div>

          <!-- Footer Logo Section -->
          <div class="image-section">
            <label class="section-label">🔖 Footer Logo</label>
            <div class="upload-area" id="footer_logo_area" data-input="footer_logo">
              <div class="upload-icon">📤</div>
              <div class="upload-text">Click or drag to upload Footer Logo</div>
              <div class="upload-hint">Supported: JPG, PNG, GIF, SVG, WEBP</div>
              <input type="file" class="image-upload" id="footer_logo" name="footer_logo" accept="image/*">
            </div>
            <div class="preview-container">
              <img id="footer_logo_preview" class="preview-image" alt="Preview">
              <div id="footer_logo_filename" class="file-name"></div>
            </div>
            <?php if (setting('footer_logo')): ?>
              <div class="current-image-label">Current Image:</div>
              <img src="<?= htmlspecialchars(setting('footer_logo')) ?>" alt="Footer Logo" class="img-thumbnail mt-2" width="100">
            <?php endif; ?>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Logos</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 🌐 Brand Information Modal -->
<div class="modal fade" id="brandInfoModal" tabindex="-1" role="dialog" aria-labelledby="brandInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-top" role="document">
    <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Brand Information</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="section" value="brand_info">

          <div class="form-group">
            <label for="tab_title">Title</label>
            <input type="text" class="form-control" id="tab_title" name="tab_title" value="<?= htmlspecialchars(setting('tab_title') ?? '') ?>">
          </div>

          <div class="form-group">
            <label for="footer_description">Footer Description</label>
            <textarea class="form-control html-editor" id="footer_description" name="footer_description" rows="3"><?= htmlspecialchars(setting('footer_description') ?? '') ?></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Brand Info</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Image preview and drag-drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const imageInputs = document.querySelectorAll('.image-upload');
    
    // Handle file input change
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            handleFileSelect(e.target.files[0], this.id);
        });
    });
    
    // Handle drag and drop
    const uploadAreas = document.querySelectorAll('.upload-area');
    
    uploadAreas.forEach(function(area) {
        const inputId = area.getAttribute('data-input');
        const input = document.getElementById(inputId);
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            area.addEventListener(eventName, function() {
                area.classList.add('drag-over');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, function() {
                area.classList.remove('drag-over');
            }, false);
        });
        
        // Handle dropped files
        area.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                // Set the file to the input
                input.files = files;
                // Trigger change event
                handleFileSelect(files[0], inputId);
            }
        }, false);
    });
    
    // Function to handle file selection
    function handleFileSelect(file, inputId) {
        const previewId = inputId + '_preview';
        const filenameId = inputId + '_filename';
        const uploadAreaId = inputId + '_area';
        const previewImg = document.getElementById(previewId);
        const filenameDiv = document.getElementById(filenameId);
        const uploadArea = document.getElementById(uploadAreaId);
        
        if (file) {
            // Check if file is an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.add('active');
                    filenameDiv.textContent = '✓ Selected: ' + file.name;
                    filenameDiv.style.color = '#28a745';
                    uploadArea.classList.add('active');
                    
                    // Update upload area text
                    const uploadText = uploadArea.querySelector('.upload-text');
                    uploadText.textContent = '✓ Image selected! Click to change';
                    uploadText.style.color = '#28a745';
                };
                
                reader.readAsDataURL(file);
            } else {
                alert('Please select a valid image file.');
                document.getElementById(inputId).value = '';
            }
        } else {
            previewImg.classList.remove('active');
            previewImg.src = '';
            filenameDiv.textContent = '';
            uploadArea.classList.remove('active');
            
            // Reset upload area text
            const uploadText = uploadArea.querySelector('.upload-text');
            const label = uploadArea.closest('.image-section').querySelector('.section-label').textContent;
            uploadText.textContent = 'Click or drag to upload ' + label.substring(2);
            uploadText.style.color = '#718096';
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>