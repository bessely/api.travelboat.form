<?php

/**
 *description                       : LE SCRIPT DE GESTION DES MATELOTS POUR TOUT LE PROJET
 * @Version                          : API.TRAVELBOAT.FORM API 1.0.0
 *@Author                           : YAO BESSELY SUNDAY JUNIOR : +2250709116844 besselymail@gmail.com
 *Codez en pensant que celui qui maintiendra votre code est un psychopathe qui connaît votre adresse.
 */


require_once "src/model/Utilisateurs.php";
require_once "src/model/Matelot.php";
$Matelot    = new Matelot();
$Users      = new Utilisateur();
/**
 *Appel les fonctions adequoites selon le traitement demandé par le Users
 */
match (strtoupper(extractUri()['service'])) {
    "NEW"         => newMatelot(),
    "UPDATE"      => updateMatelot(),
    "LIST"        => listMatelot(),
    "GETONE"      => getOneMatelot(),
    "UPDATESTATE" => updateMatelotState(),
    // "GETONEFILE" => GetOneMatelot(),
    default  => reject("Le Endpoint [ ".extractUri()['service']." ] est introuvable dans les services de 'matelot'"),
};

/**
 *Inscription du Matelot
 */
function newMatelot(){
    global $Users,$Matelot;
    $ID_USER_CREAT            = $Users->isUserAlow(array("SUPER ADMIN","OPE1"))['id_user'];
    $NAME_MATELOT             = controlParams( $_POST['NAME_MATELOT'             ] , "NAME_MATELOT"             ,"string" ,[1  ,255 ],true  );
    $SURNAME_MATELOT          = controlParams( $_POST['SURNAME_MATELOT'          ] , "SURNAME_MATELOT"          ,"string" ,[1  ,255 ],true  );
    $EMAIL_MATELOT            = controlParams( $_POST['EMAIL_MATELOT'            ] , "EMAIL_MATELOT"            ,"mail"   ,[6  ,255 ],true  );
    $CONTACT1_MATELOT         = controlParams( $_POST['CONTACT1_MATELOT'         ] , "CONTACT1_MATELOT"         ,"string" ,[8  ,20  ],true  );
    $CONTACT2_MATELOT         = controlParams( $_POST['CONTACT2_MATELOT'         ] , "CONTACT2_MATELOT"         ,"string" ,[8  ,20  ],false );
    $BITHDATE_MATELOT         = controlParams( $_POST['BITHDATE_MATELOT'         ] , "BITHDATE_MATELOT"         ,"date"   ,[10 ,10  ],false );
    $NATIONALITY_MATELOT      = controlParams( $_POST['NATIONALITY_MATELOT'      ] , "NATIONALITY_MATELOT"      ,"string" ,[1  ,255 ],false );
    $ADDRESS_MATELOT          = controlParams( $_POST['ADDRESS_MATELOT'          ] , "ADDRESS_MATELOT"          ,"string" ,[8  ,20  ],false );
    $BOX_NUM_MATELOT          = controlParams( $_POST['BOX_NUM_MATELOT'          ] , "BOX_NUM_MATELOT"          ,"string" ,[8  ,20  ],true  );
    $EXPIR_DATE_BOX_MATELOT   = controlParams( $_POST['EXPIR_DATE_BOX_MATELOT'   ] , "EXPIR_DATE_BOX_MATELOT"   ,"date"   ,[10 ,10  ],true  );
    $RANK_MATELOT             = controlParams( $_POST['RANK_MATELOT'             ] , "RANK_MATELOT"             ,"string" ,[3  ,50  ],true  );
    $PASSP_NUM_MATELOT        = controlParams( $_POST['PASSP_NUM_MATELOT'        ] , "PASSP_NUM_MATELOT"        ,"string" ,[8  ,20  ],true  );
    $EXPIR_DATE_PASSP_MATELOT = controlParams( $_POST['EXPIR_DATE_PASSP_MATELOT' ] , "EXPIR_DATE_PASSP_MATELOT" ,"date"   ,[10 ,10  ],true  );

    // verifier la duplicoter de certain elément à caractère unique
    if ($Matelot->scalableSearch("EMAIL_MATELOT",$EMAIL_MATELOT)) {
        reject("Désolé, mais il semblerait que le email soit déjà pris.");
    }
    if ($Matelot->scalableSearch("BOX_NUM_MATELOT",$BOX_NUM_MATELOT)) {
        reject("Désolé, mais il semblerait que le box num soit dupliqué.");
    }
    if ($Matelot->scalableSearch("PASSP_NUM_MATELOT",$PASSP_NUM_MATELOT)) {
        reject("Désolé, mais il semblerait que le numéro de passport soit dupliqué.");
    }

    $DATA_Users   = compact("NAME_MATELOT", 
                            "SURNAME_MATELOT", 
                            "EMAIL_MATELOT", 
                            "CONTACT1_MATELOT",
                            "CONTACT2_MATELOT",
                            "BITHDATE_MATELOT",
                            "NATIONALITY_MATELOT",
                            "ADDRESS_MATELOT",
                            "BOX_NUM_MATELOT",
                            "EXPIR_DATE_BOX_MATELOT",
                            "RANK_MATELOT",
                            "PASSP_NUM_MATELOT",
                            "EXPIR_DATE_PASSP_MATELOT",
                            'ID_USER_CREAT'
                    );
    if ($id=$Matelot->creer($DATA_Users)) {
        response(array(
            'message'          => "Sauvegarder avec succès",
            'data'             => $id,
        ));
    }
}

/**
 *Inscription du Matelot
 */
function updateMatelot(){
    global $Users,$Matelot;
    $ID_USER_UPDATE           = $Users->isUserAlow(array("SUPER ADMIN","OPE1"))['id_user'];
    $ID_MATELOT               = controlParams(intval($_POST['ID_MATELOT'               ]), "ID_MATELOT"               ,"integer" ,[700000 ,700000000 ],true  );
    $NAME_MATELOT             = controlParams(       $_POST['NAME_MATELOT'             ] , "NAME_MATELOT"             ,"string"  ,[1      ,255       ],true  );
    $SURNAME_MATELOT          = controlParams(       $_POST['SURNAME_MATELOT'          ] , "SURNAME_MATELOT"          ,"string"  ,[1      ,255       ],true  );
    $EMAIL_MATELOT            = controlParams(       $_POST['EMAIL_MATELOT'            ] , "EMAIL_MATELOT"            ,"mail"    ,[6      ,255       ],true  );
    $CONTACT1_MATELOT         = controlParams(       $_POST['CONTACT1_MATELOT'         ] , "CONTACT1_MATELOT"         ,"string"  ,[8      ,20        ],true  );
    $CONTACT2_MATELOT         = controlParams(       $_POST['CONTACT2_MATELOT'         ] , "CONTACT2_MATELOT"         ,"string"  ,[8      ,20        ],false );
    $BITHDATE_MATELOT         = controlParams(       $_POST['BITHDATE_MATELOT'         ] , "BITHDATE_MATELOT"         ,"date"    ,[10     ,10        ],false );
    $NATIONALITY_MATELOT      = controlParams(       $_POST['NATIONALITY_MATELOT'      ] , "NATIONALITY_MATELOT"      ,"string"  ,[1      ,255       ],false );
    $ADDRESS_MATELOT          = controlParams(       $_POST['ADDRESS_MATELOT'          ] , "ADDRESS_MATELOT"          ,"string"  ,[8      ,20        ],false );
    $BOX_NUM_MATELOT          = controlParams(       $_POST['BOX_NUM_MATELOT'          ] , "BOX_NUM_MATELOT"          ,"string"  ,[8      ,20        ],true  );
    $EXPIR_DATE_BOX_MATELOT   = controlParams(       $_POST['EXPIR_DATE_BOX_MATELOT'   ] , "EXPIR_DATE_BOX_MATELOT"   ,"date"    ,[10     ,10        ],true  );
    $RANK_MATELOT             = controlParams(       $_POST['RANK_MATELOT'             ] , "RANK_MATELOT"             ,"string"  ,[3      ,50        ],true  );
    $PASSP_NUM_MATELOT        = controlParams(       $_POST['PASSP_NUM_MATELOT'        ] , "PASSP_NUM_MATELOT"        ,"string"  ,[8      ,20        ],true  );
    $EXPIR_DATE_PASSP_MATELOT = controlParams(       $_POST['EXPIR_DATE_PASSP_MATELOT' ] , "EXPIR_DATE_PASSP_MATELOT" ,"date"    ,[10     ,10        ],true  );

    // verifier la duplicoter de certain elément à caractère unique
    if ($Matelot->scalableSearch_("EMAIL_MATELOT",$EMAIL_MATELOT,$ID_MATELOT)) {
        reject("Désolé, mais il semblerait que le email soit déjà pris.");
    }
    if ($Matelot->scalableSearch_("BOX_NUM_MATELOT",$BOX_NUM_MATELOT,$ID_MATELOT)) {
        reject("Désolé, mais il semblerait que le box num soit dupliqué.");
    }
    if ($Matelot->scalableSearch_("PASSP_NUM_MATELOT",$PASSP_NUM_MATELOT,$ID_MATELOT)) {
        reject("Désolé, mais il semblerait que le numéro de passport soit dupliqué.");
    }
    if (!$Matelot->scalableSearch("ID_MATELOT",$ID_MATELOT)) {
        reject("Désolé, Matelot non exitant.");
    }

    $DATA_Users   = compact("NAME_MATELOT", 
                            "SURNAME_MATELOT", 
                            "EMAIL_MATELOT", 
                            "CONTACT1_MATELOT",
                            "CONTACT2_MATELOT",
                            "BITHDATE_MATELOT",
                            "NATIONALITY_MATELOT",
                            "ADDRESS_MATELOT",
                            "BOX_NUM_MATELOT",
                            "EXPIR_DATE_BOX_MATELOT",
                            "RANK_MATELOT",
                            "PASSP_NUM_MATELOT",
                            "EXPIR_DATE_PASSP_MATELOT",
                            'ID_USER_UPDATE',
                            'ID_MATELOT'
                    );
    if ($Matelot->modifier($DATA_Users)) {
        response("Correctement mise à jour");
    }
}

function listMatelot(){
    global $Matelot;
    $start         = controlParams(intval($_POST['START'         ]),"START"         , "integer" ,[0,10000000 ],true);
    $length        = controlParams(intval($_POST['LENGTH'        ]),"LENGTH"        , "integer" ,[0,1000     ],true);
    $STATE_MATELOT = controlParams(      ($_POST['STATE_MATELOT' ]),"STATE_MATELOT" , "string"  ,[0,1000     ],true);
    $infos_client     = $Matelot->lister(" ORDER BY NAME_MATELOT ASC LIMIT $length " . ($start == 0 ? "" : "OFFSET " . $start) . " ",$STATE_MATELOT);
    $infos_client_all = $Matelot->totalList($STATE_MATELOT);
    response(array(
        'message'          => count($infos_client) . ' Matelot(s) trouvé(s)',
        'data'             => $infos_client,
        'recordsTotal'     => count($infos_client),
        'recordsFiltered'  => $infos_client_all["nbr"],
    ));
}

function getOneMatelot(){
    global $Matelot;
    $ID_MATELOT     = controlParams(intval($_POST['ID_MATELOT']), "ID_MATELOT" ,"integer" ,[700000 ,700000000 ],true  );
    $infos_user     = $Matelot->scalableSearch("ID_MATELOT",$ID_MATELOT);
    if ($infos_user) {
        $infos_user['date_add_matelot_letter'] = date_to_letter($infos_user['created_at']);
        response(array('data' => $infos_user ?? []));
    }
    reject("Aucune occurence");
}


function updateMatelotState(){
    global $Users,$Matelot;
    $ID_MATELOT    = controlParams(intval($_POST['ID_MATELOT'    ]), "ID_MATELOT"    , "integer" , [700000 , 700000000 ], true);
    $STATE_MATELOT = controlParams(      ($_POST['STATE_MATELOT' ]), "STATE_MATELOT" , "string"  , [3      , 50        ], true);
    $Users->isUserAlow(array("SUPER ADMIN"));
    if ($Matelot->scalableSearch("ID_MATELOT",$ID_MATELOT)) {
        if ($Matelot->updateState($ID_MATELOT,$STATE_MATELOT)) {
            response(array(
                'message'   => "L'état du matelot est passé à ".$STATE_MATELOT,
            ));
        }
        reject("Désolé, mais une erreur inconnue est survenue pendant la modification du l'état du matelot. Veuillez réessayez !");
    }
    reject("Désolé, mais il semblerait que ce matelot n'existe plus.");
}
