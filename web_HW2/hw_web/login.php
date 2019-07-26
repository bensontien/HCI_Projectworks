<?PHP
header("Content-Type: text/html; charset=utf8");
if(!isset($_POST["login"])){
exit("Wrong");
}//檢測是否有login操作

$servername = "localhost";
$username = "root";
$password = "sheisyuri0405";
$dbname = "hw_web";

$username_form = $_POST['username'];//post獲得使用者名稱
$passowrd_form = $_POST['password'];//post獲得使用者密碼單值

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
    //$num_rows=mysqli_num_rows($result);//返回一個數值
    
    
    if($num_rows){//0 false 1 true
        echo "Login successfully";
        header("refresh:0;url=homepage.html");//如果成功跳轉至welcome.html頁面
        exit;
    }else{
        echo "使用者名稱或密碼錯誤";
        echo "
            <script>
            setTimeout(function(){window.location.href='login.html';},1000);
            </script>
            ";//如果錯誤使用js 1秒後跳轉到登入頁面重試;
    }



$conn = null;
?>