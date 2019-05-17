<?php
require_once('./index.php');
require_once('wnp_info.php');



        $querry_select_wnp =
            "SELECT wnp_documents.id, wnp_documents.nip, wnp_documents.nr_fv, wnp_documents.data_zawarcia, wnp_documents.wartosc_brutto
            FROM wnp
            LEFT JOIN wnp_documents ON wnp_documents.wnp_id=wnp.id
            WHERE wnp.numer_wniosku= CONCAT('WNP-RPSL.','$wnp_numer')
            ORDER BY 1 ASC";

        $result = mysqli_query($conn_lsi, $querry_select_wnp);
        $result2 = mysqli_query($conn_lsi, $querry_select_wnp);

        $all = $result->fetch_all();
        $how_many = sizeof($all);
        $wszystkie_id = [];

        if ($wnp_max > $how_many) {
            $max_more_than_all_e = 'Liczba dokumentów do wylosowania jest większa, niż liczba dokumenów dołączona do wniosku. Dla tego wniosku możesz wylosować nie więcej, niż ' . $how_many . '.';
            $class_for_error = 'error';
        } else {

            foreach ($all as $value) {
                array_push($wszystkie_id, $value[0]);
            };

            ///////////////////////// ALGORYTM LOSOWANIA BEZ POWTORZEN ////////////////////Mirossław Zelent//
            $ile_pytan = $how_many; //z ilu pytan losujemy?
            $ile_wylosowac =  $wnp_max; //ile pytan wylosowac?
            $ile_juz_wylosowano = 0; //zmienna pomocnicza
            for ($i = 1; $i <= $ile_wylosowac; $i++) {
                do {
                    $liczba = rand(1, $ile_pytan); //losowanie w PHP
                    $losowanie_ok = true;

                    for ($j = 1; $j <= $ile_juz_wylosowano; $j++) {
                        //czy liczba nie zostala juz wczesniej wylosowana?
                        if ($liczba == $wylosowane[$j]) $losowanie_ok = false;
                    }

                    if ($losowanie_ok == true) {
                        //mamy unikatowa liczbe, zapiszmy ja do tablicy
                        $ile_juz_wylosowano++;
                        $wylosowane[$ile_juz_wylosowano] = $liczba;
                    }
                } while ($losowanie_ok != true);
            }


            // UTWÓRZ TABLICĘ Z WYLOSOWANYMI ID
            $picked_ids = [];

if(isset($wylosowane)){

            foreach ($wylosowane as $value) {
                array_push($picked_ids, $wszystkie_id[$value]);
            }}

            // // WYPISZ NA EKRANIE
            // foreach ($picked_ids as $key => $value) {
            //     echo ($value . '<br/>');
            // }
            // echo '<br/>';

            // print_r('all: ' . $all[5][0]);
            // echo '<br/>';
            // print_r($picked_ids);
        }


