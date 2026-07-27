<?php
session_start();
require_once "db_connect.php";

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $login = $_POST['login'];
    $pass  = $_POST['password'];

    $res = mysqli_query($conn,"SELECT * FROM users WHERE login='$login'");
    $user = mysqli_fetch_assoc($res);

    if($user && $pass == $user['password']){

        $_SESSION['user'] = $user['login'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == "admin"){
            header("Location: admin_panel.php");
        } else {
            header("Location: user_panel.php");
        }
        exit;

    } else {
        $error = "Błędne dane";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie</title>

<style>
body{font-family:Arial;background:#f4f4f4;}
.box{
    width:300px;
    margin:80px auto;
    background:white;
    padding:20px;
    border-radius:10px;
}
input,button{
    width:100%;
    padding:10px;
    margin:5px 0;
}
</style>
</head>

<body>

<div class="box">

<h2>?? Logowanie</h2>

<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
<input name="login" placeholder="Login">
<input name="password" type="password" placeholder="Hasło">
<button>Zaloguj</button>
</form>

</div>

</body>
</html>