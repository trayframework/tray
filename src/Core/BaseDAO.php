<?php
namespace Tray\Core;
use RuntimeException;
use Tray\Core\Database\Manager;
use Tray\Core\Database\QueryBuilder;
class BaseDAO
{
    protected Manager $manager;
    protected string $table;
    public string $primaryKey = '';
    public string $table_prefix = GP_TABLE_PREFIX;
    public string $field_prefix = '';
    public string $Spread = "_";
    public string $Prefix = "";
    public string $InstanceName = '';
    public array $FieldsForm = [];

    public function __construct(Manager $manager, string $TableName)
    {
        $this->manager = $manager;
        $this->table = $this->table_prefix . $TableName;

        if (empty($this->table)) {
            throw new RuntimeException("DaGP: Table name is empty.");
        }
    }

    protected function builder(): QueryBuilder
    {
        $builder = $this->manager->table($this->table);
        if (!$builder instanceof QueryBuilder) {
            throw new RuntimeException("DaGP: Invalid QueryBuilder returned.");
        }
        return $builder;
    }

    public function all(): array
    {
        return $this->builder()->get();
    }

    public function find(mixed $id): ?array
    {
        if (empty($this->primaryKey)) {
            throw new RuntimeException("DaGP: primaryKey not defined.");
        }
        return $this->builder()->where($this->primaryKey, $id)->first();
    }

    public function create(array $data): int
    {
        $data = $this->applyPresetFields($data, $this->PreSetFieldInsert());
        return $this->builder()->insert($data);
    }

    public function update(array $data, mixed $id): int
    {
        if (empty($this->primaryKey)) {
            throw new RuntimeException("DaGP: primaryKey not defined.");
        }

        $data = $this->applyPresetFields($data, $this->PreSetFieldUpdate());
        return $this->builder()->where($this->primaryKey, $id)->update($data);
    }

    public function delete(mixed $id): int
    {
        if (empty($this->primaryKey)) {
            throw new RuntimeException("DaGP: primaryKey not defined.");
        }
        return $this->builder()->where($this->primaryKey, $id)->delete();
    }

    public function where(string|array $column, mixed $value = null): QueryBuilder
    {
        $builder = $this->builder();
        if (is_array($column)) {
            foreach ($column as $col => $val) {
                $builder->where($col, $val);
            }
        } else {
            $builder->where($column, $value);
        }
        return $builder;
    }

    public function query(): QueryBuilder
    {
        return $this->builder();
    }

    protected function PreSetFieldInsert() {
        $Fields['inf_cre_dt']			= array(null,null,'datetime',null,false); 
        $Fields['inf_cre_usr']			= array(null,null,'userid',null,false); 
        return $Fields;
    }
    protected function PreSetFieldUpdate() {
        $Fields['inf_mod_dt']			= array(null,null,'datetime',null,false); 
        $Fields['inf_mod_usr']			= array(null,null,'userid',null,false); 
        return $Fields;
    }

    protected function applyPresetFields(array $data, array $preset): array
    {
        foreach ($preset as $field => $setting) {
            if (!isset($data[$field])) {
                switch ($setting['type']) {
                    case 'datetime':
                        $data[$field] = date('Y-m-d H:i:s');
                        break;
                    case 'userid':
                        $data[$field] = \Genpro::getUserSession('UserID');
                        break;
                    case 'ipaddr':
                        $data[$field] = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
                        break;
                    case 'status':
                        $data[$field] = $setting['default'] ?? 'active';
                        break;
                    case 'custom':
                        $data[$field] = is_callable($setting['default']) ? call_user_func($setting['default']) : $setting['default'];
                        break;
                }
            }
        }
        return $data;
    }
}