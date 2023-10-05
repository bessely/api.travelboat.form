<?php

/**
 *description                       : LE SCRIPT DE GESTION DES UsersS POUR TOUT LE PROJET
 *@Version                          : API.TRAVELBOAT.FORM API 1.0.0
 *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
 *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */

require_once "src/model/Utilisateurs.php";
require_once "src/model/Session.php";
$Users      = new Utilisateur();
$Session    = new Session();
/**
 *Appel les fonctions adequoites selon le traitement demandé par le Users
 */
match (strtoupper(extractUri()['service'])) {
  "NEW"             => createUsers(),
  "UPDATEUSERINFOS" => updateUserInfos(),
  "UPDATEUSERPSWD"  => updateUserPswd(),
  "INITUSERPSWD"    => initUserPswd(),
  "UPDATEUSERSTATE" => updateUserState(),
  "UPDATEUSEREMAIL" => updateUserEmail(),
  "LIST"            => listUtilisateur(),
  "GETONE"          => getOneUtilisateur(),
  default           => reject("Le Endpoint [ ".extractUri()['service']." ] est introuvable dans les services de 'user'"),
};

/**
 *Inscription du Users : envoi de mail de validation
 */
function createUsers(){
  global $Users;
  $NAME_USER    = controlParams($_POST['NAME_USER'    ], "NAME_USER"    , "string" , [2, 50 ], true);
  $SURNAME_USER = controlParams($_POST['SURNAME_USER' ], "SURNAME_USER" , "string" , [2, 255 ], true);
  $EMAIL_USER   = controlParams($_POST['EMAIL_USER'   ], "EMAIL_USER"   , "mail"   , [6, 255 ], true);
  $PWD_USER     = controlParams($_POST['PWD_USER'     ], "PWD_USER"     , "string" , [6, 50  ], true);
  if ($Users->rechercher_mail($EMAIL_USER)) {
    reject("Désolé, mais il semblerait que ce email soit déjà inscrit.");
  }
  $DATA_Users   = compact("NAME_USER", "SURNAME_USER", "EMAIL_USER", "PWD_USER");
  if ($Users->creer($DATA_Users)) {
    response("Correctement inscrit et activé !");
  }
}

function updateUserInfos(){
  global $Users;
  $ID_USER      = controlParams(intval($_POST['ID_USER'      ]), "ID_USER"      , "integer" , [1000, 100000 ], true);
  $NAME_USER    = controlParams(       $_POST['NAME_USER'    ] , "NAME_USER"    , "string"  , [2   , 50     ], true);
  $SURNAME_USER = controlParams(       $_POST['SURNAME_USER' ] , "SURNAME_USER" , "string"  , [2   , 255    ], true);

  if ($Users->rechercher_id($ID_USER)) {
      if ($Users->modifier(compact('ID_USER','NAME_USER','SURNAME_USER'))) {
          response("Les infmations de l'utilisateur ".$NAME_USER." ont correctement été mis à jour.");
      }
      reject("Désolé, mais une erreur inconnue est survenue pendant la modification des informations. Veuillez réessayez !");
  }
  reject("Désolé, mais il semblerait que cet utilisateur n'existe plus.");
}

function updateUserPswd(){
  global $Users;
  $ID_USER      = controlParams(intval($_POST['ID_USER'      ]), "ID_USER"      , "integer" , [1000, 100000 ], true);
  $OLD_PWD_USER = controlParams(       $_POST['OLD_PWD_USER' ] , "OLD_PWD_USER" , "string"  , [6   , 50     ], true);
  $NEW_PWD_USER = controlParams(       $_POST['NEW_PWD_USER' ] , "NEW_PWD_USER" , "string"  , [6   , 50     ], true);

  if ($userInfos=$Users->rechercher_id($ID_USER)) {
    if ($NEW_PWD_USER!==$OLD_PWD_USER) {
      if (password_verify($OLD_PWD_USER, $userInfos['pwd_user'])) {
        if ($Users->modifier(compact('ID_USER','NEW_PWD_USER'))) {
            response("Le mot de passe a correctement été mis à jour.");
        }
        reject("Désolé, mais une erreur inconnue est survenue pendant la modification du mot de passe. Veuillez réessayez !");
      }else{
          reject("mot de passe est incorrect.");
      }
    }else{
      reject("Les mots de passe ne peuvent pas être identitiques.");
    }
  }
  reject("Désolé, mais il semblerait que cet utilisateur n'existe plus.");
}

function initUserPswd(){
  global $Users;
  $ID_USER = controlParams(intval($_POST['ID_USER' ]), "ID_USER" , "integer" , [1000 , 100000 ], true);
  $Users->isUserAlow(array("SUPER ADMIN"));
  if ($userInfos=$Users->rechercher_id($ID_USER)) {
        $NEW_PWD_USER=random_code(6);
        if ($Users->modifier(compact('ID_USER','NEW_PWD_USER'))) {
          response(array(
            'message'   => 'Le mot de passe a correctement été réinitialisé.',
            'data'      => $NEW_PWD_USER,
          ));
        }
        reject("Désolé, mais une erreur inconnue est survenue pendant la modification du mot de passe. Veuillez réessayez !");
  }
  reject("Désolé, mais il semblerait que cet utilisateur n'existe plus.");
}

function updateUserState(){
  global $Users;
  $ID_USER    = controlParams(intval($_POST['ID_USER'    ]), "ID_USER"    , "integer" , [1000 , 100000 ], true);
  $STATE_USER = controlParams(      ($_POST['STATE_USER' ]), "STATE_USER" , "string"  , [3    , 50     ], true);
  isUserAlow(array("SUPER ADMIN"));
  if ($userInfos=$Users->rechercher_id($ID_USER)) {
        if ($Users->modifier(compact('ID_USER','STATE_USER'))) {
          response(array(
            'message'   => "L'état de l'utilisateur est passé à ".$STATE_USER,
          ));
        }
        reject("Désolé, mais une erreur inconnue est survenue pendant la modification du l'état de l'utilisateur. Veuillez réessayez !");
  }
  reject("Désolé, mais il semblerait que cet utilisateur n'existe plus.");
}

function updateUserEmail(){
  global $Users;
  $ID_USER    = controlParams(intval($_POST['ID_USER'    ]), "ID_USER"    , "integer" , [1000 , 100000 ], true);
  $EMAIL_USER = controlParams(       $_POST['EMAIL_USER' ] , "EMAIL_USER" , "mail"  , [6    , 250    ], true);

  if ($userInfos=($Users->rechercher_id($ID_USER))) {
    if (!$Users->rechercher_mail_($EMAIL_USER,$ID_USER)) {
      if ($Users->modifier(compact('ID_USER','EMAIL_USER'))) {
          response("Le mail de l'utilisateur ".$userInfos['name_user']." a correctement été mis à jour.");
      }
      reject("Désolé, mais une erreur inconnue est survenue pendant la modification du mail. Veuillez réessayez !");
    }
    reject("Cet email appartient déjà a un autre utilisateur.");
  }
  reject("Désolé, mais il semblerait que cet utilisateur n'existe plus.");
}

function getOneUtilisateur(){
    global $Users;
    $id_user    = controlParams(intval($_POST['ID_USER'   ]), "ID_USER"   , "integer", [1000, 10000000], true);
    $infos_user     = $Users->rechercher_id($id_user);
    if ($infos_user) {
        $infos_user['date_add_user_letter'] = date_to_letter($infos_user['created_at']);
    }
    response(array('data' => $infos_user ?? []));
}

function listUtilisateur(){
  global $Users;
  $start      = controlParams(intval($_POST['START'     ]),"START"     , "integer",[0,10000000],true);
  $length     = controlParams(intval($_POST['LENGTH'    ]),"LENGTH"    , "integer",[0,1000    ],true);
  $STATE_USER = controlParams(      ($_POST['STATE_USER']),"STATE_USER", "string" ,[0,1000    ],true);
  $infos_client     = $Users->lister(" ORDER BY NAME_USER ASC LIMIT $length " . ($start == 0 ? "" : "OFFSET " . $start) . " ",$STATE_USER);
  $infos_client_all = $Users->totalList($STATE_USER);
  response(array(
    'message'          => count($infos_client) . ' utilisateur(s) trouvé(s)',
    'data'             => $infos_client,
    'recordsTotal'     => count($infos_client),
    'recordsFiltered'  => $infos_client_all["nbr_user"],
  ));
}