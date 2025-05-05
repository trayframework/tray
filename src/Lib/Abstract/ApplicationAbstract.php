<?php
namespace Tray\Lib\Abstract;

abstract class ApplicationAbstract
{
    protected ?array $DbInstanceList = null;
    protected string $sessionPrefix = 'user_';

    public function __construct()
    {
        // boleh override di subclass
    }
    abstract protected function getAccessUrl();
    public function setDBList(array $DbInstanceList): void
    {
        $this->DbInstanceList = $DbInstanceList;
    }

    public function Start(string $AppInstance): void
    {
        // override this method in child class
    }
    
    public function setSessionPrefix(string $AppsName): void
    {
        $this->sessionPrefix = $AppsName === 'cp' ? 'admin_' : 'user_';

        if (!defined('SESSIONFIX')) {
            define('SESSIONFIX', $this->sessionPrefix); // if needed globally
        }
    }

    public function getSessionPrefix(): string
    {
        return $this->sessionPrefix;
    }

    public function getHost(): ?string
    {
        $accessUrl = $this->getAccessUrl();
        return $accessUrl[1] ?? null;
    }

    public function getUrl(string $mod = '', string $act = '', array $params = []): string
    {
        $scheme = $_SERVER['REQUEST_SCHEME']
            ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Dapatkan root tanpa /index.php (jika ada)
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $basePath = $scriptDir === '/' ? '' : $scriptDir;

        $path = [];
        if ($mod !== '') $path[] = $mod;
        if ($act !== '') $path[] = $act;

        $query = http_build_query($params);
        $url = $scheme . '://' . $host . $basePath;

        if (!empty($path)) {
            $url .= '/' . implode('/', $path);
        }

        if (!empty($query)) {
            $url .= '?' . $query;
        }

        return $url;
    }
    public function ScanDir2($dir)
    {
       if (is_dir($dir)) {
           if ($dh = opendir($dir)) {
               //echo 2;
               while (($file = readdir($dh)) !== false) {
                   if ($file != '.' and $file != '..' and $file != '.svn') {
                       if (is_dir($dir . '/' . $file)) {
                           return $this->ScanDir2($dir . '/' . $file . '/');
                       } else {
                           $results[] = $dir . '/' . $file;
                       }
                   }
               }
               closedir($dh);
           }
       }
       return $results;
    }
    public function Debug(mixed $data): void
    {
        echo '<pre>';
        print_r($data);
        echo '</pre><hr>';
    }
}
