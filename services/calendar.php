<?php
/**
        *Description                       : Traitement des Dates dans tt l'application. Cette fonction sont disponible globalment
*/


/**
        *CUSTUM DAY IN FRENCH FOR DATE TO LETTER FUNTION
 */
define("JOURS", array(
    '',
    'Lundi',
    'Mardi',
    'Mercredi',
    'Jeudi',
    'Vendredi',
    'Samedi',
    'Dimanche'
));

/**
        *CUSTUM WEEK DAY
 */
define("WEEKEND", array(
    'Samedi',
    'Dimanche'
));

/**
        *CUSTUM OUVRABLE DAY
 */
define("OUVRABLE", array(
    'Lundi',
    'Mardi',
    'Mercredi',
    'Jeudi',
    'Vendredi',
));

/**     
        *CUSTUM MONTH IN FRENCH FOR DATE TO LETTER FUNTION
 */
define("MOIS", array(
    '',
    'Janvier',
    'Février',
    'Mars',
    'Avril',
    'Mai',
    'Juin',
    'juillet',
    'Août',
    'Septembre',
    'Octobre',
    'Novembre',
    'Décembre'
));

/**
    *DATE ET HEURE A LA DEMANDE
 * @param string $choix
 * @return string la date ou l'heure demandée   | Nous some en gmdate heure global universel
 * @example $date = custum_calendar("maintenant"); //date et heure actuelle
 */
function custum_calendar(string $choix): String {
    $_HEURE_DATE=array(
        "maintenant"        => gmdate("Y-m-d H:i:s"),//maintenant à la seconde pret
        "aujourdhui"        => gmdate("Y-m-d"),//maintenant Sans heure ni seconde
        "heur_instant"      => gmdate("H:i:s"),
        "0_heur"            => "00:00:00",
        "minuit"            => "23:59:59",
        "1_heure"           => gmdate("Y-m-d H:i:s",strtotime('1 hour')),//Durant une heure
        "24_heure"          => gmdate("Y-m-d H:i:s",strtotime('next day')),//Durant 24 heures
        "trois_jours"       => gmdate("Y-m-d H:i:s",strtotime('3 day')), //Trois (3) Jours
        "une_semaine"       => gmdate("Y-m-d H:i:s",strtotime('7 day')), //Une semaine
        "un_mois"           => gmdate("Y-m-d H:i:s",strtotime('next Month')),//Un mois
        "trois_mois"        => gmdate("Y-m-d H:i:s",strtotime('3 Month')),//Trois (3) mois
        "six_mois"          => gmdate("Y-m-d",strtotime('6 Month')),//Six (6) mois
        "annee"             => gmdate("Y-m-d H:i:s",strtotime('12 Month')),//Une année
        "this_annee"        => gmdate("Y",strtotime('this year')),//cette annee
        "hier"              => gmdate("Y-m-d",strtotime('last day')), //hier
        "demain"            => gmdate("Y-m-d",strtotime('next day')), //demain
        "semaine_prochaine" => gmdate("Y-m-d",strtotime('this day + 7 day')), //semaine prochaine
        "mois_prochain"     => gmdate("Y-m-d",strtotime('first day of next Month')),// mois prochaine
        "mois_passe_deb"    => gmdate("Y-m-d",strtotime('first day of last Month')), //1 er jour du mois passé
        "mois_passe_fin"    => gmdate("Y-m-d",strtotime('last day of last Month')),// dernier jours du mois passé
        "mois_deb"          => gmdate("Y-m-d H:i:s",strtotime('first day of this Month')), //1er jours de ce mois
        "mois_fin"          => gmdate("Y-m-d H:i:s",strtotime('last day of this Month')), // dernier jours de ce mois
        "annee_deb"         => gmdate("Y-m-d H:i:s",strtotime('01 january this year + 1 day')), //1er jours de l'année
        "annee_fin"         => gmdate("Y-m-d H:i:s",strtotime('31 december this year + 1 day')),// dernier jours de l'année
        "10_ans"            => gmdate("Y-m-d H:i:s",strtotime('10 year')),// 10 ans
    );
    return $_HEURE_DATE[$choix];
}

function getCurrentYear(){
    $today =new DateTime("now", new DateTimeZone('UTC'));
    $year = $today->format('Y');
    return $year;
}

/**
 *RETOURNE LA DATE DU JOUR CORRESPONDANT EN PARTANT DU NBR DE DE MINUTES DONNE
 * @param string $min 
 * @return string le nombre de minute a rajouter 
 * @example $date = give_me_xminute("30"); //date et heure actuelle + 30 minutes
 */
function give_me_xminute(string $min) {
    return gmdate("Y-m-d H:i:s",strtotime($min.' minute'));
}

/**
 *RETOURNE LA DATE DU JOUR CORRESPONDANT EN PARTANT DU NBR DE D'HEURE DONNE
 * @param string $hour 
 * @return string le nombre d'heure
 * @example $date = give_me_xhour("2"); //date et heure actuelle + 2 heures
 */
function give_me_xhour(string $hour) {
    return gmdate("Y-m-d H:i:s",strtotime($hour.' hour'));
}

/**
 *RETOURNE UNE DATE FUTUR A PARTIR D'UNE DATE DONNEE PLUS LE NBRE DE JOUR D'ECART
 * @param {string} $nbr_jr_add int -x, +x jours: OU string: "next", "previous", "this"
 * @return {String} $dat_depart la date donné pour le depart
 * @example $date = give_next_day("2020-01-01", 2); //date actuelle + 2 jours
 */
function give_next_day(string $dat_depart, int $nbr_jr_add) {
    if ($nbr_jr_add!==0) {
        return date("Y-m-d",strtotime($dat_depart." ".$nbr_jr_add." days"));
    }
    return date("Y-m-d",strtotime($dat_depart));
}

/**
    *LA MEME CHOSE QUE give_next_day + les heures
 * @param string $days int -x, +x jours: OU string: "next", "previous", "this"
 * @return string la date correspondante
 * @example $date = give_x_day("2"); //date actuelle + 2 jours
 */
function give_x_day(string $days) {
    return gmdate("Y-m-d H:i:s",strtotime($days.' day'));
}

/**
 *LA MEME CHOSE QUE give_x_day mais avecles mois
 * @param [string] $month int: -x, +x mois OU string: "next", "previous", "this"
 * @param [String] $format le format
 */
function give_me_xmonth(string $month, string $format="Y-m-d H:i:s") {
    return gmdate($format,strtotime($month.' Month'));
}

/**
 *RETOURNE LA DATE DONNEE AU FORMAT AAAA-MM-JJ AU FORMAT  JJ JOUR MOIS ANNEE en lettre
 * @param string la date à convertir au format AAAA-MM-JJ
 * @return string la date à convertir au format JJ JOUR MOIS ANNEE  
 * @example $date = date_to_letter("2022-06-28") -> Le Mardi 28 JUIN 2022
 */
function date_to_letter(string $date_x) {
    return JOURS[date('N',strtotime($date_x))].' '.date('j',strtotime($date_x)).' '.MOIS[date('n',strtotime($date_x))].' '.date('Y',strtotime($date_x));
}

/**
    *DIS SI UNE DATE TOMBE UN WEEKend ou pas
 * @return string 
 */
function isItWeekDate(string $date_x) {
    $day= JOURS[date('N',strtotime($date_x))];
    if (in_array(trim($day), WEEKEND) ) {
        return $day;
    }
    return false;
}

/**
    *DIS SI UNE DATE TOMBE UN Jr Ouvrables ou pas
 * @return string  
 */
function isItOuvrableDate(string $date_x) {
    $day= JOURS[date('N',strtotime($date_x))];
    if (in_array(trim($day), OUVRABLE) ) {
        return $day;
    }
    return false;
}

/**Calcul le nombre de jour entre 2 date 
 * @param string $date_deb // Date de debut au format YYYY-MM-JJ
 * @param string $date_fin // Date de fin au format YYYY-MM-JJ
 * @return int // Nombre de jour
 * @example $nbrJour = nbrJour("2020-01-01","2020-01-03"); // 3
*/
function nbrJour($date_deb,$date_fin){
    if ($date_deb === $date_fin) {
        return 1;
    }
    $earlier  = new DateTime($date_deb);
    $later    = new DateTime($date_fin);
    $abs_diff = $later->diff($earlier)->format("%a");
    return $abs_diff;
}

/** Verifie que la date exite dans le calendrier
 * @param string $date // Date à tester au format YYYY-MM-JJ
 * @return bool // true si la date existe, false sinon false si la date n'existe pas
 * @example isDateInCalendar("2020-02-31"); // false
 */
function isDateInCalendar(string $date) {
    // Convertir la date en trois variables séparées pour le jour, le mois et l'année
    list($day, $month, $year) = explode('/', $date);
    // Vérifier si la date existe
    return checkdate($month, $day, $year);
}

/**Calcule le nopbre de minute entre 2 heures
 * 
 */
function calculerIntervalleTempsEnMinutes($heure1, $heure2){
    $datetime1 = new DateTime($heure1);
    $datetime2 = new DateTime($heure2);
    $interval = $datetime1->diff($datetime2);
    $heures = $interval->h;
    $minutes = $interval->i;
    $totalMinutes = ($heures * 60) + $minutes;
    return $totalMinutes;
}


/**
 *LA MEME CHOSE QUE give_x_day mais avecles mois
 * @param [string] $month int: -x, +x mois OU string: "next", "previous", "this"
 * @param [String] $format le format
 */
function convertDate(string $date, string $format="Y-m-d") {
    $dateObj = date_create_from_format("d/m/Y", $date);
    return date_format($dateObj, $format);
}

?>
