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
    public function write(array $data) :MIXED{
        $token              = token(32); // !token session 32 digit
        $session_precedante = $this->checkUserSession($data['id_user']);
        if ($session_precedante) {
            $req = DB_INSTANCE->prepare("UPDATE  `session`
                SET
                    TOKEN               = :last_token,
                    LAST_DATE_CONNECT   = :last_date_connect,
                    LAST_DATE_DECONNECT = :last_date_deconnexion,
                    DATE_LIMIT_SESSION  = :date_limit_session,
                    SESSION_STATE       = :session_state,
                    NBR_VISITE          = :nbr_visite
                WHERE ( ID_USER=:id_user)
                ");
                $param = array(
                    'last_token'           => $token, // token session 
                    'last_date_connect'    => custum_calendar("maintenant"),
                    'last_date_deconnexion'=> null,
                    'date_limit_session'   => custum_calendar("24_heure"), // date du jour + 24_heure
                    'session_state'        => "open",
                    'nbr_visite'           => 1 + intval($session_precedante['nbr_visite']), 
                    'id_user'              => $data["id_user"],
                ); 
                if ($req->execute($param)) {return $token;}
                return false;
        }else{  //1ere visite
            $req = DB_INSTANCE->prepare("INSERT INTO  `session`
                (
                    TOKEN,
                    LAST_DATE_CONNECT,
                    DATE_LIMIT_SESSION,
                    ID_USER,
                    SESSION_STATE,
                    NBR_VISITE
                )
                VALUES
                (
                    :last_token,
                    :last_date_connect,
                    :date_limit_session,
                    :id_user,
                    :session_state,
                    :nbr_visite
                )");
                $param = array(
                    'last_token'         => $token, // token session
                    'last_date_connect'  => custum_calendar("maintenant"),
                    'date_limit_session' => custum_calendar("24_heure"), // date du jour + 24_heure
                    'id_user'            => $data["id_user"],
                    'session_state'      => "open",
                    'nbr_visite'         => 1,
                ); 
                if ($req->execute($param)) {return $token;}
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
    public function checkUserSession(string $id_user){
        $req= DB_INSTANCE->prepare("SELECT * FROM `session` JOIN user ON user.ID_USER =session.ID_USER WHERE session.ID_USER=:id_user");
        $req->bindParam(':id_user', $id_user);
        if ($req->execute()) {
            return $req->fetch(PDO::FETCH_ASSOC);
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
                                        WHERE (session.LAST_DATE_CONNECT>=:DT1 AND session.LAST_DATE_CONNECT<=:DT2 ) AND SESSION_STATE LIKE :STATEX 
                                        {$data['ordre']}
                                    ");
        $req->bindParam(':DT1'   , $data['DATE1'        ]);
        $req->bindParam(':DT2'   , $data['DATE2'        ]);
        $req->bindParam(':STATEX', $data['SESSION_STATE']);
        if ($req->execute()) {
            return $req->fetchAll(PDO::FETCH_ASSOC);
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
                                        WHERE (session.LAST_DATE_CONNECT>=:DT1 AND session.LAST_DATE_CONNECT<=:DT2 ) AND SESSION_STATE LIKE :STATEX 
                                    ");
        $req->bindParam(':DT1'   , $data['DATE1'        ]);
        $req->bindParam(':DT2'   , $data['DATE2'        ]);
        $req->bindParam(':STATEX', $data['SESSION_STATE']);
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
                return $this->checkUserSession($session_user['id_user']);
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