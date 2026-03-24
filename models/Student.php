<?php
/**
 * Модель ученика
 */
class Student {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает ученика по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.full_name, u.email, u.login, c.name as class_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            JOIN classes c ON s.class_id = c.id
            WHERE s.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает ученика по user_id
     */
    public function findByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.full_name, u.email, c.name as class_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            JOIN classes c ON s.class_id = c.id
            WHERE s.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }
    
    /**
     * Создаёт запись ученика
     */
    public function create($userId, $classId) {
        $stmt = $this->db->prepare("
            INSERT INTO students (user_id, class_id) VALUES (:user_id, :class_id)
        ");
        $stmt->execute([':user_id' => $userId, ':class_id' => $classId]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновляет класс ученика
     */
    public function updateClass($studentId, $classId) {
        $stmt = $this->db->prepare("UPDATE students SET class_id = :class_id WHERE id = :id");
        return $stmt->execute([':class_id' => $classId, ':id' => $studentId]);
    }
    
    /**
     * Удаляет запись ученика по user_id
     */
    public function deleteByUserId($userId) {
        $stmt = $this->db->prepare("DELETE FROM students WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }
    
    /**
     * Получает учеников по классу
     */
    public function getByClassId($classId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.full_name, u.email
            FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.class_id = :class_id AND u.is_active = 1
            ORDER BY u.full_name
        ");
        $stmt->execute([':class_id' => $classId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает всех учеников
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT s.*, u.full_name, u.email, c.name as class_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            JOIN classes c ON s.class_id = c.id
            WHERE u.is_active = 1
            ORDER BY c.name, u.full_name
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Получает количество учеников в классе
     */
    public function countByClass($classId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM students s
            JOIN users u ON s.user_id = u.id
            WHERE s.class_id = :class_id AND u.is_active = 1
        ");
        $stmt->execute([':class_id' => $classId]);
        $result = $stmt->fetch();
        return (int)$result['cnt'];
    }
    
    /**
     * Получает детей родителя
     */
    public function getChildrenByParentId($parentUserId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.full_name, u.email, c.name as class_name, 
                   ps.relationship
            FROM parent_student ps
            JOIN students s ON ps.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN classes c ON s.class_id = c.id
            WHERE ps.parent_user_id = :parent_user_id
            ORDER BY u.full_name
        ");
        $stmt->execute([':parent_user_id' => $parentUserId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Привязывает родителя к ученику
     */
    public function addParent($studentId, $parentUserId, $relationship, $isPrimary = false) {
        $stmt = $this->db->prepare("
            INSERT INTO parent_student (parent_user_id, student_id, relationship, is_primary)
            VALUES (:parent_user_id, :student_id, :relationship, :is_primary)
            ON DUPLICATE KEY UPDATE relationship = :relationship2, is_primary = :is_primary2
        ");
        $stmt->execute([
            ':parent_user_id' => $parentUserId,
            ':student_id' => $studentId,
            ':relationship' => $relationship,
            ':is_primary' => $isPrimary ? 1 : 0,
            ':relationship2' => $relationship,
            ':is_primary2' => $isPrimary ? 1 : 0,
        ]);
    }
    
    /**
     * Удаляет связи родитель-ученик для родителя
     */
    public function removeParentLinks($parentUserId) {
        $stmt = $this->db->prepare("DELETE FROM parent_student WHERE parent_user_id = :pid");
        $stmt->execute([':pid' => $parentUserId]);
    }
    
    /**
     * Получает родителей ученика
     */
    public function getParents($studentId) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.full_name, u.email, u.phone, ps.relationship, ps.is_primary
            FROM parent_student ps
            JOIN users u ON ps.parent_user_id = u.id
            WHERE ps.student_id = :student_id
            ORDER BY ps.is_primary DESC, u.full_name
        ");
        $stmt->execute([':student_id' => $studentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Средний балл ученика по предмету
     */
    public function getAverageGrade($studentId, $subjectId = null, $termId = null) {
        $where = ['g.student_id = :student_id'];
        $params = [':student_id' => $studentId];
        
        if ($subjectId) {
            $where[] = 'g.subject_id = :subject_id';
            $params[':subject_id'] = $subjectId;
        }
        
        if ($termId) {
            $where[] = 'g.date BETWEEN t.start_date AND t.end_date';
            $params[':term_id'] = $termId;
        }
        
        $whereStr = implode(' AND ', $where);
        
        $sql = "SELECT ROUND(AVG(g.grade), 2) as avg_grade FROM grades g";
        if ($termId) {
            $sql .= " JOIN terms t ON t.id = :term_id";
        }
        $sql .= " WHERE {$whereStr}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['avg_grade'];
    }
    /**
 * Средний балл ученика за период
 */
public function getAverageGradeByPeriod($studentId, $subjectId = null, $dateFrom = null, $dateTo = null) {
    $where = ['g.student_id = :student_id'];
    $params = [':student_id' => $studentId];

    if ($subjectId) {
        $where[] = 'g.subject_id = :subject_id';
        $params[':subject_id'] = $subjectId;
    }

    if ($dateFrom) {
        $where[] = 'g.date >= :date_from';
        $params[':date_from'] = $dateFrom;
    }

    if ($dateTo) {
        $where[] = 'g.date <= :date_to';
        $params[':date_to'] = $dateTo;
    }

    $whereStr = implode(' AND ', $where);

    $stmt = $this->db->prepare("
        SELECT ROUND(AVG(g.grade), 2) as avg_grade
        FROM grades g
        WHERE {$whereStr}
    ");
    $stmt->execute($params);
    $result = $stmt->fetch();

    return $result['avg_grade'];
}
}