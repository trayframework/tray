<?php
namespace Tray\Core;
use Tray\Core\ApplicationAbstract;

class AppKernel
{
    protected string $appInstance;
    protected ApplicationAbstract $app;
    protected array $dbList;

    public function __construct(string $appInstance, array $dbList, ApplicationAbstract $app)
    {
        $this->appInstance = $appInstance;
        $this->dbList = $dbList;
        $this->app = $app;
    }

    public function boot(): void
    {
        $this->app->setSessionPrefix($this->appInstance);
        $this->app->setDBList($this->dbList);
        $this->app->Start($this->appInstance);
    }

    public function getApp(): ApplicationAbstract
    {
        return $this->app;
    }

    public function getSessionPrefix(): string
    {
        return $this->app->getSessionPrefix();
    }

    public function getUrl(string $mod = '', string $act = '', array $params = []): string
    {
        return $this->app->getUrl($mod, $act, $params);
    }
    public function getCurrentURL(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
    
        return $scheme . '://' . $host . $uri;
    }
    public function debug(mixed $data): void
    {
        $this->app->Debug($data);
    }
}
