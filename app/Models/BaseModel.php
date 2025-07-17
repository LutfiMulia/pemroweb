<?php

namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find record by ID
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        
        $result = $stmt->fetch();
        
        if ($result) {
            return $this->hideFields($result);
        }
        
        return null;
    }
    
    /**
     * Find first record matching conditions
     */
    public function findWhere($conditions)
    {
        $where = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $whereClause = implode(' AND ', $where);
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$whereClause} LIMIT 1");
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        
        if ($result) {
            return $this->hideFields($result);
        }
        
        return null;
    }
    
    /**
     * Get all records
     */
    public function all($orderBy = null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        return array_map([$this, 'hideFields'], $results);
    }
    
    /**
     * Get records matching conditions
     */
    public function where($conditions, $orderBy = null, $limit = null, $offset = null)
    {
        $where = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $whereClause = implode(' AND ', $where);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $results = $stmt->fetchAll();
        
        return array_map([$this, 'hideFields'], $results);
    }
    
    /**
     * Create new record
     */
    public function create($data)
    {
        // Filter data to only include fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add timestamps if enabled
        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($filteredData);
            
            $id = $this->db->lastInsertId();
            
            return $this->find($id);
        } catch (PDOException $e) {
            throw new \Exception("Error creating record: " . $e->getMessage());
        }
    }
    
    /**
     * Update record
     */
    public function update($id, $data)
    {
        // Filter data to only include fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add updated timestamp if enabled
        if ($this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = [];
        foreach ($filteredData as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }
        
        $fieldsClause = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET {$fieldsClause} WHERE {$this->primaryKey} = :id";
        
        $filteredData['id'] = $id;
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($filteredData);
            
            return $this->find($id);
        } catch (PDOException $e) {
            throw new \Exception("Error updating record: " . $e->getMessage());
        }
    }
    
    /**
     * Delete record
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new \Exception("Error deleting record: " . $e->getMessage());
        }
    }
    
    /**
     * Count records
     */
    public function count($conditions = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            
            foreach ($conditions as $key => $value) {
                $where[] = "{$key} = ?";
                $params[] = $value;
            }
            
            $whereClause = implode(' AND ', $where);
            $sql .= " WHERE {$whereClause}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Check if record exists
     */
    public function exists($conditions)
    {
        return $this->count($conditions) > 0;
    }
    
    /**
     * Get paginated results
     */
    public function paginate($page = 1, $perPage = 20, $conditions = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $totalCount = $this->count($conditions);
        
        // Get records
        $records = empty($conditions) 
            ? $this->all($orderBy, $perPage, $offset)
            : $this->where($conditions, $orderBy, $perPage, $offset);
        
        return [
            'data' => $records,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalCount,
                'total_pages' => ceil($totalCount / $perPage),
                'has_previous' => $page > 1,
                'has_next' => $page < ceil($totalCount / $perPage)
            ]
        ];
    }
    
    /**
     * Execute raw query
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Error executing query: " . $e->getMessage());
        }
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit()
    {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return $this->db->rollback();
    }
    
    /**
     * Filter data to only include fillable fields
     */
    private function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Hide fields from result
     */
    private function hideFields($data)
    {
        if (empty($this->hidden)) {
            return $data;
        }
        
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }
    
    /**
     * Get table name
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Get primary key
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    
    /**
     * Get fillable fields
     */
    public function getFillable()
    {
        return $this->fillable;
    }
    
    /**
     * Get hidden fields
     */
    public function getHidden()
    {
        return $this->hidden;
    }
}
