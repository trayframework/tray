<?php
namespace Tray\Lib\Datagrid;
class DGContainer {
	private $GPDGConfigs = [];
	protected array $properties = [];
	function __construct($arr=array()){ }
	function add(mixed $Obj) {
		$name = $Obj->name;
		$this->{$name} = $Obj;
	}
	public function get() {
		$ObjList = get_object_vars($this);
		if(isset($ObjList['properties'])) $ObjList = $ObjList['properties'];
		foreach($ObjList as $ObjName=>$ObjItem) {
			if(is_object($ObjItem))
			$this->GPDGConfigs[$ObjName] = $ObjItem->Get();
		}
		return $this->GPDGConfigs;
	}
	public function __set(string $name, mixed $value): void
    {
        if(property_exists(__CLASS__,$name)){
            $this->{$name} = $value; 
        }
        else
        $this->properties[$name] = $value;
    }
	public function __get(string $name)
    {
        if(property_exists(__CLASS__,$name)){
            return $this->{$name}; 
        }
        elseif (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }
        return null;
    }
}