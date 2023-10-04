<?php

/**
 *TRAITEMENT DES FICHIERS
*@param string this->traitement   SUPPRESSION  DEPLACEMENT REMPLACEMENT
*@param string this->id du fichier
*@param array FILES le fichier en POST
*@param string this->extension l'extension du fichier en POST
 */
class Fichier{
        private $traitement;
        private $id;
        private $FILES;
        private $extension;
        private $table;
        function __construct(string $table, string $traitement, int $id, array $FILES){
                $this->table      = $table;
                $this->traitement = $traitement;
                $this->id         = $id;
                if (isset($FILES) && !empty($FILES)) {
                        $this->FILES     = $FILES;
                        if (isset($FILES['fichier'])){
                                $this->extension = strtolower(pathinfo($this->FILES['fichier']['name'], PATHINFO_EXTENSION));
                        }else {
                                $this->extension = strtolower(pathinfo($this->FILES['name'], PATHINFO_EXTENSION));
                        }
                }
        }
        public function trouver_dossier(): string{
                $dossier = true;
                if ($this->table == "administrateur"){
                        $dossier = "src/img/administrateurs/";
                }
                if ($this->table == "patients"){
                        $dossier = "src/img/clients/";
                }
                if ($this->table == "paraticiens"){
                        $dossier = "src/img/proprietaires/";
                }
                if ($this->table == "configuration"){
                        $dossier = "src/img/configurations/";
                }
                return  $dossier;
        }
        public function deplacement_fichier(string $lien_depart, string $destination): BOOL{
                if (is_uploaded_file($lien_depart)){ //Je m'assure que le fichier est configuration au lieu de depart
                        if (move_uploaded_file($lien_depart, $destination)){ //je deplace et je veriefie l'état du deplacement
                                sleep(1); //fait patienter une seconde (apres ecriture sur disque)
                                return true;
                        } else {
                                return false;
                        }
                } else {
                        return false;
                }
        }
        /* SUPPRESSION SIMPLE */
        public function supprimer_fichier(string $emplacement): VOID{
                // @unlink($emplacement); //l'arrobase verifie l'existance du fichier avt suppression
                array_map('unlink', glob($emplacement)); //ciblage et supression (cette methode prend en compte les liens relatifs avec *) exemple "some/dir/*.txt"
        }
        public function traitement_fichier(string $nom_fichier){
                if ($nom_fichier === ""){$nom_fichier = nom_aleatoire(6);}
                if (isset($this->FILES['fichier'])){
                        $lien_depart = $this->FILES['fichier']['tmp_name']; //ou le fichier est situer dans la machine du client (tres svt un espace temporaire ou le composant inout-file le depose)
                }else {
                        $lien_depart = $this->FILES['tmp_name'];
                }
                if ($this->traitement == "SUPPRESSION"){
                        $this->supprimer_fichier($this->trouver_dossier() . $nom_fichier . "." . $this->extension); //SUPPRESSION cibler sur le nom du fichier
                        if (!(file_exists($this->trouver_dossier() .$nom_fichier . "." . $this->extension))){ //Rechercher l'existance du fichier supprimer
                                return true; //si le fichier n'existe plus c'est que la suppression a reussi
                        } 
                        return false;
                }
                if ($this->traitement == "SUPPRESSION BULK"){ // suppresion en masse
                        $this->supprimer_fichier($this->trouver_dossier() . $this->id . "_*." . $this->extension); //SUPPRESSION A PARTIR D'UN MOT CLE 
                        if (!(file_exists($this->trouver_dossier() .$this->id . "_." . $this->extension))){ //Rechercher l'existance du fichier supprimer
                                return true; //si le fichier n'existe plus c'est que la suppression a reussi
                        } 
                        return false;
                }
                if (!empty($this->FILES)){//Bien entendu il faut que le fichier exite en POST POUR CES 2 TYPES DE TRAITEMENT
                        if ($this->traitement == "CREATION"){
                                /*DEPLACEMENT SIMPLE DU CLIENT VERS SERVEUR*/
                                $destination = $this->trouver_dossier() . $this->id . "_" . $nom_fichier . "." . $this->extension;
                                if ($this->deplacement_fichier($lien_depart, $destination)) { //deplacement du nouveau fichier
                                        return getEnvironment()["LOCALHOST"].$this->trouver_dossier() . $this->id . "_" . $nom_fichier . "." . $this->extension;
                                }
                                return false;
                        }
                        if ($this->traitement == "MODIFICATION"){
                                /* SUPPRESSION +++ REMPLACEMENT */
                                $this->supprimer_fichier($this->trouver_dossier() . $this->id . "_*.". $this->extension); //ancien fichier
                                //recherché le fichier sans le retrouvé pour être sûr qu'il à bien été supprimé
                                if (!(file_exists($this->trouver_dossier() . $this->id . "*." . $this->extension))){ //Rechercher l'existance de l'ancien fichier
                                        if (isset($this->FILES['fichier'])){
                                                $lien_depart = $this->FILES['fichier']['tmp_name']; //ou le fichier est situer dans la machine du client (tres svt un espace temporaire ou le composant inout-file le depose)
                                        }else{
                                                $lien_depart = $this->FILES['tmp_name'];
                                        } 
                                        $destination = $this->trouver_dossier() . $this->id . "_" . $nom_fichier . "." . $this->extension; // destination + fichier à deplacer (nom fichier = id_fature+nom aléatoire)
                                        if ($this->deplacement_fichier($lien_depart, $destination)) { //deplacement du nouveau fichier
                                                return getEnvironment()["LOCALHOST"].$this->trouver_dossier() . $this->id . "_" . $nom_fichier . "." . $this->extension;
                                        }
                                }
                                return false;
                        }
                } else {
                        return false;
                }
        }
}