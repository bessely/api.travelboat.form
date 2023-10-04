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
                        'NAME_USER'    => $data['NAME_USER'],
                        'SURNAME_USER' => $data['SURNAME_USER'],
                        'EMAIL_USER'   => $data['EMAIL_USER'],
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
                                'EMAIL_USER' => $data['EMAIL_USER']
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
                                'SURNAME_USER' => $data['SURNAME_USER'],
                                'NAME_USER'    => $data['NAME_USER'],
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
                if ($req->execute($param)) {return true;}
                return false;
        }

        /** 
                *La liste des users ou recherche dans la table user
                *@param string $ordre
         */
        public function lister(string $ordre) :ARRAY{
                // ! $_POST['search']['value'] provient de datatable
                if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
                        $searchval = "%" . trim(strtolower($_POST['search']['value'])) . "%";
                } else {
                        $searchval = "%";
                }
                $req = DB_INSTANCE->prepare("SELECT * FROM `user` u WHERE 
                                                                (
                                                                        LOWER(NAME_USER) LIKE :searchval OR 
                                                                        LOWER(SURNAME_USER) LIKE :searchval OR 
                                                                        LOWER(EMAIL_USER)  LIKE :searchval 
                                                                ) {$ordre}");
                $req->bindParam(':searchval', $searchval, PDO::PARAM_STR);
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
        public function totalList() : MIXED{
                if (!empty($_POST['search']['value'])) {
                        $searchval = "%" . $_POST['search']['value'] . "%";
                } else {
                        $searchval = "%";
                }
                $req = DB_INSTANCE->prepare("SELECT COUNT(*) AS nbr_user FROM `user` u
                                                                JOIN `avoir` a ON a.ID_USER = u.ID_USER 
                                                                JOIN `profil` p ON p.ID_PROFIL =a.ID_PROFIL
                                                                WHERE 
                                                                (
                                                                        LOWER(NAME_USER) LIKE :searchval OR 
                                                                        LOWER(SURNAME_USER) LIKE :searchval OR 
                                                                        LOWER(EMAIL_USER)  LIKE :searchval 
                                                                )
                                                        ");
                $req->bindParam(':searchval', $searchval, PDO::PARAM_STR);
                if ($req->execute()) {
                        return $req->fetch(PDO::FETCH_ASSOC);
                }
                return [];
        }

        /**
                *Modification de l'etat du compte user
                *@param {int} $id id_utilisateur  
                *@param {string} $etat nouvel etat_user
         */
        public function update_etat_compte(int $id, string $etat): BOOL{
                if ($id > 0) {
                        $req   = DB_INSTANCE->prepare("UPDATE  `user` SET nom_villex=:etat_user WHERE (id_utilisateur  =:id_utilisateur  )");
                        $param = array('id_utilisateur  '  => $id,'etat_user' => $etat);
                        if ($req->execute($param)) {return true;}
                }
                return false;
        }

        /**
                *Réinitialisation du mot de passe user
                *@param {int} $id id_utilisateur  
                *@param {string} $mdp nouveau mot de passe
         */
        public function init_pwd_utilisateur(int $id, string $password): BOOL{
                if ($id > 0) {
                        $req   = DB_INSTANCE->prepare("UPDATE  `user`
                                                        SET
                                                        pwd_utilisateur=:pwd_utilisateur
                                                        WHERE (id_utilisateur  =:id_utilisateur  )
                                                        ");
                        $param = array('id_utilisateur  '  => $id,'pwd_utilisateur' => password_hash(trim($password), PASSWORD_DEFAULT));                                                      
                        if ($req->execute($param)) {return true;}
                }
                return false;
        }

        /**recherche d'un user par mail
         * @param {string} dans $data id_ville
         */
        public function rechercher_mail(string $email) :MIXED{
                if (!is_null($email) && !empty($email)) {
                        $req = DB_INSTANCE->prepare('SELECT * FROM `user` WHERE EMAIL_USER =:email');
                        $req->bindParam(':email', $email);
                        if ($req->execute()) {
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                $data_user=$req->fetch();
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
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return $req->fetch();
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
                *suppression (SOFT DELETE) d'un user par id_utilisateur
                *@param {int} $id id_utilisateur  
         */
        public function supprimer(int $id_utilisateur  ){
                return $this->update_etat_compte((int) $id_utilisateur  , "supprimer");
        }
}