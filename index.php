<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Katalog Monet</title>

<style>
body{
    margin:0;
    font-family:Arial;
    height:100vh;
    overflow:hidden;

    /* ?? TAPETA Z MONETAMI */
    background-image: url('https://images.unsplash.com/photo-1605902711622-cfb43c4437d3?auto=format&fit=crop&w=1600&q=80');
    background-size: cover;
    background-position: center;
}

/* ?? ciemna nakładka */
.overlay{
    position:fixed;
    top:0;left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.55);
}

/* ?? GÓRNY SIDEBAR */
.topbar{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    background:rgba(0,0,0,0.75);
    display:flex;
    justify-content:center;
    gap:20px;
    padding:12px;
    z-index:10;
}

.topbar a{
    color:white;
    text-decoration:none;
    padding:8px 14px;
    background:#1e88e5;
    border-radius:6px;
    transition:0.2s;
}

.topbar a:hover{
    background:#42a5f5;
    transform:scale(1.05);
}

/* ?? CENTRALNY NAPIS */
.center{
    position:relative;
    z-index:2;
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    color:white;
    text-align:center;
}

.center h1{
    font-size:45px;
    text-shadow:0 2px 10px black;
}

/* ?? DOLNY SIDEBAR */
.bottombar{
    position:fixed;
    bottom:0;
    left:0;
    width:100%;
    background:rgba(0,0,0,0.75);
    color:#ddd;
    text-align:center;
    padding:8px;
    font-size:14px;
}
</style>

</head>

<body>

<div class="overlay"></div>

<!-- ?? MENU -->
<div class="topbar">
    <a href="login.php">?? Logowanie</a>
    <a href="register.php">?? Rejestracja</a>
    <a href="kontynenty.php">?? Katalog</a>
</div>

<!-- ?? TREŚĆ -->
<div class="center">
    <h1>?? Katalog Monet Świata</h1>
    <p>Kolekcja monet z różnych epok i krajów</p>
</div>

<!-- ?? STOPKA Z ROKIEM -->
<div class="bottombar">
    © <span id="year"></span> Katalog Monet | Wszystkie prawa zastrzeżone
</div>

<script>
document.getElementById("year").innerText = new Date().getFullYear();
</script>

</body>
</html>