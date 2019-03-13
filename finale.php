<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | FINALE</title>
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
  $con = mysqli_connect('localhost', 'uStrong', 'SuperPippo!!!', 'comprovendo');

  //Controllo connessione DB
  if (!$con) {
      die("Connection failed: " .mysqli_connect_error());
  }
  ?>

    <?php
    if(!isset($_SESSION["utente"]))
      die("<p class=\"centertext\">Attenzione! Questa pagina &egrave; accessibile solo agli utenti autenticati.</p><p class=\"centertext\"><a href=\"login.php\">Effettua il login</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");
    if(!isset($_SESSION["tot"]))
      die("<p class=\"centertext\">Attenzione! Per accedere a questa pagina devi aver confermato un ordine.</p><p class=\"centertext\"><a href=\"acquista.php\">Torna alla pagina ACQUISTA</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

$ses=$_SESSION["utente"];
//Controllo sessioni, ciclo for e query update
  if (isset($_SESSION["k"])){
    $k=$_SESSION["k"];


    for($j=1; $j<=$k; $j++){
      if (isset($_SESSION["parz$j"]) && isset($_SESSION["vend$j"])){
        $parz=$_SESSION["parz$j"];
        $vend=$_SESSION["vend$j"];

        //Controllo che il prezzo non superi la disponibilità
        if(isset($_SESSION["utente"])){
          $ses=$_SESSION["utente"];
          $query= "SELECT * FROM utenti WHERE nick = '$ses'";
          $res=mysqli_query($con, $query);
          if(!$res)
            die("Errore query: ".mysqli_error());
          $row = mysqli_fetch_assoc($res);
          if ($row["money"]<$parz)
            die ("<p class=\"centertext\">Attenzione! La quantit&agrave; di denaro presente nel borsellino non &egrave; sufficiente per portare a termine l'acquisto!</p><p class=\"centertext\"><a href=\"acquista.php\">Torna alla pagina ACQUISTA</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");
          //Libero memoria risultato
          mysqli_free_result($res);
        }

        //Unset sessioni
        unset($_SESSION["parz$j"]);
        unset($_SESSION["vend$j"]);

        //Query #1 (accredito denaro al venditore)
        $query="UPDATE utenti SET money=money+$parz WHERE nick='$vend'";
        $result=mysqli_query($con, $query);
        if (!$result)
        die ("Errore query: ".mysqli_error());

        //Query #2 (addebito denaro all'acquirente)
        $query="UPDATE utenti SET money=money-$parz WHERE nick='$ses'";
        $result=mysqli_query($con, $query);
        if (!$result)
        die ("Errore query: ".mysqli_error());

        if (isset($_SESSION["quantita$j"])){
          $qtà=$_SESSION["quantita$j"];
          $id=$_SESSION["prod$j"];
          unset($_SESSION["quantita$j"]);
          $query="UPDATE prodotti SET qty=qty-$qtà WHERE pid='$id'";
          $result=mysqli_query($con, $query);
          if (!$result)
            die ("Errore query: ".mysqli_error());
      }
    }
  }
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

    <h4>Ordine confermato</h4>
    <p>Il suo ordine &egrave; andato a buon fine! Le abbiamo addebitato sul suo borsellino elettronico un importo totale pari a <strong>&euro;<?php echo number_format($_SESSION["tot"]/100,2); ?></strong>.</p>
    <p>Di seguito gli articoli acquistati:</p>
    <table id="table">
      <thead>
        <tr>
        <th>Prodotto</th>
        <th>Quantit&agrave;</th>
        <th>Venditore</th>
        <th>Prezzo unitario</th>
        <th>Prezzo parziale</th>
      </tr>
      </thead>
      <tbody>
    <?php
    unset($_SESSION["tot"]);

    //Query e stampa riepilogo
    for($j=1; $j<=$k; $j++){
      if (isset($_SESSION["prod$j"]) && isset($_SESSION["qta$j"])) {
        $prodotto=$_SESSION["prod$j"];
        $q=$_SESSION["qta$j"];
        $sql="SELECT nome, prezzo, vend FROM prodotti WHERE pid='$prodotto'";
        $res=mysqli_query($con, $sql);
        $row=mysqli_fetch_assoc($res);

        //Controllo query
        if (!$res)
          die("Errore query: ".mysqli_error());

        //Stampa risultati
        $pri=number_format($row["prezzo"]/100, 2);
        $parziale=$q*$pri;
        echo "<tr><td>".$row["nome"]."</td><td>".$q."</td><td>".$row["vend"]."</td><td>&euro; ".$pri."</td><td>&euro; ".number_format($parziale, 2)."</td></tr>";

        //Unset sessioni
        unset($_SESSION["prod$j"]);
        unset($_SESSION["qta$j"]);

        //Libero memoria risultato
        mysqli_free_result($res);
  }
}

//Chiusura connessione
mysqli_close($con);
?>
      </tbody>
    </table>

<noscript id="noscript">
  <p class="centertext">Attenzione! Questa pagina utilizza JavaScript. Essendo disabilitato, alcune funzionalità potrebbero non essere disponibili.</p>
</noscript>

<!-- Pulsante STAMPA -->
<script>
function stampa(){
    window.print();
}
</script>

<div class="pad">
  <button class="printbutton" id="printbutton" onclick="print()">STAMPA CONFERMA</button>
</div>

  <footer>
    <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
  </footer>

</body>
</html>
