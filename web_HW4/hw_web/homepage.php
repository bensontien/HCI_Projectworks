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
        text-align: center
        }
    table#t01 td {
        text-align: center
        
        }
	
        
    </style>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
</head>
<body>
<br><br>
<div class="container-fluid well span3">
	<div class="row-fluid">
        
        <div class="span7">
            
            <?php

                foreach ($rows as $row) {
                    
                    echo "<img src='".$row[5]."' class='img-circle' width='200' height='200'>";
                    echo "<h3>$row[1]</h3>";
                    echo "<h4>Email: $row[2]</h4>";
                    echo "<h4>Sex: $row[4]</h4>";

                }
            
            ?>
            
            
        </div>
        
        <div >
            <div class="btn-group">
                <a class="btn dropdown-toggle btn-info" data-toggle="dropdown" >
                    USER 
                    <span class="icon-user icon-white"></span><span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="ChangeImagepage.php"><span class="icon-picture"></span> Update your Image</a></li>
                    <li><a href="uploadpage.php"><span class="icon-file"></span> Upload your File</a></li>
<!--                    <li><a href="#"><span class="icon-pencil"></span> Update your info</a></li>-->
                    <li><a href="index.html" action = logout> Logout</a></li>
                </ul>
            </div>
        </div>
</div>
</div>
    
    
<div class="container-fluid well span12">
    <h3 style="color:Black;">Member List</h3>
    <form action="homepage.php" method="post">
        <table style="width:100%" id="t01">
            <tr>
                
                <th>Icon</th>
                <th>UserID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Sex</th>
   
            </tr>
            
            <tr>
            
            <?php
            
                $sql = "select * from user";
                $conn->query('set names utf8;');
                $result = $conn->query($sql);
                $rows = $result->fetchAll();
            
                foreach ($rows as $row) {
                    
                    echo "<tr>";
                    echo "<td><img src='".$row[5]."' class='img-circle' width='100' height='100'></td>";
                    echo "<td>$row[0]</td>";
                    echo "<td>$row[1]</td>";
                    echo "<td>$row[2]</td>";
                    echo "<td>$row[4]</td>";
                    echo "</tr>";
                }
            
            ?>
            
            </tr>
            
        </table>    
    </form>   
    
</div>
    
<div class="container-fluid well span5">
    <h3 style="color:Black;">Post Topic</h3>
    <form action="homepage.php" method="post">
          
        <h4 style="color:Black;">Topic Title<br/></h4>
        <input type = "text" name="topic_title" style="width:400px">
        <h4 style="color:Black;"><br/>Content<br/></h4>
        <textarea style = "resize:none; width:400px; height:300px;" name="content"></textarea><br/>
        <br/><input class="btn dropdown-toggle btn-info" class="btn btn-primary" class="icon-upload icon-white" type="submit" name="topic_submit" value = "Post"style = "resize:none; width:100px; height:50px;">
        
        <?php
        
        
        $topic_title = @$_POST['topic_title'];
        $content = @$_POST['content'];
        $time = date('Y-m-d H:i:s');
            
            if(isset($_POST['topic_submit'])){
                
                if($topic_title && $content){
                    
                    if(strlen($topic_title)>=1 && strlen($topic_title)<=70){
                        
                        $sql = "INSERT INTO topics (topic_id,topic_title,topic_content,topic_creator,date,replies) VALUES (null,'$topic_title','$content','$username_form','$time','0')";
                        // use exec() because no results are returned
                        $conn->exec($sql);
                        
                        
                        
                    }else{
                        
                        echo '<br/><p style="color:Tomato;">Topic title must between 1 and 70 charcters long.</p><br/>';
                        
                    }
                    
                }else{
                    
                        echo '<br/><p style="color:Tomato;">Please Fill the field Correctly.</p><br/>';
                    
                }
                
            }
        
        
        ?>
        
    </form>   
    
</div>
    
<div class="container-fluid well span10">
    <h3 style="color:Black;">Topic List</h3>
    <form action="homepage.php" method="post">
        <table style="width:100%" id="t01">
            <tr>
                
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Replies</th>
                <th>Date</th>
   
            </tr>
            
            <tr>
            
            <?php
            
                $sql = "select * from topics";
                $conn->query('set names utf8;');
                $result = $conn->query($sql);
                $rows = $result->fetchAll();
            
                foreach ($rows as $row) {
                    
                    echo "<tr>";
                    echo "<td>$row[0]</td>";
                    echo "<td><a href='topicpage.php?tid=$row[0]'>".$row[1]."</a></td>";
                    echo "<td>$row[3]</td>";
                    echo "<td>$row[5]</td>";
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
