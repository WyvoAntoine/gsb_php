<?php
$action = $_GET['action'];
switch($action){
	
	
    case 'parametreCompte':{
        if ($_SESSION['id']==1){
            include("vues/admin/v_parametreCompte.php");
        }
        else{
            include("vues/user/v_parametreCompte_Utili.php");
        }
        break;
    }
    
    case 'demandeModification':{
            if ($_SESSION['id'] == 1){
            include("vues/admin/v_modificationCompte.php");            
            }else{
                include("vues/user/v_modificationCompte_Utili.php"); 
            }
		break;
}

    case 'demandeSuppression':{
        include('vues/user/v_supprimerCompte_Utili.php');
        break;
    }
    
    case 'confirmSupDef':{
        include("vues/user/v_confirmSupprimerCompte_Utili.php");
        break;
    }
    
    case 'supprimerDef':{
        $pdo= PdoGsb::getPdoGsb();
        $id=$_SESSION['id'];
        PdoGsb::SupprimerCompte($id);
        include 'vues/v_connexion.php';
        echo'Votre compte a été supprimé !';
        break;
    }
    
    case 'confirmArchivage':{
        include ("vues/user/v_confirmArchivageCompte_Utili.php");
        break;
    }
    
    case 'archivageOk':{
        $pdo= PdoGsb::getPdoGsb();
        $id=$_SESSION['id'];        
        $dateDebutLog;
        $dateFinLog;
        
       
        $tabM= PdoGsb::RecupMedecinArchiver($id);
        $tabV= PdoGsb::RecupVisioArchiver($id);
        $tabH= PdoGsb::RecupHistoriqueArchiver($id);
        $tabP= PdoGsb::RecupProduitArchiver($id);
        
        
        $dateNaiss=$tabM['dateNaissance'];
        $dateCrea=$tabM['dateCreation'];
            
         $newIdMedecin= PdoGsb::AjouteArchivageMedecin($dateNaiss, $dateCrea);
        
            foreach ($tabH as $medecin){
                $dateDebutLog=$medecin['dateDebutLog'];
                $dateFinLog=$medecin['dateFinLog'];
                PdoGsb::AjouteArchivageHistorique($newIdMedecin,$dateDebutLog, $dateFinLog);
        }
        
            foreach ($tabV as $medecin){
                $idVisio=$medecin['idVisio'];
                $dateInscri=$medecin['dateInscription'];
                PdoGsb::AjouteArchivageVisio($newIdMedecin,$idVisio, $dateInscri);
        }
            foreach ($tabP as $medecin){
                $idProd=$medecin['idProduit'];
                $date=$medecin['Date'];
                $heure=$medecin['Heure'];
                PdoGsb::AjouteArchivageMedecinProduit($newIdMedecin, $idProd, $date, $heure);
        }

        PdoGsb::SupprimerCompte($id);
        include 'vues/v_connexion.php';
        echo'Votre compte a été supprimé !';
        
        break;
    }
    
    case 'coockie':{
        setcookie('cookieAccepter','true',time()+15778800);
        include('vues/v_connexion.php');
        $_SESSION['$showcookie'] = false;
        
        break;
    }
    
    case 'coockieRefuser':{
        setcookie('cookieRefuser','false');
        include('vues/v_connexion.php');
        $_SESSION['$showcookie'] = false;
        
        break;
    }
    
     case 'json':{
    
        $nom;
        $prenom;
        $mail;
        $dateNaissance;
        $dateConsentement;
        $dateCreation;
        $rpps;
        $tab= PdoGsb::RecupMedecin($_SESSION['id']);
        foreach($tab as $info){
            $nom=$info['nom'];
            $prenom=$info['prenom'];
            $rpps=$info['rpps'];
            $dateConsentement=$info['dateConsentement'];
            $dateCreation=$info['dateCreation'];
            $mail=$info['mail'];
            $dateNaissance=$info['dateNaissance'];
            
        }
         //Suppression d'un fichier
        if (file_exists("css/Mesinfos.odt")){
            unlink("css/Mesinfos.odt");
            PdoGsb::toJson($nom, $prenom, $dateConsentement, $dateCreation, $rpps, $mail, $dateNaissance);
            include('vues/user/download.php');
        }else{
        PdoGsb::toJson($nom, $prenom, $dateConsentement, $dateCreation, $rpps, $mail, $dateNaissance);
        include('vues/user/download.php');
        //include('vues/user/v_parametreCompte_Utili.php');
        
        }
         
        break;
    }
      
        
    case 'modification':{
            $nom=$_POST['nom'];
            $prenom=$_POST['prenom'];
            $id=$_SESSION['id'];
            $mdp= htmlspecialchars($_POST['Modifmdp1']);
            $mdp2= htmlspecialchars($_POST['Modifmdp2']);
            $pdo= PdoGsb::getPdoGsb();
            $rempli=false;            
            if (empty($mdp)==true && empty($mdp2)==true){
                $modifSansMdp=$pdo->modifNomPrenom($nom,$prenom,$id);
                $message="<h3 class='text-center'>Modification effectuée !</h3>";
                echo $message;
                        
            $rempli=false; }
            else{
                if(verif2Strings($mdp, $mdp2)==true){
                    if(verifSecuPWP($mdp)){
                        $modifAvecMdp=$pdo->modifNomPrenomMdp($nom,$prenom,$mdp,$id);
                        $message="<h3 class='text-center'>Modification effectuée !</h3>";
                        echo $message;
                    }

                }
                else{
                    echo 'Les mots de passes ne sont pas similaires !';
                }
            }
        
                
            
            
                        break;
                        
   
                        
              
     
}}
        ?>