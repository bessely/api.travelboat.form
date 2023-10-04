<?php
/** Page    : Traitement des profils et accèes
    @Version   : API.TRAVELBOAT.FORM API 1.0.0
    @Author   : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
    @Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
require_once('src/model/Profil.php');
require_once('src/model/Utilisateurs.php');
$Profil = new Profil();
$User   = new Utilisateur();

/**
 *Appel les fonctions adequoites selon le traitement demandé par le Users
 */
match (strtoupper(extractUri()['service'])) {
    "NEW"        => createProfil(),
    "UPDATE"     => updateProfil(),
    "LIST"       => listProfil(),
    "GETONE"     => getOneProfil(),
    "ATTACHUSER" => attacheProfilToUser(), 
    default      => reject("Le Endpoint [ ".extractUri()['service']." ] est introuvable dans les services de 'profil'"),
};

/**
 *Inscription du Users : envoi de mail de validation
 */
function createProfil(){
    global $Profil;
    $NAME_PROFIL      = controlParams($_POST['NAME_PROFIL'    ], "NAME_PROFIL"    , "string" , [2, 255 ], true);
    $PRIVILEGE_PROFIL = controlParams($_POST['PRIVILEGE_PROFIL' ], "PRIVILEGE_PROFIL" , "array" , [1, 20 ], true);
    if ($Profil->loadProfileByName($NAME_PROFIL)) {
        reject("Désolé, mais un profil ayant le même nom existe déja.");
    }
    controlPrivilegeList($PRIVILEGE_PROFIL);
    if ($Profil->newProfil($NAME_PROFIL,$PRIVILEGE_PROFIL)) {
        response("Le profil ".$NAME_PROFIL." a correctement été créé.");
    }
    reject("Désolé, mais une erreur inconnue est survenue pendant la création du profil. Veuillez réessayez !");
}

function updateProfil(){
    global $Profil;
    $ID_PROFIL        = controlParams(intval($_POST['ID_PROFIL'        ]), "ID_PROFIL"        , "integer" , [1, 200 ], true);
    $NAME_PROFIL      = controlParams(       $_POST['NAME_PROFIL'      ] , "NAME_PROFIL"      , "string"  , [2, 255 ], true);
    $PRIVILEGE_PROFIL = controlParams(       $_POST['PRIVILEGE_PROFIL' ] , "PRIVILEGE_PROFIL" , "array"   , [1, 20  ], true);

    if ($Profil->getOneProfil($ID_PROFIL) && controlPrivilegeList($PRIVILEGE_PROFIL)) {
        if ($oldProfil=$Profil->loadProfileByName($NAME_PROFIL)) {
            if ($oldProfil['id_profil']!==$ID_PROFIL) {
                reject("Désolé, mais un profil ayant le même nom existe déja. Essayez de trouver une autre appelation pour ce profil.");
            }
        }
        if ($Profil->updateProfil($ID_PROFIL,$NAME_PROFIL,$PRIVILEGE_PROFIL)) {
            response("Le profil ".$NAME_PROFIL." a correctement été mis à jour.");
        }
        reject("Désolé, mais une erreur inconnue est survenue pendant la modification du profil. Veuillez réessayez !");
    }
    reject("Désolé, mais il semblerait que se profile n'existe plus.");
}

function getOneProfil(){
    global $Profil;
    $ID_PROFIL    = controlParams(intval($_POST['ID_PROFIL'   ]), "ID_PROFIL"   , "integer", [1, 200], true);
    $infos_profil   = $Profil->getOneProfil($ID_PROFIL);
    if ($infos_profil) {
        $infos_profil['date_add_letter'] = date_to_letter($infos_profil['created_at']);
        response(array('data' => $infos_profil ? $infos_profil  : []));
    }
    reject("Aucun profils trouvé");
}

function listProfil(){
    global $Profil;
    $profils     = $Profil->listProfil(" ORDER BY NAME_PROFIL ASC ");
    if ($profils) {
        for ($i=0; $i < count($profils) ; $i++) { 
            $profils[$i]['date_add_user_letter'] = date_to_letter($profils[$i]['created_at']);
        }
    }
    response(array(
    'message'          => count($profils) . ' profil(s) trouvé(s)',
    'data'             => $profils,
    ));
}

function attacheProfilToUser(){
    global $Profil,$User;
    $PROFIL  = controlParams($_POST['PROFIL'   ], "PROFIL"   , "array", [1, 200], true);
    $ID_USER = controlParams(intval($_POST['ID_USER'   ]), "ID_USER"   , "integer", [1000, 10000000], true);
    if ($User->rechercher_id($ID_USER)) {
        for ($i=0; $i < count($PROFIL) ; $i++) { 
            if (empty($PROFIL[$i]) || !$Profil->getOneProfil($PROFIL[$i])) {
                reject("Le profil N° ".($i+1). " n'est pas reconnu. Veuillez réessayez avec de bonnes valeurs.");
            }
        }
        $Profil->attacheProfilToUser($PROFIL,$ID_USER);
        response("Profil(s) correctement rattaché(s)");
    }
    reject("Désolé, mais il semblerait que cet utilisateur n'existe plus.");
}

function controlPrivilegeList($PRIVILEGE_PROFIL){
    for ($i=0; $i < count($PRIVILEGE_PROFIL) ; $i++) { 
        if ($PRIVILEGE_PROFIL[$i]==="") {
            reject("Le privilège N° ".($i+1). " ne peut pas être une chaine de caractère vide. Veuillez renseignez la valeur.");
        }
    }
    return true;
}
