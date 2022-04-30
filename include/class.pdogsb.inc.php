<?php

/** 
 * Classe d'accÃ¨s aux donnÃ©es. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsbextranet';   		
      	private static $user='root' ;    		
      	private static $mdp='' ;	
	private static $monPdo;
	private static $monPdoGsb=null;
		
/**
 * Constructeur privÃ©, crÃ©e l'instance de PDO qui sera sollicitÃ©e
 * pour toutes les mÃ©thodes de la classe
 */				
	private function __construct(){
          PdoGsb::$monPdo = null;
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui crÃ©e l'unique instance de la classe
 
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;  
	}
/**
 * vÃ©rifie si le login et le mot de passe sont corrects
 * renvoie true si les 2 sont corrects
 * @param type $lePDO
 * @param type $login
 * @param type $pwd
 * @return bool
 * @throws Exception
 */
function checkUser($login,$pwd):bool {
   
    $user=false;
    $pdo = PdoGsb::$monPdo;
    $monObjPdoStatement=$pdo->prepare("SELECT motDePasse FROM medecin WHERE mail= :login AND token IS NULL");
    $bvc1=$monObjPdoStatement->bindValue(':login',$login,PDO::PARAM_STR);
    if ($monObjPdoStatement->execute()) {
        $unUser=$monObjPdoStatement->fetch();
        if (is_array($unUser)){
           if (count($unUser)!=1){
                if (checkPWD($pwd, $unUser['motDePasse']))
                    $user=true;
        }
    }
    else
        throw new Exception("erreur dans la requÃªte");
return $user;   
}}


	

function donneLeMedecinByMail($login) {
    
    $pdo = PdoGsb::$monPdo;
    $monObjPdoStatement=$pdo->prepare("SELECT id, nom, prenom,mail FROM medecin WHERE mail= :login");
    $bvc1=$monObjPdoStatement->bindValue(':login',$login,PDO::PARAM_STR);
    if ($monObjPdoStatement->execute()) {
        $unUser=$monObjPdoStatement->fetch();
       
    }
    else
        throw new Exception("erreur dans la requÃªte");
return $unUser;   
}

//Hasher le mail
/*public static function recupKey(){
    
    $fichier = file("css/Oui-Oui.txt");
    $total=count($fichier);
    for($i=0; $i<$total; $i++){
        $key=$fichier[$i];
    }
    return $key;
    
}

public static function recupNonce(){
    $fichier=file("css/Oui-Oui-Nounce.txt");
    $total=count($fichier);
    for($i=0; $i<$total; $i++){
        $nonce=$fichier[$i];
    }
    return $nonce;
}

public static function hashMail($mail){
    $key= PdoGsb::recupKey();
    $nonce= PdoGsb::recupNonce();
    $encrypted = sodium_crypto_secretbox($mail, $nonce, $key);
    return $encrypted;
}*/


public function tailleChampsMail(){
    

    
     $pdoStatement = PdoGsb::$monPdo->prepare("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = 'medecin' AND COLUMN_NAME = 'mail'");
    $execution = $pdoStatement->execute();
$leResultat = $pdoStatement->fetch();
      
      return $leResultat[0];
    
       
       
}

public function tailleChampsNom(){
    

    
     $pdoStatement = PdoGsb::$monPdo->prepare("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = 'visioconference' AND COLUMN_NAME = 'nomVisio'");
    $execution = $pdoStatement->execute();
$leResultat = $pdoStatement->fetch();
      
      return $leResultat[0];
    
       
       
}

public function tailleChampsObjectif(){
    

    
     $pdoStatement = PdoGsb::$monPdo->prepare("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = 'visioconference' AND COLUMN_NAME = 'objectif'");
    $execution = $pdoStatement->execute();
$leResultat = $pdoStatement->fetch();
      
      return $leResultat[0];
    
       
       
}

public function tailleChampsUrl(){
    

    
     $pdoStatement = PdoGsb::$monPdo->prepare("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = 'visioconference' AND COLUMN_NAME = 'url'");
    $execution = $pdoStatement->execute();
$leResultat = $pdoStatement->fetch();
      
      return $leResultat[0];
    
       
       
}

public function creeMedecin($nom,$prenom,$email, $mdp)
{
    $motdepasse= hashPWD($mdp);
    //$email = self::hashMail($email);
    $pdoStatement = PdoGsb::$monPdo->prepare("INSERT INTO medecin(id,nom,prenom,mail,motDePasse,dateCreation,dateConsentement) "
            . "VALUES (null, :leNom,:lePrenom ,:leMail, :leMdp, now(),now())");
    $bv1 = $pdoStatement->bindValue(':leMail', $email); 
    $bv2 = $pdoStatement->bindValue(':leMdp', $motdepasse);
    $bv3 = $pdoStatement->bindValue(':leNom', $nom);
    $bv4 = $pdoStatement->bindValue(':lePrenom', $prenom);
    $execution = $pdoStatement->execute();
    return $execution;
    
}


function testMail($email){
    $pdo = PdoGsb::$monPdo;
    $pdoStatement = $pdo->prepare("SELECT count(*) as nbMail FROM medecin WHERE mail = :leMail");
    $bv1 = $pdoStatement->bindValue(':leMail', $email);
    $execution = $pdoStatement->execute();
    $resultatRequete = $pdoStatement->fetch();
    if ($resultatRequete['nbMail']==0)
        $mailTrouve = false;
    else
        $mailTrouve=true;
    
    return $mailTrouve;
}

public static function modifNomPrenom($nom,$prenom,$id){
    $pdo= PdoGsb::$monPdo;
    $pdoStatement = $pdo->prepare("UPDATE medecin SET nom=:leNom, prenom=:lePrenom WHERE id=:id");
    $bv1=$pdoStatement->bindValue(":leNom",$nom);
    $bv2=$pdoStatement->bindValue(":lePrenom",$prenom);
     $bv3=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
    $resultatRequete=$pdoStatement->fetch();
    return $executionOk;
    
}

public static function modifNomPrenomMdp($nom,$prenom,$mdp,$id){
    $mdp=hashPWD($mdp);
    $pdo= PdoGsb::$monPdo;
    $pdoStatement = $pdo->prepare("UPDATE medecin SET nom=:leNom, prenom=:lePrenom,motDePasse=:leMdp WHERE id=:id");
    $bv1=$pdoStatement->bindValue(":leNom",$nom);
    $bv2=$pdoStatement->bindValue(":lePrenom",$prenom);
    $bv3=$pdoStatement->bindValue(":leMdp",$mdp);
    $bv4=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
    $resultatRequete=$pdoStatement->fetch();
    return $executionOk;
   
}




function connexionInitiale($mail){
     $pdo = PdoGsb::$monPdo;
     //$mail = self::hashMail($mail);
    $medecin= $this->donneLeMedecinByMail($mail);
    $id = $medecin['id'];
    $this->ajouteConnexionInitiale($id);
    
}

function ajouteConnexionInitiale($id){
    $pdoStatement = PdoGsb::$monPdo->prepare("INSERT INTO historiqueconnexion "
            . "VALUES (:leMedecin, now(), now())");
    $bv1 = $pdoStatement->bindValue(':leMedecin', $id);
    $execution = $pdoStatement->execute();
    return $execution;
    
}

public static function ajouteConnexion($id){
    $pdoStatement = PdoGsb::$monPdo->prepare("INSERT INTO historiqueconnexion(idMedecin ,dateDebutLog,dateFinLog) VALUES (?, now(), null)");    
    $bv2 = $pdoStatement->bindValue(1, $id);
    $execution = $pdoStatement->execute();
    return $execution;
    
}

public static function ajouteDeconnexion($id){
    $pdoStatement = PdoGsb::$monPdo->prepare("UPDATE historiqueconnexion SET dateFinLog = now() WHERE dateFinLog IS NULL AND idMedecin = ?");    
    $bv2 = $pdoStatement->bindValue(1, $id);
    $execution = $pdoStatement->execute();
    return $execution;
    
}

function donneinfosmedecin($id){
  
       $pdo = PdoGsb::$monPdo;
           $monObjPdoStatement=$pdo->prepare("SELECT id,nom,prenom FROM medecin WHERE id= :lId");
    $bvc1=$monObjPdoStatement->bindValue(':lId',$id,PDO::PARAM_INT);
    if ($monObjPdoStatement->execute()) {
        $unUser=$monObjPdoStatement->fetch();
   
    }
    else
        throw new Exception("erreur");
           
    
}

public function modifMail($nouveaumail){
    $id=$_POST['idCache'];
    $pdoStatement = PdoGsb::$monPdo->prepare("UPDATE medecin SET mail=':leMail' WHERE id=$id"); 
    $bv1 = $pdoStatement->bindValue(':leMail', $nouveaumail);
    $execution = $pdoStatement->execute();
    return $execution;
}

public static function listeVisio(){
    $pdo = PdoGsb::$monPdo;
    
    $sql=("SELECT * FROM visioconference");
    $requete=$pdo->prepare($sql);
    $executionOk=$requete->execute();
    $tab=$requete->fetchAll();
    $requete->closeCursor();
    return $tab;
//    while($tab=$requete->fetch(PDO::FETCH_ASSOC)){
//        echo($tab['nomVisio'])." |";
//        echo($tab['objectif'])." |";
//        echo($tab['url'])." |";
//        echo($tab['dateVisio']),'<br/>'.'<br/>';
//        echo("-----------------------------------------------------------------"
//                . "------------------------------------------------------------"
//                . "----------------------").'<br/>'.'<br/>';
//    }
}

public static function listeVisio2($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql=("SELECT * FROM visioconference WHERE id=:lID");
    $requete=$pdo->prepare($sql);
    $bv1=$requete->bindValue(':lID',$id);
    $executionOk=$requete->execute();
    $tab=$requete->fetchAll();
    $requete->closeCursor();
    return $tab;
//    while($tab=$requete->fetch(PDO::FETCH_ASSOC)){
//        echo($tab['nomVisio'])." |";
//        echo($tab['objectif'])." |";
//        echo($tab['url'])." |";
//        echo($tab['dateVisio']),'<br/>'.'<br/>';
//        echo("-----------------------------------------------------------------"
//                . "------------------------------------------------------------"
//                . "----------------------").'<br/>'.'<br/>';
//    }
}

public static function etatServeur(){
    $pdo = PdoGsb::$monPdo;
    $co;
    $sql = 'SELECT * FROM maintenance';
    $requete = $pdo->prepare($sql);
    $executionOk = $requete->execute();
    
    while ($tab = $requete->fetch(PDO::FETCH_ASSOC)){   
        if ($tab['Etat'] == 1){
        $co = 1;
    }else if ($tab['Etat'] == 0){
        $co = 0;
    }
    }
    
    return $co;
    
}

public static function ajouteVisio($nom,$objectif,$url,$date) {
    
    $pdo = PdoGsb::$monPdo;
    //var_dump($pdo);
    $requete = $pdo->prepare("INSERT INTO visioconference(id,nomVisio,objectif,url,dateVisio) VALUES(null, :leNom, :lObjectif,:lUrl,:laDate)");
    $bv1 = $requete->bindValue(':leNom',$nom,PDO::PARAM_STR);
    $bv2 = $requete->bindValue(':lObjectif', $objectif,PDO::PARAM_STR);
    $bv3 = $requete->bindValue(':lUrl', $url,PDO::PARAM_STR);
    $bv3 = $requete->bindValue(':laDate', $date);
    $executionok = $requete->execute();
    return $executionok;



}
public static function modifVisio($nom,$objectif,$url,$date,$id){
    $pdo= PdoGsb::$monPdo;
    $pdoStatement = $pdo->prepare("UPDATE visioconference SET nomVisio=:leNom , objectif=:lObjectif , url=:lUrl , dateVisio=:laDate WHERE id=:id");
    $bv1=$pdoStatement->bindValue(":leNom",$nom, PDO::PARAM_STR);
    $bv2=$pdoStatement->bindValue(":lObjectif",$objectif, PDO::PARAM_STR);
    $bv3=$pdoStatement->bindValue(":lUrl",$url, PDO::PARAM_STR);
    $bv4=$pdoStatement->bindValue(":laDate",$date,PDO::PARAM_STR);
    $bv5=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
    $resultatRequete=$pdoStatement->fetch();
   return $executionOk;
}

public static function supprimerVisio($id){
    $pdo= PdoGsb::$monPdo;
    $pdoStatement = $pdo->prepare("DELETE FROM visioconference WHERE id=:id");
    $bv1=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
return $executionOk;
}

public static function modifMaintenance($m){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "UPDATE maintenance SET Etat = ?";
    
    $rs_modif = $pdo->prepare($sql);

    $rs_modif->bindValue(1,$m);
    
    $executionOk= $rs_modif->execute();
 return $executionOk;
}

public static function listeProduit(){
    $pdo = PdoGsb::$monPdo;
    
    $sql = 'SELECT * FROM produit';
    $requete = $pdo->prepare($sql);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetchAll();
    $requete->closeCursor();
    
    
    
//    while ($tab = $requete->fetch(PDO::FETCH_ASSOC)){   
//        echo $tab['nom'].', ';
//        echo $tab['objectif'].', ';
//        echo $tab['information'].', ';
//        echo $tab['effetIndesirable'],'<br>','<br>';
    
    return $tab;
    
    
}

public static function listeProduitV2($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "SELECT * FROM produit WHERE id = '{$id}'";
    $requete = $pdo->prepare($sql);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetchAll();
    $requete->closeCursor();
    
    
    
//    while ($tab = $requete->fetch(PDO::FETCH_ASSOC)){   
//        echo $tab['nom'].', ';
//        echo $tab['objectif'].', ';
//        echo $tab['information'].', ';
//        echo $tab['effetIndesirable'],'<br>','<br>';
    
    return $tab;
    
    
}
public static function modifProduit($id,$nom,$objectif,$info,$effet){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "UPDATE produit SET nom = ?, objectif = ?, information = ?, effetIndesirable = ? WHERE Id  = '{$id}'";
    
    $rs_modif = $pdo->prepare($sql);

    $rs_modif->bindValue(1,$nom,PDO::PARAM_STR);
    $rs_modif->bindValue(2,$objectif,PDO::PARAM_STR);
    $rs_modif->bindValue(3,$info,PDO::PARAM_STR);
    $rs_modif->bindValue(4,$effet,PDO::PARAM_STR);
    
    $executionOk= $rs_modif->execute();
 return $executionOk;
}

public static function suppProduit($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "DELETE FROM produit WHERE Id  = '{$id}'";    
    $rs_modif = $pdo->prepare($sql);
    $executionOk= $rs_modif->execute();
 return $executionOk;
}

public static function AjouteProduit($nom,$obj,$info,$effet){
    $pdo = PdoGsb::$monPdo;
    
    $requete = $pdo->prepare("INSERT INTO produit(nom,objectif,information,effetIndesirable) VALUES (?,?,?,?)");
    
    $bv1 = $requete->bindValue(1,$nom,PDO::PARAM_STR);
    $bv2 = $requete->bindValue(2,$obj,PDO::PARAM_STR);
    $bv3 = $requete->bindValue(3,$info,PDO::PARAM_STR);
    $bv4 = $requete->bindValue(4,$effet,PDO::PARAM_STR);
    $executionok = $requete->execute();
 return $executionok;
}

public static function SupprimerCompte($id){
    $pdo= PdoGsb::$monPdo;
    $pdoStatement = $pdo->prepare("DELETE FROM historiqueconnexion WHERE idMedecin=:id");
    $bv1=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
    
    $pdoStatement = $pdo->prepare("DELETE FROM medecinvisio WHERE idMedecin=:id");
    $bv2=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
    
    $pdoStatement = $pdo->prepare("DELETE FROM medecinproduit WHERE idMedecin=:id");
    $bv3=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();
    
    $pdoStatement = $pdo->prepare("DELETE FROM medecin WHERE id=:id");
    $bv4=$pdoStatement->bindValue(":id",$id);
    $executionOk=$pdoStatement->execute();

    return $executionOk;
}

public static function ArchiverCompte($id){
    $pdo= PdoGsb::$monPdo;
    
}

public static function RecupMedecinArchiver($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "SELECT dateNaissance,dateCreation FROM medecin WHERE id = :lId";
    $requete = $pdo->prepare($sql);
    $bv1=$requete->bindValue(':lId',$id);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetch();
    $requete->closeCursor();
    
    return $tab;
}

public static function RecupProduitArchiver($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "SELECT idProduit,Date,Heure FROM medecinproduit WHERE idMedecin = :lId";
    $requete = $pdo->prepare($sql);
    $bv1=$requete->bindValue(':lId',$id);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetchAll();
    $requete->closeCursor();
    
    return $tab;
}

public static function RecupVisioArchiver($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "SELECT idVisio,dateInscription FROM medecinvisio WHERE idMedecin = :lId";
    $requete = $pdo->prepare($sql);
    $bv1=$requete->bindValue(':lId',$id);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetchAll();
    $requete->closeCursor();
    
    return $tab;
}

public static function RecupHistoriqueArchiver($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "SELECT dateDebutLog,dateFinLog FROM historiqueconnexion WHERE idMedecin = :lId";
    $requete = $pdo->prepare($sql);
    $bv1=$requete->bindValue(':lId',$id);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetchAll();
    $requete->closeCursor();
    
    return $tab;
}

public static function AjouteArchivageMedecin($dateNaiss,$crea){
    $pdo= PdoGsb::$monPdo;
    
    $sql="INSERT INTO archivagemedecin VALUES(null, :lAnneeNaiss, :laDateCreaCompte)";
    $requete=$pdo->prepare($sql);
    
    $bv1=$requete->bindValue(':lAnneeNaiss',$dateNaiss);
    $bv2=$requete->bindValue(':laDateCreaCompte',$crea);
    $executionok = $requete->execute();
     $id=$pdo->lastInsertId();
    return $id;
    
}

public static function AjouteArchivageMedecinProduit($id,$idProd,$date,$heure){
    $pdo= PdoGsb::$monPdo;
    //    $select="SELECT MAX(idMedecin) FROM archivagemedecin";
//    $valeur = $pdo->prepare($select);
//    $execution=$valeur->execute();
   
    $sql="INSERT INTO archivageproduit VALUES(:idMedecin, :idProduit, :date,:heure)";
    $requete=$pdo->prepare($sql);
    $bv1=$requete->bindValue(':idMedecin',$id);
    $bv2=$requete->bindValue(':idProduit',$idProd);
    $bv3=$requete->bindValue(':date',$date);
    $bv4=$requete->bindValue(':heure',$heure);
    $executionok = $requete->execute();
    return $executionok;
    
}

public static function AjouteArchivageVisio($id,$idViso,$dateInscri){
   $pdo= PdoGsb::$monPdo;
//    $select="SELECT MAX(idMedecin) FROM archivagemedecin";
//    $valeur = $pdo->prepare($select);
//    $execution=$valeur->execute();
   
    
    $sql="INSERT INTO archivagevisio VALUES(:idMedecin, :idVisio,:date)";
    $requete=$pdo->prepare($sql);
    $bv1=$requete->bindValue(':idMedecin',$id);
    $bv2=$requete->bindValue(':idVisio',$idViso);
    $bv3=$requete->bindValue(':date',$dateInscri);
    $executionok = $requete->execute();
    return $executionok;
    
}

public static function AjouteArchivageHistorique($id,$dateDebutLog,$dateFinLog){
       $pdo= PdoGsb::$monPdo;
//    $select="SELECT MAX(idMedecin) FROM archivagemedecin";
//    $valeur = $pdo->prepare($select);
//    $execution=$valeur->execute();
    
    $sql="INSERT INTO archivagehistoriqueconnexion VALUES(:idMedecin, :dateDebut,:dateFin)";
    $requete=$pdo->prepare($sql);
    $bv1=$requete->bindValue(':idMedecin',$id);
    $bv2=$requete->bindValue(':dateDebut',$dateDebutLog);
    $bv3=$requete->bindValue(':dateFin',$dateFinLog);
    $executionok = $requete->execute();
    return $executionok;
    
}

public static function RecupMedecin($id){
    $pdo = PdoGsb::$monPdo;
    
    $sql = "SELECT dateConsentement,dateCreation,rpps,nom,prenom,mail,dateNaissance FROM medecin WHERE id = :lId";
    $requete = $pdo->prepare($sql);
    $bv1=$requete->bindValue(':lId',$id);
    $executionOk = $requete->execute();
    
    $tab = $requete->fetchAll();
    $requete->closeCursor();
    
    return $tab;
}

public static function decryptMail($mail){
    $key= PdoGsb::recupKey();
    $nonce= PdoGsb::recupNonce();
    $decrypted = sodium_crypto_secretbox_open($mail, $nonce, $key);
    return $decrypted;
}

public static function toJson($nom,$prenom,$dateConsent,$dateCrea,$rpps,$mail,$dateNaiss){
    $file='css/Mesinfos.odt';
    $json=json_encode(["nom"=>$nom,
                        "prenom"=>$prenom,
                "dateConsent"=>$dateConsent,"rpps"=>$rpps,"dateCrea"=>$dateCrea,"mail"=>$mail,"dateNaiss"=>$dateNaiss,]);
    $sd= file_put_contents($file,$json,FILE_APPEND | LOCK_EX);
    
}

}
?>