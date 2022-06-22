<?php
//Ajout spécial de Thierry : anniversaire de la promo
$birthday = array();
$birthday['Anthony'] = new DateTime('24 april 1985');
$birthday['Stephane'] = new DateTime('1 february 1992');
$birthday['Loic'] = new DateTime('13 september 1982');
$birthday['Dimitri'] = new DateTime('11 september 1991');
$birthday['Quentin'] = new DateTime('16 march 1992');
$birthday['Mehdi'] = new DateTime('10 august 1998');
$birthday['Thibaud'] = new DateTime('13 november 1994');
$birthday['Maxime'] = new DateTime('15 june 1987');
$birthday['Aurore'] = new DateTime('7 july 1985');
$birthday['Lea'] = new DateTime('4 february 2011');
$birthday['Sylvie'] = new DateTime('6 august 1960');
$birthday['Gerard'] = new DateTime('6 may 1956');
$birthday['Vincent'] = new DateTime('4 march 1989');
$birthday['Pierre'] = new DateTime('24 november 1948');
// $birthday[''] = new DateTime('');
//les options du select des années :
$yearSelect = '';

//fetchage
function fetchGo($year) {
    $apiUrl = 'https://calendrier.api.gouv.fr/jours-feries/metropole/'.$year.'.json';
        $response = @file_get_contents($apiUrl, False);
        $data = json_decode($response);
        return $data;
}
//regex
$yearPattern = "/^[12][0-9]{3}$/";
$monthPattern = "/^([1-9]|1[012])$/";
function whatsTheFirstDayOfTheMonth($month, $year)
{
    $date1 = new DateTime('1-' . $month . '-' . $year);
    $result = $date1->format('N'); // N permet de lister de 1 à 7 contrairement à w ......
    return $result; //renvoi le 1er jour du mois
}
function getMonthInLetter($month, $year) {
    $date1 = new DateTime('1-' . $month . '-' . $year);
    $result = $date1->format('F');
    return $result; //renvoi le mois en lettre
}
//LA grosse fonction de création du calendrier
function createTehCalendar($nbDays, $firstDay, $year, $monthInLetter, $month, $birthday)
{
    $firstDayNumber = $firstDay; // necessaire pour la boucle while ligne 72
    //de combien de lignes ai-je besoin ( décalage lié au premier jour + nb de jours ds le mois) ?
    if ($firstDayNumber + $nbDays > 36) { 
        $fiveOrSixLign = 42;
    } elseif ($firstDayNumber + $nbDays <= 36 && $firstDayNumber + $nbDays > 29) {
        $fiveOrSixLign = 35;
    } else {
        $fiveOrSixLign = 28; // pour le cas de février SI le 1er commence le Lundi... 
    }
    $dayNumberToUse = intval($firstDayNumber) - 1; //typage en int pour l'utiliser en chiffre dans la boucle
    //la div du tableau
    $calendar = '<div id="answer">';
    //en tete du tableau
    $calendar .= "<h2>$monthInLetter $year</h2>";
    $calendar .= '<table>';
    $calendar .= '<tbody>';
    $calendar .= '<tr>';
    $calendar .= '<th>Lundi</th>';
    $calendar .= '<th>Mardi</th>';
    $calendar .= '<th>Mercredi</th>';
    $calendar .= '<th>Jeudi</th>';
    $calendar .= '<th>Vendredi</th>';
    $calendar .= '<th>Samedi</th>';
    $calendar .= '<th>Dimanche</th>';
    $calendar .= '</tr>';
    // fin en tete du tableau
    $calendar .= '<tr>';
    $firstDayNumberBis = $firstDayNumber; //j'en ai besoin 2 fois, une fois sans modif et une fois avec modif
    // tant que firstDayNumber est supérieur à 1 alors ajoute des cases vides
    while ($firstDayNumber > 1) {
        $calendar .= '<td class="emptyCell"></td>';
        $firstDayNumber--;
    }
    // on peut commencer à remplir le calendrier
    // tant que i est inférieur à 35 ou 42 
    for ($i = 1; $i <= ($fiveOrSixLign+1) - $firstDayNumberBis; $i++) {
        if ($i <= $nbDays) { //Si i est inférieur a nbDays, on ajoute le chiffre dans la case(et la case aussi...), et ca c'est bien.
            //SECTION BIRTHDAY
            $birtdayName = ''; //Va me dire si y a eu un ou plusieurs anniv pour 1 jour
            foreach ($birthday as $key => $value) {
                //contrôle de l'année de naissance
                $yearTemp = $value->format('Y');
                // Si l'année selectionnée est strictement supérieur à l'année de naissance alors ..
                if ( $year > $yearTemp) { 
                    //Je controle deja le mois.
                    $monthTemp = $value->format('m');
                    //si le mois correspond ?
                    if ($month == $monthTemp) {
                        //Alors on controle le jour
                        $daysTemp = $value->format('d');
                        //si le jour correspond ?
                        if ($i == $daysTemp) { 
                            //alors j'ajoute le nom de l'heureux élu à la variable
                            $age = $year - $yearTemp;
                            $birtdayName .= $key . ' ' . $age.' ans';
                        }
                    }
                }
            }
            //SECTION JOURS FERIES
            $data = fetchGo($year);
            if (!empty($data)) {
                $holidayName = '';
                foreach ($data as $key => $value) {                
                    $holidayDate = new DateTime($key);
                    $holidayMonth = $holidayDate->format('m');
                    //controle du mois
                    if ($holidayMonth == $month) {
                        //controle du jour
                        $holidayDay = $holidayDate->format('d');
                        if ($holidayDay == $i) {
                            $holidayName = $value;
                        }
                    }
                }
            }
            //si il y a eu 1 ou plusieurs anniversaire(s)
            if (!empty($birtdayName) && !empty($holidayName)) {
                $calendar .= '<td>' . $i . '<br><img class="calendarLogo" src="public/assets/img/calendar.svg" title="'.$holidayName.'" alt="logo calendar"><img class="birthdayLogo" src="public/assets/img/birthday.svg" title="'.$birtdayName.'" alt="logo birthday">' .'</td>';
                $dayNumberToUse += 1; //c'est cette variable qui permet de savoir si on passe a la ligne ou non.
                //Sinon c'est que y a pas
            } elseif (!empty($birtdayName)) {
                $calendar .= '<td>' . $i . '<br><img class="birthdayLogo" src="public/assets/img/birthday.svg" title="'.$birtdayName.'" alt="logo birthday">' .'</td>';
                $dayNumberToUse += 1; //c'est cette variable qui permet de savoir si on passe a la ligne ou non.
            } elseif (!empty($holidayName)) {
                $calendar .= '<td>' . $i . '<br><img class="calendarLogo" src="public/assets/img/calendar.svg" title="'.$holidayName.'" alt="logo calendar">'.'</td>';
                $dayNumberToUse += 1; //c'est cette variable qui permet de savoir si on passe a la ligne ou non.
            } else {
                $calendar .= '<td>' . $i . '</td>';
                $dayNumberToUse += 1; //c'est cette variable qui permet de savoir si on passe a la ligne ou non.
            }
        } elseif ($i > $nbDays) { //si i est supérieur a nbdays, on ajoute une case vide pour completer à 35 ou 42 cases.
            $calendar .= '<td class="emptyCell"></td>';
            $dayNumberToUse += 1; //c'est cette variable qui permet de savoir si on passe a la ligne ou non.
        }
        if ($dayNumberToUse % 7 == 0 && $i < $nbDays) { // modulo == 0 ? alors go a la ligne
            $calendar .= '</tr><tr>';
        }
    }
    $calendar .= '</tr>';
    $calendar .= '</tbody>';
    $calendar .= '</table>';
    $calendar .= '</div>';
    return $calendar;
}


if (!empty($_POST['yearInput']) && !empty($_POST['monthInput'])) {    
    $year = $_POST['yearInput'];                        //année souhaité
    $month = $_POST['monthInput'];                      // mois souhaité
    // si les 2 champs sont remplis on vérifie les patterns
    if (!preg_match($yearPattern, $year)) {
        $yearError = '<br><span class="redTxt">Veuillez entrer un année valide</span>';
    }
    if (!preg_match($monthPattern, $month)) {
        $monthError = '<br><span class="redTxt">Veuillez entrer un mois valide</span>';
    }
    //creation calendrier SI les 2 data au dessus sont OK
    if (empty($yearError) && empty($monthError)) {    
        $nbDaysToThrow = cal_days_in_month(CAL_GREGORIAN, $month, $year);  // Nombre de jours qu'on devra afficher
        $firstDay = whatsTheFirstDayOfTheMonth($month, $year); // Le premier jour du mois
        $monthInLetter = getMonthInLetter($month, $year); // Le mois en lettre
        $calendar = createTehCalendar($nbDaysToThrow, $firstDay, $year, $monthInLetter, $month, $birthday); // Création du calendrier
    }  
}
for ($i=date('Y') - 20; $i < date('Y') + 5; $i++) {
    $isSelected = (!empty($year) && $year == $i) ? 'selected' : '';
    $yearSelect .= "<option value=\"$i\" $isSelected>$i</option>";
}    
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Calendrier personalisable">
    <link rel="stylesheet" href="public/assets/css/style.css">
    <title>Personal Calendar</title>
</head>

<body>
    <img id="hamburger" src="public/assets/img/hamburger.svg" alt="logo menu opener">
    <header>
        <h1>Votre calendrier personnel</h1>        
    </header>
    <main>
        <nav id="desktopMenu">
            <form action="" method="POST">
                <h3>choisir un mois et une année</h3>
                <?=$globalError??''?>
                <div class="desktopMenuDiv">
                    <div class="subMenuDiv">
                        <label for="yearInput">Année:</label>
                        <select name="yearInput" id="yearInput">
                            <?=$yearSelect?>
                        </select>
                        <!-- <input type="number" name="yearInput" id="yearInput" min="1900" max="2099" step="1" value="<?=(!empty($year))?$year:'2022'?>"> -->
                        <?=$yearError??''?>
                    </div>
                    <div class="subMenuDiv">
                        <label for="monthInput">Mois:</label>
                        <select name="monthInput" id="monthInput">
                            <option value="none">Choisir un mois</option>
                            <option value="1" <?=(!empty($month) && $month == 1)?'selected':''?>>Janvier</option>
                            <option value="2" <?=(!empty($month) && $month == 2)?'selected':''?>>Février</option>
                            <option value="3" <?=(!empty($month) && $month == 3)?'selected':''?>>Mars</option>
                            <option value="4" <?=(!empty($month) && $month == 4)?'selected':''?>>Avril</option>
                            <option value="5" <?=(!empty($month) && $month == 5)?'selected':''?>>Mai</option>
                            <option value="6" <?=(!empty($month) && $month == 6)?'selected':''?>>Juin</option>
                            <option value="7" <?=(!empty($month) && $month == 7)?'selected':''?>>Juillet</option>
                            <option value="8" <?=(!empty($month) && $month == 8)?'selected':''?>>Août</option>
                            <option value="9" <?=(!empty($month) && $month == 9)?'selected':''?>>Septembre</option>
                            <option value="10" <?=(!empty($month) && $month == 10)?'selected':''?>>Octobre</option>
                            <option value="11" <?=(!empty($month) && $month == 11)?'selected':''?>>Novembre</option>
                            <option value="12" <?=(!empty($month) && $month == 12)?'selected':''?>>Décembre</option>
                        </select>
                        <?=$monthError??''?>
                    </div>
                </div>
                <input type="submit" name="submit" value="envoyer">
            </form>
        </nav>
        <nav id="mobileMenu">
            <form action="" method="POST">
                <h3>Choisir l'année et le mois</h3>
                <label for="yearInput">Année:</label>
                <!-- <input type="number" name="yearInput" id="yearInput" min="1900" max="2099" step="1" value="<?=(!empty($year))?$year:'2022'?>"> -->
                <select name="yearInput" id="yearInput">
                            <?=$yearSelect?>
                        </select>
                <?=$yearError??''?>
                <label for="monthInput">Mois:</label>
                <select name="monthInput" id="monthInput">
                    <option value="none">Choisir un mois</option>
                    <option value="1" <?=(!empty($month) && $month == 1)?'selected':''?>>Janvier</option>
                    <option value="2" <?=(!empty($month) && $month == 2)?'selected':''?>>Février</option>
                    <option value="3" <?=(!empty($month) && $month == 3)?'selected':''?>>Mars</option>
                    <option value="4" <?=(!empty($month) && $month == 4)?'selected':''?>>Avril</option>
                    <option value="5" <?=(!empty($month) && $month == 5)?'selected':''?>>Mai</option>
                    <option value="6" <?=(!empty($month) && $month == 6)?'selected':''?>>Juin</option>
                    <option value="7" <?=(!empty($month) && $month == 7)?'selected':''?>>Juillet</option>
                    <option value="8" <?=(!empty($month) && $month == 8)?'selected':''?>>Août</option>
                    <option value="9" <?=(!empty($month) && $month == 9)?'selected':''?>>Septembre</option>
                    <option value="10" <?=(!empty($month) && $month == 10)?'selected':''?>>Octobre</option>
                    <option value="11" <?=(!empty($month) && $month == 11)?'selected':''?>>Novembre</option>
                    <option value="12" <?=(!empty($month) && $month == 12)?'selected':''?>>Décembre</option>
                </select>
                <?=$monthError??''?>
                <input type="submit" name="submit" value="Envoyer">
            </form>
        </nav>
            <?= $calendar ?? '' ?>
    </main>
<script src="public/assets/js/app.js"></script>
</body>

</html>