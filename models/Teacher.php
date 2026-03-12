<?php
/**
 * Модель учителя
 */
class Teacher {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает учителя по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT t.*, u.full_name, u.email, u.login
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            WHERE t.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает учителя по user_id
     */
    public function findByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT t.*, u.full_name, u.email
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            WHERE t.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }
    
    /**
     * Создаёт запись учителя
     */
    public function create($userId, $isClassTeacher = false) {
        $stmt = $this->db->prepare("
            INSERT INTO teachers (user_id, is_class_teacher)
            VALUES (:user_id, :is_class_teacher)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':is_class_teacher' => $isClassTeacher ? 1 : 0,
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Удаляет запись учителя по user_id
     */
    public function deleteByUserId($userId) {
        $stmt = $this->db->prepare("DELETE FROM teachers WHERE user_id = :user_id");
        return $stmt->execute([':user_id' => $userId]);
    }
    
    /**
     * Получает всех учителей
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT t.*, u.full_name, u.email, u.login, u.is_active,
                   r.name as role_name
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE u.is_active = 1
            ORDER BY u.full_name
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Получает предметы учителя
     */
    public function getSubjects($teacherId) {
        $stmt = $this->db->prepare("
            SELECT s.* FROM subjects s
            JOIN teacher_subjects ts ON s.id = ts.subject_id
            WHERE ts.teacher_id = :teacher_id
            ORDER BY s.name
        ");
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает классы учителя
     */
    public function getClasses($teacherId) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT c.* FROM classes c
            JOIN teacher_class_subjects tcs ON c.id = tcs.class_id
            WHERE tcs.teacher_id = :teacher_id
            ORDER BY c.name
        ");
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает классы учителя по конкретному предмету
     */
    public function getClassesBySubject($teacherId, $subjectId) {
        $stmt = $this->db->prepare("
            SELECT c.* FROM classes c
            JOIN teacher_class_subjects tcs ON c.id = tcs.class_id
            WHERE tcs.teacher_id = :teacher_id AND tcs.subject_id = :subject_id
            ORDER BY c.name
        ");
        $stmt->execute([
            ':teacher_id' => $teacherId,
            ':subject_id' => $subjectId,
        ]);
        return $stmt->fetchAll();
    }
    
    /**
     * Устанавливает предметы учителя
     */
    public function setSubjects($teacherId, $subjectIds) {
        // Удаляем старые связи
        $stmt = $this->db->prepare("DELETE FROM teacher_subjects WHERE teacher_id = :teacher_id");
        $stmt->execute([':teacher_id' => $teacherId]);
        
        // Добавляем новые
        if (!empty($subjectIds)) {
            $stmt = $this->db->prepare("
                INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES (:teacher_id, :subject_id)
            ");
            foreach ($subjectIds as $subjectId) {
                $stmt->execute([
                    ':teacher_id' => $teacherId,
                    ':subject_id' => $subjectId,
                ]);
            }
        }
    }
    
    /**
     * Устанавливает классы по предметам для учителя
     */
    public function setClassSubjects($teacherId, $classSubjects) {
        // Удаляем старые связи
        $stmt = $this->db->prepare("DELETE FROM teacher_class_subjects WHERE teacher_id = :tid");
        $stmt->execute([':tid' => $teacherId]);
        
        if (!empty($classSubjects)) {
            $stmt = $this->db->prepare("
                INSERT INTO teacher_class_subjects (teacher_id, subject_id, class_id) 
                VALUES (:tid, :sid, :cid)
            ");
            foreach ($classSubjects as $cs) {
                $stmt->execute([
                    ':tid' => $teacherId,
                    ':sid' => $cs['subject_id'],
                    ':cid' => $cs['class_id'],
                ]);
            }
        }
    }
    
    /**
     * Получает все привязки учителя к классам и предметам
     */
    public function getClassSubjects($teacherId) {
        $stmt = $this->db->prepare("
            SELECT tcs.*, s.name as subject_name, c.name as class_name
            FROM teacher_class_subjects tcs
            JOIN subjects s ON tcs.subject_id = s.id
            JOIN classes c ON tcs.class_id = c.id
            WHERE tcs.teacher_id = :teacher_id
            ORDER BY c.name, s.name
        ");
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает нагрузку учителя
     */
    public function getWorkload($teacherId) {
        $stmt = $this->db->prepare("
            SELECT tw.*, s.name as subject_name, c.name as class_name
            FROM teacher_workload tw
            JOIN subjects s ON tw.subject_id = s.id
            JOIN classes c ON tw.class_id = c.id
            WHERE tw.teacher_id = :teacher_id
            ORDER BY c.name, s.name
        ");
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает общую нагрузку (часы) учителя
     */
    public function getTotalHours($teacherId) {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(hours_per_week), 0) as total
            FROM teacher_workload WHERE teacher_id = :teacher_id
        ");
        $stmt->execute([':teacher_id' => $teacherId]);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }
    
    /**
     * Получает всех учителей с нагрузкой
     */
    public function getAllWithWorkload() {
        $stmt = $this->db->query("
            SELECT t.id, t.user_id, u.full_name,
                   COUNT(DISTINCT tcs.class_id) as class_count,
                   COUNT(DISTINCT tcs.subject_id) as subject_count,
                   COALESCE(SUM(tw.hours_per_week), 0) as total_hours
            FROM teachers t
            JOIN users u ON t.user_id = u.id
            LEFT JOIN teacher_class_subjects tcs ON t.id = tcs.teacher_id
            LEFT JOIN teacher_workload tw ON t.id = tw.teacher_id
            WHERE u.is_active = 1
            GROUP BY t.id, t.user_id, u.full_name
            ORDER BY u.full_name
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Проверяет, ведёт ли учитель данный предмет в данном классе
     */
    public function teachesSubjectInClass($teacherId, $subjectId, $classId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM teacher_class_subjects
            WHERE teacher_id = :tid AND subject_id = :sid AND class_id = :cid
        ");
        $stmt->execute([':tid' => $teacherId, ':sid' => $subjectId, ':cid' => $classId]);
        $result = $stmt->fetch();
        return $result['cnt'] > 0;
    }
    /**
 * Возвращает учителей, которые ведут предмет (teacher_subjects)
 */
public function getTeachersBySubject($subjectId) {
    $stmt = $this->db->prepare("
        SELECT t.id, u.full_name
        FROM teacher_subjects ts
        JOIN teachers t ON ts.teacher_id = t.id
        JOIN users u ON t.user_id = u.id
        WHERE ts.subject_id = :sid AND u.is_active = 1
        ORDER BY u.full_name
    ");
    $stmt->execute([':sid' => (int)$subjectId]);
    return $stmt->fetchAll();
}
}