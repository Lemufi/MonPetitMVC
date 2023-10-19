<?php
include_once PATH_VIEWg . "header.html";
echo "<p>Nombre de clients trouvés : ".count($clients) ."</p>";

foreach ($clients as $client){
    echo $client->getId() . " " . $client->getTitreCli() . " " . $client->getPrenomCli() . " " . $client->getNomCli() . "<br>";
}
include_once PATH_VIEW . "footer.html";