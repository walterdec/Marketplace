<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | INFO</title>
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

  //Controllo sessione ordine
  if(isset($_SESSION["tot"]))
    unset($_SESSION["tot"]);
?>

</div>

<?php
//Controllo login
if(isset($_SESSION["utente"])){
  $query = "SELECT nome, vend, qty, prezzo FROM prodotti";
  $result = mysqli_query($con,$query);

  //Controllo query
  if (mysqli_num_rows($result)==0)
    die ("<p>Errore: query fallita!</p>");

  //Stampa table head
  echo "<div id=\"info1\"><table id=\"table\"><thead><tr><th>Prodotto</th><th>Venditore</th><th>Quantit&agrave;</th><th>Prezzo unitario</th></tr></thead><tbody>";

  //Estrazione dati e stampa
  while($row = mysqli_fetch_assoc($result)){
    if($row["qty"]!=0)
      echo "<tr><td>".$row["nome"]."</td><td>".$row["vend"]."</td><td>".$row["qty"]."</td><td>&euro; ".number_format($row["prezzo"]/100, 2)."</td></tr>";}
    echo "</tbody></table></div>";

    //Libero memoria risultato
    mysqli_free_result($result);
}

  else {
    //Query
    $query= "SELECT nome, qty FROM prodotti";
    $result = mysqli_query($con,$query);

    //Controllo query
    if (mysqli_num_rows($result)==0)
      die ("<p>Errore: query fallita!</p>");

    //Stampa thead
    echo "<div id=\"info2\"><table id=\"table\"><tr><th>Prodotto</th><th>Quantit&agrave;</th></tr>";

    //Estrazione dati e stampa
    while($row = mysqli_fetch_assoc($result)){
    if ($row["qty"]!=0)
      echo "<tr><td>".$row["nome"]."</td><td>".$row["qty"]."</td></tr>";
    }
    echo "</table></div>";

    //Libero memoria risultato
    mysqli_free_result($result);
  }

  //Chiusura connessione
  mysqli_close($con);
?>

<footer>
  <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
</footer>

</body>
</html>
