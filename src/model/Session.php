<?php
/**     
        *description                       : LA CLASSE DES SESSIONS:  CE FICHIER REND POSSIBLE TTES LES MANIPS SUR LA BASE DE DONNEES CONCERNANT LES SESSIONS DE SEJOURZEN
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */

class Session{
    /** 
            *Écriture d'une nouvelle session : lorsqu'une session existe déjà elle est modifiée 
        *@param {int} dans $data id_user
        *@param {string} dans $data user_type
        *@param {string} dans $data ip
    */
    public function write(array $data,string $profil) :MIXED{
        $token              = token(32); // !token session 32 digit
        $session_precedante = $this->checkUserSession($data['id_user'],$profil);
        $req                = "";
        if ($session_precedante) {
                $req = DB_INSTANCE->prepare("UPDATE  `session`
                    SET
                        TOKEN               =:token,
                        LAST_DATE_CONNECT   =:last_date_connect,
                        DATE_LIMIT_SESSION  =:date_limit_session,
                        SESSION_STATE       =:session_state,
                        NBR_VISITE          =:nbr_visite
                    WHERE ( ID_USER=:id_user AND USE_PROFIL=:use_profil)
                    ");
        }else{  //1ere visite
            $session_precedante['nbr_visite']=0;
            $req = DB_INSTANCE->prepare("INSERT INTO  `session`
                (
                    TOKEN,
                    LAST_DATE_CONNECT,
                    DATE_LIMIT_SESSION,
                    SESSION_STATE,
                    NBR_VISITE,
                    ID_USER,
                    USE_PROFIL
                )
                VALUES
                (
                    :token,
                    :last_date_connect,
                    :date_limit_session,
                    :session_state,
                    :nbr_visite,
                    :id_user,
                    :use_profil
                )");
        }
        $param = array(
            'token'                => $token, // token session 
            'last_date_connect'    => custum_calendar("maintenant"),
            'date_limit_session'   => custum_calendar("24_heure"), // date du jour + 24_heure
            'session_state'        => "open",
            'nbr_visite'           => 1 + intval($session_precedante['nbr_visite']), 
            'id_user'              => $data["id_user"],
            'use_profil'           => $profil,
        );
        if ($req->execute($param)) {
            require_once 'Utilisateurs.php';
            $Users=new Utilisateur();
            $Users->updateState($data['id_user'],"ACTIF");
            return $token;
        }
        return false;
    }

    /**
            *Fermeture d'une session 
            *@param {int} dans $data id_user
            *@param {string} dans $data user_type
    */
    public function close(string $token) :BOOL{
        $req = DB_INSTANCE->prepare("UPDATE `session`
            SET
                LAST_DATE_DECONNECT = :last_date_deconnexion,
                SESSION_STATE       = :session_state
            WHERE ( TOKEN=:token )
        ");
        $param = array(
            'last_date_deconnexion'=> custum_calendar("maintenant"),
            'session_state'        => "close",
            'token'                => $token,
        ); 
        if ($req->execute($param)) {return true;}
        return false;
    }

    /**
                *Chargement d'un utilisateur d'un id_user et du user_type
                *@param {int} dans $data id_user
                *@param {string} dans $data user_type
    */
    public function checkUserSession(int $id_user,string $profil){
        $req= DB_INSTANCE->prepare("SELECT * FROM `session` JOIN user ON user.ID_USER =session.ID_USER WHERE session.ID_USER=:id_user AND session.USE_PROFIL=:profil");
        $req->bindParam(':id_user', $id_user);
        $req->bindParam(':profil', $profil);
        if ($req->execute()) {
            $sessionUser=$req->fetch(PDO::FETCH_ASSOC);
            if ($sessionUser) {
                require_once 'Utilisateurs.php';
                $Users=new Utilisateur();
                $sessionUser['profil']=$Users->getProfilPrivilegeUser($sessionUser['id_user']);
                return $sessionUser;
            }
        }
        return false;
    }

    /**
                *session log
                *@param {int} dans $data id_user
                *@param {string} dans $data user_type
    */
    public function list(array $data){
        $data['DATE1']=$data['DATE1']." 00:00:00";
        $data['DATE2']=$data['DATE2']." 23:59:59";
        $req= DB_INSTANCE->prepare("SELECT * FROM `session`
                                        INNER JOIN user ON user.ID_USER =session.ID_USER 
                                        WHERE ((session.LAST_DATE_CONNECT>=:DT1 AND session.LAST_DATE_CONNECT<=:DT2 ) AND (SESSION_STATE LIKE :STATEX  AND USE_PROFIL LIKE :STATEXX)) 
                                        {$data['ordre']}
                                    ");
        $req->bindParam(':DT1'        , $data['DATE1'         ]);
        $req->bindParam(':DT2'        , $data['DATE2'         ]);
        $req->bindParam(':STATEX'     , $data['SESSION_STATE' ]);
        $req->bindParam(':STATEXX'    , $data['USE_PROFIL'    ]);
        if ($req->execute()) {
            $sessionUser=$req->fetchAll(PDO::FETCH_ASSOC);
            if ($sessionUser) {
                for ($i=0; $i < count($sessionUser) ; $i++) { 
                    require_once 'Utilisateurs.php';
                    $Users=new Utilisateur();
                    $sessionUser[$i]['profil']=$Users->getProfilPrivilegeUser($sessionUser[$i]['id_user']);
                }
                return $sessionUser;
            }
        }
        return [];
    }

    /**
                *session log
                *@param {int} dans $data id_user
                *@param {string} dans $data user_type
    */
    public function listTotal(array $data){
        $data['DATE1']=$data['DATE1']." 00:00:00";
        $data['DATE2']=$data['DATE2']." 23:59:59";
        $req= DB_INSTANCE->prepare("SELECT COUNT(*)  as nbr FROM `session`
                                        INNER JOIN user ON user.ID_USER =session.ID_USER 
                                        WHERE ((session.LAST_DATE_CONNECT>=:DT1 AND session.LAST_DATE_CONNECT<=:DT2 ) AND (SESSION_STATE LIKE :STATEX  AND USE_PROFIL LIKE :STATEXX)) 
                                    ");
        $req->bindParam(':DT1'        , $data['DATE1'         ]);
        $req->bindParam(':DT2'        , $data['DATE2'         ]);
        $req->bindParam(':STATEX'     , $data['SESSION_STATE' ]);
        $req->bindParam(':STATEXX'    , $data['USE_PROFIL'    ]);
        if ($req->execute()) {
            return $req->fetch(PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
                *Chargement d'une session dans à partir d'un token donné
                *@param {string} token
    */
    public function findUserByToken(string $token){
        $req= DB_INSTANCE->prepare("SELECT * FROM `session` WHERE (TOKEN=:token AND SESSION_STATE='open') ");
        $req->bindParam(':token', $token);
        if ($req->execute()) {
            $session_user= $req->fetch(PDO::FETCH_ASSOC);
            if ($session_user) {
                return $this->checkUserSession($session_user['id_user'],$session_user['use_profil']);
            }
        }
        return [];
    }

    /**
                *Chargement d'une session dans à partir du token
    */
    public function findUserInSession() : ARRAY{
        if ( isset($_SESSION['token']) && !empty($_SESSION['token'])){
                return $this->findUserByToken($_SESSION['token']); 
        }
        return [];
    }
}

?>