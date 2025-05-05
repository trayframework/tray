<?php
namespace Tray\Core\Database;
use ADOConnection;
class Database
{
    protected static ?ADOConnection $connection = null;
    public static function connect(string $dbtype,array $config,bool $isDebug): mixed
    {
        $db = \ADONewConnection($dbtype);
        $db->debug = $isDebug;
        if ($dbtype === 'mssqlnative') {
            $db->setConnectionParameter('ReturnDatesAsStrings', true);
        }
        $dbhost = $config['dbhost'] ?? 'localhost';
        $dbuser = $config['dbuser'] ?? '';
        $dbpass = $config['dbpassword'] ?? '';
        $dbname = $config['dbname'] ?? '';
        $connected  = $db->connect($dbhost, $dbuser, $dbpass, $dbname);
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        // Force ADOdb to throw on error
        $db->raiseErrorFn = function($conn, $errno, $errmsg) use ($config) {
            throw new \RuntimeException("DB Error ($errno): $errmsg [{$config['database']}]");
        };
        if (!$connected) {
            return false;
        }
        self::$connection = $db;
        return $db;
    }
    /**
     * Dapatkan sambungan aktif
     */
    public static function getDb(): ADOConnection
    {
        if (!self::$connection) {
            throw new \RuntimeException("No database connection set.");
        }
        return self::$connection;
    }
}
