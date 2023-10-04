<?php


class profil{

    /**
     *CREER UN NIVEAU d'Acces
    *@param  ID_PROFIL 
    *@param  NAME_PROFIL
    *@param  PRIVILEGE_PROFIL
    */
    public function newProfil(string $NAME_PROFIL, array $PRIVILEGE):BOOL{
        $req = DB_INSTANCE->prepare("INSERT INTO  `profil`
                                (
                                    NAME_PROFIL,
                                    PRIVILEGE_PROFIL,
                                    CREATED_AT
                                )
                                VALUES
                                (
                                    :NAME_PROFIL, :PRIVILEGE_PROFIL, :CREATED_AT
                                )");
        return $req->execute(array(
            'NAME_PROFIL'      => $NAME_PROFIL    ,
            'PRIVILEGE_PROFIL' => implode     (",", $PRIVILEGE),
            'CREATED_AT'       => custum_calendar("maintenant"),
            ));
    }

    /**
     *MODIFIER UN NIVEAU
    *@param  ID_PROFIL 
    *@param  NAME_PROFIL
    *@param  PRIVILEGE_PROFIL
    **/
    public function updateProfil(int $ID_PROFIL , string $NAME_PROFIL, array $PRIVILEGE):BOOL{
        $req = DB_INSTANCE->prepare("UPDATE  `profil`
                                                        SET
                                                        NAME_PROFIL=:NAME_PROFIL,
                                                        PRIVILEGE_PROFIL=:PRIVILEGE_PROFIL
                                                    WHERE (ID_PROFIL =:ID_PROFIL )
                                                    ");
        return $req->execute(array(
            'NAME_PROFIL'      => $NAME_PROFIL    ,
            'PRIVILEGE_PROFIL' => implode     (",", $PRIVILEGE),
            'ID_PROFIL'        => $ID_PROFIL
        ));
    }

    /**
     *LIER UN UTILISATEUR A UN OU DES PROFILS
    *@param  ID_PROFIL 
    *@param  ID_USER
    **/
    public function attacheProfilToUser(array $PROFIL , int $ID_USER):VOID{
        $req = DB_INSTANCE->prepare("DELETE FROM  `avoir` WHERE (ID_USER =:ID_USER )");
        $req->execute(array(
            'ID_USER'       => $ID_USER
        ));
        for ($i=0; $i < count($PROFIL); $i++) {
            $req = DB_INSTANCE->prepare("INSERT INTO `avoir`
                                                        (
                                                            ID_PROFIL,
                                                            EXPIRED_DATE,
                                                            ID_USER
                                                        )
                                                            VALUES
                                                        (
                                                            :ID_PROFIL,
                                                            :EXPIRED_DATE,
                                                            :ID_USER
                                                        )
                                        ");
            $req->execute(array(
                'ID_PROFIL'    => $PROFIL[$i],
                'EXPIRED_DATE' => LICENCE==='free' ? custum_calendar("une_semaine") : custum_calendar("10_ans") ,
                'ID_USER'      => $ID_USER,
            ));
        }
    }

    /**
     *CHARGE LES ROLES SELON LE ID DE l'profil
    *@param ID_PROFIL   l'identifiant du niveau qui s'apprete à faire objet de traitement
    **/
    public function getOneProfil(int $ID_PROFIL ){
        $req=DB_INSTANCE->prepare('SELECT * FROM `profil` WHERE (ID_PROFIL =:id)');
        $req->bindParam(':id',$ID_PROFIL );
        if ($req->execute()) {
            $profil = $req->setFetchMode(PDO::FETCH_ASSOC);
            
        }
        return $req->fetch();
    }

    /**
     *CHARGE LES ROLES SELON LE ID DE l'profil
    *@param ID_PROFIL   l'identifiant du niveau qui s'apprete à faire objet de traitement
    **/
    public function loadProfileByName(string $NAME_PROFIL ){
        $req=DB_INSTANCE->prepare('SELECT * FROM `profil` WHERE (NAME_PROFIL =:NAME_PROFIL)');
        $req->bindParam(':NAME_PROFIL',$NAME_PROFIL );
        $req->execute();
        $req->setFetchMode(PDO::FETCH_ASSOC);
        return $req->fetch();
    }

    public function listProfil($order){
        $req=DB_INSTANCE->prepare("SELECT * FROM `profil` {$order}");
        $req->execute();
        $req->setFetchMode(PDO::FETCH_ASSOC);
        return $req->fetchAll();
    }

}
?>
