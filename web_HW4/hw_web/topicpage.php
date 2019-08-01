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
        

        


    <title>Comment - Bootsnipp.com</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        /**
 * Oscuro: #283035
 * Azul: #03658c
 * Detalle: #c7cacb
 * Fondo: #dee1e3
 ----------------------------------*/
 * {
     margin: 0;
 	padding: 0;
 	-webkit-box-sizing: border-box;
 	-moz-box-sizing: border-box;
 	box-sizing: border-box;
 }

 a {
 	color: #03658c;
 	text-decoration: none;
 }

ul {
	list-style-type: none;
}

body {
	font-family: 'Roboto', Arial, Helvetica, Sans-serif, Verdana;
	background: #dee1e3;
}

/** ====================
 * Lista de Comentarios
 =======================*/
.comments-container {
	margin: 60px auto 15px;
	width: 768px;
}

.comments-container h1 {
	font-size: 36px;
	color: #283035;
	font-weight: 400;
}

.comments-container h1 a {
	font-size: 18px;
	font-weight: 700;
}

.comments-list {
	margin-top: 30px;
	position: relative;
}

/**
 * Lineas / Detalles
 -----------------------*/
.comments-list:before {
	content: '';
	width: 2px;
	height: 100%;
	background: #c7cacb;
	position: absolute;
	left: 32px;
	top: 0;
}

.comments-list:after {
	content: '';
	position: absolute;
	background: #c7cacb;
	bottom: 0;
	left: 27px;
	width: 7px;
	height: 7px;
	border: 3px solid #dee1e3;
	-webkit-border-radius: 50%;
	-moz-border-radius: 50%;
	border-radius: 50%;
}

.reply-list:before, .reply-list:after {display: none;}
.reply-list li:before {
	content: '';
	width: 60px;
	height: 2px;
	background: #c7cacb;
	position: absolute;
	top: 25px;
	left: -55px;
}


.comments-list li {
	margin-bottom: 15px;
	display: block;
	position: relative;
}

.comments-list li:after {
	content: '';
	display: block;
	clear: both;
	height: 0;
	width: 0;
}

.reply-list {
	padding-left: 88px;
	clear: both;
	margin-top: 15px;
}
/**
 * Avatar
 ---------------------------*/
.comments-list .comment-avatar {
	width: 65px;
	height: 65px;
	position: relative;
	z-index: 99;
	float: left;
	border: 3px solid #FFF;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	-webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	-moz-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	box-shadow: 0 1px 2px rgba(0,0,0,0.2);
	overflow: hidden;
}

.comments-list .comment-avatar img {
	width: 100%;
	height: 100%;
}

.reply-list .comment-avatar {
	width: 50px;
	height: 50px;
}

.comment-main-level:after {
	content: '';
	width: 0;
	height: 0;
	display: block;
	clear: both;
}
/**
 * Caja del Comentario
 ---------------------------*/
.comments-list .comment-box {
	width: 680px;
	float: right;
	position: relative;
	-webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
	-moz-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
	box-shadow: 0 1px 1px rgba(0,0,0,0.15);
}

.comments-list .comment-box:before, .comments-list .comment-box:after {
	content: '';
	height: 0;
	width: 0;
	position: absolute;
	display: block;
	border-width: 10px 12px 10px 0;
	border-style: solid;
	border-color: transparent #FCFCFC;
	top: 8px;
	left: -11px;
}

.comments-list .comment-box:before {
	border-width: 11px 13px 11px 0;
	border-color: transparent rgba(0,0,0,0.05);
	left: -12px;
}

.reply-list .comment-box {
	width: 610px;
}
.comment-box .comment-head {
	background: #FCFCFC;
	padding: 10px 12px;
	border-bottom: 1px solid #E5E5E5;
	overflow: hidden;
	-webkit-border-radius: 4px 4px 0 0;
	-moz-border-radius: 4px 4px 0 0;
	border-radius: 4px 4px 0 0;
}

.comment-box .comment-head i {
	float: right;
	margin-left: 14px;
	position: relative;
	top: 2px;
	color: #A6A6A6;
	cursor: pointer;
	-webkit-transition: color 0.3s ease;
	-o-transition: color 0.3s ease;
	transition: color 0.3s ease;
}

.comment-box .comment-head i:hover {
	color: #03658c;
}

.comment-box .comment-name {
	color: #283035;
	font-size: 14px;
	font-weight: 700;
	float: left;
	margin-right: 10px;
}

.comment-box .comment-name a {
	color: #283035;
}

.comment-box .comment-head span {
	float: left;
	color: #999;
	font-size: 13px;
	position: relative;
	top: 1px;
}

.comment-box .comment-content {
	background: #FFF;
	padding: 12px;
	font-size: 15px;
	color: #595959;
	-webkit-border-radius: 0 0 4px 4px;
	-moz-border-radius: 0 0 4px 4px;
	border-radius: 0 0 4px 4px;
}

.comment-box .comment-name.by-author, .comment-box .comment-name.by-author a {color: #03658c;}
.comment-box .comment-name.by-author:after {
	content: 'autor';
	background: #03658c;
	color: #FFF;
	font-size: 12px;
	padding: 3px 5px;
	font-weight: 700;
	margin-left: 10px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
}

/** =====================
 * Responsive
 ========================*/
@media only screen and (max-width: 766px) {
	.comments-container {
		width: 480px;
	}

	.comments-list .comment-box {
		width: 390px;
	}

	.reply-list .comment-box {
		width: 320px;
	}
}
        
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
                    $img = $row[5];

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
                    <li><a href="homepage.php"><span class="icon-home"></span> HomePage</a></li>
                    <li><a href="ChangeImagepage.php"><span class="icon-picture"></span> Update your Image</a></li>
                    <li><a href="uploadpage.php"><span class="icon-file"></span> Upload your File</a></li>
<!--                    <li><a href="#"><span class="icon-pencil"></span> Update your info</a></li>-->
                    <li><a href="index.html" action = logout> Logout</a></li>
                </ul>
            </div>
        </div>
</div>
</div>
    
<?php
    

        if($_GET["tid"]){
        
        $sql = "select * from topics where topic_id ='".$_GET['tid']."'";
        $conn->query('set names utf8;');
        $result = $conn->query($sql);
        $rows = $result->fetchAll();
            
        foreach ($rows as $row) {
                    
                    $topic_id = $row[0];
                    $topic_title = $row[1];
                    $topic_content = $row[2];
                    $topic_creator = $row[3];
                    $date = $row[4];
                    $replies = $row[5];
                }
        
    }
    
?>
    
<div class="container">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">
	<div class="row">
	<!-- Contenedor Principal -->
    <div class="comments-container">
		<?php echo "<h1 style='color:White; font-size:60px;'>$topic_title</h1>";?>

		<ul id="comments-list" class="comments-list">
			<li>
				<div class="comment-main-level">
					<!-- Avatar -->
					
					<!-- Contenedor del Comentario -->
					<div class="comment-box">
						<div class="comment-head">
                            
                            <?php 
                            
                            echo "<h6>Poster:</h6>";
                            echo "<h2>".$topic_creator."</h2>";
                            echo "<h6>Last time</h6>";
                            echo "<h7>".$date."</h7><br/>";
            
                            if($username_form ==$topic_creator ){
                            
                            ?>
                            
                            
                            <form method="post">
                            
                            <input type="submit" name ="deletetopic" class="btn btn-primary" value="Delete"><span class="icon-trash icon-white" ></span><br/><br/>
                            
                            <br/><h5>Edit:</h5>
                            <input type="text" name ="edittext"><span class="icon-white" ></span><br/><br/>
                            <input type="submit" name ="edittopic" class="btn btn-primary" value="edit"><span class="icon-trash icon-white" ></span><br/>
                                
                                
                            <?php
                                
                            }
                                
                            if(isset($_POST['deletetopic'])){
                                
                                ?>
                                
<!--
                                    <form method="post">
                                        
                                        <br/><h3>Would you want to delete this topic?</h3>
                                        <input type="submit" name ="deletetopicYES" class="btn btn-primary" value="Yes"><span class="icon-white" ></span><br/><br/>
                                        
                                        <input type="submit" name ="deletetopicNO" class="btn btn-primary" value="No"><span class="icon-white" ></span><br/><br/>
                                
                                    </form>
-->
                                    
                                <?php
                                
                                
//                                if(isset($_POST['deletetopicYES'])){
                                    
                                $sql = "DELETE FROM replies WHERE topic_id='$topic_id' ";
                                // use exec() because no results are returned
                                $conn->exec($sql);
                                
                                $sql = "DELETE FROM topics WHERE topic_title='$topic_title' and topic_creator='$topic_creator'";
                                // use exec() because no results are returned
                                $conn->exec($sql);
                                
                                echo "
                                    <script>
                                    setTimeout(function(){window.location.href='homepage.php';},500);
                                   </script>
                                    ";
                                    
                                    
//                                }
//                                
//                                if(isset($_POST['deletetopicNO'])){
//                                    
//                                    echo "
//                                    <script>
//                                    setTimeout(function(){window.location.href='topicpage.php?tid=$topic_id';},500);
//                                    </script>
//                                    ";
//                                    
//                                }
                            
                                
                            }
            
            
                            if(isset($_POST['edittopic'])){
                                
                            
                                
                            $edit_content = @$_POST['edittext'];
                            $edit_time =  date('Y-m-d H:i:s');
                                
                            $sql = "UPDATE topics SET topic_content='$edit_content' WHERE topic_id='$topic_id'";
                            $conn->exec($sql);
                                
                            $sql = "UPDATE topics SET date ='$edit_time' WHERE topic_id='$topic_id'";
                            $conn->exec($sql);
  
   
                            }
            
                                
                            ?>
                                    
                            
                            
                            </form>
                            
                            
                            

						</div>
						<div class="comment-content">
							
                            
                            <?php 
                            
                            echo "<h1>".$topic_content."</h1>";
                            
                            ?>
                            
						</div>
					</div>
				</div>
			</li>

			
		</ul>
        
        <div class="container-fluid well span10">
    <h3 style="color:Black;">Reply List</h3>
    <form method="post">
        
        <br/><h5>Which Reply do you want to delete?</h5>
        <input type="text" name ="replytodelete"><span class="icon-white" ></span><br/><br/>
        
        <br/><h5>Timestamp?</h5>
        <input type="text" name ="lastreplytimetodelete"><span class="icon-white" ></span><br/><br/>
        
        <input type="submit" name ="deletereply" class="btn btn-primary" value="delete"><br/>
        
        
        <br/><h5>Which Reply do you want to edit?</h5>
        <input type="text" name ="replybeforetext"><span class="icon-white" ></span><br/><br/>
        
        <br/><h5>Timestamp?</h5>
        <input type="text" name ="lastreplytime"><span class="icon-white" ></span><br/><br/>
        
        <br/><h5>Edit content:</h5>
        <input type="text" name ="replyaftertext"><span class="icon-white" ></span><br/><br/>
        
        <input type="submit" name ="editreply" class="btn btn-primary" value="edit"><br/>
        
        <br/>
        <table style="width:100%" id="t01">
            <tr>
                
                <th>Author</th>
                <th>Reply</th>
                <th>Date</th>
   
            </tr>
            
            <tr>
            
            <?php
            
                $sql = "select * from replies where topic_id = '$topic_id'";
                $conn->query('set names utf8;');
                $result = $conn->query($sql);
                $rows = $result->fetchAll();
            
                foreach ($rows as $row) {
                    
                    echo "<tr>";
                    echo "<td>$row[2]</td>";
                    echo "<td>$row[3]</td>";
                    echo "<td>$row[4]</td>";
                    echo "</tr>";
                }
            
            if(isset($_POST['deletereply'])){
                
                
                                
            $last_reply =  @$_POST['replytodelete'];              
            $timeforsure_delete =  @$_POST['lastreplytimetodelete'];                   
                
            $sql = "DELETE FROM replies WHERE comment='$last_reply' and date = '$timeforsure_delete ' and reply_creator = '$username_form'";
            $conn->exec($sql); 
                
//            echo "<p style=color:Tomato;'>You can not edit this reply</p><br/>";
                  
   
            }
            
            if(isset($_POST['editreply'])){
                
                
                                
            $last_reply =  @$_POST['replybeforetext'];              
            $timeforsure =  @$_POST['lastreplytime'];                   
            $edit_reply = @$_POST['replyaftertext'];
            $edit_time =  date('Y-m-d H:i:s');
                
                     
            $sql = "UPDATE replies SET comment='$edit_reply' WHERE comment='$last_reply' and date = '$timeforsure' and reply_creator = '$username_form'";
            $conn->exec($sql); 
                                
            $sql = "UPDATE replies SET date ='$edit_time' WHERE comment='$edit_reply' and date = '$timeforsure'";
            $conn->exec($sql);
                
                
//            echo "<p style=color:Tomato;'>You can not edit this reply</p><br/>";
                  
   
            }
            
            ?>
            
            </tr>
            
        </table>    
    </form>   
    
</div>
        
    <div class="container-fluid well span10">
    <h3 style="color:Black;">Post Reply</h3>
    <form method="post">
          
        <h4 style="color:Black;"><br/>Reply<br/></h4>
        <textarea style = "resize:none; width:750px; height:100px;" name="reply_content"></textarea><br/>
        <br/><input type="submit" name="reply_submit" value="Reply" class="btn btn-primary">
        
        <?php
        
        $reply_content = @$_POST['reply_content'];
        $time = date('Y-m-d H:i:s');
            
            if(isset($_POST['reply_submit'])){
                
                if($reply_content){
                    
                    if(strlen($reply_content)>=1){
                        
                        
                        $sql = "INSERT INTO replies (reply_id,topic_id,reply_creator,comment,date) VALUES (null,'$topic_id','$username_form','$reply_content','$time')";
                        // use exec() because no results are returned
                        $conn->exec($sql);
                        
                        $sql = "UPDATE topics SET replies = replies + 1 where topic_id='$topic_id'";
                        $conn->exec($sql);
                        
                        echo "
                            <script>
                            setTimeout(function(){window.location.href='topicpage.php?tid=$topic_id;},500);
                            </script>
                            ";
                        
                        
                        
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
            
    if(isset($_POST['delete'])){
        
        header("refresh:0;url=index.html");
        
    }
               
        
    }

?>
