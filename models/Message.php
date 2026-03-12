<?php
/**
 * Модель сообщения
 */
class Message {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Получает сообщение по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   us.full_name as sender_name, us.login as sender_login,
                   ur.full_name as receiver_name, ur.login as receiver_login,
                   rs.display_name as sender_role, rr.display_name as receiver_role
            FROM messages m
            JOIN users us ON m.sender_id = us.id
            JOIN roles rs ON us.role_id = rs.id
            JOIN users ur ON m.receiver_id = ur.id
            JOIN roles rr ON ur.role_id = rr.id
            WHERE m.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Получает входящие сообщения
     */
    public function getInbox($userId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT m.*, us.full_name as sender_name, rs.display_name as sender_role
            FROM messages m
            JOIN users us ON m.sender_id = us.id
            JOIN roles rs ON us.role_id = rs.id
            WHERE m.receiver_id = :user_id
            ORDER BY m.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Количество входящих сообщений
     */
    public function countInbox($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM messages WHERE receiver_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $result = $stmt->fetch();
        return (int)$result['cnt'];
    }
    
    /**
     * Получает исходящие сообщения
     */
    public function getSent($userId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT m.*, ur.full_name as receiver_name, rr.display_name as receiver_role
            FROM messages m
            JOIN users ur ON m.receiver_id = ur.id
            JOIN roles rr ON ur.role_id = rr.id
            WHERE m.sender_id = :user_id
            ORDER BY m.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Отправляет сообщение
     */
    public function send($senderId, $receiverId, $subject, $message) {
        $stmt = $this->db->prepare("
            INSERT INTO messages (sender_id, receiver_id, subject, message)
            VALUES (:sender_id, :receiver_id, :subject, :message)
        ");
        $stmt->execute([
            ':sender_id'   => $senderId,
            ':receiver_id' => $receiverId,
            ':subject'     => $subject,
            ':message'     => $message,
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Помечает сообщение как прочитанное
     */
    public function markAsRead($id, $userId) {
        $stmt = $this->db->prepare("
            UPDATE messages SET is_read = 1, read_at = NOW() 
            WHERE id = :id AND receiver_id = :user_id
        ");
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    
    /**
     * Удаляет сообщение
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Количество непрочитанных сообщений
     */
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM messages 
            WHERE receiver_id = :user_id AND is_read = 0
        ");
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return (int)$result['cnt'];
    }
    
    /**
     * Получает список доступных получателей для пользователя
     */
    public function getAvailableRecipients($userId, $userRole) {
        $db = $this->db;
        
        switch ($userRole) {
            case 'admin':
            case 'director':
                // Может писать всем
                $stmt = $db->prepare("
                    SELECT u.id, u.full_name, r.display_name as role_name
                    FROM users u JOIN roles r ON u.role_id = r.id
                    WHERE u.id != :uid AND u.is_active = 1
                    ORDER BY r.id, u.full_name
                ");
                $stmt->execute([':uid' => $userId]);
                break;
            
            case 'head_teacher':
                // Может писать учителям, классным руководителям, директору
                $stmt = $db->prepare("
                    SELECT u.id, u.full_name, r.display_name as role_name
                    FROM users u JOIN roles r ON u.role_id = r.id
                    WHERE u.id != :uid AND u.is_active = 1
                    AND r.name IN ('director', 'teacher', 'class_teacher', 'admin')
                    ORDER BY r.id, u.full_name
                ");
                $stmt->execute([':uid' => $userId]);
                break;
            
            case 'class_teacher':
            case 'teacher':
                // Может писать коллегам, родителям своих учеников, завучу, директору
                $stmt = $db->prepare("
                    SELECT u.id, u.full_name, r.display_name as role_name
                    FROM users u JOIN roles r ON u.role_id = r.id
                    WHERE u.id != :uid AND u.is_active = 1
                    AND r.name IN ('director', 'head_teacher', 'teacher', 'class_teacher', 'parent', 'admin')
                    ORDER BY r.id, u.full_name
                ");
                $stmt->execute([':uid' => $userId]);
                break;
            
            case 'parent':
                // Может писать учителям и классным руководителям
                $stmt = $db->prepare("
                    SELECT u.id, u.full_name, r.display_name as role_name
                    FROM users u JOIN roles r ON u.role_id = r.id
                    WHERE u.id != :uid AND u.is_active = 1
                    AND r.name IN ('teacher', 'class_teacher', 'head_teacher')
                    ORDER BY r.id, u.full_name
                ");
                $stmt->execute([':uid' => $userId]);
                break;
            
            case 'student':
    // Ученик может писать учителям и классным руководителям
                $stmt = $db->prepare("
                SELECT u.id, u.full_name, r.display_name as role_name
                FROM users u JOIN roles r ON u.role_id = r.id
                WHERE u.id != :uid AND u.is_active = 1
                AND r.name IN ('teacher', 'class_teacher', 'head_teacher')
                ORDER BY r.id, u.full_name
            ");
            $stmt->execute([':uid' => $userId]);
            break;

            default:
             return [];
        }
        
        return $stmt->fetchAll();
    }
}