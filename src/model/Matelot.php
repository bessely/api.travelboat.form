<?php
/**
        *description                       : LA CLASSE Matelot:  CE FICHIER REND POSSIBLE TTES LES MANIPS SUR LA BASE DE DONNEES CONCERNANT LES userS DE SEJOURZEN
        *@Version                          : SEJOURZEN API 1.0.0
        *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
        *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */
class Matelot{
        /**
                *La creation d'un user
                *@param array $data : les données du user
         */
        public function creer(array $data) :MIXED{
                $req  = DB_INSTANCE->prepare("INSERT INTO  `matelot`
                                (
                                        NAME_MATELOT,
                                        SURNAME_MATELOT,
                                        EMAIL_MATELOT,
                                        CONTACT1_MATELOT,
                                        CONTACT2_MATELOT,
                                        BITHDATE_MATELOT,
                                        NATIONALITY_MATELOT,
                                        ADDRESS_MATELOT,
                                        BOX_NUM_MATELOT,
                                        EXPIR_DATE_BOX_MATELOT,
                                        RANK_MATELOT,
                                        PASSP_NUM_MATELOT,
                                        EXPIR_DATE_PASSP_MATELOT,
                                        ID_USER_CREAT,
                                        CREATED_AT
                                )
                                VALUES
                                (
                                        :NAME_MATELOT,
                                        :SURNAME_MATELOT,
                                        :EMAIL_MATELOT,
                                        :CONTACT1_MATELOT,
                                        :CONTACT2_MATELOT,
                                        :BITHDATE_MATELOT,
                                        :NATIONALITY_MATELOT,
                                        :ADDRESS_MATELOT,
                                        :BOX_NUM_MATELOT,
                                        :EXPIR_DATE_BOX_MATELOT,
                                        :RANK_MATELOT,
                                        :PASSP_NUM_MATELOT,
                                        :EXPIR_DATE_PASSP_MATELOT,
                                        :ID_USER_CREAT,
                                        :CREATED_AT
                                )");
                $param = array(
                        'NAME_MATELOT'             => trim($data['NAME_MATELOT']),
                        'SURNAME_MATELOT'          => trim($data['SURNAME_MATELOT']),
                        'EMAIL_MATELOT'            => trim($data['EMAIL_MATELOT']),
                        'CONTACT1_MATELOT'         => trim($data['CONTACT1_MATELOT']),
                        'CONTACT2_MATELOT'         => trim($data['CONTACT2_MATELOT']),
                        'BITHDATE_MATELOT'         => trim($data['BITHDATE_MATELOT']),
                        'NATIONALITY_MATELOT'      => trim($data['NATIONALITY_MATELOT']),
                        'ADDRESS_MATELOT'          => trim($data['ADDRESS_MATELOT']),
                        'BOX_NUM_MATELOT'          => trim($data['BOX_NUM_MATELOT']),
                        'EXPIR_DATE_BOX_MATELOT'   => trim($data['EXPIR_DATE_BOX_MATELOT']),
                        'RANK_MATELOT'             => trim($data['RANK_MATELOT']),
                        'PASSP_NUM_MATELOT'        => trim($data['PASSP_NUM_MATELOT']),
                        'EXPIR_DATE_PASSP_MATELOT' => trim($data['EXPIR_DATE_PASSP_MATELOT']),
                        'ID_USER_CREAT'            => trim($data['ID_USER_CREAT']),
                        'CREATED_AT'               => custum_calendar("maintenant"),
                );
                if ($req->execute($param)) {
                        $req = DB_INSTANCE->prepare('SELECT ID_MATELOT FROM `matelot` ORDER BY CREATED_AT DESC LIMIT 1');
                        if ($req->execute()) {
                                return $req->fetch(PDO::FETCH_ASSOC);
                        }
                }
                return false;
        }

        /**
                *La modification d'un user [mot de passe ou infos user  
                *@param {array}     $data         Cette fonction retourn "true" qd tt c'est bien passé ou false le cas échéant
         */
        public function modifier(array $data) :BOOl{
                $req = DB_INSTANCE->prepare("UPDATE  `matelot`
                                                        SET
                                                                NAME_MATELOT             =:NAME_MATELOT,
                                                                SURNAME_MATELOT          =:SURNAME_MATELOT,
                                                                EMAIL_MATELOT            =:EMAIL_MATELOT,
                                                                CONTACT1_MATELOT         =:CONTACT1_MATELOT,
                                                                CONTACT2_MATELOT         =:CONTACT2_MATELOT,
                                                                BITHDATE_MATELOT         =:BITHDATE_MATELOT,
                                                                NATIONALITY_MATELOT      =:NATIONALITY_MATELOT,
                                                                ADDRESS_MATELOT          =:ADDRESS_MATELOT,
                                                                BOX_NUM_MATELOT          =:BOX_NUM_MATELOT,
                                                                EXPIR_DATE_BOX_MATELOT   =:EXPIR_DATE_BOX_MATELOT,
                                                                RANK_MATELOT             =:RANK_MATELOT,
                                                                PASSP_NUM_MATELOT        =:PASSP_NUM_MATELOT,
                                                                EXPIR_DATE_PASSP_MATELOT =:EXPIR_DATE_PASSP_MATELOT,
                                                                ID_USER_UPDATE           =:ID_USER_UPDATE,
                                                                UPDATE_AT                =:UPDATE_AT
                                                        WHERE (ID_MATELOT=:ID_MATELOT )
                                                ");
                $param = array(
                        "ID_MATELOT"               => trim($data['ID_MATELOT']),
                        "NAME_MATELOT"             => trim($data['NAME_MATELOT']),
                        "SURNAME_MATELOT"          => trim($data['SURNAME_MATELOT']),
                        "EMAIL_MATELOT"            => trim($data['EMAIL_MATELOT']),
                        "CONTACT1_MATELOT"         => trim($data['CONTACT1_MATELOT']),
                        "CONTACT2_MATELOT"         => trim($data['CONTACT2_MATELOT']),
                        "BITHDATE_MATELOT"         => trim($data['BITHDATE_MATELOT']),
                        "NATIONALITY_MATELOT"      => trim($data['NATIONALITY_MATELOT']),
                        "ADDRESS_MATELOT"          => trim($data['ADDRESS_MATELOT']),
                        "BOX_NUM_MATELOT"          => trim($data['BOX_NUM_MATELOT']),
                        "EXPIR_DATE_BOX_MATELOT"   => trim($data['EXPIR_DATE_BOX_MATELOT']),
                        "RANK_MATELOT"             => trim($data['RANK_MATELOT']),
                        "PASSP_NUM_MATELOT"        => trim($data['PASSP_NUM_MATELOT']),
                        "EXPIR_DATE_PASSP_MATELOT" => trim($data['EXPIR_DATE_PASSP_MATELOT']),
                        "ID_USER_UPDATE"           => trim($data['ID_USER_UPDATE']),
                        "UPDATE_AT"                => custum_calendar("maintenant"),
                );
                if ($req->execute($param)) {
                        return true;
                }
                return false;
        }

        /** 
                *La liste des users ou recherche dans la table user
                *@param string $ordre
         */
        public function lister(string $ordre, string $STATE_MATELOT) :ARRAY{
                // ! $_POST['search']['value'] provient de datatable
                if (isset($_POST['search']['value']) && !empty($_POST['search']['value'])) {
                        $searchval = "%" . trim(strtolower($_POST['search']['value'])) . "%";
                } else {
                        $searchval = "%";
                }
                $req = DB_INSTANCE->prepare("SELECT * FROM `matelot` WHERE 
                                                                (
                                                                                (
                                                                                        LOWER(NAME_MATELOT        ) LIKE :searchval OR
                                                                                        LOWER(SURNAME_MATELOT     ) LIKE :searchval OR
                                                                                        LOWER(EMAIL_MATELOT       ) LIKE :searchval OR
                                                                                        LOWER(CONTACT1_MATELOT    ) LIKE :searchval OR
                                                                                        LOWER(CONTACT2_MATELOT    ) LIKE :searchval OR
                                                                                        LOWER(NATIONALITY_MATELOT ) LIKE :searchval OR
                                                                                        LOWER(ADDRESS_MATELOT     ) LIKE :searchval OR
                                                                                        LOWER(BOX_NUM_MATELOT     ) LIKE :searchval OR
                                                                                        LOWER(RANK_MATELOT        ) LIKE :searchval OR
                                                                                        LOWER(PASSP_NUM_MATELOT   ) LIKE :searchval
                                                                                ) AND (STATE_MATELOT<>'DELETED' AND STATE_MATELOT LIKE :STATE_MATELOT)
                                                                ) {$ordre}");
                $req->bindParam(':searchval', $searchval, PDO::PARAM_STR);
                $req->bindParam(':STATE_MATELOT', $STATE_MATELOT, PDO::PARAM_STR);
                if ($req->execute()) {
                        return $req->fetchAll(PDO::FETCH_ASSOC);
                }
                return [];
        }

        /** 
                *La liste des user ou recherche dans la table user
         */
        public function totalList(string $STATE_MATELOT) : MIXED{
                if (!empty($_POST['search']['value'])) {
                        $searchval = "%" . $_POST['search']['value'] . "%";
                } else {
                        $searchval = "%";
                }
                $req = DB_INSTANCE->prepare("SELECT COUNT(*) AS nbr FROM `matelot` 
                                                                WHERE 
                                                                (
                                                                        (
                                                                                LOWER(NAME_MATELOT        ) LIKE :searchval OR
                                                                                LOWER(SURNAME_MATELOT     ) LIKE :searchval OR
                                                                                LOWER(EMAIL_MATELOT       ) LIKE :searchval OR
                                                                                LOWER(CONTACT1_MATELOT    ) LIKE :searchval OR
                                                                                LOWER(CONTACT2_MATELOT    ) LIKE :searchval OR
                                                                                LOWER(NATIONALITY_MATELOT ) LIKE :searchval OR
                                                                                LOWER(ADDRESS_MATELOT     ) LIKE :searchval OR
                                                                                LOWER(BOX_NUM_MATELOT     ) LIKE :searchval OR
                                                                                LOWER(RANK_MATELOT        ) LIKE :searchval OR
                                                                                LOWER(PASSP_NUM_MATELOT   ) LIKE :searchval
                                                                        ) AND (STATE_MATELOT<>'DELETED' AND STATE_MATELOT LIKE :STATE_MATELOT)
                                                                )
                                                        ");
                $req->bindParam(':searchval', $searchval, PDO::PARAM_STR);
                $req->bindParam(':STATE_MATELOT', $STATE_MATELOT, PDO::PARAM_STR);

                if ($req->execute()) {
                        return $req->fetch(PDO::FETCH_ASSOC);
                }
                return [];
        }

        /**
                *recherche d'un user par mail  sauf celui defini - dans id_matelot
                *@param {int} $id id_matelot  
                *@param {string} dans $data id_ville
         */
        public function rechercher_mail_(string $email, int $id) :MIXED{
                if (!is_null($email) && !empty($email) && !is_null($id) && !empty($id)) {
                        $req = DB_INSTANCE->prepare("SELECT * FROM `matelot` WHERE EMAIL_MATELOT=:email AND ID_MATELOT  <>:id");
                        $req->bindParam(':email', $email);
                        $req->bindParam(':id', $id);
                        if ($req->execute()) {
                                return $req->fetch(PDO::FETCH_ASSOC);
                        }
                }
                return [];
        }


        /**recherche d'un user par mail
         * @param {string} dans $data id_ville
         */
        public function scalableSearch(string $COLUMN, string $VALUE ) :MIXED{
                if (!is_null($VALUE) && !empty($VALUE)) {
                        $req = DB_INSTANCE->prepare("SELECT * FROM `matelot` WHERE {$COLUMN} =:valuex");
                        $req->bindParam(':valuex', $VALUE);
                        if ($req->execute()) {
                                return $req->fetch(PDO::FETCH_ASSOC);
                        }
                }
                return [];
        }

        /**recherche d'un user par mail
         * @param {string} dans $data id_ville
         */
        public function scalableSearch_(string $COLUMN, string $VALUE, int $id ) :MIXED{
                if (!is_null($VALUE) && !empty($VALUE)) {
                        $req = DB_INSTANCE->prepare("SELECT * FROM `matelot` WHERE {$COLUMN} =:valuex AND ID_MATELOT  <>:id");
                        $req->bindParam(':valuex', $VALUE);
                        $req->bindParam(':id', $id);
                        if ($req->execute()) {
                                return $req->fetch(PDO::FETCH_ASSOC);
                        }
                }
                return [];
        }

        /**
                *UpdateSTate d'un user par id_matelot
                *@param {int} $id id_matelot  
         */
        public function updateState(int $ID_MATELOT , string $state  ){
                $req = DB_INSTANCE->prepare("UPDATE  `matelot` SET  STATE_MATELOT=:STATE_MATELOT WHERE (ID_MATELOT=:ID_MATELOT )");
                $param = array(
                        'ID_MATELOT'     => $ID_MATELOT ,
                        'STATE_MATELOT'   => trim($state),
                );
                $req->execute($param);
                return true;
        }

        /**
                *suppression (SOFT DELETE) d'un user par id_matelot
                *@param {int} $id id_matelot  
         */
        public function supprimer(int $ID_MATELOT  ){
                $this->updateState($ID_MATELOT,"DELETED");
                return true;
        }
}