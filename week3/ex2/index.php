<?php
include "connection.php";
?>

<html lang="en" xmlns="">
<head>
    <title>Laptop Shop</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <!-- short column display for forms rows -->
   <!--visit https://www.w3schools.com/bootstrap/bootstrap_forms.asp search for forms template and use it.-->
    <div class="col-lg-6">
    <h2>Laptop data form</h2>
    <form action="" name="form1" method="post">
        <div class="form-group">
            <label for="brand">Brand:</label>
            <input type="text" class="form-control" id="brand" placeholder="Enter Brand" name="brand">
        </div>
        <div class="form-group">
            <label for="model">Model:</label>
            <input type="text" class="form-control" id="model" placeholder="Enter Model" name="model">
        </div>
        <div class="form-group">
            <label for="processor">Processor:</label>
            <input type="text" class="form-control" id="processor" placeholder="Enter Processor" name="processor">
        </div>
        <div class="form-group">
            <label for="ram">Ram:</label>
            <input type="text" class="form-control" id="ram" placeholder="Enter Ram" name="ram">
        </div>
        <div class="form-group">
            <label for="storage">Storage:</label>
            <input type="text" class="form-control" id="storage" placeholder="Enter Storage" name="storage">
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" class="form-control" id="price" placeholder="Enter Price" name="price">
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="text" class="form-control" id="stock" placeholder="Enter Stock" name="stock">
        </div>
        <button type="submit" name="insert" class="btn btn-default">Insert</button>
        <button type="submit" name="update" class="btn btn-default">Update</button>
        <button type="submit" name="delete" class="btn btn-default">Delete</button>

    </form>
</div>
</div>

<!-- new column inserted for records -->
<!-- Search for boostrap table template online and copy code -->
<div class="col-lg-12">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Id</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Processor</th>
            <th>RAM</th>
            <th>Storage</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        <!-- Database connection -->
        <?php
        if (!empty($link)) {
            $res=mysqli_query($link,"select * from laptops");
        }
        while($row=mysqli_fetch_array($res))
        {
            echo "<tr>";
            echo "<td>"; echo $row["id"]; echo "</td>";
            echo "<td>"; echo $row["brand"]; echo "</td>";
            echo "<td>"; echo $row["model"]; echo "</td>";
            echo "<td>"; echo $row["processor"]; echo "</td>";
            echo "<td>"; echo $row["ram"]; echo "</td>";
            echo "<td>"; echo $row["storage"]; echo "</td>";
            echo "<td>"; echo $row["price"]; echo "</td>";
            echo "<td>"; echo $row["stock"]; echo "</td>";
            echo "<td>"; ?> <a href="edit.php?id=<?php echo $row["id"]; ?>"><button type="button" class="btn btn-success">Edit </button></a> <?php echo "</td>";
            echo "<td>"; ?> <a href="delete.php?id=<?php echo $row["id"]; ?>"><button type="button" class="btn btn-danger">Delete </button></a> <?php echo "</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>

<!-- new records insertion into database table -->
<!-- records delete from database table -->
<!-- records update from database table -->

<!-- to automatically refresh the pages after crud activity   window.location.href=window.location.href; -->
<?php
if(isset($_POST["insert"]))
{
    mysqli_query($link,"insert into laptops values (NULL,'$_POST[brand]' ,'$_POST[model]','$_POST[processor]','$_POST[ram]','$_POST[storage]','$_POST[price]','$_POST[stock]')");
   ?>
    <script type="text/javascript">
    window.location.href=window.location.href;
    </script>
    <?php

}

if(isset($_POST["delete"]))
{
    mysqli_query($link,"delete from laptops where model='$_POST[model]'");
    ?>
    <script type="text/javascript">
        window.location.href=window.location.href;
    </script>
    <?php
}

if(isset($_POST["update"]))
{
    mysqli_query($link,"update laptops set brand='$_POST[brand]', processor='$_POST[processor]', ram='$_POST[ram]', storage='$_POST[storage]', price='$_POST[price]', stock='$_POST[stock]','where model='$_POST[model]'");
    ?>
    <script type="text/javascript">
        window.location.href=window.location.href;
    </script>
    <?php
}
?>
</html>