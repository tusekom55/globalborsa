<?php
/**
 * Base Model Class
 */

class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
        
        // Auto-set table name if not defined
        if (!$this->table) {
            $className = get_class($this);
            $this->table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
        }
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $result = $this->db->selectOne($sql, ['id' => $id]);
        
        return $result ? $this->hideFields($result) : null;
    }

    public function findBy($field, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        $result = $this->db->selectOne($sql, ['value' => $value]);
        
        return $result ? $this->hideFields($result) : null;
    }

    public function all($orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $results = $this->db->select($sql);
        
        return array_map([$this, 'hideFields'], $results);
    }

    public function where($conditions, $params = [], $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $results = $this->db->select($sql, $params);
        
        return array_map([$this, 'hideFields'], $results);
    }

    public function create($data)
    {
        // Filter only fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add timestamps
        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $id = $this->db->insert($this->table, $filteredData);
        
        return $this->find($id);
    }

    public function update($id, $data)
    {
        // Filter only fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add updated timestamp
        if ($this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $affected = $this->db->update(
            $this->table, 
            $filteredData, 
            "{$this->primaryKey} = :id", 
            ['id' => $id]
        );
        
        return $affected > 0 ? $this->find($id) : null;
    }

    public function delete($id)
    {
        return $this->db->delete(
            $this->table, 
            "{$this->primaryKey} = :id", 
            ['id' => $id]
        ) > 0;
    }

    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        $result = $this->db->selectOne($sql, $params);
        
        return (int)$result['count'];
    }

    public function exists($conditions, $params = [])
    {
        return $this->count($conditions, $params) > 0;
    }

    public function paginate($page = 1, $perPage = null, $conditions = null, $params = [], $orderBy = null)
    {
        if ($perPage === null) {
            $perPage = ITEMS_PER_PAGE;
        }
        
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $total = $this->count($conditions, $params);
        
        // Get data
        $sql = "SELECT * FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->db->select($sql, $params);
        $data = array_map([$this, 'hideFields'], $data);
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    public function search($query, $fields, $page = 1, $perPage = null)
    {
        if ($perPage === null) {
            $perPage = ITEMS_PER_PAGE;
        }
        
        $conditions = [];
        $params = [];
        
        foreach ($fields as $field) {
            $conditions[] = "{$field} LIKE :query";
        }
        
        $whereClause = implode(' OR ', $conditions);
        $params['query'] = "%{$query}%";
        
        return $this->paginate($page, $perPage, $whereClause, $params);
    }

    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function hideFields($data)
    {
        if (empty($this->hidden) || !is_array($data)) {
            return $data;
        }
        
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }

    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollback()
    {
        return $this->db->rollback();
    }

    public function raw($sql, $params = [])
    {
        return $this->db->select($sql, $params);
    }

    public function rawOne($sql, $params = [])
    {
        return $this->db->selectOne($sql, $params);
    }
}
