<?php
//  SETUP 
ini_set('display_errors', 0); 
error_reporting(E_ALL);
session_start();

//  1. KONEKSI DATABASE 
$conn = new mysqli('db-service', 'user', 'userpassword', 'app_db');
if ($conn->connect_error) die("Koneksi Database Gagal");

//  2. LOGOUT LOGIC 
if (isset($_GET['logout'])) {
    $is_sso = (isset($_SESSION['auth_source']) && $_SESSION['auth_source'] == 'sso');
    session_destroy();
    if ($is_sso) {
        $keycloakLogoutUrl = 'http://localhost:8081/realms/smartguard/protocol/openid-connect/logout';
        $redirectBack = 'http://localhost:8080/index.php';
        header("Location: $keycloakLogoutUrl?post_logout_redirect_uri=" . urlencode($redirectBack) . "&client_id=web-app");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

// 3. HANDLE SSO LOGIN 
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $tokenEndpoint = 'http://keycloak_server:8080/realms/smartguard/protocol/openid-connect/token';
    $postData = http_build_query([
        'grant_type' => 'authorization_code', 'client_id' => 'web-app',
        'code' => $code, 'redirect_uri' => 'http://localhost:8080/index.php'
    ]);
    $opts = ['http' => ['method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => $postData]];
    $context = stream_context_create($opts);
    $response = @file_get_contents($tokenEndpoint, false, $context);

    if ($response) {
        $json = json_decode($response, true);
        
        // Ambil Username
        $id_parts = explode('.', $json['id_token']);
        $id_payload = json_decode(base64_decode($id_parts[1]), true);
        $kc_username = $id_payload['preferred_username'];

        // Ambil Role
        $acc_parts = explode('.', $json['access_token']);
        $acc_payload = json_decode(base64_decode($acc_parts[1]), true);
        $roles = $acc_payload['realm_access']['roles'] ?? [];

        $role = 'user';
        if (in_array('manager', $roles)) $role = 'manager';
        elseif (in_array('admin', $roles)) $role = 'admin';

        $_SESSION['username'] = $kc_username;
        $_SESSION['role'] = $role;
        $_SESSION['auth_source'] = 'sso';
        
        header("Location: index.php");
        exit();
    }
}

//  4. HANDLE NATIVE LOGIN 
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role']; 
        $_SESSION['auth_source'] = 'native';
        header("Location: index.php"); 
        exit();
    } else {
        $message = "<div style='color:red; margin-bottom:10px;'>Login Gagal!</div>";
    }
}

//  5. AMBIL DATA PARKIR 
$parking_data = [];
if (isset($_SESSION['username'])) {
    $res = $conn->query("SELECT * FROM parking_slots");
    if ($res) while($row = $res->fetch_assoc()) $parking_data[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>SmartGuard Parking</title>
    <?php if (isset($_SESSION['username'])): ?>
        <meta http-equiv="refresh" content="3">
    <?php endif; ?>
    
    <style>
        body { font-family: sans-serif; background: #eef2f3; padding-top: 50px; display: flex; justify-content: center; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 12px; width: 420px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .slot-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 20px; }
        .slot { padding: 15px; text-align: center; color: white; border-radius: 8px; font-weight: bold; transition: all 0.5s ease; }
        .EMPTY { background: #28a745; transform: scale(1); } 
        .OCCUPIED { background: #dc3545; transform: scale(0.95); opacity: 0.9; }
        input, button { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; cursor: pointer; font-weight:600; }
        .btn-sso { display:block; width:100%; padding:12px 0; background:#4c6f8f; color:white; text-align:center; text-decoration:none; margin-top:10px; border-radius: 6px; font-weight:600; }
        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; color: white; margin-bottom: 15px; font-weight: bold;}
        .role-admin { background: #d63384; } .role-manager { background: #fd7e14; } .role-user { background: #0d6efd; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['username'])): ?>
    <div class="card">
        <h2 style="text-align:center; color:#333;">🔐 Login SmartGuard</h2>
        <?= $message ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk (Local DB)</button>
        </form>
        <div style="text-align:center; margin: 15px 0;">ATAU</div>
        <a href="sso_login.php" class="btn-sso">🔑 Login with Keycloak (SSO)</a>
    </div>

<?php else: ?>
    <div class="card" style="width: 500px;">
        <div style="display:flex; justify-content:space-between;">
            <h3 style="margin:0">Dashboard Parkir</h3>
            <a href="?logout=true" style="color:red; text-decoration:none; font-weight:bold;">Logout ➜</a>
        </div>
        <br>
        <div class="role-badge role-<?= $_SESSION['role'] ?>">ROLE: <?= strtoupper($_SESSION['role']) ?></div>
        
        <div style="float:right; font-size:10px; color:green; margin-top:-30px;">
            🔴 LIVE UPDATE
        </div>

        <div class="slot-grid">
            <?php foreach ($parking_data as $s): ?>
                <div class="slot <?= $s['status'] ?>">
                    <?= $s['slot_name'] ?><br><small><?= $s['status'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

</body>
</html>