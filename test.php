<?php
try{
$size = 12;
function Genere_Password($size)
{
    // Initialisation des caractères utilisables (Majuscule Minuscule)
    $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

    for($i=0;$i<$size;$i++)
    {
        $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
    }
		
    return $password;
}
$password = Genere_Password($size);
$url = 'https://taxibus.ddns.fraxion.com:8083/Webservice_Repartition_Test/Service_Transport_Collectif/v1/usager/recherche'; 
$http_auth_ident = 'Test_TC:jojk2d3d6hwd34'; // username:password 
$numero_usager = $_POST['numero_usager'];
$data = array('numero' => $numero_usager);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded, Authorization: Basic VGVzdF9UQzpqb2prMmQzZDZod2QzNA==\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
        
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$parsed_json = json_decode($result);
$id_usager = $parsed_json->{'id'};

$url = 'https://taxibus.ddns.fraxion.com:8083/Webservice_Repartition_Test/Service_Transport_Collectif/v1/usager/'.$id_usager; 
$http_auth_ident = 'Test_TC:jojk2d3d6hwd34'; // username:password 
$numero_usager = $_POST['numero_usager'];
$data = array('mot_passe' => $password);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded, Authorization: Basic VGVzdF9UQzpqb2prMmQzZDZod2QzNA==\r\n",
        'method'  => 'PATCH',
        'content' => http_build_query($data),
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$mail = $_POST['mail'];  // Déclaration de l'adresse de destination.
if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui rencontrent des bogues.
{
	$passage_ligne = "\r\n";
}
else
{
	$passage_ligne = "\n";
}
//=====Déclaration des messages au format texte et au format HTML.
$message_txt = "Merci de vous etre inscrit sur Taxibus, voici votre Mot de passe: $password";
$message_html = "<html><head></head><body><i>Merci de vous etre inscrit sur Taxibus</i>, voici votre Mot de passe: <i> $password  </i></body></html>";
//==========
 
//=====Création de la boundary
$boundary = "-----=".md5(rand());
//==========
 
//=====Définition du sujet.
$sujet = "Bienvenue !";
//=========
 
//=====Création du header de l'e-mail.
$header = "From: \"TaxiBus\"<taxibusv@box5388.bluehost.com>".$passage_ligne;
$header.= "Reply-to: \"TaxiBus\" <info@taxibusvalleyfield.com>".$passage_ligne;
$header.= "MIME-Version: 1.0".$passage_ligne;
$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
//==========
 
//=====Création du message.
$message = $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format texte.
$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_txt.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary.$passage_ligne;
//=====Ajout du message au format HTML
$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
$message.= $passage_ligne.$message_html.$passage_ligne;
//==========
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
//==========
 
//=====Envoi de l'e-mail.
mail($mail,$sujet,$message,$header);
//==========
}catch(Exception $e) {
    exit();
}
?>
