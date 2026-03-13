<?php
require_once 'bootstrap.php';
require_once 'authentication.php';

$auth = Authentication::get();

// LOGIN FORM
if ($_POST['login']) {
    if ($auth->attempt([
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ])) {
        echo "<p style='color:green'>✅ Login successful!</p>";
        echo "<p>User: " . $auth->user()['name'] . "</p>";
        echo "<p>Token: " . substr($auth->createToken($auth->user()), 0, 30) . "...</p>";
    } else {
        echo "<p style='color:red'>❌ Invalid credentials</p>";
    }
}

// PROTECTED CONTENT
echo "<h3>Protected Content:</h3>";
Authentication::requireAuth(function() use ($auth) {
    echo "<p>User ID: " . $auth->id() . "</p>";
    echo "<p>Can 'edit-users'? " . ($auth->can('edit-users') ? 'Yes' : 'No') . "</p>";
    echo "<p>Is Admin? " . ($auth->hasRole('admin') ? 'Yes' : 'No') . "</p>";
});

// API ENDPOINT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api'])) {
    Authentication::requireAbility('read', function() {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'user' => Authentication::get()->user()]);
        exit;
    });
}

// LOGOUT
if ($_POST['logout']) {
    $auth->logout();
    echo "<p style='color:orange'>👋 Logged out</p>";
}
?>

<!DOCTYPE html>
<html>
<body>
    <?php if (!$auth->check()): ?>
    <h2>Login</h2>
    <form method="POST">
        <input name="email" placeholder="admin@example.com" required><br>
        <input name="password" type="password" placeholder="password123" required><br>
        <button name="login">Login</button>
    </form>
    <?php else: ?>
    <form method="POST">
        <button name="logout">Logout</button>
        <button name="api" value="1">Test API</button>
    </form>
    <?php endif; ?>
</body>
</html>
