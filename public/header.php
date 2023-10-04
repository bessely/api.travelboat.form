<?php
// Autoriser les requêtes provenant de n'importe quel origine
header("Access-Control-Allow-Origin: *");
// Autoriser les méthodes HTTP suivantes
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Autoriser les entêtes HTTP personnalisées
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Définir la durée de mise en cache pour les informations de contrôle d'accès
header("Access-Control-Max-Age: 3600");
// Définir le type de contenu de la réponse
header('Content-Type: application/json; charset=UTF-8');
// Vérifier si une requête OPTIONS est envoyée
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  // Renvoyer une réponse avec les entêtes CORS autorisées
  header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Authorization");
  header("Access-Control-Max-Age: 3600");
  exit(0);
}
// Vérifier si une requête GET ou POST est envoyée
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405); // !Methode not Allowed
  $reponse = array(
    'reponse' => 'error',
    'message' => 'POST, GET et OPTIONS sont les méthodes autoriées par Séjourzen API 1.0.0 !!!',
    'serveur' => $_SERVER['SERVER_NAME'],
  );
  print json_encode($reponse);
  exit();
}