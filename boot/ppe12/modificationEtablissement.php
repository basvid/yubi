
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

// MODIFIER UN ÉTABLISSEMENT 

// Déclaration du tableau des civilités
$tabCivilite=array("M.","Mme","Melle");  

$action=$_REQUEST['action'];
$id=$_REQUEST['id'];

// Si on ne "vient" pas de ce formulaire, il faut récupérer les données à partir 
// de la base (en appelant la fonction obtenirDetailEtablissement) sinon on 
// affiche les valeurs précédemment contenues dans le formulaire
if ($action=='demanderModifEtab')
{
   $lgEtab=obtenirDetailEtablissement($connexion, $id);
  
   $nom=$lgEtab['nom'];
   $adresseRue=$lgEtab['adresseRue'];
   $codePostal=$lgEtab['codePostal'];
   $ville=$lgEtab['ville'];
   $tel=$lgEtab['tel'];
   $adresseElectronique=$lgEtab['adresseElectronique'];
   $type=$lgEtab['type'];
   $civiliteResponsable=$lgEtab['civiliteResponsable'];
   $nomResponsable=$lgEtab['nomResponsable'];
   $prenomResponsable=$lgEtab['prenomResponsable'];
   $nombreChambresOffertes=$lgEtab['nombreChambresOffertes'];
}
else
{
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

   verifierDonneesEtabM($connexion, $id, $nom, $adresseRue, $codePostal, $ville,  
                        $tel, $nomResponsable, $nombreChambresOffertes);      
   if (nbErreurs()==0)
   {        
      modifierEtablissement($connexion, $id, $nom, $adresseRue, $codePostal, $ville, 
                            $tel, $adresseElectronique, $type, $civiliteResponsable, 
                            $nomResponsable, $prenomResponsable, $nombreChambresOffertes);
   }
}

echo "
<form method='POST' action='modificationEtablissement.php?'>
   <input type='hidden' value='validerModifEtab' name='action'>
   <table width='85%' cellspacing='0' cellpadding='0' align='center' 
   class='table table-bordered'>
   
      <tr class='enTeteTabNonQuad'>
         <td colspan='3'>$nom ($id)</td>
      </tr>
      <tr>
         <td><input type='hidden' value='$id' name='id'></td>
      </tr>";
      
      echo '
      <tr class="ligneTabNonQuad">
	  <div class="form-group">
         <td> <label for="input$nom">Nom*</label>: </td>
         <td><input type="text" class="form-control"value="'.$nom.'" name="nom" size="50" 
         maxlength="45"></td>
	  </div>
      </tr>
      <tr class="ligneTabNonQuad">
       <div class="form-group">
         <td> <label for="input$adresseRue"> Adresse*</label>: </td>
         <td><input type="text" class="form-control" value="'.$adresseRue.'" name="adresseRue" 
         size="50" maxlength="45"></td>
       </div>
	  </tr>
      <tr class="ligneTabNonQuad">
         <div class="form-group">
         <td> <label for="input$codePostal"> Code postal*</label>: </td>
         <td><input type="number" class="form-control"value="'.$codePostal.'" name="codePostal" 
         size="4" maxlength="5"></td>
      </div>
	 </tr>
      <tr class="ligneTabNonQuad">
       <div class="form-group">
         <td> <label for="input$ville">Ville*</label>: </td>
         <td><input type="text"class="form-control"value="'.$ville.'" name="ville" size="40" 
         maxlength="35"></td>
       </div>
	  </tr>
      <tr class="ligneTabNonQuad">
        <div class="form-group">
         <td> <label for="input$tel">Téléphone*</label>: </td>
         <td><input type="number" class="form-control"value="'.$tel.'" name="tel" size ="20" 
         maxlength="10"></td>
       </div>
	  </tr>
      <tr class="ligneTabNonQuad">
         <div class="form-group">
         <td> <label for="input$adresseElectronique"> E-mail:</label> </td>
         <td><input type="text" class="form-control"value="'.$adresseElectronique.'" name=
         "adresseElectronique" size ="75" maxlength="70"></td>
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
               </select>&nbsp; &nbsp; &nbsp; 
			   <div class="form-group"> 
		          <label for="input$n">Nom*: </label>
               <input type="text" class="form-control"value="'.$nomResponsable.'" name=
               "nomResponsable" size="26" maxlength="25">
			   </div>
               &nbsp; &nbsp; &nbsp; 
			   <div class="form-group"> 
		          <label for="input$p">Prénom: </label>
               <input type="text"  class="form-control"value="'.$prenomResponsable.'" name=
               "prenomResponsable" size="26" maxlength="25"></div>
            </td>
         </tr>
         <tr class="ligneTabNonQuad">
            <div class="form-group">
         <td> <label for="input$nombreChambresOffertes">Nombre chambres offertes*: </label></td>
            <td><input type="text" class="form-control"value="'.$nombreChambresOffertes.'" name=
            "nombreChambresOffertes" size ="2" maxlength="3"></td>
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
if ($action=='validerModifEtab')
{
   if (nbErreurs()!=0)
   {
      afficherErreurs();
   }
   else
   {
      echo "
      <h5><center>La modification de l'établissement a été effectuée</center></h5>";
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