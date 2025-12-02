<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

include './db.php';
$conn = make_connection();
if(!$conn){
    die("Cannot connect to database");
}

include './Static/header.html'; 

$message = "";

if(isset($_GET['email']) && !empty($_GET['email'])){
    $email = $_GET['email'];

    $sql = "SELECT id, username, email FROM users WHERE email = '$email'";
    $result = pg_query($conn, $sql);

    //luon tra ve cung 1 thong bao
    $message = "📧 Nếu email tồn tại, hướng dẫn reset đã được gửi";
}
?>

<div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5>🔐 Quên mật khẩu</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Nhập email để nhận hướng dẫn reset mật khẩu</p>

                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" 
                                   placeholder="your@email.com" 
                                   value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Gửi hướng dẫn reset</button>
                    </form>

                    <?php if(!empty($message)): ?>
                        <div class="alert alert-info mt-3">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
                