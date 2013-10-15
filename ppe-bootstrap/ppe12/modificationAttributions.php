<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.png">

    <title>Starter Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
<style> body { padding: 60px;}</style>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="debut.html">Accueil</a></li>
            <li><a href="gestion.php">Gestion &eacute;tablissements</a></li>
            <li><a href="consultationAttributions.php">Attributions chambres</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container">
	 <div class="starter-template">
<?php

include("_gestionBase.inc.php"); 
include("_controlesEtGestionErreurs.inc.php");

// CONNEXION AU SERVEUR MYSQL PUIS SÉLECTION DE LA BASE DE DONNÉES festival

$connexion=connect();
if (!$connexion)
{
   ajouterErreur("Echec de la connexion au serveur MySql");
   afficherErreurs();
   exit();
}
if (!selectBase($connexion))
{
   ajouterErreur("La base de données festival est inexistante ou non accessible");
   afficherErreurs();
   exit();
}

// EFFECTUER OU MODIFIER LES ATTRIBUTIONS POUR L'ENSEMBLE DES ÉTABLISSEMENTS

// CETTE PAGE CONTIENT UN TABLEAU CONSTITUÉ DE 2 LIGNES D'EN-TÊTE (LIGNE TITRE ET 
// LIGNE ÉTABLISSEMENTS) ET DU DÉTAIL DES ATTRIBUTIONS 
// UNE LÉGENDE FIGURE SOUS LE TABLEAU

// Recherche du nombre d'établissements offrant des chambres pour le 
// dimensionnement des colonnes
$nbEtabOffrantChambres=obtenirNbEtabOffrantChambres($connexion);
$nb=$nbEtabOffrantChambres+1;
// Détermination du pourcentage de largeur des colonnes "établissements"
$pourcCol=50/$nbEtabOffrantChambres;

$action=$_REQUEST['action'];

// Si l'action est validerModifAttrib (cas où l'on vient de la page 
// donnerNbChambres.php) alors on effectue la mise à jour des attributions dans 
// la base 
if ($action=='validerModifAttrib')
{
   $idEtab=$_REQUEST['idEtab'];
   $idGroupe=$_REQUEST['idGroupe'];
   $nbChambres=$_REQUEST['nbChambres'];
   modifierAttribChamb($connexion, $idEtab, $idGroupe, $nbChambres);
}

echo "
<table width='80%' cellspacing='0' cellpadding='0' align='center' 
class='table table-bordered'>";

   // AFFICHAGE DE LA 1ÈRE LIGNE D'EN-TÊTE
   echo "
   <tr class='enTeteTabQuad'>
      <td colspan=$nb><strong>Attributions</strong></td>
   </tr>";
      
   // AFFICHAGE DE LA 2ÈME LIGNE D'EN-TÊTE (ÉTABLISSEMENTS)
   echo "
   <tr class='ligneTabQuad'>
      <td>&nbsp;</td>";
      
   $req=obtenirReqEtablissementsOffrantChambres();
   $rsEtab=mysql_query($req, $connexion);
   $lgEtab=mysql_fetch_array($rsEtab);

   // Boucle sur les établissements (pour afficher le nom de l'établissement et 
   // le nombre de chambres encore disponibles)
   while ($lgEtab!=FALSE)
   {
      $idEtab=$lgEtab["id"];
      $nom=$lgEtab["nom"];
      $nbOffre=$lgEtab["nombreChambresOffertes"];
      $nbOccup=obtenirNbOccup($connexion, $idEtab);
                    
      // Calcul du nombre de chambres libres
      $nbChLib = $nbOffre - $nbOccup;
      echo "
      <td valign='top' width='$pourcCol%'><i>Disponibilités : $nbChLib </i> <br>
      $nom </td>";
      $lgEtab=mysql_fetch_array($rsEtab);
   }
   echo "
   </tr>"; 

   // CORPS DU TABLEAU : CONSTITUTION D'UNE LIGNE PAR GROUPE À HÉBERGER AVEC LES 
   // CHAMBRES ATTRIBUÉES ET LES LIENS POUR EFFECTUER OU MODIFIER LES ATTRIBUTIONS
         
   $req=obtenirReqIdNomGroupesAHeberger();
   $rsGroupe=mysql_query($req, $connexion);
   $lgGroupe=mysql_fetch_array($rsGroupe);
         
   // BOUCLE SUR LES GROUPES À HÉBERGER 
   while ($lgGroupe!=FALSE)
   {
      $idGroupe=$lgGroupe['id'];
      $nom=$lgGroupe['nom'];
      echo "
      <tr class='ligneTabQuad'>
         <td width='25%'>$nom</td>";
      $req=obtenirReqEtablissementsOffrantChambres();
      $rsEtab=mysql_query($req, $connexion);
      $lgEtab=mysql_fetch_array($rsEtab);
           
      // BOUCLE SUR LES ÉTABLISSEMENTS
      while ($lgEtab!=FALSE)
      {
         $idEtab=$lgEtab["id"];
         $nbOffre=$lgEtab["nombreChambresOffertes"];
         $nbOccup=obtenirNbOccup($connexion, $idEtab);
                   
         // Calcul du nombre de chambres libres
         $nbChLib = $nbOffre - $nbOccup;
                  
         // On recherche si des chambres ont déjà été attribuées à ce groupe
         // dans cet établissement
         $nbOccupGroupe=obtenirNbOccupGroupe($connexion, $idEtab, $idGroupe);
         
         // Cas où des chambres ont déjà été attribuées à ce groupe dans cet
         // établissement
         if ($nbOccupGroupe!=0)
         {
            // Le nombre de chambres maximum pouvant être demandées est la somme 
            // du nombre de chambres libres et du nombre de chambres actuellement 
            // attribuées au groupe (ce nombre $nbmax sera transmis si on 
            // choisit de modifier le nombre de chambres)
            $nbMax = $nbChLib + $nbOccupGroupe;
            echo "
            <td class='reserve'>
            <a href='donnerNbChambres.php?idEtab=$idEtab&amp;idGroupe=$idGroupe&amp;nbChambres=$nbMax'>
            $nbOccupGroupe</a></td>";
         }
         else
         {
            // Cas où il n'y a pas de chambres attribuées à ce groupe dans cet 
            // établissement : on affiche un lien vers donnerNbChambres s'il y a 
            // des chambres libres sinon rien n'est affiché     
            if ($nbChLib != 0)
            {
               echo "
               <td class='reserveSiLien'>
               <a href='donnerNbChambres.php?idEtab=$idEtab&amp;idGroupe=$idGroupe&amp;nbChambres=$nbChLib'>
               __</a></td>";
            }
            else
            {
               echo "<td class='reserveSiLien'>&nbsp;</td>";
            }
         }    
         $lgEtab=mysql_fetch_array($rsEtab);
      } // Fin de la boucle sur les établissements    
      $lgGroupe=mysql_fetch_array($rsGroupe);  
   } // Fin de la boucle sur les groupes à héberger
echo "
</table>"; // Fin du tableau principal

// AFFICHAGE DE LA LÉGENDE
echo "
<table align='center' width='80%'>
   <tr>
      <td width='34%' align='left'><a href='consultationAttributions.php'>Retour</a>
      </td>
      <td class='reserveSiLien'>&nbsp;</td>
      <td width='30%' align='left'>Réservation possible si lien</td>
      <td class='reserve'>&nbsp;</td>
      <td width='30%' align='left'>Chambres réservées</td>
   </tr>
</table>";

?>
</div>
    </div><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../../assets/js/jquery.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
  </body>
</html>