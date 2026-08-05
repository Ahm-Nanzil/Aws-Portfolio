<?php
session_start();
require __DIR__ . '/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: admin/index.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
include 'header.php';
?>

<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; background: linear-gradient(to right, #009dffff, #4e09eeff);">
    <form method="POST" style="background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); width: 350px; text-align: center;">
        <h2 style="margin-bottom: 30px; color: #333;">Login</h2>

        <?php if (!empty($error)) : ?>
            <p style="color: red; margin-bottom: 20px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Username" required 
               style="width: 100%; padding: 12px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc;">

        <input type="password" name="password" placeholder="Password" required
               style="width: 100%; padding: 12px; margin-bottom: 30px; border-radius: 5px; border: 1px solid #ccc;">

        <button type="submit" 
                style="width: 100%; padding: 12px; border: none; border-radius: 5px; background: linear-gradient(to right, #009dffff, #4e09eeff); color: #fff; font-weight: bold; cursor: pointer; transition: opacity 0.3s;">
            Login
        </button>
    </form>
</div>

<?php
include 'footer.php';
?>
