<?php

// Job 1

include "user.php";

$marceau = new User;

$marceau->register("marceau", "marceau", "marceau.maubert@laplateforme.io", "Marceau", "Maubert");
$marceau->connect("marceau", "marceau");
$marceau->update("marceau", "marceau", "marceau.maubert2@gmail.com", "Marceau", "Maubert");
$marceau->delete();

echo "<h1>Job 1:<br/></h1>";
var_dump($marceau->getAllInfos());

// Job 2

include "user-pdo.php";

$amar = new UserPDO;

$amar->register("amar", "amar", "amar.slimane@laplateforme.io", "Amar", "Slimane");
$amar->connect("amar", "amar");
$amar->update("amar", "amar", "amar.slimane@gmail.com", "Amar", "Slimane");
$amar->delete();

echo "<h1>Job 2:<br/></h1>";
var_dump($amar->getAllInfos());

// Job 3

include "lpdo.php";

$lpdo = new LPDO("127.0.0.1", "root", "", "classes-php");
$lpdo->close();

echo "<h1>Job 3:<br/></h1>";
$lpdo->connect("127.0.0.1", "root", "", "classes-php");
var_dump($lpdo->getTables());
$lpdo->getFields("users");
var_dump($lpdo->getLastQuery());
var_dump($lpdo->getLastResult());
$lpdo->close();

?>