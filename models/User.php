<?php
/**
 * Модель пользователя
 */
class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает пользователя по ID
     */
   public function findById($id) {
    $id = (int)$id;
    $stmt = $this->db->prepare("
        SELECT u.*, r.name as role_name, r.display_name as role_display_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}
    /**
     * Получает пользователя по логину
     */
    public function findByLogin($login) {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, r.display_name as role_display_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.login = :login
        ");
        $stmt->execute([':login' => $login]);
        return $stmt->fetch();
    }
    
    /**
     * Получает список всех пользователей с фильтрацией
     */
    public function getAll($filters = [], $limit = 50, $offset = 0) {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filters['role'])) {
            $where[] = 'r.name = :role';
            $params[':role'] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(u.full_name LIKE :search OR u.login LIKE :search2 OR u.email LIKE :search3)';
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
            $params[':search3'] = '%' . $filters['search'] . '%';
        }
        
        if (isset($filters['is_active'])) {
            $where[] = 'u.is_active = :is_active';
            $params[':is_active'] = $filters['is_active'];
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name, r.display_name as role_display_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE {$whereStr}
            ORDER BY r.id ASC, u.full_name ASC
            LIMIT :limit OFFSET :offset
        ");
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Подсчёт пользователей с фильтрацией
     */
    public function countAll($filters = []) {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filters['role'])) {
            $where[] = 'r.name = :role';
            $params[':role'] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(u.full_name LIKE :search OR u.login LIKE :search2)';
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE {$whereStr}
        ");
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['cnt'];
    }
    
    /**
     * Создаёт нового пользователя
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (login, password_hash, full_name, email, phone, role_id)
            VALUES (:login, :password_hash, :full_name, :email, :phone, :role_id)
        ");
        $stmt->execute([
            ':login'         => $data['login'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':full_name'     => $data['full_name'],
            ':email'         => $data['email'] ?? null,
            ':phone'         => $data['phone'] ?? null,
            ':role_id'       => $data['role_id'],
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновляет пользователя
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['full_name'])) {
            $fields[] = 'full_name = :full_name';
            $params[':full_name'] = $data['full_name'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        if (isset($data['phone'])) {
            $fields[] = 'phone = :phone';
            $params[':phone'] = $data['phone'];
        }
        if (isset($data['role_id'])) {
            $fields[] = 'role_id = :role_id';
            $params[':role_id'] = $data['role_id'];
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = :is_active';
            $params[':is_active'] = $data['is_active'];
        }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
            $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['login'])) {
            $fields[] = 'login = :login';
            $params[':login'] = $data['login'];
        }
        
        if (empty($fields)) return false;
        
        $fieldStr = implode(', ', $fields);
        $stmt = $this->db->prepare("UPDATE users SET {$fieldStr} WHERE id = :id");
        return $stmt->execute($params);
    }
    
    /**
     * Удаляет пользователя
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Получает все роли
     */
    public function getRoles() {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY id");
        return $stmt->fetchAll();
    }
    
    /**
     * Проверяет уникальность логина
     */
    public function isLoginUnique($login, $excludeId = null) {
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE login = :login";
        $params = [':login' => $login];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['cnt'] == 0;
    }
    
    /**
     * Статистика по пользователям
     */
    public function getStatistics() {
        $stmt = $this->db->query("
            SELECT r.name, r.display_name, COUNT(u.id) as count
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id AND u.is_active = 1
            GROUP BY r.id, r.name, r.display_name
            ORDER BY r.id
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Получает пользователей по роли
     */
    public function getByRole($roleName) {
        $stmt = $this->db->prepare("
            SELECT u.*, r.display_name as role_display_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE r.name = :role AND u.is_active = 1
            ORDER BY u.full_name
        ");
        $stmt->execute([':role' => $roleName]);
        return $stmt->fetchAll();
    }
}