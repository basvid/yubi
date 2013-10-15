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

// CRÉER UN ÉTABLISSEMENT 

// Déclaration du tableau des civilités
$tabCivilite=array("M.","Mme","Melle");  

$action=$_REQUEST['action'];

// S'il s'agit d'une création et qu'on ne "vient" pas de ce formulaire (on 
// "vient" de ce formulaire uniquement s'il y avait une erreur), il faut définir 
// les champs à vide sinon on affichera les valeurs précédemment saisies
if ($action=='demanderCreEtab') 
{  
   $id='';
   $nom='';
   $adresseRue='';
   $ville='';
   $codePostal='';
   $tel='';
   $adresseElectronique='';
   $type=0;
   $civiliteResponsable='Monsieur';
   $nomResponsable='';
   $prenomResponsable='';
   $nombreChambresOffertes='';
}
else
{
   $id=$_REQUEST['id']; 
   $nom=$_REQUEST['nom']; 
   $adresseRue=$_REQUEST['adresseRue'];
   $codePostal=$_REQUEST['codePostal'];
   $ville=$_REQUEST['ville'];
   $tel=$_REQUEST['tel'];
   $adresseElectronique=$_REQUEST['adresseElectronique'];
   $type=$_REQUEST['type'];
   $civiliteResponsable=$_REQUEST['civiliteResponsable'];
   $nomResponsable=$_REQUEST['nomResponsable'];
   $prenomResponsable=$_REQUEST['prenomResponsable'];
   $nombreChambresOffertes=$_REQUEST['nombreChambresOffertes'];

   verifierDonneesEtabC($connexion, $id, $nom, $adresseRue, $codePostal, $ville, 
                        $tel, $nomResponsable, $nombreChambresOffertes);      
   if (nbErreurs()==0)
   {        
      creerEtablissement($connexion, $id, $nom, $adresseRue, $codePostal, $ville,  
                         $tel, $adresseElectronique, $type, $civiliteResponsable, 
                         $nomResponsable, $prenomResponsable, $nombreChambresOffertes);
   }
}

echo "
<form method='POST' action='creationEtablissement.php?'>
   <input type='hidden' value='validerCreEtab' name='action'>
   <table width='85%' align='center' cellspacing='0' cellpadding='0' 
   class='table table-bordered'>
   
      <tr class='enTeteTabNonQuad'>
         <td colspan='3'>Nouvel établissement</td>
      </tr>
      <tr class='ligneTabNonQuad'>
    <div class='form-group'>
         <td> <label for='input$id'>Id* </label>: </td>
         <td> <input type='number' class='form-control'id='input$id' name='id' placeholder='Enter id'> </td>
    </div>
	  </tr>";
     
      echo '
      <tr class="ligneTabNonQuad">
	  <div class="form-group">
         <td> <label for="input$nom">Nom*</label>: </td>
         <td><input type="text" class="form-control"id="input$nom" name="nom" placeholder="Enter nom"</td>
      </div> 
	  </tr>
      <tr class="ligneTabNonQuad">
      <div class="form-group"> 
		 <td><label for="input$adr"> Adresse*</label>: </td>
         <td><input type="text" class="form-control"id="input$adr" name="adresseRue" placeholder="Enter adresse"</td>
      </div>  
      </tr>
      <tr class="ligneTabNonQuad">
	  <div class="form-group"> 
         <td> <label for="input$cp">Code postal*</label>: </td>
         <td><input type="number" class="form-control"id="input$cp" name="codePostal"placeholder="Enter code postal" </td>
       </div>
	  </tr>
      <tr class="ligneTabNonQuad">
	  <div class="form-group"> 
         <td> <label for="input$v">Ville*</label>: </td>
         <td><input type="text" class="form-control"id="input$v" name="ville"placeholder="Enter ville" </td>
	  </div>
      </tr>
      <tr class="ligneTabNonQuad">
	  <div class="form-group"> 
         <td><label for="input$t"> Téléphone*</label>: </td>
         <td><input type="number" class="form-control"id="input$t" name="tel"placeholder="Enter telephone" </td>
	  </div> 
      </tr>
      <tr class="ligneTabNonQuad">
	  <div class="form-group"> 
         <td> <label for="input$e">E-mail</label>: </td>
         <td><input type="email" class="form-control"id="input$e" name="adresseElectronique"placeholder="Enter E-mail"</td>
      </div>
	  </tr>
      <tr class="ligneTabNonQuad">
         <td> Type*: </td>
         <td>';
            if ($type==1)
            {
               echo " 
               <input type='radio' name='type' value='1' checked>  
               Etablissement Scolaire
               <input type='radio' name='type' value='0'>  Autre";
             }
             else
             {
                echo " 
                <input type='radio' name='type' value='1'> 
                Etablissement Scolaire
                <input type='radio' name='type' value='0' checked> Autre";
              }
           echo "
           </td>
         </tr>
         <tr class='ligneTabNonQuad'>
            <td colspan='2' ><strong>Responsable:</strong></td>
         </tr>
         <tr class='ligneTabNonQuad'>
            <td> Civilité*: </td>
            <td> <select name='civiliteResponsable'>";
               for ($i=0; $i<3; $i=$i+1)
                  if ($tabCivilite[$i]==$civiliteResponsable) 
                  {
                     echo "<option selected>$tabCivilite[$i]</option>";
                  }
                  else
                  {
                     echo "<option>$tabCivilite[$i]</option>";
                  }
               echo '
               </select>&nbsp; &nbsp; &nbsp; &nbsp; 
			   <div class="form-group"> 
		          <label for="input$n"> Nom*: </label>
			      <input type="text" class="form-control"id="input$n" name="nomResponsable"placeholder="Enter nom">
               &nbsp; &nbsp; &nbsp; &nbsp; 
			   </div>
			   <div class="form-group">
			    <label for="input$p">Prénom: </label>
                <input type="text" class="form-control"id="input$p" name="prenomResponsable"placeholder="Enter prénom">
              </div>
		   </td>
         </tr>
          <tr class="ligneTabNonQuad">
		  <div class="form-group"> 
            <td><label for="input$nc"> Nombre chambres offertes*</label>: </td>
            <td><input type="text" class="form-control"id="input$nc" name="nombreChambresOffertes"placeholder="Enter Nombre chambres" </td> 
		  </div>
         </tr>
   </table>';
   
   echo "
   <table align='center' cellspacing='15' cellpadding='0'>
      <tr>
         <td align='right'><input type='submit' value='Valider' name='valider'>
         </td>
         <td align='left'><input type='reset' value='Annuler' name='annuler'>
         </td>
      </tr>
      <tr>
         <td colspan='2' align='center'><a href='gestion.php'>Retour</a>
         </td>
      </tr>
   </table>
</form>";

// En cas de validation du formulaire : affichage des erreurs ou du message de 
// confirmation
if ($action=='validerCreEtab')
{
   if (nbErreurs()!=0)
   {
      afficherErreurs();
   }
   else
   {
      echo "
      <h5><center>La création de l'établissement a été effectuée</center></h5>";
   }
}

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