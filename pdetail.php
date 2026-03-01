<?php 
include("connection.php");
$id=$_GET['id'];
$result=mysqli_query($call,"SELECT * FROM `product` WHERE id='$id'");
$row=mysqli_fetch_assoc($result)
?>

<img src="<?php echo $row['pimage']  ?>" alt="">
<h2><?php echo $row['pname'] ?></h2>
<p><?php echo$row['pdescription'] ?></p>
<p><?php echo$row['pprice'] ?></p>
<p><?php echo$row['pquantity'] ?></p>
<a href="">Add to cart</a>