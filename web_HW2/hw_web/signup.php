<?php

if(!isset($_POST['submit'])){
exit("Wrong");
}//判斷是否有submit操作

$servername = "localhost";
$username = "root";
$password = "sheisyuri0405";
$dbname = "hw_web";

$sql='';
$conn='';


$username_form=$_POST['username'];//post獲取表單裡的username
$email=$_POST['email'];//post獲取表單裡的email
$password_form=$_POST['password'];//post獲取表單裡的password
$sex=$_POST['sex'];//post獲取表單裡的sex

try {
    
    //連接Sever
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    //插入資訊
    $sql = "INSERT INTO user (userid,username,email,password,sex) VALUES (null,'$username_form','$email','$password_form','$sex')";
    // use exec() because no results are returned
    $conn->exec($sql);
    
     echo "New record created successfully"; 
    
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }




$conn = null;

//header("refresh:0;url=index.html");
//exit;

?>