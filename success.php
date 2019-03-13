<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | SUCCESS</title>
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
  //Borsellino elettronico
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
if(!isset($_SESSION["utente"]))
  die("<p class=\"centertext\">Attenzione! Questa pagina &egrave; accessibile solo agli utenti autenticati.</p><p class=\"centertext\"><a href=\"login.php\">Effettua il login</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

//Controllo dati
if(!isset($_SESSION["successqta"]) && !isset($_SESSION["successins"]))
    echo "<p class=\"centertext\">Attenzione! Non &egrave; stato effettuato nessun aggiornamento/inserimento.</p>";

//Controllo sessione quantità
if (isset($_SESSION["successqta"])){
    echo "<p class=\"centertext\">Aggiornamento effettuato! Nuova quantit&agrave; disponibile.</p>";
    unset($_SESSION["successqta"]);
}

//Controllo sessione inserimento
if (isset($_SESSION["successins"])){
  echo "<p class=\"centertext\">Inserimento effettuato! Un nuovo prodotto &egrave; in vendita.</p>";
  unset($_SESSION["successins"]);
}

//Chiusura connessione
mysqli_close($con);
?>

  <p class="centertext"><a href="cambia.php">Torna alla pagina CAMBIA</a></p>

  <footer>
    <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
  </footer>

</body>
</html>
