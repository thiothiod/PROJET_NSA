<?php

$serveur="localhost";
$user="root";
$pwd="";
$dbname="gestions_commande";
$connexion= mysqli_connect($serveur,$user,$pwd,$dbname);

if(!$connexion){
    echo "Erreur de connexion";
}else{
}
?>