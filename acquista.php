<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | ACQUISTA</title>
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

  //Check sessione ordine
  if(isset($_SESSION["tot"]))
    unset($_SESSION["tot"]);
    ?>

  </div>

  <?php
  //Controllo login
  if(!isset($_SESSION["utente"]))
    die("<p class=\"centertext\">Attenzione! Questa pagina &egrave; accessibile solo agli utenti autenticati.</p><p class=\"centertext\"><a href=\"login.php\">Effettua il login</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");
  ?>

  <div id="acquista">
    <form method="post" action="conferma.php">
      <fieldset>
        <legend>Acquista</legend>
        <table id="table">
          <thead>
          <tr>
            <th>Prodotto</th>
            <th>Quantit&agrave; disponibile</th>
            <th>Prezzo unitario</th>
            <th>Quantit&agrave;</th>
          </tr>
        </thead>

  <?php
  //Query
  $vend=$_SESSION["utente"];
  $query= "SELECT * FROM prodotti WHERE vend<>'$vend' ORDER BY pid";
  $result = mysqli_query($con,$query);

  //Controllo query
  if (mysqli_num_rows($result)==0)
    die ("<p>Errore: query fallita!</p>".mysqli_error($con));

  //Estrazione dati e stampa
  $j=1;
  while($row = mysqli_fetch_assoc($result)){

  if($row["qty"]!=0){
    echo "<tr><td>".$row["nome"]."</td><td>".$row["qty"]."</td><td>&euro; ".number_format($row["prezzo"]/100, 2)."</td><td>";
    echo "<select name=\"qta$j\" class=\"sel\">";
  for ($i=0; $i<=$row["qty"]; $i++)
    echo "<option value=".$i." >".$i."</option>";
    echo "</select></td></tr>";
    }
  $j++;
}

  //Libero memoria risultato
  mysqli_free_result($result);

  //Chiusura connessione
  mysqli_close($con);
  ?>

      </table>

      <div class="buttons">
        <input class="buttons" type="reset" value="AZZERA">
        <input class="buttons" type="submit" value="PROCEDI">
      </div>

    </fieldset>
  </form>
  </div>

  <footer>
    <p><?php echo basename($_SERVER['PHP_SELF']);?> - &copy;2018 Walter De Carne</p>
  </footer>

  </body>
  </html>
