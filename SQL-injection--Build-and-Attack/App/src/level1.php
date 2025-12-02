<?php 
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

include './db.php';
$conn = make_connection();
if(!$conn){
    die ("Cannot connect to database");
}

include './Static/header.html'; 

$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = pg_query($conn, $sql);

    if(!$result) {  
        $error = pg_last_error($conn);
         $message = htmlspecialchars($error);
    }
    elseif(pg_num_rows($result) > 0) {
        $user = pg_fetch_assoc($result);
        $message = "<div class='alert alert-success'>
                        ✅ Login successful! Welcome " . htmlspecialchars($user['username']) . "
                    </div>";
    }
    else{
        $message = "<div class='alert alert-warning'>
                        ❌ Invalid username or password
                    </div>";
    }
}
?>

<div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>🔐 Login Form</h5>
                </div>
                <div class="card-body">
                    <?php echo $message; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" 
                                   placeholder="Enter username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" 
                                   placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>