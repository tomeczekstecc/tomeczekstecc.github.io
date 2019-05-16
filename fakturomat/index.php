<?php
require_once('php/db_config_LSI.php');

$not_all_params_e = '';
$no_such_wnp_e = '';
$max_more_than_all_e = '';
$class_for_error = 'non-error';

if (((!isset($_POST['wnp-number'])) || (!isset($_POST['wnp-precent'])) || (!isset($_POST['wnp-max'])) || ($_POST['wnp-max'] == '') || ($_POST['wnp-number'] == '') || ($_POST['wnp-precent'] == '')) == true) {
    $not_all_params_e = 'Nie wybrano wszystkich parametrów. Wpisz pełny numer wniosku, procent oraz liczbę do wylosowania.';
    $class_for_error = 'error';
} else {
    $wnp_numer = $_POST['wnp-number'];
    $wnp_procent = $_POST['wnp-precent'];
    $wnp_max = $_POST['wnp-max'];

    $querry_select_check_for_wnp =
        "SELECT wnp.id
    FROM wnp
    WHERE wnp.numer_wniosku = CONCAT('WNP-RPSL.','$wnp_numer')";

    $result3 = mysqli_query($conn_lsi, $querry_select_check_for_wnp);
    $row_num = mysqli_num_rows($result3);

    if ($row_num <> 1) {
        $no_such_wnp_e = 'Nie znaleziono wniosku o takim numerze.';
        $class_for_error = 'error';
    } else {
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

            foreach ($wylosowane as $value) {
                array_push($picked_ids, $wszystkie_id[$value]);
            }

            // // WYPISZ NA EKRANIE
            // foreach ($picked_ids as $key => $value) {
            //     echo ($value . '<br/>');
            // }
            // echo '<br/>';

            // print_r('all: ' . $all[5][0]);
            // echo '<br/>';
            // print_r($picked_ids);
        }
    }
}

?>


<!doctype html>
<html lang="pl">

<head>
    <title>Hello, world!</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.style.css" />

</head>

<body>
    <nav class="container-fluid bg-dark p-2">
        <div class="row nav-wrap">
            <div id="nav-header text-center" class="col-md-8 col-sm-12">
                <h2 class="text-center">FAKTURO<span class="secondary-text">MAT</span></h2>
            </div>
            <div id="nav-links" class="col-md-4 col-mx-12 text-center">
                <a href="https://rpo.slaskie.pl">
                    <div id="rpo" target="_blank">rpo.slaskie.pl</div>
                </a>
                <a href="dokumentacjafs.slaskie.pl">
                    <div id="instrukce">Instrukcje</div>
                </a>
                <a href="../php/logout.php">
                    <div id="logout">Wyloguj się</div>
                </a>
            </div>
        </div>
    </nav>

    <main>
        <div class="hero p-3 bg-primary">
            <div class="hero-heading text-center">
                <h2>WYLOSUJ ŻĄDANĄ LICZBĘ DOKUMENTÓW</h2>
                <h3>
                    <p>Aplikacja słuzy do losowania wybranej liczby dokumentów potwierdzających poniesione wydatki, które
                        zostały
                        dołączone do wniosku o płatnośc złożonego w ramach RPO WSL 2014-2020. </p>
                    <p>Losując dokumenty wybierz ich
                        liczbę oraz minimalny lub maksymalny procent, rozumiany jako procent w ogólnej liczbie dokumenów.</p>

                </h3>
            </div>
            <div class="desc"></div>
        </div>



        <form action="index.php" method="POST">
            <?php
            echo '
                                <div class="' . $class_for_error . '">
                                    ' . $not_all_params_e . $no_such_wnp_e . $max_more_than_all_e . '
                                </div>'
            ?>
            <div class="container-fluid my-4">
                <div class="form-row params text-center bg-light p-5 m-1">

                    <div id="wnp-number-wrapper" class="col-md-6 col-sm-12 input-group">
                        <label for="wnp-number">Wpisz pełny numer WNP </label>
                        <div class="input-group mb-3">

                            <div class="input-group-prepend">
                                <span class="input-group-text">RPSL-WNP.</span>
                            </div>
                            <input id="wnp-number" name="wnp-number" type="text" class="form-control">

                        </div>
                    </div>

                    <div id="wnp-precent-wrapper" class="col-md-2 col-sm-12 input-group">
                        <label for="wnp-precent">Wpisz procent</label>
                        <div class="input-group mb-3">
                            <input id="wnp-precent" name="wnp-precent" class="form-control" type="number" min="0" max="100" step="1">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div id="wnp-max-wrapper" class="col-md-2 col-sm-12 input-group">
                        <label for="wnp-max">Wpisz liczbę maksymalną</label>
                        <div class="input-group mb-3">
                            <input id="wnp-max" name="wnp-max" class="form-control" type="number" min="0" max="100" step="1">
                            <div class="input-group-append">
                                <span class="input-group-text">szt.</span>
                            </div>
                        </div>
                    </div>
                    <div id="execut-btn" class="text-center col-md-2 col-sm-12">
                        <label for="submit-btn"></label>
                        <input type="submit" class="btn btn-success text-center mt-3 py-3 px-5 align-middle" value="Wylosuj">
                    </div>

                </div>
            </div>
        </form>

        <div id="summary" class="card text-primary mx-auto" style="width: 80vw;">
            <div class="card-body">
                <h5 class="card-title">Podatawowe informacje o wniosku</h5>
                <table id ="info"class="table table table-striped">
                    <tr>
                        <td>Numer wniosku o płatność: </td>
                        <td>Kolumna 2</td>
                    </tr>
                    <tr>
                        <td>Nazwa beneficjenta:</td>
                        <td>wartość 1b</td>
                    </tr>
                    <tr>
                        <td>Okres realizacji:</td>
                        <td>wartość 2b</td>
                                            </tr>
                </table>
                <p class="card-text text-dark">Some quick example text to build on the card title and make up the bulk
                    of the card's
                    content.</p>

            </div>
        </div>
        <!-- tabela ALL -->
        <div class="picked m-5 p-2">
            <h4>Wylosowane dokumenty: </h4>
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Numer Dokumentu</th>
                        <th scope="col">Nip/Pesel wystawcy</th>
                        <th scope="col">Data wystawienia</th>
                        <th scope="col">Wartość brutto</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $j = 0;
                    $suma_all = 0;
                    if (!isset($picked_ids)) {
                        null;
                    } else {
                        foreach ($picked_ids as $val) {
                            $j++;

                            for ($i = 0; $i < sizeof($all); $i++) {
                                if ($val == $all[$i][0]) {
                                    echo '
                                    <tr>
                                        <th scope="row">' . $j . '</th>
                                        <td>' . $all[$i][2] . '</td>
                                        <td>' . $all[$i][1] . '</td>
                                        <td>' . $all[$i][3] . '</td>
                                        <td>' . number_format($all[$i][4], 2, '.', ' ') . '</td>
                                    </tr>';
                                    $suma_all = $suma_all + $all[$i][4];
                                };
                            }
                        };
                    };

                    ?>
                </tbody>

            </table>
            <?php

            echo '
                <div class="suma_all">
                    Wartość wylosowanych  dokumentów: ' . number_format($suma_all,  2, '.', ' ')  . '
               PLN<br/>
               Liczba wylosowanych dokumentów: ' . $j . '
               </div>

                ';

            ?>

        </div>


        <div class="all m-5 p-2">
            <h4>Wszystkie dokumenty: </h4>
            <table class="table table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Numer Dokumentu</th>
                        <th scope="col">Nip/Pesel wystawcy</th>
                        <th scope="col">Data wystawienia</th>
                        <th scope="col">Wartość brutto</th>
                    </tr>
                </thead>
                <tbody>


                    <?php
                    if (!isset($result2)) {
                        null;
                    } else {
                        $v = 0;
                        while ($row = $result2->fetch_assoc()) {
                            $v++;
                            echo '
                                    <tr>
                                    <th scope ="row " >' . $v . ' </th>
                                     <td>' . $row['nr_fv'] .  ' </td>
                                     <td>' . $row['nip'] .  ' </td>
                                     <td>' . $row['data_zawarcia'] .  ' </td>
                                     <td>' . number_format($row['wartosc_brutto'],  2, '.', ' ')  .  ' </td>
                                     </tr>';
                        }
                    };
                    ?>

                </tbody>
            </table>
        </div>





        </div>
    </main>

    <div class="footer bg-dark text-center p-3">
        Zespoł LSI &copy;
    </div>
</body>