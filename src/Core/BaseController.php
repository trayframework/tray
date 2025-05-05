<?php
namespace Tray\Core;
use Smarty\Smarty;
use Tray\Core\Database\Database;

abstract class BaseController
{
    protected Smarty $view;
    public $Model = null;
    protected string $Module;
    protected string $ModuleFullPath;
    protected string $ViewPath;
    protected string $ThemeSel = 'default'; // default theme
    public string $ActionDesc = '';
    public function __construct($parent,&$tmpl,$action,$actions=array(),$access='private')
    {
        $this->view = $tmpl;
        $this->autoLoadModel();
        $this->ModuleFullPath = GP_APPROOT.'/src/application/'.GP_APP.'/modules/'.$this->Module;
		$this->ViewPath 	  = $this->ModuleFullPath.'/html/';
		$this->Assign('GP_MODLOAD',GP_MODLOAD);
		$this->Assign('GP_ACTION',GP_ACTION);
		$this->Assign('GP_LINKALIAS',GP_LINKALIAS);
    }
    public function setTheme(?string $theme): void
    {
        if($theme != "")
        $this->ThemeSel = $theme;
    }
    public function getTheme(): string
    {
        return $this->ThemeSel;
    }
    /**
     * Auto-load model berdasarkan nama controller
     */
    protected function autoLoadModel(): void
    {
        $controllerClass = get_class($this); // e.g. App\Controllers\UserController
        $parts = explode('\\', $controllerClass);
        $controllerName = end($parts); // UserController
        $this->Module = strtolower(str_replace("Controller","",$controllerName));
        if (!str_ends_with($controllerName, 'Controller')) {
            return;
        }
        $ClassName = str_replace('Controller', '', $controllerName); 
        $modelName = str_replace('Controller', 'Model', $controllerName); // UserModel
        $modelClass = '\\App\\'.GP_APP.'\\modules\\'.strtolower($ClassName).'\\' . $modelName;
        if (class_exists($modelClass)) {
           
            $db = Database::getDb();
            $this->Model = new $modelClass($db);
        }
    }

    /**
     * Dapatkan model instance
     * @param string|null $name Jika kosong, ambil default berdasarkan controller
     */
    protected function model(string $name = ''): mixed
    {
        if ($name === '') {
            $controllerClass = get_class($this);
            $controllerName = basename(str_replace('\\', '/', $controllerClass));
            $modelKey = str_replace('Controller', 'Model', $controllerName);
            return $this->models[$modelKey] ?? null;
        }

        return $this->models[$name] ?? null;
    }

    /**
     * Assign variable ke template: kekal nama Asal Huruf Besar
     */
    public function Assign(string $key, mixed $value): void
    {
        $this->view->assign($key, $value);
    }

    /**
     * Render template (e.g. 'user/list.tpl')
     */
    protected function render(string $template): void
    {
        $this->view->display($template);
    }
   
}
