<?php
namespace Tray\Lib\Abstract;

abstract class FieldsDataAbstract
{
    // Force Extending class to define this method
    abstract protected function Get();
	protected function PreSetField() {
        $Fields['inf_cre_dt']			= array(null,null,'datetime',null,false); 
        $Fields['inf_cre_usr']			= array(null,null,'userid',null,false); 
		$Fields['inf_mod_dt']			= array(null,null,'datetime',null,false); 
        $Fields['inf_mod_usr']			= array(null,null,'userid',null,false); 
        return $Fields;
    }
    public function GrpObj($Array,$Prefix) {
		$Results = array();
		$FieldSet = array();
		$Values = array();
		$Fields  = $this->Get();
		
		if(is_array($Array) && count($Array)>0) {
				foreach($Array as $Key=>$Item) {
						if(array_key_exists($Key,$Fields)) {
							$FieldInfo = $Fields[$Key];
							$FieldSet[] = $Key;
							if($FieldInfo[2] == 'int') {
								
								if($Item != '' || $Item >=0) {
										$Values[] = intval($Item);
								}
								else $Values[] = null;
							}
							elseif($FieldInfo[2] == 'decimal') {
								if($Item != '') {
									$Values[] = round($Item,2);
								}
								else $Values[] = null;
							}
							elseif($FieldInfo[2] == 'date') {
								if($Item != '') {
									list($d,$m,$y) = explode("/",$Item);
									$Values[]  =  $y."-".$m."-".$d;
								}
								else
								$Values[] = null;
							}
                            elseif($FieldInfo[2] == 'datetime') {
								if($Item != '') {
                                    list($date,$tm) = explode(" ",$Item);
									list($d,$m,$y) = explode("/",$date);
									$Values[]  =  $y."-".$m."-".$d.' '.$tm;
								}
								else
								$Values[] = null;
							}
							elseif($FieldInfo[2] == 'json') {
								if(is_array($Item)) {
									$Values[] = json_encode($Item,JSON_UNESCAPED_SLASHES);
								}
								else $Values[] = null;
							}
							else {
								if($Item != '') 
								$Values[] = $Item;
								else $Values[] = null;
							}
						}
						else $Results[$Key] = $Item;
				}
		}
		return array($FieldSet,$Values);
	}
	public function RebuildObj($Array,$Prefix) {
		$Results = array();
		$FieldSet = array();
		$Values = array();
		$Fields  = $this->Get();
		$PresetField = $this->PreSetField();
		
		if(is_array($Array) && count($Array)>0) {
			foreach($Array as $Key=>$Item) {
				//Debug($Fields);
				//Debug($PresetField);
				//print_r($Key);
				if(array_key_exists($Key,$PresetField)) {
					//Debug($Key);
				}
				else {
					$FieldName = $Prefix.'_'.$Key;
					if(array_key_exists($Key,$Fields)) {
						$FieldInfo = $Fields[$Key];
						if($FieldInfo[2] == 'int') {
							if($Item != '' || $Item >=0) {
									$Values = intval($Item);
							}
							else $Values = null;
						}
						elseif($FieldInfo[2] == 'decimal') {
							if($Item != '') {
								$Values = round($Item,2);
							}
							else $Values = null;
						}
						elseif($FieldInfo[2] == 'date') {
							if($Item != '') {
								$Values  =  $Item;
							}
							else
							$Values = null;
						}
						elseif($FieldInfo[2] == 'datetime') {
							if($Item != '') {
								$Values  =  $Item;
							}
							else
							$Values = null;
						}
						elseif($FieldInfo[2] == 'json') {
							if(!is_array($Item)) {
								$Values = json_decode($Item);
							}
							else $Values = $Item;
						}
						else {
							if($Item != '') 
							$Values = $Item;
							else $Values = null;
						}

						$Results[$Key] = $Values;
					}
					
				}
			}
		}

		Debug($Results);
	}
}