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
                    <li><a href="ChangeImagepage.php"><span class="icon-picture"></span> Update your Image</a></li>
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
                
           
            <br><h3>Select file to upload(File must be under 1MB):</h3>
            <br><input class="btn dropdown-toggle " type="file" name="file" id="file">
            
            <input class="btn dropdown-toggle btn-info" type="submit" name="upload" value="UPLOAD" class="btn btn-primary" class="icon-upload icon-white"><br>
                
            <?php
            
            if (isset($_POST['upload'])){
                
                $errors = array();
                
                $file_name = $_FILES['file']['name'];
                $file_s = $_FILES['file']['size'];
                $file_tmp = $_FILES['file']['tmp_name'];
                $time = date('Y-m-d H:i:s');
                $upload= 1 ;
                
                
                if ($_FILES['file']['size'] > 1000000){
                    
                    echo "<p style=color:Tomato;'>File must be under 1MB</p><br/>";
                    $upload= 0 ;
                }
                
                
                
                if ($upload){
                    
                    if (file_exists('files/'.$file_name)) {
                        
                        echo "<p style='color:Tomato;'>Sorry, file already exists.</p><br/>";
//                        echo "Would you want to cover it?<br/>";
                        
                ?>      
<!--
                        <form  method="post" >
                    
                        <input type='submit' method='post' name ='CoverYes' value="Yes"><br/> 
                        <input type='submit' method='post' name ='CoverNo' value="No">
                
                        </form>
-->
                        
                <?php       
//                        if (isset($_POST['CoverYes'])){
//                            
//                        unlink('files/'.$file_name);
//                            
//                        move_uploaded_file($file_tmp, 'files/'.$file_name);
//                        $files_up = 'files/'.$file_name;
//                        
//                        $sql = "INSERT INTO file (fileid,username,filename,filesize,time) VALUES (null,'$username_form','$file_name','$file_s','$time')";
//                        $stmt = $conn->prepare($sql);
//                        $stmt->execute();
//                        echo $stmt->rowCount() . " UPDATED successfully";
                            
//                        }
                    
                    }else{
                        
                        move_uploaded_file($file_tmp, 'files/'.$file_name);
                        $files_up = 'files/'.$file_name;
                        
                        $sql = "INSERT INTO file (fileid,username,filename,filesize,time) VALUES (null,'$username_form','$file_name','$file_s','$time')";
                        $conn->exec($sql);
                        echo " UPDATED successfully";
                    }
                    

                    
                }else{
                    
                    
                    echo "<p style=color:Tomato;'>Sorry, your file was not uploaded.</p><br/>";
                    
                }
                
                
            }
            
            
            ?>
                          
            </form>
            
        </div>
</div>
</div>
<div class="container-fluid well span12">
    <h3 style="color:Black;">File List</h3>
    <form action="homepage.php" method="post">
        <table style="width:100%" id="t01">
            <tr>
                
                <th>Filename</th>
                <th>Filesize</th>
                <th>DATE</th>
   
            </tr>
            
            <tr>
            
            <?php
            
                $sql = "select * from file where username = '$username_form'";
                $conn->query('set names utf8;');
                $result = $conn->query($sql);
                $rows = $result->fetchAll();
            
                foreach ($rows as $row) {
                    
                    echo "<tr>";
                    echo "<td><a download href='/files/$row[2]'>".$row[2]."</a></td>";
                    echo "<td>$row[3]</td>";
                    echo "<td>$row[4]</td>";
                    echo "</tr>";
                }
            
            ?>
            
            </tr>
            
        </table>    
    </form>   
    
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
