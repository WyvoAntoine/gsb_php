<style>
    footer{
        text-align:center;
        position: absolute;
        width:100%;
    }
</style>
	
<footer id="footer" class)="panel-footer">
    <?php if($_SESSION['$showcookie']) { ?>
	<div class="cookie-alert">
	   En poursuivant votre navigation sur ce site, 
           vous acceptez l’utilisation de cookies pour vous 
           proposer des contenus et services adaptés à vos 
           centres d’intérêts.<br /><a class="btn btn-primary" href="index.php?uc=droit&action=coockieRefuser">Refuser</a><a class="btn btn-primary" href="index.php?uc=droit&action=coockie">Accepter</a>
	</div>
	<?php } ?>
            <h2>Voici le lien vers la <a href="vues/v_politiqueprotectiondonnees.html">Politique de protection des données</a></h2>
     
 </footer>