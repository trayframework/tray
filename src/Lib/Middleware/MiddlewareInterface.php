<?php
namespace Tray\Lib\Middleware;
interface MiddlewareInterface
{
    /**
     * Proses permintaan sebelum sampai ke handler sebenar.
     *
     * @param callable $next Fungsi seterusnya dalam pipeline
     * @return mixed
     */
    public function handle(callable $next): mixed;
}
