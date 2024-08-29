<?php
namespace API\Config;

class SimploDB
{
    private $pdo;

    public function __construct($config)
    {
        $port = isset($config['port']) ? ";port={$config['port']}" : "";
        $dsn = "mysql:host={$config['host']}{$port};dbname={$config['database']};charset=utf8mb4";
        $this->pdo = new \PDO($dsn, $config['username'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_PERSISTENT => true
        ]);
    }

    public function select($table, $columns = '*', $conditions = null, $limit = null, $offset = 0)
    {
        $sql = "SELECT {$columns} FROM {$table}";
        $params = [];

        if ($conditions) {
            $whereData = $this->buildWhere($conditions);
            $sql .= " WHERE " . $whereData['sql'];
            $params = $whereData['params'];
        }

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = (int) $limit;
            if ($offset > 0) {
                $sql .= " OFFSET :offset";
                $params[':offset'] = (int) $offset;
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($table, $conditions = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        $params = [];

        if ($conditions) {
            $whereData = $this->buildWhere($conditions);
            $sql .= " WHERE " . $whereData['sql'];
            $params = $whereData['params'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['count'];
    }

    public function insert($table, $data)
    {
        $columns = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);
            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $this->sanitizeValue($value));
            }
            $stmt->execute();

            $lastInsertId = $this->pdo->lastInsertId();

            $this->pdo->commit();

            return $lastInsertId;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Error en la inserción: " . $e->getMessage());
        }
    }

    public function update($table, $data, $where)
    {
        $set = [];
        $params = [];

        foreach ($data as $column => $value) {
            $set[] = "`{$column}` = :set_{$column}";
            $params["set_{$column}"] = $this->sanitizeValue($value);
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $set);

        if ($where) {
            $whereData = $this->buildWhere($where);
            $sql .= " WHERE " . $whereData['sql'];
            $params = array_merge($params, $whereData['params']);
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $rowCount = $stmt->rowCount();

            $this->pdo->commit();

            return $rowCount;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw new \Exception("Error en la actualización: " . $e->getMessage());
        }
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM {$table}";
        $params = [];

        if ($where) {
            $whereData = $this->buildWhere($where);
            $sql .= " WHERE " . $whereData['sql'];
            $params = $whereData['params'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    private function buildWhere($where)
    {
        $conditions = [];
        $params = [];

        foreach ($where as $key => $value) {
            if (is_array($value)) {
                if (isset($value['operator'])) {
                    switch (strtoupper($value['operator'])) {
                        case 'IN':
                        case 'NOT IN':
                            $placeholders = ':' . implode(', :', array_map(function($i) use ($key) { return "{$key}_{$i}"; }, array_keys($value['values'])));
                            $conditions[] = "{$key} " . $value['operator'] . " ({$placeholders})";
                            foreach ($value['values'] as $i => $v) {
                                $params["{$key}_{$i}"] = $v;
                            }
                            break;
                        case 'BETWEEN':
                            $conditions[] = "{$key} BETWEEN :{$key}_start AND :{$key}_end";
                            $params["{$key}_start"] = $value['values'][0];
                            $params["{$key}_end"] = $value['values'][1];
                            break;
                        default:
                            // Manejar otros operadores si es necesario
                            break;
                    }
                } else {
                    $or = [];
                    foreach ($value as $i => $condition) {
                        $or[] = "{$condition['field']} LIKE :{$key}_{$i}";
                        $params["{$key}_{$i}"] = $condition['value'];
                    }
                    $conditions[] = '(' . implode(' OR ', $or) . ')';
                }
            } else {
                $conditions[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        return [
            'sql' => implode(' AND ', $conditions),
            'params' => $params
        ];
    }

    private function sanitizeValue($value)
    {
        if ($value === "true") return 1;
        if ($value === "false") return 0;
        return $value;
    }

    public function __destruct()
    {
        // Cerrar explícitamente la conexión
        $this->pdo = null;
    }
}