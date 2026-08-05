<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $company = $_POST['company'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $postal_code = $_POST['postal_code'];
    $about_me = $_POST['about_me'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
      $uploadDir = 'assets/img/';
      $fileName = basename($_FILES['avatar']['name']);
      $targetPath = $uploadDir . time() . '_' . $fileName;
      $userId = $_SESSION['user_id'];

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$targetPath, $userId]);
            $_SESSION['avatar'] = $targetPath;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }


    $update = $pdo->prepare("UPDATE users SET 
        username = ?, email = ?, first_name = ?, last_name = ?, 
        company = ?, address = ?, city = ?, country = ?, 
        postal_code = ?, about_me = ?, updated_at = NOW()
        WHERE id = ?");
    $update->execute([
        $username, $email, $first_name, $last_name, $company,
        $address, $city, $country, $postal_code, $about_me, $user_id
    ]);

    // Refresh data
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $success = "Profile updated successfully!";
}
?>

<div class="content">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="title">Edit Profile</h5>
          <?php if (!empty($success)): ?>
            <div style="color: green; margin-top: 10px;"><?php echo $success; ?></div>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <form id="avatarForm" action="" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-5 pr-md-1">
                <div class="form-group">
                  <label>Company</label>
                  <input type="text" name="company" class="form-control" placeholder="Company" value="<?php echo htmlspecialchars($user['company']); ?>">
                </div>
              </div>
              <div class="col-md-3 px-md-1">
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>
              </div>
              <div class="col-md-4 pl-md-1">
                <div class="form-group">
                  <label>Email address</label>
                  <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 pl-md-1">
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
              </div>

              <div class="col-md-4 pr-md-1">
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                </div>
              </div>
              <div class="col-md-4 pl-md-1">
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Address</label>
                  <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 pr-md-1">
                <div class="form-group">
                  <label>City</label>
                  <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($user['city']); ?>">
                </div>
              </div>
              <div class="col-md-4 px-md-1">
                <div class="form-group">
                  <label>Country</label>
                  <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($user['country']); ?>">
                </div>
              </div>
              <div class="col-md-4 pl-md-1">
                <div class="form-group">
                  <label>Postal Code</label>
                  <input type="text" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($user['postal_code']); ?>">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-8">
                <div class="form-group">
                  <label>About Me</label>
                  <textarea name="about_me" rows="4" class="form-control"><?php echo htmlspecialchars($user['about_me']); ?></textarea>
                </div>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-fill btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<div class="col-md-4">
            <div class="card card-user">
              <div class="card-body">
                <p class="card-text">
                  </p><div class="author">
                    <div class="block block-one"></div>
                    <div class="block block-two"></div>
                    <div class="block block-three"></div>
                    <div class="block block-four"></div>
                    <a href="javascript:void(0)">
                      <img class="avatar" src="assets/img/anime3.png" alt="...">
                      <h5 class="title"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                      <h5 class="title mt-2"></h5>
          
           
                    </a>
                    <p class="description">
                      <?php echo htmlspecialchars($user['company']); ?>
                    </p>
                  </div>
                <p></p>
                <div class="card-description">
                  <?php echo htmlspecialchars($user['about_me']); ?>
                                </div>
              </div>
              <div class="card-footer">
                <div class="button-container">
                  <button href="javascript:void(0)" class="btn btn-icon btn-round btn-facebook">
                    <i class="fab fa-facebook"></i>
                  </button>
                  <button href="javascript:void(0)" class="btn btn-icon btn-round btn-twitter">
                    <i class="fab fa-twitter"></i>
                  </button>
                  <button href="javascript:void(0)" class="btn btn-icon btn-round btn-google">
                    <i class="fab fa-google-plus"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
    </div>
  </div>
</div>
<script>
document.getElementById('avatarInput').addEventListener('change', function() {
  document.getElementById('avatarForm').submit();
});
</script>


<?php include 'includes/footer.php'; ?>
