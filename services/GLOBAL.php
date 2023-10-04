<?php

        /**
                *description                       : MES CONSTANTES ET VARIABLES GLOBALES
        */
        define("DB_INSTANCE", Database::getConnexion()); // une instance de connection a la base qui sera utiliser a chaque session
        define("VERSION", "1.0.0");
        define("LICENCE", "free");
        define("APPNAME", "API.TRAVELBOAT.FORM");
        /** NOTIFIY PAIEMENT URI*/
        define("NOTIFY_URL", "http://localhost/pay-notification.php");
        define("NOTIFY_URL_LOCAL", "http://localhost/travelboat.form");
        define("NOTIFY_URL_PRO","https://dev.clinik.ci/patient/pay-notification.php");
        define("RETURN_URL", "http://localhost/pay-notification.php");
        /** DEV DOMAINE URI */
        define("LOCALHOST", "http://localhost/travelboat.form");
        define("ACCUEIL", "http://localhost/travelboat.form");
        define("ACCUEIL_PRO", "http://localhost/travelboat.form");
        /** PROJECT USER TYPE */
        define("USER_TYPE", array(
                'Admin'   => '1',
                'matelot' => '2',
                'user'    => '3',
                'filiale' => '4',
        ));
        define("USER_TYPE_x", array(
                '1' => 'Admin',
                '2' => 'matelot',
                '3' => 'user',
                '4' => 'filiale',
        ));
        // /** PAIEMENT CONFIG FROM DATABASE*/
        define("SOCIETE", "SUPERMARITIME SA");
        define("SOCIETESMS",  "SUPERMARITIME SA");
        define("EMAIL_CONTACT", "contact@clinik.ci");
        // /** SMTP CONFIG FROM DATABASE*/
        define("EMAIL_SMTP", "mail.clinik.ci");
        define("PORT_SMTP", "587");
        define("EMAIL_ENV", "no-reply@clinik.ci");
        define("MDP_SMTP", "TQQiyZ0d=MG#");
        define("PROTOCOL_EXCH_SMTP", "TSL");
        // /** TEMPLATE MAIL CONFIG FROM DATABASE*/
