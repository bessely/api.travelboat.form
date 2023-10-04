<?php 
/**
        *description                       : LE SCRIPT D'ENVOI DES MAILS POUR TOUT LE PROJET
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once "vendor/phpmailer/src/PHPMailer.php";
require_once "vendor/phpmailer/src/SMTP.php";
require_once "vendor/phpmailer/src/Exception.php";
require_once "vendor/phpmailer/src/OAuth.php";
require_once "vendor/phpmailer/src/POP3.php";
class  SendMail{
        /**
                *Envoi de Mail
         */
        public  function sendMail(string $receiver_name, string $email_reception, string $objet, string $body,  string $copyCache = NULL){
                if (EMAIL_SMTP){
                        $mail             = new PHPMailer(true);
                        // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
                        $mail->CharSet    = 'UTF-8';
                        $mail->Encoding   = 'base64';
                        $mail->setLanguage('fr');
                        $mail->isSMTP();
                        $mail->SMTPSecure = strtolower(PROTOCOL_EXCH_SMTP); //tls ssl
                        $mail->SMTPAuth    = true;
                        $mail->isHTML(true);
                        $mail->Host       = EMAIL_SMTP;
                        $mail->Port       = PORT_SMTP; //465; //587
                        $mail->Username   = EMAIL_ENV;
                        $mail->Password   = MDP_SMTP;
                        $mail->setFrom(EMAIL_ENV, SOCIETE);
                        $mail->addAddress($email_reception, $receiver_name);
                        if (!is_null($copyCache)) {
                                $mail->addCC($copyCache);
                        }
                        $mail->Subject     = $objet;
                        $mail->Body        = $body;
                        if ($mail->send()) {
                                $mail->clearAddresses();
                                $mail->clearAttachments();
                                $mail->SmtpClose();
                                unset($mail);
                                return true;
                        }
                        $mail->clearAddresses();
                        $mail->clearAttachments();
                        $mail->SmtpClose();
                        return $mail->ErrorInfo;
                }
                reject("Paramtères SMTP pour l'envoi des mails inexistants !");
        }

        /**
                *Sauvegarde des Mails 
         */  
        public function saveMail (array $data){
                $req = Database::getConnexion()->prepare("INSERT INTO  `mail`
                (
                        ID_MAIL,
                        BODY_MAIL,
                        TEMPLATE_MAIL,
                        OBJECT_MAIL,
                        DESTINATAIRE_MAIL,
                        CODEORLINK_MAIL,
                        USER_TYPE_MAIL,
                        ID_USER_MAIL,
                        DATE_SEND_MAIL,
                        DELAY_VALIDITY_MAIL,
                        TOKEN
                )
                VALUES
                (
                        :ID_MAIL,
                        :BODY_MAIL,
                        :TEMPLATE_MAIL,
                        :OBJECT_MAIL,
                        :DESTINATAIRE_MAIL,
                        :CODEORLINK_MAIL,
                        :USER_TYPE_MAIL,
                        :ID_USER_MAIL,
                        :DATE_SEND_MAIL,
                        :DELAY_VALIDITY_MAIL,
                        :TOKEN
                )");
                $param = array(
                        'ID_MAIL'             => NULL,
                        'BODY_MAIL'           => $data["body_mail"],
                        'TEMPLATE_MAIL'       => $data["template_mail"],
                        'OBJECT_MAIL'         => $data["object_mail"],
                        'DESTINATAIRE_MAIL'   => $data["destinataire_mail"],
                        'CODEORLINK_MAIL'     => $data["codeorlink_mail"],
                        'USER_TYPE_MAIL'      => $data["user_type"],
                        'ID_USER_MAIL'        => $data["id_user"],
                        'DATE_SEND_MAIL'      => custum_calendar("maintenant"),
                        'DELAY_VALIDITY_MAIL' => give_next_day(custum_calendar("maintenant"),1), // +++ le lendemain à 0 heur 0 minute
                        'TOKEN'               => $data["token"],
                ); 
                if ($req->execute($param)) {return true;}
                return false;
        }

        /**
                *Suppression des infos sur la procédure d'activation
                *@param string $mail contient soit un mail ou un token
         */  
        public function deleteMail(string $mail){
                $req = Database::getConnexion()->prepare("DELETE FROM `mail` WHERE TOKEN =:mail OR DESTINATAIRE_MAIL=:mail");
                $req->bindParam(':mail', $mail);
                if ($req->execute()) {return true;} 
                return false;
        }

        /**
                *Chargement d'un MAIL par token
         */  
        public function loadOneMailByToken($token){
                $req = Database::getConnexion()->prepare("SELECT * FROM `mail` WHERE TOKEN =:token LIMIT 1");
                $req->bindParam(':token', $token);
                if ($req->execute()) {
                        $req->setFetchMode(PDO::FETCH_ASSOC);
                        return $req->fetch();
                }
                return false;
        }

        /**
                *Chargement d'un MAIL par destinataire
         */  
        public function loadOneMailByMailSender($mail){
                $req = Database::getConnexion()->prepare("SELECT * FROM `mail` WHERE DESTINATAIRE_MAIL =:mail ORDER BY DATE_SEND_MAIL DESC LIMIT 1");
                $req->bindParam(':mail', $mail);
                if ($req->execute()) {
                        $req->setFetchMode(PDO::FETCH_ASSOC);
                        return $req->fetch();
                }
                return false;
        }

        /**
                *Envoi de lien de validation d'un mail user [CLIENT - PROIETAIRE - ADMINISTRATEUR] par mail
         */  
        // public function mailValidationLink(array $data){
        //         $user_type = isset($data['user_type']) && !empty($data['user_type']) ? trim($data['user_type']) : false;
        //         if (!$user_type) {
        //                 reject("Type d'utilisateur non renseigné.");
        //         }
        //         $usertype  = array_search(trim($data['user_type']), USER_TYPE) ? trim($data['user_type']) : false; 
        //         if (!$usertype) {
        //                 reject("Type d'utilisateur '" . $data['user_type'] . "' non pris en charge");
        //         }
        //         $nom              = $data[ENTITIES[$usertype]['name']];
        //         $prenoms          = $data[ENTITIES[$usertype]['prenoms']];
        //         $email            = $data[ENTITIES[$usertype]['email']];
        //         $contact          = $data[ENTITIES[$usertype]['contact']];
        //         $pays             = $data[ENTITIES[$usertype]['pays']];
        //         $genre            = $data[ENTITIES[$usertype]['genre']];
        //         $naissance        = $data[ENTITIES[$usertype]['naissance']];
        //         $socio            = $data[ENTITIES[$usertype]['socio']];
        //         $mdp              = $data[ENTITIES[$usertype]['password']];
        //         $code_parrainnage = $data['code_parrainnage'] ?? NULL;
        //         $copyCacher       = $data['copyCacher'] ?? NULL;
        //         $token            = token(32);
        //         $link             = getEnvironment()["DOMAINE_ACTIVACTION_URI"]."?endpoint=confirmation&traitement=ACTIVATION&token=".$token; // ! le lien d'activation //
        //         $object_mail      = mb_strtoupper(SOCIETE)." - ACTIVATION DE COMPTE";
        //         $body_mail        = str_replace("[link]",$link,TEMPLATE_NOTIF_CONF_MAIL);
        //         $body_mail        = str_replace("[societe]",SOCIETE,$body_mail);
        //         $body_mail        = str_replace("[prenoms]",$prenoms,$body_mail);
        //         $body_mail        = str_replace("[contact1]",TELEPHONE1,$body_mail);
        //         $body_mail        = str_replace("[email_info]",EMAIL_INFOS,$body_mail);
        //         if (!$this->sendMail(SOCIETE,$email,$object_mail,$body_mail, $copyCacher)){
        //                 reject("Une erreur est survenue, Veuillez réessayer utérieurement.");
        //         }
        //         //sauvegarde du mail
        //         $id_user           = NULL;
        //         $user_type         = $user_type;
        //         $token             = $token;
        //         $template_mail     = "TEMPLATE_NOTIF_CONF_MAIL";
        //         $destinataire_mail = $email;
        //         $codeorlink_mail   = json_encode(compact("email","nom","prenoms","mdp","contact","pays","genre","naissance","socio", "code_parrainnage"));
        //         $data_Mail         = compact("body_mail","template_mail","object_mail","destinataire_mail","codeorlink_mail","user_type","id_user","token");
        //         $this->saveMail($data_Mail);
        //         return true;
        // }

        /**
                *Envoi de lien de validation d'un mail user [ADMINISTRATEUR] par mail
         */  
        // public function mailValidationLinkForAdmin(array $DATA_ADMIN){
        //         $object_mail    = mb_strtoupper(SOCIETE)." - VOS ACCÈS AU BACKOFFICE ADMIN";
        //         $email_content  = "Cher(e) ".(stylisizeThisData("name",$DATA_ADMIN['nom_pre_admin'])).", <br /> <a href=".(getEnvironment()["DOMAINE_ACTIVACTION_ADMIN_URI"])."?endpoint=confirmation&traitement=ACTIVATION&token=".$DATA_ADMIN['token'].">cliquez ici pour activer votre compte administrateur</a>.";
        //         $access         = "<br />login : ".$DATA_ADMIN['email_admin']."</br> Mot de passe : ".$DATA_ADMIN['mdp_admin'];
        //         //Remplace du marquace [contenu] par le véritable contenu
        //         $body           = str_replace("[content]",$email_content.$access,TEMPLATE_CLASSIC_MAIL);
        //         $body           = str_replace("[societe]",SOCIETE,$body);
        //         $body           = str_replace("[contact1]",TELEPHONE1,$body);
        //         $body_mail      = str_replace("[email_info]",EMAIL_INFOS,$body); 
        //         //envoi des accèes et du lien d'activaction par mail 
        //         if (!$this->sendMail($DATA_ADMIN['nom_pre_admin'],$DATA_ADMIN['email_admin'],$object_mail,$body_mail)){
        //                 reject("Une erreur est survenue, Veuillez réessayer utérieurement.");
        //         }
        //         //sauvegarde du mail
        //         $user_type         = "administrateur";
        //         $template_mail     = "TEMPLATE_CLASSIC_MAIL";
        //         $id_user           = $DATA_ADMIN['id_user'];
        //         $token             = $DATA_ADMIN['token']; 
        //         $destinataire_mail = $DATA_ADMIN['email_admin'];
        //         $codeorlink_mail   = json_encode($DATA_ADMIN);
        //         $data_Mail         = compact("body_mail","template_mail","object_mail","destinataire_mail","codeorlink_mail","user_type","id_user","token");
        //         $this->saveMail($data_Mail);
        //         return true;
        // }
        /**
                *Envoi de code de validation d'un mail user [CLIENT - PROIETAIRE - ADMINISTRATEUR] par mail
         */  
        // public function mailValidationCode(array $data){
        //         $code        = random_code(6);
        //         $nom         = $data[ENTITIES[$data['user_type']]['name']];
        //         $email       = $data[ENTITIES[$data['user_type']]['email']];
        //         $object_mail = mb_strtoupper(SOCIETE)." - CONFIRMATION D'EMAIL";
        //         $content     = "Cher(e) ".(stylisizeThisData("name",$nom)).", <br/> Veuillez utiliser ce code pour confirmer votre nouvelle adresse mail : ".(stylisizeThisData("code",$code))." <br/> Cordialement.";
        //         $body_mail   = str_replace("[content]",$content,TEMPLATE_CODE_MAIL); //Remplace du marquace [code] par le véritable $code
        //         $body_mail   = str_replace("[societe]",SOCIETE,$body_mail); 
        //         $body_mail   = str_replace("[contact1]",TELEPHONE1,$body_mail); 
        //         $body_mail   = str_replace("[email_info]",EMAIL_INFOS,$body_mail);
        //         if (!$this->sendMail($nom,$email,$object_mail,$body_mail)){
        //                 reject("Une erreur est survenue, Veuillez réessayer utérieurement.");
        //         }
        //         //sauvegarde du sms
        //         $id_user           = $data['id_user'];
        //         $user_type         = $data['user_type'];
        //         $token             = $data['token'];
        //         $template_mail     = "TEMPLATE_CODE_MAIL";
        //         $destinataire_mail = $email;
        //         $codeorlink_mail   = $code;
        //         $data_Mail         = compact("body_mail","template_mail","object_mail","destinataire_mail","codeorlink_mail","user_type","id_user","token");
        //         $this->saveMail($data_Mail);
        //         return true;
        // }

        /**
                *Envoi de lien de validation d'un mail user [CLIENT - PROIETAIRE - ADMINISTRATEUR] pour recuperation du mot de passe
         */
        // public function mailRecoverLink(array $data){
        //         $token       = $data['token'];
        //         $nom         = $data['name'];
        //         $email       = $data['email'];
        //         $object_mail = mb_strtoupper(SOCIETE) . " - LIEN DE REPRISE DU MOT DE PASSE";
        //         $content     = "Cher(e) " . (stylisizeThisData("name", $nom)) . ", <br /> <a href=" . (getEnvironment()["DOMAINE_RCOVER_PSW_URI"]) . "?endpoint=confirmation&traitement=RECOVERPSW&token=" . $token . ">cliquez ici pour renouveler votre mot de passe</a>.";
        //         $body_mail   = str_replace("[content]", $content, TEMPLATE_CLASSIC_MAIL); //Remplace du marquace [content] par le véritable $code
        //         $body_mail   = str_replace("[societe]", SOCIETE, $body_mail);
        //         $body_mail   = str_replace("[contact1]", TELEPHONE1, $body_mail);
        //         $body_mail   = str_replace("[email_info]", EMAIL_INFOS, $body_mail);
        //         if (!$this->sendMail($nom, $email, $object_mail, $body_mail)) {
        //                 reject("Une erreur est survenue, Veuillez réessayer utérieurement.");
        //         }
        //         //sauvegarde du sms
        //         $id_user           = $data['id_user'];
        //         $user_type         = $data['user_type'];
        //         $template_mail     = "TEMPLATE_CLASSIC_MAIL";
        //         $destinataire_mail = $email;
        //         $codeorlink_mail   = $token;
        //         $data_Mail         = compact("body_mail", "template_mail", "object_mail", "destinataire_mail", "codeorlink_mail", "user_type", "id_user", "token");
        //         $this->saveMail($data_Mail);
        //         return true;
        // }
}

?>