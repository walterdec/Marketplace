<?php
session_start();

//Controllo login ed eventuale messaggio errore
if(!isset($_SESSION["utente"]))
  die("<!DOCTYPE html>
    <html lang=\"it\">
    <head>
      <meta charset=\"utf-8\">
      <title>C/V | LOGOUT</title>
      <meta name=\"author\" content=\"Walter De Carne\">
      <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
      <link rel=\"stylesheet\" href=\"stylesheet.css\" type=\"text/css\">
      <link rel=\"shortcut icon\" href=\"favicon.png\" type=\"image/x-icon\">
    </head>

  <body id=\"body\">

  <h1>COMPRO-VENDO</h1>

  <div id=\"borsellino\">
  <p>anonimo<br>&euro; 0</p>
  </div>

  <div id=\"menu\">
    <nav>
     <a href=\"home.php\">Home</a>
     <a href=\"login.php\">Login</a>
     <a href=\"info.php\">Info</a>
     <a href=\"cambia.php\">Cambia</a>
     <a href=\"acquista.php\">Acquista</a>
     <a>Logout</a>
    </nav>
  </div>

  <p class=\"centertext\">Attenzione! Hai gi&agrave; effettuato il logout!</p>
  <p class=\"centertext\"><a href=\"home.php\">Torna alla pagina HOME</a>

  <footer>
    <p>".basename($_SERVER['PHP_SELF'])." - &copy;2018 Walter De Carne</p>
  </footer>");

//Distruzione sessione e reindirizzamento
if(isset($_SESSION["utente"])){
  session_destroy();
  header('Location: login.php');
}

?>
