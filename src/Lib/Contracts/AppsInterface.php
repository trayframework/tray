<?php
namespace Tray\Lib\Contracts;
interface AppsInterface
{
    /**
     * Inisialisasi sistem utama aplikasi
     */
    public function Init(&$AppCore);

    /**
     * Set pembolehubah global untuk template dan modul
     */
    public function SetGlobalVar(
        $AppCore,
        $tmpl,
        $modload,
        $action,
        $code,
        $CurrentPage,
        $menuID,
        $WebType,
        $WebCode,
        $lang,
        $ThemeSel,
        $SiteID,
        $SiteCode,
        $isMobile
    );

    /**
     * Load konfigurasi tambahan
     */
    public function LoadConfig(&$AppCore);

    /**
     * Build routing khas
     */
    public function BuildRoute(&$AppCore);

    /**
     * Tambah fungsi ekstra dalam Smarty template
     */
    public function BuildExtraFeatures(&$tmpl);
}
