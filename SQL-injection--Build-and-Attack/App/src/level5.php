<?php 
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

include './db.php';
$conn = make_connection();
if(!$conn){
    die ("Cannot connect to database");
}
?>

<?php include './Static/header.html'; ?>

<!--Xu ly request-->
<?php 
#Xu ly thieu id
if(!isset($_GET['id']) || empty($_GET['id'])){
    ?>
    <script>
        window.location.href = 'level5.php?id=1';
    </script>

    <?php
}

$id = $_GET['id'];
$sql_query = "SELECT * FROM products WHERE id = $id";
$result = pg_query($conn, $sql_query);

#xu ly khi request fail
if(!$result){
    $error = pg_last_error($conn);
    echo htmlspecialchars($error);
}
elseif(pg_num_rows($result) > 0){
    $product = pg_fetch_assoc($result);
}
?>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if(!empty($product['image'])): ?>
                                    <img src="./Static/Images/<?php echo $product['image']; ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?php echo $product['name']; ?>"
                                         style="max-height: 200px; object-fit: cover; width: 100%;">
                                <?php else: ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-8">
                                <h4 class="text-primary"><?php echo htmlspecialchars($product['name']); ?></h4>
                                <p class="text-success h5">💰 $<?php echo number_format($product['price'], 2); ?></p>
                                <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
           