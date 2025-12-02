<?php 
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

include './db.php';
$conn = make_connection();
if(!$conn){
    die("Cannot connect to database");
}
?>

<?php include './Static/header.html'; ?>
<!-- Search Form -->
<form method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Enter cake's name"  value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
        <button class="btn btn-primary" type="submit">🔍 Search</button>
    </div>
</form>

<?php
if(isset($_GET['q']) && !empty($_GET['q'])){
    $q = urldecode($_GET['q']);
    try{
        $sql = "SELECT * FROM products WHERE name LIKE '%$q%' OR description LIKE '%$q%'";
        $result = pg_query($conn, $sql);

        if(!$result){
            echo "<div class='alert alert-danger'>❌ No cakes match</div>";
        }
        else{
            $count = pg_num_rows($result);
            if($count > 0){
                echo "<div class='alert alert-success'>✅ Find out $count cakes match</div>";

            }
            else{
                echo "<div class='alert alert-danger'>❌ No cakes match</div>";
            }
        }
    }
    catch(Exception $e){
        echo "<div class='alert alert-danger'>❌ No cakes match</div>";
    }
}
?>