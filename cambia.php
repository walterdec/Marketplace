<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <title>C/V | CAMBIA</title>
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
  //Controllo autenticazione utente
  if(!isset($_SESSION["utente"]))
    die("<p class=\"centertext\">Attenzione! Questa pagina &egrave; accessibile solo agli utenti autenticati.</p><p class=\"centertext\"><a href=\"login.php\">Effettua il login</a></p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

  //Query
  $sql= "SELECT * FROM prodotti WHERE vend='$ses' ORDER BY pid";
  $result= mysqli_query($con, $sql);
  if(!$result)
    die("Errore query: ".mysqli_error());
  $prod=mysqli_num_rows($result);

  //Controllo presenza almeno 1 prodotto
  if($prod>0){
  ?>

  <div class="cambia">
  <form method="post" action="cambia.php">
    <fieldset>
      <legend>Modifica quantit&agrave;</legend>
      <table id="table">
        <tr>
          <th>Prodotto</th>
          <th>Quantit&agrave; attuale</th>
          <th>Prezzo</th>
          <th>Nuova quantit&agrave;</th>
        </tr>

  <?php
  }
  //Stampa dati
  $i=1;
  while($row = mysqli_fetch_assoc($result)){
    echo "<tr><td>".$row["nome"]."</td><td>".$row["qty"]."</td><td>&euro; ".number_format($row["prezzo"]/100, 2)."</td><td><input type=\"number\" min=\"0\" name=\"qta$i\"></td></tr>";
    $i++;
  }

  //Controllo presenza almeno 1 prodotto
  if($prod>0){
  ?>

    </table>

    <div class="button">
      <input type="submit" value="AGGIORNA">
    </div>

    </fieldset>
  </form>
  </div>

  <?php
}

//Libero memoria risultato
mysqli_free_result($result);

   ?>

<!-- Controllo formato corretto prezzo lato client (JS)-->
  <script>
  function checkprice(){
    var price = document.getElementById("costo").value;
    var regexcosto= /^[0-9]{1,}\.[0-9]{2}$/;

    if(!price.match(regexcosto)){
      document.getElementById("costo").style.borderColor='red';
      document.getElementById("costo").style.borderWidth = 'medium';
    }
    else if(price.match(regexcosto) != null && price.match(regexcosto).length == 1){
        document.getElementById("costo").style.borderColor='#4EB1BA';
        document.getElementById("costo").style.borderWidth = 'medium';
      }
  }
  </script>

  <div class="cambia">
  <form method="post" action="cambia.php">
      <fieldset>
        <legend>Nuovo prodotto</legend>
        <div id="formins">
          <div class="cambiainp">
            <label>Nome</label>
            <input type="text" name="nome" autocomplete="off" required>
          </div>
          <div class="cambiainp">
            <label>Prezzo</label>
            <input type="text" name="costo" id="costo" autocomplete="off" placeholder="Esempio: 2.00, 14.99" oninput="checkprice()" required> &euro;
          </div>
          <div class="cambiainp">
            <label>Quantit&agrave;</label>
            <input type="number" min="0" class="qta" name="quantita" autocomplete="off" required>
          </div>

        </div>

        <div class="button">
          <input type="submit" value="INSERISCI">
        </div>

      </fieldset>
    </form>
  </div>

  <noscript id="noscript">
    <p class="centertext">Attenzione! Questa pagina utilizza JavaScript. Essendo disabilitato, alcune funzionalità potrebbero non essere disponibili.</p>
  </noscript>

<div id="errorecambia">
<?php
//Prima sezione

$flag=0;
//Controllo che i valori siano numerici (o nulli) e inserisco nel vettore associativo
 for($i=1; $i<=$prod; $i++){
   if(isset($_REQUEST["qta$i"])){
     $flag=1;
     if ((is_numeric($_REQUEST["qta$i"]) && $_REQUEST["qta$i"]>=0) || $_REQUEST["qta$i"] == '')
        $quantità[$i]=$_REQUEST["qta$i"];
  }
}

//Query cambio quantità
$sql= "SELECT pid FROM prodotti WHERE vend='$ses' ORDER BY pid";
$result= mysqli_query($con, $sql);
if(!$result)
  die ("<p>Query fallita: ".mysqli_error($con)."</p>");

  /* Uso query parametrizzata poiché anche se vi è un controllo fatto dal browser sull'input (non posso inserire nulla se non un numero) questo
  controllo può essere aggirato usando un browser che non supporta l'input type=number e che quindi legge l'input semplicemente come type=text */

  if($flag==1){
    foreach ($quantità as $i => $qtà){
        $row = mysqli_fetch_assoc($result);
        $id=$row["pid"];
        if($qtà!=''){
          $stmt=mysqli_prepare($con, "UPDATE prodotti SET qty=? WHERE pid=?");
          mysqli_stmt_bind_param($stmt, "ss", $qtà, $id);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);

            if (mysqli_stmt_errno())
              echo("Errore query: ".mysqli_stmt_error());
            else{
                  $_SESSION["successqta"]=1;
                  header("Location: success.php");
                }
            }
        }
      }

     //Libero memoria risultato
     mysqli_free_result($result);

//Seconda sezione
if (isset($_REQUEST["nome"]) && isset($_REQUEST["costo"]) && isset($_REQUEST["quantita"])){
  $nome=$_REQUEST["nome"];
  $costo=$_REQUEST["costo"];
  $quantità=$_REQUEST["quantita"];

  //Controllo presenza e validità dei dati
  if($nome=="" || $costo=="" || $quantità=="")
    die("<p>Errore inserimento: dati mancanti.</p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

  if($costo==0)
    die ("<p>Errore inserimento: costo inserito non valido.</p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

  if($quantità<0)
    die("<p>Errore inserimento: quantit&agrave; inserita non valida.</p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

  $regexcosto= '/^[0-9]{1,}\.[0-9]{2}$/';
  if (!preg_match($regexcosto, $costo))
    die("<p>Attenzione! Inserire il prezzo nel formato corretto (due cifre decimale, punto come separatore).</p><footer><p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p></footer>");

  //Query parametrizzata inserimento prodotto
  $costomod=$costo*100;
  $stmt=mysqli_prepare($con, "INSERT INTO prodotti (nome, qty, prezzo, vend) VALUES (?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "ssss", $nome, $quantità, $costomod, $ses);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);

  if (mysqli_stmt_errno()) {
    echo("Errore query: ".mysqli_stmt_error());}
    else{
        $_SESSION["successins"]=1;
        header("Location: success.php");
      }
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
