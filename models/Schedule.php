<?php
/**
 * Модель расписания
 */
class ScheduleModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает расписание класса
     */
    public function getByClass($classId) {
        $stmt = $this->db->prepare("
            SELECT sc.*, s.name as subject_name, u.full_name as teacher_name
            FROM schedule sc
            JOIN subjects s ON sc.subject_id = s.id
            JOIN teachers t ON sc.teacher_id = t.id
            JOIN users u ON t.user_id = u.id
            WHERE sc.class_id = :class_id
            ORDER BY sc.day_of_week, sc.lesson_order
        ");
        $stmt->execute([':class_id' => $classId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает расписание класса структурированное по дням
     */
    public function getByClassStructured($classId) {
        $lessons = $this->getByClass($classId);
        $structured = [];
        
        foreach ($lessons as $lesson) {
            $day = $lesson['day_of_week'];
            $structured[$day][] = $lesson;
        }
        
        return $structured;
    }
    
    /**
     * Получает расписание учителя
     */
    public function getByTeacher($teacherId) {
        $stmt = $this->db->prepare("
            SELECT sc.*, s.name as subject_name, c.name as class_name
            FROM schedule sc
            JOIN subjects s ON sc.subject_id = s.id
            JOIN classes c ON sc.class_id = c.id
            WHERE sc.teacher_id = :teacher_id
            ORDER BY sc.day_of_week, sc.lesson_order
        ");
        $stmt->execute([':teacher_id' => $teacherId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Получает расписание учителя структурированное по дням
     */
    public function getByTeacherStructured($teacherId) {
        $lessons = $this->getByTeacher($teacherId);
        $structured = [];
        
        foreach ($lessons as $lesson) {
            $day = $lesson['day_of_week'];
            $structured[$day][] = $lesson;
        }
        
        return $structured;
    }
    
    /**
     * Добавляет урок в расписание
     */
    public function addLesson($data) {
        // Проверка на пересечение по классу
        if ($this->hasClassConflict($data['class_id'], $data['day_of_week'], $data['lesson_order'])) {
            return ['success' => false, 'error' => 'В это время у класса уже есть урок'];
        }
        
        // Проверка на пересечение по учителю
        if ($this->hasTeacherConflict($data['teacher_id'], $data['day_of_week'], $data['lesson_order'])) {
            return ['success' => false, 'error' => 'У учителя уже есть урок в это время'];
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO schedule (class_id, subject_id, teacher_id, day_of_week, lesson_order, time_start, time_end, room)
            VALUES (:class_id, :subject_id, :teacher_id, :day_of_week, :lesson_order, :time_start, :time_end, :room)
        ");
        
        $stmt->execute([
            ':class_id'     => $data['class_id'],
            ':subject_id'   => $data['subject_id'],
            ':teacher_id'   => $data['teacher_id'],
            ':day_of_week'  => $data['day_of_week'],
            ':lesson_order' => $data['lesson_order'],
            ':time_start'   => $data['time_start'],
            ':time_end'     => $data['time_end'],
            ':room'         => $data['room'] ?? null,
        ]);
        
        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }
    
    /**
     * Удаляет урок из расписания
     */
    public function deleteLesson($id) {
        $stmt = $this->db->prepare("DELETE FROM schedule WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Обновляет урок в расписании
     */
    public function updateLesson($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE schedule SET 
                subject_id = :subject_id, 
                teacher_id = :teacher_id,
                time_start = :time_start,
                time_end = :time_end,
                room = :room
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id'         => $id,
            ':subject_id' => $data['subject_id'],
            ':teacher_id' => $data['teacher_id'],
            ':time_start' => $data['time_start'],
            ':time_end'   => $data['time_end'],
            ':room'       => $data['room'] ?? null,
        ]);
    }
    
    /**
     * Проверяет конфликт по классу
     */
    public function hasClassConflict($classId, $dayOfWeek, $lessonOrder, $excludeId = null) {
        $sql = "SELECT COUNT(*) as cnt FROM schedule 
                WHERE class_id = :class_id AND day_of_week = :day AND lesson_order = :order";
        $params = [':class_id' => $classId, ':day' => $dayOfWeek, ':order' => $lessonOrder];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['cnt'] > 0;
    }
    
    /**
     * Проверяет конфликт по учителю
     */
    public function hasTeacherConflict($teacherId, $dayOfWeek, $lessonOrder, $excludeId = null) {
        $sql = "SELECT COUNT(*) as cnt FROM schedule 
                WHERE teacher_id = :teacher_id AND day_of_week = :day AND lesson_order = :order";
        $params = [':teacher_id' => $teacherId, ':day' => $dayOfWeek, ':order' => $lessonOrder];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['cnt'] > 0;
    }
    
    /**
     * Копирует расписание из одного класса в другой
     */
    public function copySchedule($fromClassId, $toClassId) {
        // Удаляем текущее расписание целевого класса
        $stmt = $this->db->prepare("DELETE FROM schedule WHERE class_id = :class_id");
        $stmt->execute([':class_id' => $toClassId]);
        
        // Копируем
        $stmt = $this->db->prepare("
            INSERT INTO schedule (class_id, subject_id, teacher_id, day_of_week, lesson_order, time_start, time_end, room)
            SELECT :to_class_id, subject_id, teacher_id, day_of_week, lesson_order, time_start, time_end, room
            FROM schedule WHERE class_id = :from_class_id
        ");
        return $stmt->execute([
            ':to_class_id' => $toClassId,
            ':from_class_id' => $fromClassId,
        ]);
    }
    
    /**
     * Стандартное время уроков
     */
    public static function getLessonTimes() {
        return [
            1 => ['start' => '08:30', 'end' => '09:15'],
            2 => ['start' => '09:25', 'end' => '10:10'],
            3 => ['start' => '10:25', 'end' => '11:10'],
            4 => ['start' => '11:25', 'end' => '12:10'],
            5 => ['start' => '12:20', 'end' => '13:05'],
            6 => ['start' => '13:20', 'end' => '14:05'],
            7 => ['start' => '14:15', 'end' => '15:00'],
            8 => ['start' => '15:10', 'end' => '15:55'],
        ];
    }
    
    /**
     * Текущий урок (определяет, какой урок сейчас идёт)
     */
    public static function getCurrentLesson() {
        $now = date('H:i');
        $times = self::getLessonTimes();
        
        foreach ($times as $order => $time) {
            if ($now >= $time['start'] && $now <= $time['end']) {
                return $order;
            }
        }
        return null;
    }
    
    /**
     * Получает запись расписания по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT sc.*, s.name as subject_name, 
                   u.full_name as teacher_name, c.name as class_name
            FROM schedule sc
            JOIN subjects s ON sc.subject_id = s.id
            JOIN teachers t ON sc.teacher_id = t.id
            JOIN users u ON t.user_id = u.id
            JOIN classes c ON sc.class_id = c.id
            WHERE sc.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}