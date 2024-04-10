<?php

class Database {
    private $connection;
    private $where = [];
    private $limit;
    private $sort;
    private $debugMode = false;
    private $lastQuery;

    public function __construct($config) {
        $this->connection = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        if ($this->connection->connect_error) {
            die("Erro de conexão: " . $this->connection->connect_error);
        }
    }

    public function limit($start, $count) {
        $this->limit = "LIMIT $start, $count";
        return $this;
    }

    public function sort($column, $order = 'ASC') {
        $this->sort = "ORDER BY $column $order";
        return $this;
    }

    public function where($conditions) {
        foreach ($conditions as $key => $value) {
            $operand = $this->connection->real_escape_string($value);
            // Adiciona automaticamente o operador LIKE se o valor contiver curingas
            if (strpos($value, '%') !== false || strpos($value, '_') !== false) {
                $this->where[] = "$key LIKE '$operand'";
            } else {
                $this->where[] = "$key = '$operand'";
            }
        }
        return $this;
    }

    public function buildWhereClause($conditions) {
        $where = [];
        foreach ($conditions as $key => $value) {
            $operand = $this->connection->real_escape_string($value);
            if (strpos($key, '[') !== false && strpos($key, ']') !== false) {
                // Extrai o operador e a coluna
                $operator = substr($key, strpos($key, '[') + 1, strpos($key, ']') - strpos($key, '[') - 1);
                $column = substr($key, 0, strpos($key, '['));
                if ($operator === '=') {
                    $operator = 'LIKE';
                    $operand = "'$operand%'";
                } elseif ($operator === '~') {
                    $operator = 'LIKE';
                    $operand = "'%$operand%'";
                } else {
                    $operand = "'$operand'";
                }
                $where[] = "$column $operator $operand";
            } else {
                $where[] = "$key = '$operand'";
            }
        }
        return implode(" AND ", $where);
    }
    

    public function select($table, $columns = '*', $conditions = []) {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
    
        $sql = "SELECT $columns FROM $table";
    
        if (!empty($conditions)) {
            $whereClause = $this->buildWhereClause($conditions);
            $sql .= " WHERE $whereClause";
        }
    
        if ($this->limit) {
            $sql .= " $this->limit";
        }
    
        if ($this->sort) {
            $sql .= " $this->sort";
        }
    
        // Alteração: substituir '=' por 'LIKE' na cláusula WHERE
        $sql = str_replace('=', 'LIKE', $sql);

        $result = $this->execute($sql);
    
        if ($this->debugMode) {
            $this->debug($sql);
        }
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function insert($table, $data) {
        $keys = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_map([$this->connection, 'real_escape_string'], array_values($data))) . "'";

        $sql = "INSERT INTO $table ($keys) VALUES ($values)";

        return $this->execute($sql);
    }

    public function update($table, $data, $conditions = []) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = '" . $this->connection->real_escape_string($value) . "'";
        }

        $sql = "UPDATE $table SET " . implode(", ", $set);

        if (!empty($conditions)) {
            $whereClause = $this->buildWhereClause($conditions);
            $sql .= " WHERE $whereClause";
        }

        // Alteração: substituir '=' por 'LIKE' na cláusula WHERE
        $sql = str_replace('=', 'LIKE', $sql);

        return $this->execute($sql);
    }

    public function delete($table, $conditions = []) {
        $sql = "DELETE FROM $table";

        if (!empty($conditions)) {
            $whereClause = $this->buildWhereClause($conditions);
            $sql .= " WHERE $whereClause";
        }

        // Alteração: substituir '=' por 'LIKE' na cláusula WHERE
        $sql = str_replace('=', 'LIKE', $sql);

        return $this->execute($sql);
    }

    public function count($table, $column = null, $conditions = []) {
        $sql = "SELECT COUNT(*) as count";
    
        if ($column !== null) {
            $sql .= ", $column";
        }
    
        $sql .= " FROM $table";
    
        if (!empty($conditions)) {
            $whereClause = $this->buildWhereClause($conditions);
            $sql .= " WHERE $whereClause";
        }
    
        // Alteração: substituir '=' por 'LIKE' na cláusula WHERE
        $sql = str_replace('=', 'LIKE', $sql);

        if ($this->debugMode) {
            $this->debug($sql);
        }
    
        return (int) $this->execute($sql)->fetch_assoc()['count'];
    }

    public function has($table, $conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM $table";
    
        if (!empty($conditions)) {
            $whereClause = $this->buildWhereClause($conditions);
            $sql .= " WHERE $whereClause";
        }
    
        // Alteração: substituir '=' por 'LIKE' na cláusula WHERE
        $sql = str_replace('=', 'LIKE', $sql);

        if ($this->debugMode) {
            $this->debug($sql);
        }
    
        $result = $this->execute($sql)->fetch_assoc();
        return (int) $result['count'] > 0;
    }

    public function debug($sql = null) {
        if ($sql !== null) {
            echo "<div style=\"background-color: #f0f0f0; padding: 10px; font-family: 'Courier New', monospace; font-size: 14px;\">";
            echo "<span style=\"color: black;\">" . htmlspecialchars($sql) . "</span><br>";
            echo "</div>";
    
            return $this;
        } elseif (!$this->debugMode) {
            $this->debugMode = true;
            echo "<div style=\"background-color: yellow; color: black; padding: 10px; font-family: 'Courier New', monospace; font-size: 13px; font-weight: bold;\">MODO DE DEBUG ATIVADO.</div>";
            return $this;
        }
        return $this;
    }

    public function __get($name) {
        if ($name === 'debug') {
            return function() {
                return $this->debug();
            };
        }
    }

    private function execute($sql) {
        $this->lastQuery = $sql;
        if ($this->debugMode) {
            $this->debug($sql);
        }
        return $this->connection->query($sql);
    }
}

?>
