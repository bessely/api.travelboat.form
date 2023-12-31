<?php
/**
        *description                       : LA CLASSE user:  CE FICHIER REND POSSIBLE TTES LES MANIPS SUR LA BASE DE DONNEES CONCERNANT LES userS DE SEJOURZEN
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
class Utilisateur{
        /**
                *La creation d'un user
                *@param array $data : les données du user
         */
        public function creer(array $data) :BOOL{
                $req  = DB_INSTANCE->prepare("INSERT INTO  `user`
                                (
                                        NAME_USER,
                                        SURNAME_USER,
                                        EMAIL_USER,
                                        PWD_USER,
                                        CREATED_AT
                                )
                                VALUES
                                (
                                        :NAME_USER,
                                        :SURNAME_USER,
                                        :EMAIL_USER,
                                        :PWD_USER,
                                        :CREATED_AT
                                )");
                $param = array(
                        'NAME_USER'    => trim($data['NAME_USER']),
                        'SURNAME_USER' => trim($data['SURNAME_USER']),
                        'EMAIL_USER'   => trim($data['EMAIL_USER']),
                        'PWD_USER'     => password_hash(trim($data['PWD_USER']), PASSWORD_DEFAULT), // password_hash est une fonction php pour crypter une chaine de caractère en l'occurence notre mpd
                        'CREATED_AT'   => custum_calendar("maintenant"),
                );
                if ($req->execute($param)) {
                        return true;
                }
                return false;
        }

        /**
                *La modification d'un user [mot de passe ou infos user  
                *@param {array}     $data         Cette fonction retourn "true" qd tt c'est bien passé ou false le cas échéant
         */
        public function modifier(array $data) :BOOl{
                $traitement =strtoupper(extractUri()['service']);
                if ($traitement === "UPDATEUSERPSWD"){  // !modification de mot de passe
                        $req   = DB_INSTANCE->prepare("UPDATE  `user`
                                                                SET
                                                                PWD_USER       =:NEW_PWD_USER
                                                                WHERE (ID_USER   =:ID_USER  )
                                                        ");
                        $param = array(
                                'ID_USER'      => $data['ID_USER'],
                                'NEW_PWD_USER' => password_hash(trim($data["NEW_PWD_USER"]), PASSWORD_DEFAULT),
                        );
                }
                if ($traitement === "INITUSERPSWD"){  // !reinitialisation de mot de passe
                        $req   = DB_INSTANCE->prepare("UPDATE  `user`
                                                                SET
                                                                PWD_USER       =:NEW_PWD_USER
                                                                WHERE (ID_USER   =:ID_USER  )
                                                        ");
                        $param = array(
                                'ID_USER'      => $data['ID_USER'],
                                'NEW_PWD_USER' => password_hash(trim($data["NEW_PWD_USER"]), PASSWORD_DEFAULT),
                        );
                }
                if ($traitement === "UPDATEUSEREMAIL"){  // !modification de mail
                        $req   = DB_INSTANCE->prepare("UPDATE  `user`
                                                                SET
                                                                EMAIL_USER     =:EMAIL_USER
                                                                WHERE (ID_USER =:ID_USER  )
                                                        ");
                        $param = array(
                                'ID_USER'    => $data['ID_USER'],
                                'EMAIL_USER' => trim($data['EMAIL_USER'])
                        );
                }
                if ($traitement === "UPDATEUSERINFOS"){ // !modification SANS  changement de mot de passe ni de numero
                        $req  = DB_INSTANCE->prepare("UPDATE  `user`
                                                SET
                                                        NAME_USER    = :NAME_USER,
                                                        SURNAME_USER = :SURNAME_USER
                                                WHERE (ID_USER=:ID_USER)
                                                ");
                        $param = array(
                                'ID_USER'      => $data['ID_USER'],
                                'SURNAME_USER' => trim($data['SURNAME_USER']),
                                'NAME_USER'    => trim($data['NAME_USER']),
                        );
                }
                if ($traitement === "UPDATEUSERPIC"){ // !modification de la photo de profil
                        $req = DB_INSTANCE->prepare("UPDATE  `user` 
                                                                        SET  PHOTO_PROFIL_user=:photo_user 
                                                                        WHERE (id_utilisateur          =:id_utilisateur  )
                                                                ");
                        $param = array(
                                'id_utilisateur'=> $data['id_utilisateur'],
                                'photo_user'    => $data['photo_profil_user'],
                        );
                }
                if ($traitement === "UPDATEUSERSTATE"){ // !modification su status de l'utilisateur
                        $req = DB_INSTANCE->prepare("UPDATE  `user` 
                                                                        SET  STATE_USER=:STATE_USER 
                                                                        WHERE (ID_USER=:ID_USER)
                                                                ");
                        $param = array(
                                'ID_USER'      => $data['ID_USER'],
                                'STATE_USER'   => trim($data['STATE_USER']),
                        );
                }
                if ($req->execute($param)) {return true;}
                return false;
        }

        /** 
                *La liste des users ou recherche dans la table user
                *@param string $ordre
         */
        public function lister(string $ordre, string $STATE_USER) :ARRAY{
                // ! $_POST['search']['value'] provient de datatable
                if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
                        $searchval = "%" . trim(strtolower($_POST['search']['value'])) . "%";
                } else {
                        $searchval = "%";
                }
                $req = DB_INSTANCE->prepare("SELECT * FROM `user` WHERE 
                                                                (
                                                                        (
                                                                                LOWER(NAME_USER   ) LIKE :searchval OR
                                                                                LOWER(SURNAME_USER) LIKE :searchval OR
                                                                                LOWER(EMAIL_USER  ) LIKE :searchval
                                                                        )   AND (STATE_USER<>'DELETED' AND STATE_USER LIKE :STATE_USER)
                                                                ) {$ordre}");
                $req->bindParam(':searchval', $searchval, PDO::PARAM_STR);
                $req->bindParam(':STATE_USER', $STATE_USER, PDO::PARAM_STR);
                if ($req->execute()) {
                        $users= $req->fetchAll(PDO::FETCH_ASSOC);
                        $nbr  = count($users);
                        if ($users) {
                                for ($i=0; $i < $nbr; $i++) { 
                                        $users[$i]["profil"] = $this->getProfilPrivilegeUser($users[$i]['id_user']);
                                }
                                return $users;
                        }
                }
                return [];
        }

        /** 
                *La liste des user ou recherche dans la table user
         */
        public function totalList(string $STATE_USER) : MIXED{
                if (!empty($_POST['search']['value'])) {
                        $searchval = "%" . $_POST['search']['value'] . "%";
                } else {
                        $searchval = "%";
                }
                $req = DB_INSTANCE->prepare("SELECT COUNT(*) AS nbr_user FROM `user` 
                                                                WHERE 
                                                                (
                                                                        (
                                                                                LOWER(NAME_USER   ) LIKE :searchval OR
                                                                                LOWER(SURNAME_USER) LIKE :searchval OR
                                                                                LOWER(EMAIL_USER  ) LIKE :searchval 
                                                                        )   AND (STATE_USER<>'DELETED' AND STATE_USER LIKE :STATE_USER )
                                                                )
                                                        ");
                $req->bindParam(':searchval', $searchval, PDO::PARAM_STR);
                $req->bindParam(':STATE_USER', $STATE_USER, PDO::PARAM_STR);
                if ($req->execute()) {
                        return $req->fetch(PDO::FETCH_ASSOC);
                }
                return [];
        }

        /**recherche d'un user par mail
         * @param {string} dans $data id_ville
         */
        public function rechercher_mail(string $email) :MIXED{
                if (!is_null($email) && !empty($email)) {
                        $req = DB_INSTANCE->prepare('SELECT * FROM `user` WHERE EMAIL_USER =:email');
                        $req->bindParam(':email', $email);
                        if ($req->execute()) {
                                $data_user=$req->fetch(PDO::FETCH_ASSOC);
                                if ($data_user) {
                                        $data_user["profil"] = $this->getProfilPrivilegeUser($data_user['id_user']);
                                        return $data_user;
                                }
                        }
                }
                return [];
        }

        /**
                *recherche d'un user par mail  sauf celui defini - dans id_utilisateur
                *@param {int} $id id_utilisateur  
                *@param {string} dans $data id_ville
         */
        public function rechercher_mail_(string $email, int $id) :MIXED{
                if (!is_null($email) && !empty($email) && !is_null($id) && !empty($id)) {
                        $req = DB_INSTANCE->prepare("SELECT * FROM `user` WHERE EMAIL_USER=:email AND ID_USER  <>:id");
                        $req->bindParam(':email', $email);
                        $req->bindParam(':id', $id);
                        if ($req->execute()) {
                                return $req->fetch(PDO::FETCH_ASSOC);
                        }
                }
                return [];
        }

        /**
                *recherche d'un user par id_utilisateur  
         *@param {int} $id id_utilisateur  
         */
        public function rechercher_id(int $id_utilisateur  ) :MIXED{
                if (!is_null($id_utilisateur  ) && !empty($id_utilisateur  )) {
                        $data_user=[];
                        $req = DB_INSTANCE->prepare('SELECT * FROM `user` WHERE ID_USER  =:id_utilisateur');
                        $req->bindParam(':id_utilisateur', $id_utilisateur  );
                        if ($req->execute()) {
                                $data_user = $req->fetch(PDO::FETCH_ASSOC);
                                if ($data_user) {
                                        $data_user["profil"] = $this->getProfilPrivilegeUser($id_utilisateur);
                                        return $data_user;
                                }
                        }
                }
                return [];
        }

        /**
         * RECUPERE LES PROFILS ET LEUR PRIVILEGE
         * @param int $id_utilisateur
        */
        public function getProfilPrivilegeUser(int $id_utilisateur) :MIXED{
                $req = DB_INSTANCE->prepare('SELECT * FROM `avoir` a
                                                INNER JOIN  profil p ON p.ID_PROFIL= a.ID_PROFIL
                                                WHERE ID_USER  =:id_utilisateur ORDER BY p.NAME_PROFIL'
                );
                $req->bindParam(':id_utilisateur', $id_utilisateur);
                if ($req->execute()) {
                        return $req->fetchAll(PDO::FETCH_ASSOC);
                }
                return false;
        }
        /**
                *UpdateSTate d'un user par id_utilisateur
                *@param {int} $id id_utilisateur  
         */
        public function updateState(int $id_utilisateur, string $state  ){
                $req = DB_INSTANCE->prepare("UPDATE  `user` SET  STATE_USER=:STATE_USER WHERE (ID_USER=:ID_USER)");
                $param = array(
                        'ID_USER'      => $id_utilisateur,
                        'STATE_USER'   => trim($state),
                );
                $req->execute($param);
                return true;
        }

        /**
                *suppression (SOFT DELETE) d'un user par id_utilisateur
                *@param {int} $id id_utilisateur  
         */
        public function supprimer(int $id_utilisateur  ){
                $this->updateState($id_utilisateur,"DELETED");
                return true;
        }


        /**
         *Verifie que le Users est bien logué et à le droit de mener cette action
        *Retrourn un array de Users
        */
        public function isUserAlow(array $userTypeAlow  ){
                require_once "Session.php";
                $Session    = new Session();
                $TOKEN      = controlParams(       $_POST['TOKEN'   ] , "TOKEN"   , "string"  , [32   , 32     ], true);
                $datauser   = $Session->findUserByToken($TOKEN);
                if ($datauser) {
                        if (in_array($datauser['use_profil'],$userTypeAlow)) {
                        return $datauser;
                        }else{
                        reject("Désolé, Ce profil n'est pas autorisé à mener cette action");
                        }
                }else{
                        reject("Il faut être connecté pour mener cette action, cependant votre session a expiré ou est introuvable.");
                }
                return $datauser;
        }
}