<?php
/** 
        *description                       : LE SCRIPT DE GESTION DES CONNEXIONS [ADMINISTRATEUR - CLIENT - PROPRIETAIRE] POUR TOUT LE PROJET
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
require_once "src/model/Session.php";
require_once "src/model/Utilisateurs.php";
$session = new Session();
$Users   = new Utilisateur();
/**
 *Appel les fonctions adequoites selon le traitement demander par le CLIENT
 */
match (strtoupper(extractUri()['service'])) {
        "LOGIN"   => connexion(),
        "LOGOUT"  => deconnexion(),
        "CONSOLE" => console(),
        default  => reject("Le Endpoint [ ".extractUri()['service']." ] est introuvable dans les services de 'session'"),
};

/**
        *Connexion des utilisateurs à leurs différentes interfaces
        *USER_TYPE est definie dans config/GLOBAL.php c'est un tableau des types d'utilisateur traiter dans le projet
        *ENTITIES est definie dans config/GLOBAL.php c'est un tableau des entités et les noms des colonnes correspondant dans la base de données
 */
function connexion(){
        global $Users;
        $EMAIL_USER = controlParams( $_POST['EMAIL_USER' ] , "EMAIL_USER" , "mail"   , [6 , 250 ], true);
        $PWD_USER   = controlParams( $_POST['PWD_USER'   ] , "PWD_USER"   , "string" , [6 , 50  ], true);
        $PROFIL     = $_POST['PROFIL'   ];
        $user_info  = $Users->rechercher_mail($EMAIL_USER);
        if ($user_info) {
                if (!$user_info['profil']) {
                        reject("Désolé ".$user_info['name_user'].", Aucun profil n'est rattaché a votre compte. Veuillez vous rapprochez d'un administrateur. Connexion Interdite.");
                }
                if (password_verify($PWD_USER, $user_info['pwd_user'])){
                        if ($user_info['state_user']==="ACTIF" || $user_info['state_user']==="LOCK") {
                                if (count($user_info['profil'])>1 && $PROFIL==="" ) {
                                        response(array(
                                                'message' => 'Selectionner un profil pour continuer. ',
                                                'profile' => $user_info['profil']
                                        ));
                                }else{
                                        if ($PROFIL!=="") {
                                                $PROFIL     = controlParams( $_POST['PROFIL'   ] , "PROFIL"   , "string" , [3 , 50  ], true);
                                                $list_profil= array_column($user_info['profil'],"name_profil");
                                                if (!in_array($PROFIL,$list_profil)) {
                                                        reject("Désolé, Vous ne pouvez pas continuer avec un profil sur lequel vous n'êtes pas abilité.");
                                                }
                                        }else{
                                                $PROFIL=$user_info['profil'][0]['name_profil'];
                                        }
                                }
                                creatSession( $user_info, $PROFIL );
                        }else{
                                reject("Votre compte est désactivé. Veuillez vous référez à un administrateur.");
                        }
                }
        }
        reject("Mot de passe ou email incorrect.");
}

/**
        *déconnexion des utilisateurs à leurs différentes interfaces
 */
function deconnexion(){
        $token = controlParams( $_POST['TOKEN' ] , "TOKEN" , "string"   , [32 , 32 ], true);
        closeSession($token);
}

/**
        *@param array $userdata : les données de l'utilisateur
        *@return session et ouvre une session 
 */
function creatSession(array $userdata, string $PROFIL ): void{
        global $session;
        //tentative d'ecriture de la session
        $token=$session->write($userdata,$PROFIL);
        if (!$token){
                reject('la session n\'a pas pu être démarrée : erreur interne.');
        }
        session_destroy();// pour detruire la session en cours si il y'a na une | voir public/index.php [if (!session_id()) {session_start();};]
        session_start();
        $_SESSION["token"] = $token;
        response(array(
                'message' => 'Bienvenue ' . $userdata['name_user'],
                'token'   => $token,
                'user'    => $userdata,
                'profil'  => $PROFIL
        ));
}

/**
        *Ferme la session  d'un utilisateur donné
        *@param string $token : le token de la session à cloturer
 */
function closeSession(string $token ){
        global $session;
        $user_info=$session->findUserByToken($token);
        if (!$user_info) {
                reject('Token inconnu. La session n\'a pas pu être cloturée');
        }
        $cloture = $session->close($token);
        //dans tts les cas je detruit la session pour liberer l'utilisateur
        session_destroy(); 
        if (!$cloture) {
                //Ont n'a pas pu clore la session en base
                reject('la session n\'a pas pu être fermée : erreur interne.');
        }
        response(array(
                'message' => 'A bientôt ' . $user_info['name_user'],
                'token'   => [],
        ));
}


/**
        *Ferme la session  d'un utilisateur donné
        *@param string $token : le token de la session à cloturer
 */
function console(){
        global $session;
        $DATE1         = controlParams(       $_POST['DATE1'         ]  , "Date de Début" , "date"    , [10 , 10       ], true );
        $DATE2         = controlParams(       $_POST['DATE2'         ]  , "Date de Fin"   , "date"    , [10 , 10       ], true );
        $SESSION_STATE = controlParams(       $_POST['SESSION_STATE' ]  , "SESSION_STATE" , "string"  , [1  , 10       ], true);
        $USE_PROFIL    = controlParams(       $_POST['USE_PROFIL'    ]  , "USE_PROFIL"    , "string"  , [1  , 10       ], true);
        $start         = controlParams(intval($_POST['START'         ]) , "START"         , "integer" , [0  , 10000000 ], true );
        $length        = controlParams(intval($_POST['LENGTH'        ]) , "LENGTH"        , "integer" , [1  , 100      ], true );
        $ordre  = " ORDER BY LAST_DATE_CONNECT DESC LIMIT $length " . ($start == 0 ? "" : "OFFSET " . $start);
        if (strtotime($DATE1) > strtotime($DATE2)){
                reject("La date de Début ne peut pas être supérieure à la date de Fin ");
        }
        $data   = $session->list(compact('DATE1','DATE2','ordre','SESSION_STATE','USE_PROFIL'));
        $dataT  = $session->listTotal(compact('DATE1','DATE2','ordre','SESSION_STATE','USE_PROFIL'));
        response(array(
                'message'         => count($data)." session(s) chargée(s)",
                'data'            => $data,
                'recordsTotal'    => count($data),
                'recordsFiltered' => $dataT["nbr"],
        ));
}