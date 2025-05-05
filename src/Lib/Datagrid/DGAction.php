<?php
namespace Tray\Lib\Datagrid;

class DGAction {
	public $name = 'act';
	private $att = [
		'css'=>'SBtnPreview',
		'modload'=>'',
		'action'=>'',
		'param'=>array(),
		'title'=>'title',
		'access'=>false,
		'op'=>'',
		'belongto'=>[],
		'doconfirm'=>false
	];
	function __construct($name,$arg = array()) {
		$this->name = $name;
		if(count($arg)>0) {
			$this->att = array_merge($this->att,$arg);
		}
	}
	function Param($arr) {
		if(count($arr)>0) {
			$this->att['param'] = $arr;
		}
	}
	function Extra($arr) {
		if(count($arr)>0) {
			$this->att = array_merge($this->att,$arr);
		}
	}
	function Access(bool $access) {
		$this->att['access'] = $access;
	}
	function BelongTo($arr) {
		$this->att['belongto'] = $arr;
	}
	function Confirm($arr) {
		$this->att['doconfirm'] = $arr;
	}
	function Get() {
		return $this->att;
	}
}