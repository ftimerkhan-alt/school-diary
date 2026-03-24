<?php
/**
 * Модель оценки
 */
class GradeModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает оценку по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT g.*, s.name as subject_name, 
                   st.user_id as student_user_id,
                   u.full_name as student_name,
                   ut.full_name as teacher_name
            FROM grades g
            JOIN subjects s ON g.subject_id = s.id
            JOIN students st ON g.student_id = st.id
            JOIN users u ON st.user_id = u.id
            JOIN teachers t ON g.teacher_id = t.id
            JOIN users ut ON t.user_id = ut.id
            WHERE g.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает оценки для журнала (по классу и предмету)
     */
    public function getJournal($classId, $subjectId, $dateFrom = null, $dateTo = null) {
        $where = ['st.class_id = :class_id', 'g.subject_id = :subject_id'];
        $params = [':class_id' => $classId, ':subject_id' => $subjectId];
        
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
            SELECT g.*, u.full_name as student_name, st.id as student_id
            FROM grades g
            JOIN students st ON g.student_id = st.id
            JOIN users u ON st.user_id = u.id
            WHERE {$whereStr}
            ORDER BY u.full_name, g.date
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает уникальные даты оценок для журнала
     */
    public function getJournalDates($classId, $subjectId, $dateFrom = null, $dateTo = null) {
        $where = ['st.class_id = :class_id', 'g.subject_id = :subject_id'];
        $params = [':class_id' => $classId, ':subject_id' => $subjectId];
        
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
            SELECT DISTINCT g.date 
            FROM grades g
            JOIN students st ON g.student_id = st.id
            WHERE {$whereStr}
            ORDER BY g.date
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Создаёт оценку
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO grades (student_id, subject_id, teacher_id, grade, date, comment, grade_type)
            VALUES (:student_id, :subject_id, :teacher_id, :grade, :date, :comment, :grade_type)
        ");
        $stmt->execute([
            ':student_id'  => $data['student_id'],
            ':subject_id'  => $data['subject_id'],
            ':teacher_id'  => $data['teacher_id'],
            ':grade'       => $data['grade'],
            ':date'        => $data['date'],
            ':comment'     => $data['comment'] ?? null,
            ':grade_type'  => $data['grade_type'] ?? 'current',
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновляет оценку
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['grade'])) {
            $fields[] = 'grade = :grade';
            $params[':grade'] = $data['grade'];
        }
        if (isset($data['comment'])) {
            $fields[] = 'comment = :comment';
            $params[':comment'] = $data['comment'];
        }
        if (isset($data['grade_type'])) {
            $fields[] = 'grade_type = :grade_type';
            $params[':grade_type'] = $data['grade_type'];
        }
        if (isset($data['date'])) {
            $fields[] = 'date = :date';
            $params[':date'] = $data['date'];
        }
        
        if (empty($fields)) return false;
        
        $fieldStr = implode(', ', $fields);
        $stmt = $this->db->prepare("UPDATE grades SET {$fieldStr} WHERE id = :id");
        return $stmt->execute($params);
    }
    
    /**
     * Удаляет оценку
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM grades WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Получает оценки ученика
     */
    public function getByStudent($studentId, $subjectId = null) {
        $where = ['g.student_id = :student_id'];
        $params = [':student_id' => $studentId];
        
        if ($subjectId) {
            $where[] = 'g.subject_id = :subject_id';
            $params[':subject_id'] = $subjectId;
        }
        
        $whereStr = implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT g.*, s.name as subject_name, ut.full_name as teacher_name
            FROM grades g
            JOIN subjects s ON g.subject_id = s.id
            JOIN teachers t ON g.teacher_id = t.id
            JOIN users ut ON t.user_id = ut.id
            WHERE {$whereStr}
            ORDER BY g.date DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает оценки ученика сгруппированные по предметам
     */
    public function getByStudentGrouped($studentId) {
        $stmt = $this->db->prepare("
            SELECT g.*, s.name as subject_name, s.id as subj_id
            FROM grades g
            JOIN subjects s ON g.subject_id = s.id
            WHERE g.student_id = :student_id
            ORDER BY s.name, g.date
        ");
        $stmt->execute([':student_id' => $studentId]);
        $grades = $stmt->fetchAll();
        
        $grouped = [];
        foreach ($grades as $g) {
            $grouped[$g['subj_id']]['name'] = $g['subject_name'];
            $grouped[$g['subj_id']]['grades'][] = $g;
        }
        
        return $grouped;
    }
    
    /**
     * Средний балл по классу и предмету
     */
    public function classAverage($classId, $subjectId, $dateFrom = null, $dateTo = null) {
        $where = ['st.class_id = :class_id', 'g.subject_id = :subject_id'];
        $params = [':class_id' => $classId, ':subject_id' => $subjectId];
        
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
            JOIN students st ON g.student_id = st.id
            WHERE {$whereStr}
        ");
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['avg_grade'];
    }
    
    /**
     * Средние баллы по всем предметам для класса
     */
    public function classAveragesBySubjects($classId, $dateFrom = null, $dateTo = null) {
    $where = ['st.class_id = :class_id'];
    $params = [':class_id' => $classId];

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
        SELECT s.id, s.name, ROUND(AVG(g.grade), 2) as avg_grade,
               COUNT(g.id) as grade_count
        FROM grades g
        JOIN subjects s ON g.subject_id = s.id
        JOIN students st ON g.student_id = st.id
        WHERE {$whereStr}
        GROUP BY s.id, s.name
        ORDER BY s.name
    ");
    $stmt->execute($params);
    return $stmt->fetchAll();
}
    
    /**
     * Статистика — количество оценок по значению для класса
     */
    public function gradeDistribution($classId, $subjectId = null, $dateFrom = null, $dateTo = null) {
    $where = ['st.class_id = :class_id'];
    $params = [':class_id' => $classId];

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
        SELECT g.grade, COUNT(*) as cnt
        FROM grades g
        JOIN students st ON g.student_id = st.id
        WHERE {$whereStr}
        GROUP BY g.grade
        ORDER BY g.grade DESC
    ");
    $stmt->execute($params);
    return $stmt->fetchAll();
}
}