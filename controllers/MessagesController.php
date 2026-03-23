<?php
/**
 * Контроллер сообщений
 */
class MessagesController {
    
    private $messageModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Message.php';
        $this->messageModel = new Message();
    }
    
    /**
     * Входящие сообщения
     */
    public function inbox() {
        requireAuth();
        $pageTitle = 'Входящие сообщения';
        
        $page = max(1, (int)get('page', 1));
        $perPage = 20;
        $totalMessages = $this->messageModel->countInbox(currentUserId());
        $pagination = paginate($totalMessages, $perPage, $page);
        $messages = $this->messageModel->getInbox(currentUserId(), $perPage, $pagination['offset']);
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/messages/inbox.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Отправленные сообщения
     */
    public function sent() {
        requireAuth();
        $pageTitle = 'Отправленные';
        
        $messages = $this->messageModel->getSent(currentUserId(), 50, 0);
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/messages/sent.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Написать сообщение
     */
    public function compose() {
        requireAuth();
        $pageTitle = 'Новое сообщение';
        
        $recipients = $this->messageModel->getAvailableRecipients(currentUserId(), currentRole());
        $replyTo = (int)get('reply_to', 0);
        $replyMessage = null;
        
        if ($replyTo) {
            $replyMessage = $this->messageModel->findById($replyTo);
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/messages/compose.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Отправка сообщения
     */
    public function send() {
        requireAuth();
        validateCSRF();
        
        $receiverId = (int)post('receiver_id');
        $subject = post('subject');
        $message = post('message');
        
        if (!$receiverId || !$subject || !$message) {
            setFlash('error', 'Заполните все поля');
            redirect('messages/compose');
        }
        
        try {
            $this->messageModel->send(currentUserId(), $receiverId, $subject, $message);
            setFlash('success', 'Сообщение отправлено');
            redirect('messages/sent');
        } catch (Exception $e) {
            setFlash('error', 'Ошибка отправки: ' . $e->getMessage());
            redirect('messages/compose');
        }
    }
    
    /**
     * Чтение сообщения
     */
    public function read($id) {
        requireAuth();
        $pageTitle = 'Сообщение';
        
        $message = $this->messageModel->findById($id);
        
        if (!$message) {
            setFlash('error', 'Сообщение не найдено');
            redirect('messages/inbox');
        }
        
        // Проверяем доступ
        if ($message['sender_id'] != currentUserId() && $message['receiver_id'] != currentUserId()) {
            setFlash('error', 'Нет доступа к этому сообщению');
            redirect('messages/inbox');
        }
        
        // Отмечаем как прочитанное
        if ($message['receiver_id'] == currentUserId() && !$message['is_read']) {
            $this->messageModel->markAsRead($id, currentUserId());
            $message['is_read'] = 1;
        }
        
        // Подключаем шаблон inbox с деталями сообщения
        $viewingMessage = $message;
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/messages/inbox.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Удаление сообщения
     */
    public function delete($id) {
        requireAuth();
        validateCSRF();
        
        $message = $this->messageModel->findById($id);
        if ($message && ($message['sender_id'] == currentUserId() || $message['receiver_id'] == currentUserId())) {
            $this->messageModel->delete($id);
            setFlash('success', 'Сообщение удалено');
        }
        
        redirect('messages/inbox');
    }
    
    /**
     * API: получение списка получателей
     */
    public function getRecipients() {
        requireAuth();
        header('Content-Type: application/json');
        
        $recipients = $this->messageModel->getAvailableRecipients(currentUserId(), currentRole());
        echo json_encode(['success' => true, 'data' => $recipients]);
        exit;
    }
        /**
     * Страница рассылки
     */
    public function broadcast() {
        requireAuth();

        $role = currentRole();

        // Кто имеет право рассылать
        if (!in_array($role, ['admin', 'director', 'head_teacher', 'class_teacher'])) {
            setFlash('error', 'У вас нет доступа к рассылкам');
            redirect('messages/inbox');
        }

        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Teacher.php';

        $classModel = new ClassModel();
        $userModel  = new User();
        $teacherModel = new Teacher();

        $pageTitle = 'Рассылка';

        // Список классов нужен admin/director/head_teacher
        $classes = [];
        if (in_array($role, ['admin', 'director', 'head_teacher'])) {
            $classes = $classModel->getAll(currentAcademicYear());
        }

        // Роли для выбора аудитории
        $roleTargets = [
    'all' => 'Всем пользователям',
    'teachers' => 'Только учителям и классным руководителям',
    'students' => 'Только ученикам',
    'parents' => 'Только родителям',
    'class_teachers' => 'Только классным руководителям',
];

// Если пользователь реально закреплён за классом как классный руководитель
$myClassId = getClassTeacherClassId();
if ($myClassId) {
    $roleTargets['my_class_students'] = 'Ученикам моего класса';
    $roleTargets['my_class_parents']  = 'Родителям моего класса';
    $roleTargets['my_class_all']      = 'Ученикам и родителям моего класса';
}

        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/messages/broadcast.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Отправка рассылки
     */
    public function sendBroadcast() {
        requireAuth();
        validateCSRF();

        $role = currentRole();

        if (!in_array($role, ['admin', 'director', 'head_teacher', 'class_teacher'])) {
            setFlash('error', 'У вас нет доступа к рассылкам');
            redirect('messages/inbox');
        }

        $target = post('target');
        $classId = (int)post('class_id', 0);
        $subject = trim(post('subject', ''));
        $message = trim(post('message', ''));
        $studentsMode = post('students_mode', 'all');
$studentsClassId = (int)post('students_class_id', 0);

        if ($subject === '' || $message === '') {
            setFlash('error', 'Заполните тему и текст рассылки');
            redirect('messages/broadcast');
        }

        $recipients = $this->resolveBroadcastRecipients($role, $target, $classId, $studentsMode, $studentsClassId);

        if (empty($recipients)) {
            setFlash('error', 'Не удалось определить получателей для рассылки');
            redirect('messages/broadcast');
        }

        // Отправляем каждому
        $sent = 0;
        $db = getDB();
        $db->beginTransaction();
        try {
            foreach ($recipients as $rid) {
                if ((int)$rid === (int)currentUserId()) continue;
                $this->messageModel->send(currentUserId(), (int)$rid, $subject, $message);
                $sent++;
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('error', 'Ошибка рассылки: ' . $e->getMessage());
            redirect('messages/broadcast');
        }

        setFlash('success', 'Рассылка отправлена. Получателей: ' . $sent);
        redirect('messages/sent');
    }

    /**
     * Определение получателей рассылки
     * @return int[] user_id
     */
    private function resolveBroadcastRecipients($senderRole, $target, $classId, $studentsMode = 'all', $studentsClassId = 0) {
        $db = getDB();
        $uids = [];

        // admin/director/head_teacher
        if (in_array($senderRole, ['admin', 'director', 'head_teacher'])) {

            switch ($target) {
                case 'all':
                    $stmt = $db->prepare("SELECT id FROM users WHERE is_active = 1");
                    $stmt->execute();
                    $uids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'teachers':
                    $stmt = $db->prepare("
                        SELECT u.id
                        FROM users u
                        JOIN roles r ON u.role_id = r.id
                        WHERE u.is_active = 1 AND r.name IN ('teacher','class_teacher','head_teacher')
                    ");
                    $stmt->execute();
                    $uids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'students':
    if ($studentsMode === 'class' && $studentsClassId > 0) {
        $stmt = $db->prepare("
            SELECT u.id
            FROM students s
            JOIN users u ON s.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE u.is_active = 1
              AND r.name = 'student'
              AND s.class_id = :cid
        ");
        $stmt->execute([':cid' => $studentsClassId]);
        $uids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $stmt = $db->prepare("
            SELECT u.id
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.is_active = 1 AND r.name = 'student'
        ");
        $stmt->execute();
        $uids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    break;

                case 'parents':
                    $stmt = $db->prepare("
                        SELECT u.id
                        FROM users u
                        JOIN roles r ON u.role_id = r.id
                        WHERE u.is_active = 1 AND r.name = 'parent'
                    ");
                    $stmt->execute();
                    $uids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'class_teachers':
                    $stmt = $db->prepare("
                        SELECT u.id
                        FROM users u
                        JOIN roles r ON u.role_id = r.id
                        WHERE u.is_active = 1 AND r.name = 'class_teacher'
                    ");
                    $stmt->execute();
                    $uids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    break;

                case 'class':
                    if ($classId <= 0) return [];
                    // Ученики класса
                    $stmt = $db->prepare("
                        SELECT u.id
                        FROM students s
                        JOIN users u ON s.user_id = u.id
                        WHERE u.is_active = 1 AND s.class_id = :cid
                    ");
                    $stmt->execute([':cid' => $classId]);
                    $studentUids = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    // Родители этих учеников
                    $stmt = $db->prepare("
                        SELECT DISTINCT u.id
                        FROM parent_student ps
                        JOIN students s ON ps.student_id = s.id
                        JOIN users u ON ps.parent_user_id = u.id
                        WHERE u.is_active = 1 AND s.class_id = :cid
                    ");
                    $stmt->execute([':cid' => $classId]);
                    $parentUids = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    $uids = array_values(array_unique(array_merge($studentUids, $parentUids)));
                    break;

                default:
                    return [];
            }

            return $uids;
        }

        $myClassId = getClassTeacherClassId();
if ($myClassId && in_array($target, ['my_class_students', 'my_class_parents', 'my_class_all'])) {
            if (!$myClassId) return [];

            // Ученики
            $stmt = $db->prepare("
                SELECT u.id
                FROM students s
                JOIN users u ON s.user_id = u.id
                WHERE u.is_active = 1 AND s.class_id = :cid
            ");
            $stmt->execute([':cid' => $myClassId]);
            $studentUids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Родители
            $stmt = $db->prepare("
                SELECT DISTINCT u.id
                FROM parent_student ps
                JOIN students s ON ps.student_id = s.id
                JOIN users u ON ps.parent_user_id = u.id
                WHERE u.is_active = 1 AND s.class_id = :cid
            ");
            $stmt->execute([':cid' => $myClassId]);
            $parentUids = $stmt->fetchAll(PDO::FETCH_COLUMN);

            switch ($target) {
                case 'my_class_students':
                    return $studentUids;
                case 'my_class_parents':
                    return $parentUids;
                case 'my_class_all':
                    return array_values(array_unique(array_merge($studentUids, $parentUids)));
                default:
                    return [];
            }
        }

        return [];
    }
}