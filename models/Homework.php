<?php
class Homework {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Найти домашнее задание по классу, предмету и дате
     */
    public function findByClassSubjectDate($classId, $subjectId, $date) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM homework
            WHERE class_id = :class_id
              AND subject_id = :subject_id
              AND homework_date = :homework_date
            LIMIT 1
        ");
        $stmt->execute([
            ':class_id' => (int)$classId,
            ':subject_id' => (int)$subjectId,
            ':homework_date' => $date,
        ]);
        return $stmt->fetch();
    }

    /**
     * Сохранить или обновить Д/З
     */
    public function save($data) {
        $existing = $this->findByClassSubjectDate(
            $data['class_id'],
            $data['subject_id'],
            $data['homework_date']
        );

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE homework
                SET title = :title,
    description = :description,
    teacher_id = :teacher_id
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $data['title'] ?? null,
                ':description' => $data['description'],
                ':teacher_id' => (int)$data['teacher_id'],
                ':id' => (int)$existing['id'],
            ]);
            return $existing['id'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO homework (class_id, subject_id, teacher_id, homework_date, title, description)
VALUES (:class_id, :subject_id, :teacher_id, :homework_date, :title, :description)
        ");
        $stmt->execute([
            ':class_id' => (int)$data['class_id'],
            ':subject_id' => (int)$data['subject_id'],
            ':teacher_id' => (int)$data['teacher_id'],
            ':homework_date' => $data['homework_date'],
            ':title' => $data['title'] ?? null,
            ':description' => $data['description'],
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Удалить Д/З
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM homework WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }

    /**
     * Получить домашние задания класса за период
     */
    public function getByClassPeriod($classId, $dateFrom, $dateTo) {
        $stmt = $this->db->prepare("
            SELECT h.*, s.name AS subject_name
            FROM homework h
            JOIN subjects s ON h.subject_id = s.id
            WHERE h.class_id = :class_id
              AND h.homework_date BETWEEN :date_from AND :date_to
            ORDER BY h.homework_date, s.name
        ");
        $stmt->execute([
            ':class_id' => (int)$classId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo,
        ]);
        return $stmt->fetchAll();
    }
}