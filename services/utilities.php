<?php

    /**
     *Description                       : Fonction utiles disponible globalment
    */

    /**
    *Me donne l'addresse en cours d'execution
     */
    function page_encour(){
        return parse_url(($_SERVER['REQUEST_URI']))['path'];
    }

    /**
    *Me donne le nom du fichier en cours d'execution
    */
    function fichier_encour(){
        return basename($_SERVER["PHP_SELF"]);
    }

    /**
    *Me donne le nom du dossier dans lequel le fichier en cours d'execution se trouve
    */
    function dossier_encour(){
        return basename(dirname($_SERVER['SCRIPT_NAME']));
    }

    function extractUri(): array{
        $route   = explode("/", page_encour())[2] ?? "";
        $service = explode("/", page_encour())[3] ?? "";
        $query   = explode("/", page_encour())[4] ?? "";
        return compact("route", "service", "query");
    }

    function getRequest(){

    }
    /**
        *retourne une chaine de x caractères aléatoirement lettre et chiffre
        *@param {integer} $digit le nbr de caratère
    */ 
    function token(int $digit) :STRING{
        return substr(str_shuffle(bin2hex(random_bytes($digit))),0,$digit);
    }

    /** retourne une clé unique basé sur la date
        *@param {string} $date la date à defaut je recupère la date actuelle
    */ 
    function uniqueCodeWithDate(string $date=null) :STRING{
        if (is_null($date)) {
            return gmdate("YmdHis");
        }
        return date("YmdHis", strtotime($date));
    }

    /**
        *extract date from unique codeWithDate
        *@param {string} $code le nbr de caratère
    */
    function extractDateFromUniqueCodeWithDate(string $code) :STRING{
        $rowDate = substr($code, 0, 14);
        return (substr($rowDate, 0, 4).'-'.substr($rowDate, 4, 2).'-'.substr($rowDate, 6, 2).' '.(substr($rowDate, 8, 2)).':'.substr($rowDate, 10, 2).':'.substr($rowDate, 12, 2));
    }

    /**
        *retourne un code aléatoir de x caractères
        *@param {integer} $digit le nbr de caratère
    */
    function random_code(int $digit){
            $UNIVERS= "123456789";
            return str_shuffle(substr($UNIVERS,0,$digit));
    }

    /**
        *retourne un code aléatoir de x caractères
        *@param {integer} $digit le nbr de caratère
    */
    function nom_aleatoire(int $digit): STRING{
            $UNIVERS    = '12345678983634516abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            return str_shuffle(substr($UNIVERS,0,$digit));
    }

    /**
            *retourne un code aléatoir de x caractères
            *@param {integer} $digit le nbr de caratère
    */
    function codeParrainnage(): STRING{
        $UNIVERS    = '_-ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return str_shuffle(substr($UNIVERS, 0, 8));
    }

    /**Dévoile le veritable chemin du endpoint appellé par les requetes CLIENT
     *retourne "controller/administrateur.php" quand $endpoint = administrateur
     *@param {string} $endpoint le point d'accès faisant l'objet de la requete Je le recois du CLIENT en POST dans la variable $endpoint
    */
    function catchRequest(){
        if (extractUri()['route']){
            $file = "src/controller/". strtolower(extractUri()['route']) .".php";
            if (file_exists($file)){
                    return $file;
            }else{
                reject("Service non repertorié.", 404);
            }
        }
        response("[ Bienvenue sur API.TRAVELBOAT.FORM ".VERSION." Développé par besselymail@gmail.com ] Spécifier un service pour démarrer.", 404);
    }

    /**
    * RETOUR LA BONNE VARIAVLE D'ENVIRONNEMENT
    */
    function getEnvironment() : ARRAY{
        switch ($_SERVER['SERVER_NAME']) {
            case 'proservername':
                return array(
                    'LOCALHOST'                     => ACCUEIL_PRO,
                    'DOMAINE_RCOVER_PSW_URI'        => ACCUEIL_PRO,
                    'ACCEUIL'                       => ACCUEIL_PRO,
                    'SERVER_NAME'                   => $_SERVER['SERVER_NAME']
                );
            default: // LOCALHOST
                return array(
                    'LOCALHOST'                     => LOCALHOST,
                    'DOMAINE_RCOVER_PSW_URI'        => LOCALHOST,
                    'ACCEUIL'                       => LOCALHOST,
                    'SERVER_NAME'                   => $_SERVER['SERVER_NAME']
                );
        }
    }

    /**
    *Applatisseur de fichier 
    */
    function incoming_files(){
            $files = $_FILES;
            $files2 = [];
            foreach ($files as $input => $infoArr) {
                    $filesByInput = [];
                    foreach ($infoArr as $key => $valueArr) {
                            if (is_array($valueArr)) { // file input "multiple"
                                    foreach ($valueArr as $i => $value) {
                                            $filesByInput[$i][$key] = $value;
                                    }
                            } else { // -> string, normal file input
                                    $filesByInput[] = $infoArr;
                                    break;
                            }
                    }
                    $files2 = array_merge($files2, $filesByInput);
            }
            $files3 = [];
            foreach ($files2 as $file) { // let's filter empty & errors
                    if (!$file['error']) $files3[] = $file;
            }
            return $files3;
    }
    
    /**
    *Applatisseur de fichier
    */
    function incoming_files_custum($FILES){
        $files = $FILES;
        $files2 = [];
        foreach ($files as $input => $infoArr) {
            if (is_array($infoArr)) {
                for ($i=0; $i <  count($infoArr); $i++) {
                    $filesByInput[$i][$input]= $infoArr[$i];
                }
            }else{
                $filesByInput[$input] = $infoArr;
            }
        }
        return $filesByInput;
    }

    /**
     * JSON REPONSE DE REJECT 
     * @param string $message
     * @param int $httpCode
     * @return void
     * */
    function reject(string|array $message= "Traitement introuvable",int $httpCode=400): string{
        http_response_code($httpCode);
        $reponse = array(
            'reponse'        => 'error',
            'message'        => $message,
            'service resume' => array(
                'endpoint'      => extractUri()['route'],
                'traitement'    => (extractUri()['service']),
                'requestType'   => $_SERVER['REQUEST_METHOD'],
                'requestBody'   => $_REQUEST,
                'serveur'       => $_SERVER['SERVER_NAME'],
                'version'       => VERSION,
                // 'documentation' => 'https://documenter.getpostman.com/view/18368073/2s93sW9bex'
            ),
        );
        return exit(print json_encode($reponse));
    }

    /**
     * JSON REPONSE DE SUCCESS 
     * @param array $result le resultat de la requete [message, data...] ou juste un message
     * @param string $message
     * @param int $httpCode
     * @return void
     * */
    function response(string|array $result,int $httpCode=200): string{
        http_response_code($httpCode);
        if (is_array($result)) {
            $reponse = array(
                'reponse'        => 'success',
                'service resume' => array(
                    'endpoint'      => extractUri()['route'],
                    'traitement'    => (extractUri()['service']),
                    'requestType'   => $_SERVER['REQUEST_METHOD'],
                    'requestBody'   => $_REQUEST,
                    'serveur'       => $_SERVER['SERVER_NAME'],
                    'version'       => VERSION,
                    // 'documentation' => 'https://documenter.getpostman.com/view/18368073/2s93sW9bex'
                ),
            );
            return exit(print json_encode(array_merge($result, $reponse)));
        }else{
            $reponse = array(
                'reponse'        => 'success',
                'message'        => $result,
                'service resume' => array(
                    'endpoint'      => extractUri()['route'],
                    'traitement'    => (extractUri()['service']),
                    'requestType'   => $_SERVER['REQUEST_METHOD'],
                    'requestBody'   => $_REQUEST,
                    'serveur'       => $_SERVER['SERVER_NAME'],
                    'version'       => VERSION,
                    // 'documentation' => 'https://documenter.getpostman.com/view/18368073/2s93sW9bex'
                ),
            );
            return exit(print json_encode($reponse));
        }
    }

    function getLocationFromIp(){
        return curlFetch("?fields=status,message,continent,country,countryCode,region,regionName,city,lat,lon,timezone,query&lang=fr","http://ip-api.com/json/","GET");
    }

    function tranformToBase64($data){
        return base64_encode($data);
    }

    /**
     * CURL SENDER
     */
    function curlFetch(string $fields_string, string $link, string $methode){
            if (function_exists('curl_version')) {
                    $ch            = curl_init();
                    try {
                            $ch             = curl_init();
                            curl_setopt_array($ch, array(
                                    CURLOPT_URL            => $link.$fields_string, 
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST  => strtoupper($methode),
                                    CURLOPT_SSL_VERIFYPEER => 0,
                            ));
                            $result   = curl_exec($ch);
                            $err      = curl_error($ch);
                            curl_close($ch);
                            if ($err) {
                                    reject("Error :" . $err);
                            } else {
                                    return print ($result);
                            }
                    } catch (Exception $e) {reject($e);}
            } else {reject("Vous devez activer curl ou allow_url_fopen pour utiliser les SMS.");}
    }

    function controlParams(mixed $value, string $name, string $requiredType, array $size = [1,10000], bool $required = false) :mixed {
        if ($value !== "" && $value !== NULL) {
            if (!($requiredType !== NULL && $requiredType !== "" && gettype($value) === $requiredType)) {
                if ($requiredType !== "mail" && $requiredType !== "date") {
                    reject("Le paramètre [".$name."] doit être de type ".$requiredType." type ".gettype($value)." détecté");
                }
            }
            if ($requiredType !== NULL && $requiredType !== "" && $requiredType !== NULL) {
                if ($requiredType === "mail") {
                    $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
                    if (!preg_match($emailRegex, $value)) {
                        reject("Le paramètre [".$name."] n'est pas un email valide");
                    }
                }
                if ($requiredType === "string") {
                    if (!(strlen($value) >= $size[0] && strlen($value) <= $size[1])) {
                        if ($size[0] === $size[1]) {
                            reject("Le paramètre [".$name."] doit avoir exactement ".$size[1]." caractères");
                        }
                        reject("Le nombre de caractères du paramètre [".$name."] doit être compris entre ".$size[0]." et ".$size[1]." caractères");
                    }
                }
                if ($requiredType === "integer") {
                    if (!($value >= $size[0] && $value <= $size[1])) {
                        if (!is_numeric($value) && $value>0 ) {
                            if ($_SERVER['REQUEST_METHOD']==="GET") {
                                reject("Le paramètre [".$name."] doit être de type ".$requiredType);
                            }
                            if ($required) {
                                reject("Le paramètre [".$name."] est requis");
                            }
                        } else {
                            reject("Le paramètre [".$name."] doit être compris entre ".$size[0]." et ".$size[1]);
                        }
                    }
                }
                if ($requiredType === "array" && is_array($value)) {
                    if (!(count($value) >= $size[0] && count($value) <= $size[1])) {
                        reject("Le paramètre [".$name."] doit avoir au moins ".$size[0]." enregistrement(s)");
                    }
                }
                if ($requiredType === "date") {
                    if (!(strlen($value) === $size[1])) {
                        reject("Le paramètre [".$name."] doit être au format JJ/MM/AAAA");
                    }
                    if (!(isDateInCalendar($value))) {
                        reject("Le paramètre [".$name."] contient une date non valide: le format autorisé est : JJ/MM/AAAA ");
                    }
                    return convertDate($value);
                }
                return $value;
            }
        }
        return  $required ? reject("Le paramètre [".$name."] est requis") : $value ;
    }