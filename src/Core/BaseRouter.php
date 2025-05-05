<?php
namespace Tray\Core;
class BaseRouter
{
    protected string $basePath = '';
    protected array $segments = [];

    public function __construct(string $basePath = '')
    {
        $basePath = ($basePath == '')?$this->getAliasName():'';
        $this->basePath = rtrim($basePath, '/');
        $this->parseUri();
    }

    protected function parseUri(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH); // Remove query string

        // Remove base path if applicable
        if ($this->basePath && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = trim($uri, '/');
        $this->segments = $uri === '' ? [] : explode('/', $uri);
    }

    public function getSlugArray(): array
    {
        return $this->segments;
    }

    public function getSlugString(): string
    {
        return implode('/', $this->segments);
    }

    public function getSegment(int $index, mixed $default = null): mixed
    {
        return $this->segments[$index] ?? $default;
    }
    function getAliasName(): string
    {
        $phpSelf = $_SERVER['PHP_SELF'] ?? '';
        if (str_ends_with($phpSelf, '/index.php')) {
            $alias = substr($phpSelf, 0, -strlen('/index.php'));
            return $alias === '' ? '' : $alias; 
        }
        return "";
    }
    function getSelfUrl(): string
    {
        return "";
    }
}
