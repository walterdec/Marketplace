<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | CONFERMA</title>
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
 //Creo connessione DB
 $con = mysqli_connect('localhost', 'uWeak', 'posso_leggere?', 'comprovendo');

 //Controllo connessione DB
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
    die("Errore query: ".mysqli_error());
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
  //Controllo autenticazione
   if(!isset($_SESSION["utente"]))
   die("<p class=\"centertext\">Attenzione! Questa pagina &egrave; accessibile solo agli utenti autenticati.</p><p class=\"centertext\"><a href=\"login.php\">Effettua il login</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");


  //Query #1
  $vend=$_SESSION["utente"];
  $sql="SELECT * FROM prodotti WHERE vend<>'$vend'";
  $result=mysqli_query($con, $sql);
  $prod=mysqli_num_rows($result);

  $f=0;
  $regqta= '/^[0-9]{1,5}$/';

  //Controllo sessioni
  for($j=1; $j<=$prod; $j++){
    if (isset($_REQUEST["qta$j"])){
    if ($_REQUEST["qta$j"]!=0){
      $f=1;

      //Controllo per evitare SQL Injection (qualcuno potrebbe aggiungere nuove opzioni al menu select usando la console del browser)
      if(!preg_match($regqta, $_REQUEST["qta$j"]))
        die ("<p>Attenzione! Errore menu select! Possibile tentativo SQL Injection, i campi del menu select sono stati alterati!</p>");
    }
  }
  //Controllo sessioni
    if(isset($_SESSION["parz$j"]) || isset($_SESSION["qta$j"]) || isset($_SESSION["quantita$j"])){
      unset($_SESSION["parz$j"]);
      unset($_SESSION["vend$j"]);
      unset($_SESSION["quantita$j"]);
      unset($_SESSION["prod$j"]);
      unset($_SESSION["qta$j"]);
    }

  //Estrazione e associazione a vettore
  $row = mysqli_fetch_assoc($result);
  if(!isset($_REQUEST["qta$j"]))
    $quantità[$j]=0;
  else
    $quantità[$j]=$_REQUEST["qta$j"];
  }

//Controllo selezione prodotti
if($f==0)
  die ("<p class=\"centertext\">Attenzione! Non hai selezionato alcun prodotto.</p><p class=\"centertext\"><a href=\"acquista.php\">Torna alla pagina ACQUISTA</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

  //Libero memoria risultato
  mysqli_free_result($result);
?>

<div id="acquista">
  <form method="post" action="finale.php">
    <fieldset>
      <legend>Conferma</legend>
        <table id="table">
          <thead>
            <tr>
              <th>Prodotto</th>
              <th>Quantit&agrave;</th>
              <th>Prezzo</th>
              <th>Prezzo parziale</th>
            </tr>
          </thead>
          <tbody>

<?php
  //Query #2
  $sql="SELECT * FROM prodotti WHERE vend<>'$vend'";
  $result=mysqli_query($con, $sql);

  //Estrazione vettore associativo, stampa prodotti selezionati, creazione sessioni
  $totale=0;
  foreach ($quantità as $j => $qtà){
    $row = mysqli_fetch_assoc($result);
    if($qtà!=0) {
      echo "<tr><td>".$row["nome"]."</td><td>".$qtà."</td><td>&euro;".number_format($row["prezzo"]/100, 2)."</td><td>&euro;".number_format(($qtà*$row["prezzo"])/100, 2)."</td></tr>";
      $totale=$totale+($qtà*$row["prezzo"]);

    $_SESSION["vend$j"]=$row["vend"];
    $_SESSION["parz$j"]=($qtà*$row["prezzo"]);
    $_SESSION["qta$j"]=$qtà;
    $_SESSION["quantita$j"]=$qtà;
    $_SESSION["prod$j"]=$row["pid"];
  }
}
  //Creazione sessioni
  $_SESSION["k"]=$prod;
  $_SESSION["tot"]=$totale;

  //Libero memoria risultato
  mysqli_free_result($result);

  //Chiusura connessione
  mysqli_close($con);

  //Stampa prezzo totale
  echo "</tbody><tfoot><tr><td></td><td></td><td><strong>PREZZO TOTALE</strong></td><td><strong>&euro;".number_format($totale/100, 2)."</strong></td></tr></tfoot></table>";
?>

      <div class="buttons">

<!-- Pulsante ANNULLA (JS) -->
  <script>
  function annulla(){
    window.location.replace("home.php");
  }
  </script>

  <input type="button" onclick="annulla()" value="ANNULLA">

<?php
  //Controllo denaro disponibile
  $ecc=0;
  if($totale>$money)
    $ecc=1;
  else
    echo "<input type=\"submit\" value=\"OK\">";
?>

      </div>
    </fieldset>
  </form>
  </div>

  <noscript id="noscript">
    <p class="centertext">Attenzione! Questa pagina utilizza JavaScript. Essendo disabilitato, alcune funzionalità potrebbero non essere disponibili.</p>
  </noscript>

<?php
//Totale superiore a disponibilità
if ($ecc==1)
  echo "<p id=\"text\">Attenzione! Il prezzo totale eccede la quantit&agrave; presente nel borsellino elettronico!</p>";
?>

<footer>
  <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
</footer>

</body>
</html>
