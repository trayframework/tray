<?php
namespace Tray\Core;
abstract class BaseSession
{
    protected static string $userKey = 'user';
    protected static string $globalKey = 'global';

    protected static array $_session = [];

    public static function setUserKey(string $key): void
    {
        self::$userKey = $key;
    }

    public static function setGlobalKey(string $key): void
    {
        self::$globalKey = $key;
    }

    public static function get(string $name): mixed
    {
        return $_SESSION[self::$userKey][SESSIONFIX . $name] ?? false;
    }

    public static function set(string $name, mixed $value): void
    {
        $_SESSION[self::$userKey][SESSIONFIX . $name] = $value;
        self::$_session = $_SESSION[self::$userKey];
    }

    public static function setGlobal(string $name, mixed $value): void
    {
        if (!isset($_SESSION[self::$globalKey])) {
            $_SESSION[self::$globalKey] = [];
        }

        if (is_array($value) && count($value) > 0) {
            foreach ($value as $kk => $vv) {
                $_SESSION[self::$globalKey][SESSIONFIX . $name][$kk] = $vv;
            }
        } else {
            $_SESSION[self::$globalKey][SESSIONFIX . $name] = $value;
        }
    }

    public static function getGlobal(string $name): mixed
    {
        return $_SESSION[self::$globalKey][SESSIONFIX . $name] ?? false;
    }

    public static function unsetGlobal(string $name = ""): void
    {
        if ($name === "") {
            unset($_SESSION[self::$globalKey]);
        } else {
            unset($_SESSION[self::$globalKey][SESSIONFIX . $name]);
        }
    }

    public static function unset(string $name = ""): void
    {
        if ($name === "") {
            unset($_SESSION[self::$userKey]);
        } else {
            unset($_SESSION[self::$userKey][SESSIONFIX . $name]);
        }
    }

    public static function setMultiArray(string $param, array $arr): void
    {
        foreach ($arr as $arrK => $arrItem) {
            if (is_array($arrItem) && count($arrItem) > 0) {
                foreach ($arrItem as $usrK2 => $usrRL2) {
                    $_SESSION[self::$userKey][SESSIONFIX . $param][$arrK][$usrK2] = $usrRL2;
                }
            }
        }
    }

    public static function setArray(string $param, array $arr, mixed &$sesArr = false): void
    {
        if (count($arr) === 0) return;

        foreach ($arr as $arrKey => $arrItem) {
            if (is_array($arrItem) && count($arrItem) > 0) {
                if (self::get($param) === false) {
                    self::set($param, []);
                    self::setArray($arrKey, $arrItem, $_SESSION[self::$userKey][SESSIONFIX . $param]);
                } else {
                    $sesArr = self::get($param);
                    self::setArray($arrKey, $arrItem, $sesArr);
                }
            } else {
                if (is_array($sesArr)) {
                    $sesArr[$param][$arrKey] = $arrItem;
                } else {
                    $_SESSION[self::$userKey][SESSIONFIX . $param][$arrKey] = $arrItem;
                }
            }
        }
    }
}

