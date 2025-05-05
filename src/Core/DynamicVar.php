<?php
namespace Tray\Core;
class DynamicVar {
    private array $properties = [];
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
?>