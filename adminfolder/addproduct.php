<?php
include("../../asimtemp/connection.php");

if (isset($_POST['submit'])) {
    $pname = $_POST['pname'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $imagename = $_FILES['image']['name'];
    $imagetmpname = $_FILES['image']['tmp_name'];
    $folder = "uploads/";
    $filepath = $folder . $imagename;
    $query = "INSERT INTO `product`(`pname`, `pdescription`, `pprice`, `pquantity`, `pimage`)
 VALUES ('$pname','$description','$price','$quantity','$filepath')";

    if (mysqli_query($call, $query)) {
        echo "<script>
    
    alert('product added successful')
    
    </script>";
    } else {

        echo "<script>

alert('product not added')

</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <center><br>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="">Enter product name</label><br>
            <input type="text" name="pname" id=""><br><br>
            <label for="">Enter description</label><br>
            <input type="text" name="description" id=""><br><br>
            <label for="">Enter price</label><br>
            <input type="text" name="price" id=""><br><br>
            <label for="">Select quantity</label><br>
            <input type="text" name="quantity" id=""><br><br>
            <label for="">Select image</label><br>
            <input type="file" name="image" id=""><br><br>
            <button type="submit" name="submit">Add product</button>

        </form>
    </center>
</body>

</html>