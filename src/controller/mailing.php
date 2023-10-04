<?php
/**     
        *description                       : LE SCRIPT DE GESTION DES MAILS [VALISATION | ACTIVATION DE COMPTE | MAILING] POUR TOUT LE PROJET
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
require_once "model/Mailing.php";
require_once "model/Session.php";
require_once "model/Newsletter.php";
$mailSender = new SendMail();
$session    = new Session();
$newsletter = new Newsletter();
/**
 *Appel les fonctions adequoites selon le traitement demandé par le CLIENT
 */
match ($traitement) {
        "SEND EMAIL"           => mailSend(),
        "CONTACTEZ NOUS"       => mailSend(),
        default                => reject(),
};

/**
        *Envoi classic de mail 
 */
function mailSend(){
        global $mailSender,$traitement;
        if ($traitement==="CONTACTEZ NOUS"){
                $email_destinataire = EMAIL_CONTACT;
                $nom_new            = controlClient("nom");
                $prenoms_new        = controlClient("prenoms");
                $contact_new        = controlClient("contact");
                $email_new          = controlClient("email");
                $email_objet        = "NOUVEAU MESSAGE DE ".$nom_new." FORMULAIRE DE CONTACT SÉJOUR ZEN";
                $email_content      = controlClient("email_content");
        }else{
                $email_destinataire = controlClient("email_destinataire");
                $email_objet        = controlClient("email_objet");
                $email_content      = controlClient("email_content");
        }
        //Remplace du marquace [contenu] par le véritable contenu
        $patch              = "<div style='font-size: 16px; border: solid 1px #F36F40; background-color:#FFEAE2; padding:5px; border-radius:5px;'> De la Part de <br/> Nom & Prénoms : ".$nom_new." ".$prenoms_new."  <br/> Email : ".$email_new."  <br/> Contact : ".$contact_new."</div> <br/><br/> <hr/> <br/> Message <br/>";
        $email_content      = "<div style='font-size: 16px;'>".$email_content."</div>";
        $body               = str_replace("[content]",$patch.$email_content,TEMPLATE_CLASSIC_MAIL);
        $body               = str_replace("[societe]",SOCIETE,$body);
        $body               = str_replace("[contact1]",TELEPHONE1,$body);
        $body               = str_replace("[email_info]",EMAIL_INFOS,$body);
        if (!$mailSender->sendMail($email_destinataire,$email_destinataire,$email_objet,$body)){
                reject("erreur pendant l'envoi du mail. Veuillez réessayer utérieurement.");
        }
        if ($traitement==="CONTACTEZ NOUS") {
                saveContact();
        }
        http_response_code(200);
        $reponse = array(
                'reponse' => 'success',
                'message' => 'Votre message à bien été transmis.',
        );
        print json_encode($reponse);
        exit();
}

/**
        *Inscription de l'user dans la newsletter
*/
function saveContact(){
        global $newsletter;
        $email_new            = controlClient("email");
        $nom_new              = controlClient("nom");
        $prenoms_new          = controlClient("prenoms");
        $contact_new          = controlClient("contact");
        $canal_abonnement_new = "contactez-nous";
        $type_user_new        = "prospect";
        $DATA_NEW             = compact("email_new", "nom_new", "prenoms_new", "type_user_new", "contact_new", "canal_abonnement_new");
        $newsletter->creer($DATA_NEW);
        return true;
}

/**
        *Verifie que le client est bien logué et à le droit de mener cette action
        *Retrourn un array de client
*/
function controlClient(string $what_to_control){
        if ($what_to_control == "email"){
                $email_new  = isset($_POST["email_new"]) && !empty($_POST["email_new"]) ? trim($_POST["email_new"]) : false;
                if (!$email_new){
                        reject("Remplissez l'email du client'");
                }
                // ! validation du format de l'email
                if (!filter_var($email_new, FILTER_VALIDATE_EMAIL)){
                        reject("Format email incorrect.");
                }
                return $email_new;
        }
        if ($what_to_control == "nom"){
                $nom_client  = isset($_POST["nom_new"]) && !empty($_POST["nom_new"]) && strlen($_POST["nom_new"]) >= 2 ? htmlspecialchars(trim($_POST["nom_new"])) : false;
                if (!$nom_client) {
                        reject("Remplissez le nom du client'");
                }
                return $nom_client;
        }
        if ($what_to_control == "prenoms"){
                $prenoms_client  = isset($_POST["prenoms_new"]) && !empty($_POST["prenoms_new"]) && strlen($_POST["prenoms_new"]) >= 2 ? htmlspecialchars(trim($_POST["prenoms_new"])) : false;
                if (!$prenoms_client) {
                        reject("Remplissez les prénoms du client'");
                }
                return $prenoms_client;
        }
        if ($what_to_control == "type"){
                $user_type  = isset($_POST["type_user_new"]) && !empty($_POST["type_user_new"]) && strlen($_POST["type_user_new"]) >= 2 ? htmlspecialchars(trim($_POST["type_user_new"])) : false;
                if (!$user_type){
                        reject("Remplissez le type  d'utilisateur'");
                }
                return $user_type;
        }
        if ($what_to_control == "canal"){
                $canal_abonnement_new  = isset($_POST["canal_abonnement_new"]) && !empty($_POST["canal_abonnement_new"]) && strlen($_POST["canal_abonnement_new"]) >= 2 ? htmlspecialchars(trim($_POST["canal_abonnement_new"])) : false;
                if (!$canal_abonnement_new){
                        reject("Remplissez le canal de souscriptioon pour la newsletter'");
                }
                return $canal_abonnement_new;
        }
        if ($what_to_control == "contact"){
                $contact_client = isset($_POST["contact_new"]) && !empty($_POST["contact_new"]) && strlen($_POST["contact_new"]) >= 8 && strlen($_POST["contact_new"]) <= 16  ? trim($_POST["contact_new"]) : false;
                if (!$contact_client){
                        reject("N° de téléphone mal défini.");
                }
                return  $contact_client;
        }
        if ($what_to_control == "email_destinataire"){
                $email_destinataire  = isset($_POST["email_destinataire"]) && !empty($_POST["email_destinataire"]) ? trim($_POST["email_destinataire"]) : false;
                if (!$email_destinataire){
                        reject("Remplissez l'email du destinataire");
                }
                // ! validation du format de l'email
                if (!filter_var($email_destinataire, FILTER_VALIDATE_EMAIL)){
                        reject("Format email incorrect.");
                }
                return $email_destinataire;
        }
        if ($what_to_control == "email_objet"){
                $email_objet=isset($_POST["email_objet"]) && !empty($_POST["email_objet"]) ? trim(htmlspecialchars($_POST["email_objet"])) : false;
                if (!$email_objet){
                        reject("Entrer l'objet du mail.");
                }
                return $email_objet;
        }
        if ($what_to_control == "email_content"){
                $email_content=isset($_POST["email_content"]) &&  strlen($_POST["email_content"])> 6  ? trim(htmlspecialchars($_POST["email_content"])) : false;
                if (!$email_content){
                        reject("Rallongez votre message.");
                }
                return $email_content;
        }
}