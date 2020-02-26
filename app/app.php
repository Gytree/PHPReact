<?php

use Gytree\PHPReact\React;
use Gytree\PHPReact\Component;


$hello = new Component("Hello", ['name' => "Gytree"]);

?>
<html lang="en">

<head>
    <title>Test App</title>
    <?= implode("", React::assets()) ?>
</head>

<body>
    <div id="hello"></div>
    <div id="h2"></div>
    <?= React::render($hello, "hello") ?>
    <?= React::render($hello, "h2") ?>
</body>

</html>