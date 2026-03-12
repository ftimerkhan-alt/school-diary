<?php
/**
 * Модель предмета
 */
class Subject {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает предмет по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM subjects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает все предметы
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM subjects ORDER BY name");
        return $stmt->fetchAll();
    }
    
    /**
     * Создаёт предмет
     */
    public function create($name) {
        $stmt = $this->db->prepare("INSERT INTO subjects (name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Обновляет предмет
     */
    public function update($id, $name) {
        $stmt = $this->db->prepare("UPDATE subjects SET name = :name WHERE id = :id");
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }
    
    /**
     * Удаляет предмет
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM subjects WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}