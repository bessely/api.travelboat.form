<?php

/**
 * Description                       : Traitement des appels sur les différents  endpoint [CE FICHIER EST LE POINT D'ENTRER DE TOUT LES REQUEST CLIENT]
 * LE SEUL LIEN EXPOSER COTER CLIENT, c'est L'api qui relie le back au front 
 * @Version                          : API.TRAVELBOAT.FORM API 1.0.0
 * @Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
 * Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
if (!session_id()) {
        session_start();
};
require_once("public/header.php");
require_once("database/Database.php");
require_once("services/GLOBAL.php");
require_once("services/utilities.php");
if (catchRequest()) {
        require_once("services/calendar.php");
        require_once(catchRequest());
}

