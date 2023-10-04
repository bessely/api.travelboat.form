<?php
/** 
        *description                       : LA CLASSE ADMINISTRATEUR:  CE FICHIER REND POSSIBLE TTES LES MANIPS SUR LA BASE DE DONNEES CONCERNANT LES ADMINISTRATEURS DE SEJOURZEN
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */

        // On inclut les fichiers de configuration et d'accès aux données
        require_once ('Session.php');
        $session = new Session();

class Administrateur{
        /**
                *La creation d'un Administrateur
         *@param {string} dans $data id_admin
         *@param {string} dans $data NOM_PRE_ADMIN
         *@param {string} dans $data email_admin
         *@param {string} dans $data mdp_admin
         *@return {array} retourne un tableau contenant les informations de l'administrateur crée
         */
        public function creer(array $data) :MIXED{
                $req = Database::getConnexion()->prepare("INSERT INTO  `administrateur`
                                (
                                        ID_ADMIN,
                                        NOM_PRE_ADMIN,
                                        EMAIL_ADMIN,
                                        MDP_ADMIN,
                                        DATE_CREA_ADMIN,
                                        STATUS_ADMIN
                                )
                                VALUES
                                (
                                        :id_admin,
                                        :nom_pre_admin,
                                        :email_admin,
                                        :mdp_admin,
                                        :datecrea_admin,
                                        :etat_admin
                                )");
                $param = array(
                        'id_admin'          => NULL, //le numero etant générer automatiquement depuis la configuration de la BD (numero auto)
                        'nom_pre_admin'     => $data['nom_pre_admin'],
                        'email_admin'       => $data['email_admin'],
                        'mdp_admin'         => password_hash($data['mdp_admin'], PASSWORD_DEFAULT), // password_hash est une fonction php pour crypter une chaine de caractère en l'occurence notre mpd
                        'datecrea_admin'    => custum_calendar("maintenant"),
                        'etat_admin'        => isset($_POST['send_mail_option']) ? "mail" : "actif" // si les accès doivent etre envoyer par mail le compte neccesitera d'etre activé via mail sinon actif par defaut
                );

                if ($req->execute($param)) {
                        $req = Database::getConnexion()->prepare('SELECT ID_ADMIN FROM `administrateur` ORDER BY DATE_CREA_ADMIN DESC LIMIT 1');
                        if ($req->execute()) {
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return $req->fetch();
                        }
                }
                return false;
        }

        /**
                *La modification d'un administrateur [mot de passe ou infos administrateur]
         *@param {string} dans $data id_admin
         *@param {string} dans $data nom_pre_admin
         *@param {string} dans $data email_admin
         *@param {string} dans $data mdp_admin
         *@param {string} dans $data mdp_admin2 une fois sur 2
         *@return {bool} retourne true si la modification a été effectuée sinon false
         */
        public function modifier(array $data) :BOOL{
                global $traitement;
                if ($traitement === "PASSWORD UPDATE" ){  //!modification avec changement de mot de passe
                        $req = Database::getConnexion()->prepare("UPDATE  `administrateur`
                                                        SET
                                                        MDP_ADMIN =:mdp_admin
                                                        WHERE (ID_ADMIN=:id_admin)
                                                ");
                        $param = array(
                                'id_admin'  => $data['id_admin'],
                                'mdp_admin' => password_hash(trim($data["mdp_admin2"]), PASSWORD_DEFAULT),
                        );
                }
                if ($traitement === "MODIFICATION"){ //!modification SANS  changement de mot de passe
                        $req = Database::getConnexion()->prepare("UPDATE  `administrateur`
                                                SET
                                                NOM_PRE_ADMIN                 =:nom_pre_admin,
                                                EMAIL_ADMIN                   =:email_admin
                                                WHERE (ID_ADMIN=:id_admin)
                                                ");
                        $param = array(
                                'id_admin'      => $data['id_admin'],
                                'nom_pre_admin' => $data['nom_pre_admin'],
                                'email_admin'   => $data['email_admin'],
                        );
                }
                if ($req->execute($param)) {return true;}
                return false;
        }

        /** La liste des administrateurs ou recherche dans la table administrateurs
         *@param {int} $id id_admin ou un token
         *@param {string} $etat etat_admin
         *@return {array} retourne un tableau contenant les informations de l'administrateur
         */
        public function lister( string $ordre) : MIXED{
                global $session;
                // ! $_POST['search']['value'] provient de datatable
                if (!empty($_POST['search']['value'])) {
                        $searchval = "%" . $_POST['search']['value'] . "%";
                } else {
                        $searchval = "%";
                }
                $LOGUSER = $session->findUserInSession();
                if ($LOGUSER){
                        $req     = Database::getConnexion()->prepare("SELECT * FROM `administrateur` 
                                                        WHERE ( (NOM_PRE_ADMIN LIKE :searchval OR EMAIL_ADMIN LIKE :searchval ) 
                                                        AND STATUS_ADMIN<>'DELETED' AND ID_ADMIN<>:id_admin ) {$ordre}");
                        $req->bindParam(':searchval', $searchval);
                        $req->bindParam(':id_admin', $LOGUSER['id_admin']);
                        if ($req->execute()){
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return $req->fetchAll();
                        }
                }else{
                        $req     = Database::getConnexion()->prepare("SELECT * FROM `administrateur` 
                                                        WHERE ( (NOM_PRE_ADMIN LIKE :searchval OR EMAIL_ADMIN LIKE :searchval ) 
                                                        AND STATUS_ADMIN<>'DELETED' ) {$ordre}");
                        $req->bindParam(':searchval', $searchval);
                        if ($req->execute()){
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return $req->fetchAll();
                        }
                }
                return [];
        }

        /** 
                *La liste des administrateurs ou recherche dans la table administrateurs
                *@return {int} retourne le nombre total d'administrateurs
         */
        public function totalList() : MIXED{
                global $session;
                if (!empty($_POST['search']['value'])) {
                        $searchval = "%" . $_POST['search']['value'] . "%";
                } else {
                        $searchval = "%";
                }
                $LOGUSER = $session->findUserInSession();
                if ($LOGUSER){
                        $req     = Database::getConnexion()->prepare("SELECT * FROM `administrateur` 
                                                        WHERE ( (NOM_PRE_ADMIN LIKE :searchval OR EMAIL_ADMIN LIKE :searchval ) 
                                                        AND STATUS_ADMIN<>'DELETED' AND ID_ADMIN<>:id_admin ) ");
                        $req->bindParam(':searchval', $searchval);
                        $req->bindParam(':id_admin', $LOGUSER['id_admin']);
                        if ($req->execute()){
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return count($req->fetchAll());
                        }
                }else{
                        $req     = Database::getConnexion()->prepare("SELECT * FROM `administrateur` 
                                                        WHERE ( (NOM_PRE_ADMIN LIKE :searchval OR EMAIL_ADMIN LIKE :searchval ) 
                                                        AND STATUS_ADMIN<>'DELETED' )");
                        $req->bindParam(':searchval', $searchval);
                        if ($req->execute()){
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return $req->rowCount();
                        }
                }
                return 0;
        }

        /**
         * Modification de l'etat du compte Administrateur
         *@param {int} $id id_admin
         *@param {string} $etat etat_admin
         *@return {bool} retourne true si la modification a été effectuée sinon false
         */
        public function update_etat_compte(int $id, string $etat) :BOOL{
                if ($id > 0) {
                        $req   = Database::getConnexion()->prepare("UPDATE  `administrateur` SET STATUS_ADMIN=:etat_admin WHERE (ID_ADMIN=:id_admin)");
                        $param = array('id_admin'  => $id,'etat_admin' => $etat);
                        if ($req->execute($param)) {return true;}
                }
                return false;
        }

        /**
         * Réinitialisation du mot de passe Administrateur
        *@param {int} $id id_admin
        *@param {string} $mdp nouveau mot de passe
        *@return {bool} retourne true si la modification a été effectuée sinon false
         */
        public function init_mdp_admin(int $id, string $password): BOOL{
                if ($id > 0) {
                        $req = Database::getConnexion()->prepare("UPDATE  `administrateur`
                                                        SET
                                                        MDP_ADMIN=:mdp_admin
                                                        WHERE (ID_ADMIN=:id_admin)
                                                ");
                        $param = array('id_admin'  => $id,'mdp_admin' => password_hash(trim($password), PASSWORD_DEFAULT));
                        if ($req->execute($param)) {return true;}
                }
                return false;
        }

        /**
         *Recherche d'un administrateur par mail
        *@param {string} dans $data email_admin
        *@return {array} retourne un tableau contenant les informations de l'administrateur
         */ 
        public function rechercher_mail(string $email) :MIXED{
                if (!is_null($email) && !empty($email)) {
                        $req = Database::getConnexion()->prepare('SELECT * FROM `administrateur` WHERE EMAIL_ADMIN=:email');
                        $req->bindParam(':email', $email);
                        if ($req->execute()) {
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                return $req->fetch();
                        }
                }
                return [];
        }

        /**
        * Recherche d'un administrateur par mail  sauf celui defini - dans id_admin      
        *@param {int} $id id_admin
        *@param {string} dans $data email_admin
        *@return {array} retourne un tableau contenant les informations de l'administrateur
         */
        public function rechercher_mail_(string $email, int $id) :MIXED{
                if (!is_null($email) && !empty($email) && !is_null($id) && !empty($id)) {
                        $req = Database::getConnexion()->prepare("SELECT * FROM `administrateur` WHERE EMAIL_ADMIN=:email AND ID_ADMIN<>:id");
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
         *Recherche d'un administrateur par id_admin    
         *@param {int} $id id_client
        *@return {array} retourne un tableau contenant les informations de l'administrateur
         */
        public function rechercher_id(int $id_admin) :MIXED{
                if ($id_admin > 0) {
                        $req = Database::getConnexion()->prepare('SELECT * FROM `administrateur` 
                                                        INNER JOIN `session` ON session.ID_USER=administrateur.ID_ADMIN 
                                                        WHERE administrateur.ID_ADMIN=:id_admin');
                        $req->bindParam(':id_admin', $id_admin);
                        if ($req->execute()) {
                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                $Result = $req->fetch();
                                if (!$Result) {
                                        $req = Database::getConnexion()->prepare('SELECT * FROM `administrateur` WHERE administrateur.ID_ADMIN=:id_admin');
                                        $req->bindParam(':id_admin', $id_admin);
                                        if ($req->execute()) {
                                                $req->setFetchMode(PDO::FETCH_ASSOC);
                                                $Result = $req->fetch();
                                        }
                                }
                                return $Result;
                        }
                }
                return false;
        }

        /**
         *Suppression (SOFT DELETE) d'un administrateur par id_admin 
         *@param {int} $id id_client
        *@return {bool} retourne true si la suppression a été effectuée sinon false
         */   
        public function supprimer(int $id_admin){
                return $this->update_etat_compte((int) $id_admin, "DELETED");
        }
}

?>