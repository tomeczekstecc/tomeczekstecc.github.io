<?php
require_once('./index.php');


    $querry_select_check_for_wnp =
    "SELECT wnp.id, wnp.data_zlozenia, wnp.numer_wniosku, wnp.date_from, wnp.date_to, ad.nazwa
    FROM wnp
    LEFT JOIN wnd ON wnd.id = wnp.wnd_id
    LEFT JOIN projects p ON p.id = wnd.project_id
    LEFT JOIN accounts_detail ad ON ad.id = wnd.account_detail_id
    WHERE wnp.numer_wniosku = CONCAT('WNP-RPSL.','$wnp_numer')
    LIMIT 1";

    $result3 = mysqli_query($conn_lsi, $querry_select_check_for_wnp);
    $wnp_info= mysqli_fetch_assoc($result3);
    $row_num = mysqli_num_rows($result3);



    if ($row_num <> 1) {
        $no_such_wnp_e = 'Nie znaleziono wniosku o takim numerze.';
        $class_for_error = 'error';
    } else { require_once('php/losuj.php');
    };


if(!isset($wnp_info['numer_wniosku'])){
    $wnp_info_numer='';
    $wnp_info_nazwa='';
    $wnp_info_okres_od='';
    $wnp_info_okres_do='';
    $wnp_info_okres_spr='';
    }else{
    $wnp_info_numer=$wnp_info['numer_wniosku'];
    $wnp_info_nazwa=$wnp_info['nazwa'];
    $wnp_info_okres_od=$wnp_info['date_from'];
    $wnp_info_okres_do=$wnp_info['date_to'];
    $wnp_info_okres_spr = 'od '.$wnp_info_okres_od.' do '.$wnp_info_okres_do;
}


?>