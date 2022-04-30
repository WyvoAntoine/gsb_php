<?php


if(!isset($_GET['action'])){
	$_GET['action'] = 'demandeConnexion';
}
$action = $_GET['action'];
switch($action){
	
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':{
		//$login = PdoGsb::hashMail($_POST['login']);
                $login = $_POST['login'];
		$mdp = $_POST['mdp'];
		$connexionOk = $pdo->checkUser($login,$mdp);
                if(isset($_COOKIE["cookieAccepter"]) || isset($_COOKIE["cookieRefuser"])){
		if(!$connexionOk){
			ajouterErreur("Login ou mot de passe incorrect");
			include("vues/v_erreurs.php");
			include("vues/v_connexion.php");
		}
		else { 
                        $infosMedecin = $pdo->donneLeMedecinByMail($login);
			$id = $infosMedecin['id'];
			$nom =  $infosMedecin['nom'];
			$prenom = $infosMedecin['prenom'];
			connecter($id,$nom,$prenom);
                        $connection = PdoGsb::ajouteConnexion($id);
                        if ($id == 1){                                
                            include("vues/admin/v_sommaire.php");                        
                        }else{
                            include("vues/user/v_sommaire_Utili.php");}
                }}
                else{
                    echo'<h3 style="color:red;">Veuillez accepter ou refuser les cookies avant de vous connecter !</h3>';
                    include("vues/v_connexion.php");
                }

			break;	
	}
        
        case 'demandeDeconnexion':{
            
            $id = $_SESSION['id'];
            
            $deco = PdoGsb::ajouteDeconnexion($id);
            $heure2= strtotime(date("h:i:s"),time());
            $heureCookie=strtotime($_COOKIE["CookieSession"]);
            $tempsRester=$heure2-$heureCookie;
            $message='<h3 style="color:red;">Vous êtes resté '.$tempsRester.' secondes sur notre site</h3>';
            echo $message;
            setcookie("CookieSession");
            
		include("vues/v_connexion.php");
		break;
	}
        
        case 'sommaire':{
        
            if($_SESSION['id']==1){
                include("vues/admin/v_sommaire.php");
            }
            else{
            include("vues/user/v_sommaire_Utili.php");}
            break;
        }
       
        
	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>