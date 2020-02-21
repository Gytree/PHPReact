<?php

namespace Gytree\PHPReact;

class Commands
{
    const ASSETS_PATH = __DIR__ . DIRECTORY_SEPARATOR . "Assets";
    const COMMANDS_PATH = __DIR__ . DIRECTORY_SEPARATOR . "Commands";

    public static function getBuildPath($path)
    {
        $home = $_SERVER["HOME"];
        $hash = md5($path);
        return "$home/.cache/phpr/$hash";
    }
}
