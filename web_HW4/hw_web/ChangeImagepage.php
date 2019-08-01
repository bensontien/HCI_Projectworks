<!DOCTYPE html>
<?php

    session_start();
    
    //connect
    $servername = "localhost";
    $username = "root";
    $password = "sheisyuri0405";
    $dbname = "hw_web";

    $conn='';

        try {
    
        //連接Sever
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         
    
        }
        catch(PDOException $e)
        {
        echo "Connection failed: " . $e->getMessage();
        }

    $username_form = $_SESSION["username"];
    $password_form = $_SESSION["password"];

    $sql = "select * from user where username = '$username_form' and password='$password_form'";
    $conn->query('set names utf8;');
    $result = $conn->query($sql);
    $rows = $result->fetchAll();


        if(@$_SESSION["username"]){
           

?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- This file has been downloaded from Bootsnipp.com. Enjoy! -->
    <title>OTAKU Forum</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
    <style type="text/css">
        
    body {
	  background-color: lightblue;
	}
	
	h3{
	  font-family:"Lucida Console", Monaco, monospace;
	}
	
	p{
	font-family:"Lucida Console", Monaco, monospace;
	}
        
    table#t01 tr:nth-child(even) {
        background-color: aliceblue;
        }
    table#t01 tr:nth-child(odd) {
        background-color: #fff;
        }
    table#t01 th {
        background-color: lightblue;
        color: white;
        }
	
        
    </style>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
</head>
<body>
<br><br>
<div class="container-fluid well span0">
	<div class="row-fluid">
        
        <div class="btn-group">
                <a class="btn dropdown-toggle btn-info" data-toggle="dropdown" >
                    USER 
                    <span class="icon-user icon-white"></span><span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="homepage.php"><span class="icon-home"></span> HomePage</a></li>
                    <li><a href="uploadpage.php"><span class="icon-file"></span> Upload your File</a></li>
                    <li><a href="index.html" action = logout> Logout</a></li>
                </ul>
            </div>
        
        <div class="span10">
            
            <?php
            
            
                foreach ($rows as $row) {
                    echo "<img src='".$row[5]."' class='img-circle' width='200' height='200'>";
                    echo "<h3>$row[1]</h3>";
                    echo "<h4>Email: $row[2]</h4>";
                    echo "<h4>Sex: $row[4]</h4>";

                }
            
            ?>
            
            
        </div>
        
        <div class="span10">
            
            <form  method="post" enctype="multipart/form-data"> 
                
           
            <br><h3>Select image to upload:</h3>
            <br><input class="btn dropdown-toggle " type="file" name="image" id="image">
            
            <input class="btn dropdown-toggle btn-info" type="submit" name="upload" value="UPLOAD" class="btn btn-primary" class="icon-upload icon-white"><br>
                
            <?php
            
            if (isset($_POST['upload'])){
                
                $errors = array();
                $allowed_e = array('png','jpg','jpeg');
                
                $file_name = $_FILES['image']['name'];
                $file_e = strtolower (pathinfo($file_name, PATHINFO_EXTENSION));
                $file_s = $_FILES['image']['size'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $upload= 1 ;
                
                if (in_array($file_e, $allowed_e) == false){
                    
                    $errors[] = 'This file extension is not allowed.';
                    
                }
                
                if ($_FILES['file']['size'] > 1000000){
                    
                    echo "<p style=color:Tomato;'>File must be under 2MB</p><br/>";
                    $upload= 0 ;
                }
                
                if ($upload){
                    
                    move_uploaded_file($file_tmp, 'images/'.$file_name);
                    $image_up = 'images/'.$file_name;
                    
                   

                        $sql = "UPDATE user SET profile_pic='".$image_up."' WHERE username = '$username_form'";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        echo $stmt->rowCount() . " UPDATED successfully";
                    
                        echo "
                            <script>
                            setTimeout(function(){window.location.href='homepage.php';},3000);
                            </script>
                             ";
                    
                }else{
                    
                    foreach ($errors as $error){
                        
                        echo '<p style="color:Tomato;">'.$error. '</p><br/>';
                        
                    }
                    
                }
                
                
            }
            
            
            ?>
                          
            </form>
            
        </div>
</div>
</div>


<script type="text/javascript">

</script>
</body>
</html>
<?php
    
    if(@$_GET['action']=="logout"){
        
        session_destroy();
        
    }
            
    
            
    
    
        }

?>
