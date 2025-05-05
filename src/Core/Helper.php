<?php
namespace Tray\Core;
class Helper
{
    public static function greet($name)
    {
        return "Hai, " . ucfirst($name) . "!";
    }
    public static function sum($a, $b)
    {
        return $a + $b;
    }
}
