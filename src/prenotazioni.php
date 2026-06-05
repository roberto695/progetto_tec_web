<?php

session_start();

require_once __DIR__ . "/db.php";

if (!isset($_SESSION['cf'])) {
    $html='
    <div class="placeholder-box">
        <p>Effettua il login per visualizzare e prenotare i tuoi appuntamenti.</p>
        <a href="login.php" class="bottone">Vai al login</a>
    </div>
    ';
    $header_menu = include __DIR__ . "/header.php";
    $content = file_get_contents(__DIR__ . "/template/prenotazioni.html");
    $content = str_replace("[header_menu]", $header_menu, $content);
    $content = str_replace("[prenotazioni]", $html, $content);
    echo $content;
}
else{
    $cf = $_SESSION['cf'];
    $utente = getUserInfo($cf);
    $header_menu = include __DIR__ . "/header.php";

    $content = file_get_contents(__DIR__ . "/template/prenotazioni.html");
    $content = str_replace("[header_menu]", $header_menu, $content);
    $content = str_replace("[prenotazioni]", tabella_prenotazioni($cf), $content);
    echo $content;
}


function tabella_prenotazioni($cf) {
    $result = getPrenotazioni($cf);

    $html = '<table class="prenotazioni-table">
                <tr>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Azione</th>
                </tr>';

    while ($row = $result->fetch_assoc()) {

        $data = date("Y-m-d", strtotime($row['data_ora']));
        $ora  = date("H:i", strtotime($row['data_ora']));

        $html .= "<tr>";
        $html .= "<td>$data</td>";
        $html .= "<td>$ora</td>";
        $html .= '<td>
                    <form method="POST">
                        <button type="submit" name="cancella" class="bottone_prenotazione">Cancella</button>
                    </form>
                </td>';
        $html .= "</tr>";
    }
    $html .= "</table>";

    return $html;
}
?>