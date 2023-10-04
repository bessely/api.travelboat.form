<?php

/**
 *description                       : LE SCRIPT DE GESTION DES MATELOTS POUR TOUT LE PROJET
 * @Version                          : API.TRAVELBOAT.FORM API 1.0.0
 *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
 *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */


// $Users      = new Matelot();
/**
 *Appel les fonctions adequoites selon le traitement demandé par le Users
 */
match (strtoupper(extractUri()['service'])) {
    "NEW"        => newMatelot(),
    "UPDATE"     => GetOneMatelot(),
    "LIST"       => listMatelot(),
    // "GETONE"     => GetOneMatelot(),
    // "PUTFILE"    => GetOneMatelot(),
    // "GETONEFILE" => GetOneMatelot(),
    default  => reject("Le Endpoint [ ".extractUri()['service']." ] est introuvable dans les services de 'matelot'"),
};

/**
 *Inscription du Matelot
 */
function newMatelot(){
    global $Users;
    $ID_MATELOT      = controlParams(intval($_POST['ID_MATELOT'     ]), "ID_MATELOT"     ,"integer",[1,90000],true);
    $NAME_MATELOT    = controlParams(       $_POST['NAME_MATELOT'   ] , "NAME_MATELOT"   ,"string" ,[1,255  ],true);
    $SURNAME_MATELOT = controlParams(       $_POST['SURNAME_MATELOT'] , "SURNAME_MATELOT","string" ,[1,255  ],true);
    $EMAIL_MATELOT   = controlParams(       $_POST['EMAIL_MATELOT'  ] , "EMAIL_MATELOT"  ,"mail"   ,[1,255  ],true);
    $PHONE_MATELOT   = controlParams(       $_POST['PHONE_MATELOT'  ] , "PHONE_MATELOT"  ,"string" ,[8,20   ],true);
}
