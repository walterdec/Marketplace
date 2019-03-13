<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | HOME</title>
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

    //Chiusura connessione
    mysqli_close($con);

  ?>

  </div>

  <div id="home">
    <p>Ciao<?php if(isset($_SESSION["utente"])) echo " <strong>".$_SESSION["utente"]."</strong>";?>, benvenuto/a nel marketplace <strong>COMPRO-VENDO</strong>!</p>
    <?php if(!isset($_SESSION["utente"])) echo "<p>Non hai ancora effettuato il login! <a href=\"login.php\">Clicca qui</a> ed inserisci le tue credenziali.</p><p>Puoi navigare anche senza aver effettuato il login, ma avrai accesso solo alle pagine HOME e <a href=\"info.php\">INFO</a>.</p>"; else echo"<p>Hai gi&agrave; effettuato il login! Puoi cominciare a usufruire di tutte le funzioni del marketplace. Usa il menu per spostarti da una pagina all'altra.</p>";?>
    <details open>
      <summary><em>Funzionalità delle pagine</em></summary>
        <ul class="lista">
          <li><p>La pagina HOME, quella aperta in questo momento, fornisce solo informazioni generali sul marketplace.</p></li>
          <li><p>La pagina <a href="info.php">INFO</a> fornisce informazioni sui prodotti in vendita sul marketplace. &Egrave; disponibile in versione ridotta (nome prodotto e quantit&agrave; disponibile) per gli utenti anonimi e in versione completa (nome, quantit&agrave;, venditore, prezzo) per gli utenti autenticati.</p></li>
          <li><p>La pagina <a href="cambia.php">CAMBIA</a> contiene due sezioni, la prima per modificare la quantit&agrave; di articoli da te messi in vendita (se presenti), l'altra per aggiungerne nuovi, specificando il nome, il prezzo e la quantit&agrave;.</p></li>
          <li><p>La pagina <a href="acquista.php">ACQUISTA</a> contiene un'unica sezione che ti consente di acquistare prodotti da altri venditori, selezionandone la quantit&agrave; che si desidera.</p></li>
        </ul>
    </details>
    <p>Se effettui un acquisto, l'importo complessivo dell'ordine ti sar&agrave; addebitato sul tuo borsellino elettronico. Puoi consultare l'importo residuo in ogni pagina del marketplace, nel riquadro in alto a destra.</p>
    <p>Una volta terminate le operazioni, puoi effettuare il logout usando l'apposito tasto nel menu.</p>
    <p class="centertext"><strong>Buona spesa (e buona vendita)!!</strong></p>
  </div>

  <footer>
    <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
  </footer>

</body>
</html>
