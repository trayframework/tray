<?php
namespace Tray\Core\Database;

use ADOConnection;
use Tray\Core\Database\QueryBuilder;

class Manager
{
    protected static ?ADOConnection $connection = null;

    /**
     * Tambah sambungan database
     */
    public function addConnection(ADOConnection $db): void
    {
        self::$connection = $db;
    }

    /**
     * Dapatkan sambungan aktif
     */
    public function getConnection(): ADOConnection
    {
        if (!self::$connection) {
            throw new \RuntimeException("No database connection set.");
        }
        return self::$connection;
    }

    /**
     * Query builder untuk table
     */
    public function table(string $table): QueryBuilder
    {
        $builder = new QueryBuilder($this->getConnection(), $table);
        return $builder;
    }

    /**
     * Jalankan SQL mentah (SELECT, INSERT, UPDATE, DELETE)
     */
    public function rawQuery(string $sql, array $params = []): mixed
    {
        $conn = $this->getConnection();
        $result = $conn->Execute($sql, $params);

        if (stripos(trim($sql), 'select') === 0) {
            return $result ? $result->GetRows() : [];
        }

        return $result ? $conn->Affected_Rows() : 0;
    }

    /**
     * Dapatkan ID yang baru dimasukkan
     */
    public function getInsertId(): int
    {
        return (int) $this->getConnection()->Insert_ID();
    }

    /**
     * Transaksi: mula
     */
    public function beginTransaction(): void
    {
        $this->getConnection()->BeginTrans();
    }

    /**
     * Transaksi: commit
     */
    public function commit(): void
    {
        $this->getConnection()->CommitTrans();
    }

    /**
     * Transaksi: rollback
     */
    public function rollback(): void
    {
        $this->getConnection()->RollbackTrans();
    }
}
