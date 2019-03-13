<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | LOGIN</title>
  <meta name="author" content="Walter De Carne">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="stylesheet.css" type="text/css">
  <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
</head>

<body id="body">

<h1>COMPRO-VENDO</h1>

  <div id="menu">
    <nav>
     <a href="home.php">Home</a>
     <a <?php session_start(); if(!isset($_SESSION["utente"])) echo"href=\"login.php\""?>>Login</a>
     <a href="info.php">Info</a>
     <a href="cambia.php">Cambia</a>
     <a href="acquista.php">Acquista</a>
     <a <?php if(isset($_SESSION["utente"])) echo "href=\"logout.php\""?>>Logout</a>
    </nav>
  </div>

<?php
// Creo connessione DB
$con = mysqli_connect('localhost', 'uWeak', 'posso_leggere?', 'comprovendo');

// Controllo connessione DB
if (!$con) {
    die("Connection failed: " .mysqli_connect_error());
}
?>

<div id="borsellino">
  <?php
    if(isset($_SESSION["utente"])){
    $ses=$_SESSION["utente"];
    $query= "SELECT * FROM utenti WHERE nick = '$ses'";
    $res=mysqli_query($con, $query);
    if(!$res)
      die("Errore query borsellino: ".mysqli_error());
    $row = mysqli_fetch_assoc($res);
    $money=$row["money"];
    echo "<p>$ses<br> &euro;". number_format($money/100, 2)."</p>";
    //Libero memoria risultato
    mysqli_free_result($res);
}
  else {
    echo "<p>anonimo<br>&euro; 0</p>";
  }

?>

</div>

<?php
//Controllo login
if(isset($_SESSION["utente"]))
  die("<p class=\"centertext\">Attenzione! Hai gi&agrave; effettuato il login! Continua a navigare sul nostro sito.</p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");
?>

  <form method="post" action="login.php">
    <div id="login">

      <div class="loginp">
        <label>Nome utente</label> <input type="text" class="login" name="utente" id="utente" autocomplete="off" oninput="controllouser()" value="<?php if(isset($_COOKIE["utente"])) echo $_COOKIE["utente"];?>" required>
      </div>

      <div class="loginp">
        <label>Password</label> <input type="password" name="pass" id="pass" placeholder="4-6 numeri" autocomplete="off" oninput="controllopass()" required>
      </div>

    </div>

    <div class="pass">
      <input type="checkbox" onclick="mostrapass()">Mostra password
    </div>

    <div class="logbuttons">
      <input type="reset" value="PULISCI">
      <input type="submit" value="OK">
    </div>
  </form>

<!-- Funzioni JS (mostra password, controllo sintassi user e password lato client) -->
<script>
function mostrapass() {
    var password = document.getElementById("pass");

    if (password.type === "password")
        password.type = "text";
    else
        password.type = "password";
}

function controllouser(){
  var user = document.getElementById("utente").value;
  var regexuser = /^[A-za-z\$](?=.*\${0,})(?=.*[a-zA-Z]{0,})(?=.*\d{1,})[a-zA-Z\d\$]{2,7}$/;

  if(!user.match(regexuser)){
    document.getElementById("utente").style.borderColor='red';
    document.getElementById("utente").style.borderWidth = 'medium';
  }
  else if(user.match(regexuser) != null && user.match(regexuser).length == 1){
      document.getElementById("utente").style.borderColor='#4EB1BA';
      document.getElementById("utente").style.borderWidth = 'medium';
    }
}

function controllopass(){
  var pass = document.getElementById("pass").value;
  var regexpass = /^[0-9]{4,6}$/;

  if(!pass.match(regexpass)){
    document.getElementById("pass").style.borderColor='red';
    document.getElementById("pass").style.borderWidth = 'medium';
  }
  else if(pass.match(regexpass) != null && pass.match(regexpass).length == 1){
      document.getElementById("pass").style.borderColor='#4EB1BA';
      document.getElementById("pass").style.borderWidth = 'medium';
    }
}

</script>

<noscript id="noscript">
  <p class="centertext">Attenzione! Questa pagina utilizza JavaScript. Essendo disabilitato, alcune funzionalità potrebbero non essere disponibili.</p>
</noscript>

<div id="text">
<?php
  if (isset($_REQUEST["utente"]) && isset($_REQUEST["pass"])){
    $utente = $_REQUEST["utente"];
    $pass=$_REQUEST["pass"];

  $query= "SELECT nick, pass FROM utenti WHERE nick=\"$utente\"";
  $result = mysqli_query($con,$query);

//Controllo corrispondenza nome utente
if (mysqli_num_rows($result)==0){
  session_destroy();
  echo ("<p>Autenticazione fallita: utente non trovato. Reinserisci i dati!</p>");
}

//Espressioni regolari
$regexutente='/^[A-za-z\$](?=.*\${0,})(?=.*[a-zA-Z]{0,})(?=.*\d{1,})[a-zA-Z\d\$]{2,7}$/';
$regexpass='/^[0-9]{4,6}$/';

//Controllo user e password lato server
if(!preg_match($regexutente, $utente))
  echo ("<p>Attenzione! Lo username pu&ograve; contenere solo caratteri alfanumerici o $, di cui il primo alfabetico o $. Inoltre dev'essere lungo da 3 a 8 caratteri, di cui almeno 1 numerico e 1 non numerico.</p>");
else if(!preg_match($regexpass, $pass))
  echo ("<p>Attenzione! La password pu&ograve; contenere solo caratteri numerici, e dev'essere lunga da 4 a 6 caratteri).</p>");

//Controllo corrispondenza password, se c'è match reindirizzamento e creazione cookie e sessione
while($row = mysqli_fetch_assoc($result)){
  if($row["pass"]==$pass){
    setcookie("utente", $_REQUEST["utente"], time()+172800);
    $_SESSION["utente"]=$utente;
    header('Location: info.php');
  }
    else
      echo "<p>Autenticazione fallita: password errata. Reinserisci i dati!</p>";
    }

    //Libero memoria risultato
    mysqli_free_result($result);
}

//Chiusura connessione
mysqli_close($con);
?>

</div>

<footer>
  <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
</footer>

</body>
</html>
