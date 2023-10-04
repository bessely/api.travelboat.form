<?php
/**CONNEXION À LA BASE DE DONNÉES
 * Description                       : Connexion à la base de donnée
 * @Version                          : API.TRAVELBOAT.FORM API 1.0.0
 * @Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844
 * Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
*/

class Database{
  public static function  getConnexion(){
    $MASTER=array(
      'HOST'     => "localhost",
      'DBNAME'   => "supermaritimeform",
      'USERNAME' => "root",
      'PASSWORD' => '43D!.//DqQr9DQq$D.'
    );
    $LOCAL=array(
      'HOST'     => "localhost",
      'DBNAME'   => "supermaritimeform",
      'USERNAME' => "root",
      'PASSWORD' => ""
    );
    switch ($_SERVER['SERVER_NAME']) {
      case 'prodservername':
            $config= $MASTER;
          break;
      default: // LOCALHOST
            $config= $LOCAL;
          break;
    }
    //configuration de l'objet PDO {C'est lui qui me permet de faire la connection à MYSQL (le serveur de base de donnée)}
    $options  = array(
      PDO::ATTR_CASE               => PDO::CASE_LOWER,
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    );
    try{
      return new PDO("mysql:host=".$config['HOST'].";dbname=".$config['DBNAME'], $config['USERNAME'], $config['PASSWORD'], $options); // !! retourne une seule instance de connexion à chaque invocation  à cause du mot clé "statique"
    } catch (PDOException $e) {
      http_response_code(401); //Bad Request
      $reponse = array(
        'reponse'  => 'error',
        'message'  => "IMPOSSIBLE DE SE CONNECTER À LA BASE DE DONNÉES",
        'erro_log' => $e->getMessage(),
      );
      print json_encode($reponse);
      exit();
    }
    return false;
  }
}