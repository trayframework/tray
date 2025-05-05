<?php
namespace Tray\Core\Database;
use ADOConnection;

class QueryBuilder
{
    protected ADOConnection $db;
    protected string $table;
    protected array $wheres = [], $joins = [], $selects = ['*'], $groupBy = [], $orderBy = [];
    protected ?int $limit = null, $offset = null;

    public function __construct(ADOConnection $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }
    
    public function where(string $column, $value): self
    {
        $this->wheres[] = [$column, $value];
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->joins[] = "LEFT JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function select(array $columns): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function groupBy(array $columns): self
    {
        $this->groupBy = $columns;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$column " . strtoupper($direction);
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";
        if ($this->joins) $sql .= ' ' . implode(' ', $this->joins);

        $params = [];
        if ($this->wheres) {
            $conditions = [];
            foreach ($this->wheres as [$col, $val]) {
                $conditions[] = "$col = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if ($this->groupBy) $sql .= " GROUP BY " . implode(', ', $this->groupBy);
        if ($this->orderBy) $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        if (!is_null($this->limit)) {
            $sql .= " LIMIT {$this->limit}";
            if (!is_null($this->offset)) $sql .= " OFFSET {$this->offset}";
        }
        
        $rs = $this->db->Execute($sql, $params);
        return $rs ? $rs->GetRows() : [];
    }

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        return $this->db->Execute($sql, array_values($data)) !== false;
    }

    public function insertGetId(array $data): int
    {
        $this->insert($data);
        return (int) $this->db->Insert_ID();
    }

    public function update(array $data): bool
    {
        if (empty($this->wheres)) throw new \Exception("Update requires at least one where clause.");

        $set = [];
        $params = [];
        foreach ($data as $col => $val) {
            $set[] = "$col = ?";
            $params[] = $val;
        }
        $conditions = [];
        foreach ($this->wheres as [$col, $val]) {
            $conditions[] = "$col = ?";
            $params[] = $val;
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $conditions);
        return $this->db->Execute($sql, $params) !== false;
    }

    public function delete(): bool
    {
        if (empty($this->wheres)) throw new \Exception("Delete requires at least one where clause.");
        $conditions = [];
        $params = [];
        foreach ($this->wheres as [$col, $val]) {
            $conditions[] = "$col = ?";
            $params[] = $val;
        }
        $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $conditions);
        return $this->db->Execute($sql, $params) !== false;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
        if ($this->joins) $sql .= ' ' . implode(' ', $this->joins);
        $params = [];
        if ($this->wheres) {
            $conditions = [];
            foreach ($this->wheres as [$col, $val]) {
                $conditions[] = "$col = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $rs = $this->db->Execute($sql, $params);
        return $rs ? (int)$rs->fields['total'] : 0;
    }

    public function countWithGroup(string $groupColumn, string $alias = 'total'): array
    {
        $sql = "SELECT {$groupColumn} AS group_value, COUNT(*) AS {$alias} FROM {$this->table}";
        if ($this->joins) $sql .= ' ' . implode(' ', $this->joins);
        $params = [];
        if ($this->wheres) {
            $conditions = [];
            foreach ($this->wheres as [$col, $val]) {
                $conditions[] = "$col = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $sql .= " GROUP BY {$groupColumn}";
        $rs = $this->db->Execute($sql, $params);
        return $rs ? $rs->GetRows() : [];
    }

    public function paginate(int $perPage, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $this->limit($perPage, $offset);
        $results = $this->get();
        $total = $this->count();
        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current' => $page,
            'last_page' => (int) ceil($total / $perPage)
        ];
    }

    public function updateOrInsert(array $where, array $data): bool
    {
        $builder = new self($this->db, $this->table);
        foreach ($where as $col => $val) $builder->where($col, $val);
        $exists = $builder->get();
        if (!empty($exists)) {
            foreach ($where as $col => $val) $this->where($col, $val);
            return $this->update($data);
        }
        return $this->insert(array_merge($where, $data));
    }

    public function updateOrInsertNative(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $updateClause = implode(', ', array_map(fn($col) => "$col = VALUES($col)", $columns));
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateClause";
        return $this->db->Execute($sql, array_values($data)) !== false;
    }

    public function updateOrInsertSmart(array $where, array $data): bool
    {
        $driver = $this->db->databaseType;
        $merged = array_merge($where, $data);
        if ($driver === 'mysql' || $driver === 'mysqli') {
            return $this->updateOrInsertNative($merged);
        } else {
            return $this->updateOrInsert($where, $data);
        }
    }

    public function upsert(array $rows, array $uniqueKeys = [], bool $withLog = false): array|bool
    {
        if (empty($rows)) return false;
        $driver = $this->db->databaseType;
        $logs = [];

        if (($driver === 'mysql' || $driver === 'mysqli') && !empty($uniqueKeys)) {
            $columns = array_keys($rows[0]);
            $placeholders = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
            $allPlaceholders = implode(', ', array_fill(0, count($rows), $placeholders));
            $updateClause = implode(', ', array_map(fn($col) => "$col = VALUES($col)", array_diff($columns, $uniqueKeys)));
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES $allPlaceholders ON DUPLICATE KEY UPDATE $updateClause";
            $binds = [];
            foreach ($rows as $row) {
                foreach ($columns as $col) $binds[] = $row[$col] ?? null;
            }
            $result = $this->db->Execute($sql, $binds);
            return $result !== false;
        }

        foreach ($rows as $row) {
            $where = [];
            foreach ($uniqueKeys as $key) {
                $where[$key] = $row[$key] ?? null;
            }
            $updated = $this->updateOrInsert($where, $row);
            if ($withLog) {
                $logs[] = [
                    'keys' => $where,
                    'status' => $updated ? 'updated/inserted' : 'failed'
                ];
            }
        }

        return $withLog ? $logs : true;
    }
    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }
    public function toSql(): string
    {
        $sql = 'SELECT ' . ($this->select ?? '*');
        $sql .= ' FROM ' . $this->table;
    
        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }
    
        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', array_column($this->wheres, 'condition'));
        }
    
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }
    
        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
    
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
    
        return $sql;
    }
}
