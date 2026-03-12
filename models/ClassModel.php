<?php
/**
 * Модель класса
 * Название ClassModel, т.к. Class — зарезервированное слово PHP
 */
class ClassModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает класс по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.full_name as teacher_name
            FROM classes c
            LEFT JOIN teachers t ON c.class_teacher_id = t.id
            LEFT JOIN users u ON t.user_id = u.id
            WHERE c.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает все классы
     */
    public function getAll($year = null) {
        $sql = "
            SELECT c.*, u.full_name as teacher_name,
                   (SELECT COUNT(*) FROM students s 
                    JOIN users us ON s.user_id = us.id 
                    WHERE s.class_id = c.id AND us.is_active = 1) as student_count
            FROM classes c
            LEFT JOIN teachers t ON c.class_teacher_id = t.id
            LEFT JOIN users u ON t.user_id = u.id
        ";
        $params = [];
        
        if ($year) {
            $sql .= " WHERE c.year = :year";
            $params[':year'] = $year;
        }
        
        $sql .= " ORDER BY CAST(REGEXP_SUBSTR(c.name, '^[0-9]+') AS UNSIGNED) ASC, c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Создаёт класс
     */
    public function create($name, $year, $classTeacherId = null) {
        $stmt = $this->db->prepare("
            INSERT INTO classes (name, year, class_teacher_id)
            VALUES (:name, :year, :class_teacher_id)
        ");
        $stmt->execute([
            ':name' => $name,
            ':year' => $year,
            ':class_teacher_id' => $classTeacherId,
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновляет класс
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params[':name'] = $data['name'];
        }
        if (isset($data['year'])) {
            $fields[] = 'year = :year';
            $params[':year'] = $data['year'];
        }
        if (array_key_exists('class_teacher_id', $data)) {
            $fields[] = 'class_teacher_id = :class_teacher_id';
            $params[':class_teacher_id'] = $data['class_teacher_id'];
        }
        
        if (empty($fields)) return false;
        
        $fieldStr = implode(', ', $fields);
        $stmt = $this->db->prepare("UPDATE classes SET {$fieldStr} WHERE id = :id");
        return $stmt->execute($params);
    }
    
    /**
     * Удаляет класс
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM classes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Получает предметы, преподаваемые в классе
     */
    public function getSubjects($classId) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT s.*, u.full_name as teacher_name, tcs.teacher_id
            FROM teacher_class_subjects tcs
            JOIN subjects s ON tcs.subject_id = s.id
            JOIN teachers t ON tcs.teacher_id = t.id
            JOIN users u ON t.user_id = u.id
            WHERE tcs.class_id = :class_id
            ORDER BY s.name
        ");
        $stmt->execute([':class_id' => $classId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает средний балл класса по предмету
     */
    public function getAverageGrade($classId, $subjectId = null) {
        $where = ['s.class_id = :class_id'];
        $params = [':class_id' => $classId];
        
        if ($subjectId) {
            $where[] = 'g.subject_id = :subject_id';
            $params[':subject_id'] = $subjectId;
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT ROUND(AVG(g.grade), 2) as avg_grade
            FROM grades g
            JOIN students s ON g.student_id = s.id
            WHERE {$whereStr}
        ");
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['avg_grade'];
    }
}