<?php
namespace Tray\Lib\Datagrid;

class DGButton {
	public $name = 'btn';
	private $att = [
		'title'=>'_BTNNAME',
		'modload'=>'_MODLOAD',
		'action'=>'_ACTION',
		'param'=>array(),
		'access'=>false
	];
	function __construct($name,$arg = array(),$permission=true) {
		$this->name = $name;
		if(count($arg)>0) {
			$this->att = array_merge($this->att,$arg);
			//Debug($arg);
			if(isset($arg['modload']) && isset($arg['action']) && !$this->att['access']) {
				$this->att['access'] = $permission;
			}
		}
	}
	function Param($arr) {
		if(count($arr)>0) {
			$this->att['param'] = $arr;
		}
		return $this;
	}
	function Access(bool $access) {
		$this->att['access'] = $access;
		return $this;
	}
	function Get() {
		return $this->att;
	}
}