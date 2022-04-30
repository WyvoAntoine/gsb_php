<?php
//on insère le fichier qui contient les fonctions
require_once ("../include/class.pdogsb.inc.php");

//appel de la fonction qui permet de se connecter à la base de données
$lePdo = PdoGsb::getPdoGsb();

$dateNaiss="2000";
$crea="2021-10-10";

var_dump(PdoGsb::AjouteArchigeMedecin($dateNaiss, $crea));