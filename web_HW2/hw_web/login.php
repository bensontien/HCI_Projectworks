<?PHP

session_start();

header("Content-Type: text/html; charset=utf8");
if(!isset($_POST["login"])){
exit("Wrong");
}//檢測是否有login操作

$servername = "localhost";
$username = "root";
$password = "sheisyuri0405";
$dbname = "hw_web";

$username_form = $_POST['username'];//post獲得使用者名稱
$passowrd_form = $_POST['password'];//post獲得使用者密碼

$result='';
$num_rows='';
$conn='';
$sql='';

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


if ($username_form && $passowrd_form){
            $sql = "select * from user where username = '$username_form' and password='$passowrd_form'";}
    
    $conn->query('set names utf8;');
    $result = $conn->query($sql);
    $num_rows = $result->fetchAll();

    
    
    if($num_rows){//0 false 1 true
        $_SESSION["username"] = $username_form;
        header("refresh:0;url=homepage.html");//如果成功跳轉至homepage.html頁面
        exit;
    }else{
        echo "Wrong Username or Password, after 3 seconds go back login page";
        echo "
            <script>
            setTimeout(function(){window.location.href='index.html';},3000);
            </script>
            ";
    }



$conn = null;
?>