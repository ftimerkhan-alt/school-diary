<?php
/**
 * Модель посещаемости
 */
class AttendanceModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает запись по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM attendance WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает посещаемость по классу, предмету и дате
     */
    public function getByClassSubjectDate($classId, $subjectId, $date) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.full_name as student_name, st.id as student_id
            FROM students st
            JOIN users u ON st.user_id = u.id
            LEFT JOIN attendance a ON a.student_id = st.id 
                AND a.subject_id = :subject_id 
                AND a.date = :date
            WHERE st.class_id = :class_id AND u.is_active = 1
            ORDER BY u.full_name
        ");
        $stmt->execute([
            ':class_id' => $classId,
            ':subject_id' => $subjectId,
            ':date' => $date,
        ]);
        return $stmt->fetchAll();
    }
    
    /**
     * Сохраняет/обновляет посещаемость
     */
    public function save($studentId, $subjectId, $date, $status, $comment = null, $markedBy = null) {
        $stmt = $this->db->prepare("
            INSERT INTO attendance (student_id, subject_id, date, status, comment, marked_by)
            VALUES (:student_id, :subject_id, :date, :status, :comment, :marked_by)
            ON DUPLICATE KEY UPDATE 
                status = VALUES(status), 
                comment = VALUES(comment),
                marked_by = VALUES(marked_by)
        ");
        return $stmt->execute([
            ':student_id' => $studentId,
            ':subject_id' => $subjectId,
            ':date' => $date,
            ':status' => $status,
            ':comment' => $comment,
            ':marked_by' => $markedBy,
        ]);
    }
    
    /**
     * Получает посещаемость ученика
     */
    public function getByStudent($studentId, $subjectId = null, $dateFrom = null, $dateTo = null) {
        $where = ['a.student_id = :student_id'];
        $params = [':student_id' => $studentId];
        
        if ($subjectId) {
            $where[] = 'a.subject_id = :subject_id';
            $params[':subject_id'] = $subjectId;
        }
        if ($dateFrom) {
            $where[] = 'a.date >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'a.date <= :date_to';
            $params[':date_to'] = $dateTo;
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT a.*, s.name as subject_name
            FROM attendance a
            JOIN subjects s ON a.subject_id = s.id
            WHERE {$whereStr}
            ORDER BY a.date DESC, s.name
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Статистика посещаемости ученика
     */
    public function getStudentStats($studentId, $subjectId = null) {
        $where = ['a.student_id = :student_id'];
        $params = [':student_id' => $studentId];
        
        if ($subjectId) {
            $where[] = 'a.subject_id = :subject_id';
            $params[':subject_id'] = $subjectId;
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
            FROM attendance a
            WHERE {$whereStr}
        ");
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Статистика посещаемости класса
     */
    public function getClassStats($classId, $dateFrom = null, $dateTo = null) {
        $where = ['st.class_id = :class_id'];
        $params = [':class_id' => $classId];
        
        if ($dateFrom) {
            $where[] = 'a.date >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'a.date <= :date_to';
            $params[':date_to'] = $dateTo;
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
            FROM attendance a
            JOIN students st ON a.student_id = st.id
            WHERE {$whereStr}
        ");
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Посещаемость по ученикам класса (для отчёта)
     */
    public function getClassStudentStats($classId) {
        $stmt = $this->db->prepare("
            SELECT st.id as student_id, u.full_name,
                COUNT(a.id) as total,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
            FROM students st
            JOIN users u ON st.user_id = u.id
            LEFT JOIN attendance a ON a.student_id = st.id
            WHERE st.class_id = :class_id AND u.is_active = 1
            GROUP BY st.id, u.full_name
            ORDER BY u.full_name
        ");
        $stmt->execute([':class_id' => $classId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Часто пропускающие ученики
     */
    public function getFrequentAbsentees($minAbsences = 5) {
        $stmt = $this->db->prepare("
            SELECT st.id, u.full_name, c.name as class_name,
                   COUNT(*) as absence_count
            FROM attendance a
            JOIN students st ON a.student_id = st.id
            JOIN users u ON st.user_id = u.id
            JOIN classes c ON st.class_id = c.id
            WHERE a.status = 'absent'
            GROUP BY st.id, u.full_name, c.name
            HAVING absence_count >= :min
            ORDER BY absence_count DESC
        ");
        $stmt->execute([':min' => $minAbsences]);
        return $stmt->fetchAll();
    }
    /**
 * Посещаемость по ученикам класса за период
 */
public function getClassStudentStatsByPeriod($classId, $dateFrom = null, $dateTo = null) {
    $where = ['st.class_id = :class_id'];
    $params = [':class_id' => $classId];

    if ($dateFrom) {
        $where[] = 'a.date >= :date_from';
        $params[':date_from'] = $dateFrom;
    }

    if ($dateTo) {
        $where[] = 'a.date <= :date_to';
        $params[':date_to'] = $dateTo;
    }

    $whereStr = implode(' AND ', $where);

    $stmt = $this->db->prepare("
        SELECT st.id as student_id, u.full_name,
            COUNT(a.id) as total,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
            SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
        FROM students st
        JOIN users u ON st.user_id = u.id
        LEFT JOIN attendance a ON a.student_id = st.id
        WHERE {$whereStr} AND u.is_active = 1
        GROUP BY st.id, u.full_name
        ORDER BY u.full_name
    ");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Часто пропускающие за период
 */
public function getFrequentAbsenteesByPeriod($minAbsences = 5, $dateFrom = null, $dateTo = null) {
    $where = ["a.status = 'absent'"];
    $params = [':min' => $minAbsences];

    if ($dateFrom) {
        $where[] = 'a.date >= :date_from';
        $params[':date_from'] = $dateFrom;
    }

    if ($dateTo) {
        $where[] = 'a.date <= :date_to';
        $params[':date_to'] = $dateTo;
    }

    $whereStr = implode(' AND ', $where);

    $stmt = $this->db->prepare("
        SELECT st.id, u.full_name, c.name as class_name,
               COUNT(*) as absence_count
        FROM attendance a
        JOIN students st ON a.student_id = st.id
        JOIN users u ON st.user_id = u.id
        JOIN classes c ON st.class_id = c.id
        WHERE {$whereStr}
        GROUP BY st.id, u.full_name, c.name
        HAVING absence_count >= :min
        ORDER BY absence_count DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}