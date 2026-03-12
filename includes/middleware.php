<?php
/**
 * Middleware: проверка прав доступа
 */

/**
 * Требует авторизации. Редирект на логин если не авторизован
 */
function requireAuth() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Необходимо войти в систему');
        redirect('login');
    }
    
    // Проверка таймаута сессии
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > SESSION_LIFETIME)) {
        logout();
        setFlash('warning', 'Сессия истекла. Войдите снова.');
        redirect('login');
    }
}

/**
 * Проверяет, имеет ли пользователь одну из указанных ролей
 * @param array|string $roles — роль или массив ролей
 */
function requireRole($roles) {
    if (!isLoggedIn()) {
        redirect('login');
    }
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    $currentRole = currentRole();
    
    if (!in_array($currentRole, $roles)) {
        setFlash('error', 'У вас нет доступа к этому разделу');
        redirect('dashboard');
    }
}

/**
 * Проверяет, является ли пользователь админом
 */
function isAdmin() {
    return currentRole() === 'admin';
}

/**
 * Проверяет, является ли пользователь директором
 */
function isDirector() {
    return currentRole() === 'director';
}

/**
 * Проверяет, является ли пользователь завучем
 */
function isHeadTeacher() {
    return currentRole() === 'head_teacher';
}

/**
 * Проверяет, является ли пользователь классным руководителем
 */
function isClassTeacher() {
    return currentRole() === 'class_teacher';
}

/**
 * Проверяет, является ли пользователь учителем (любого типа)
 */
function isTeacher() {
    return in_array(currentRole(), ['teacher', 'class_teacher']);
}

/**
 * Проверяет, является ли пользователь учеником
 */
function isStudent() {
    return currentRole() === 'student';
}

/**
 * Проверяет, является ли пользователь родителем
 */
function isParent() {
    return currentRole() === 'parent';
}

/**
 * Проверяет, может ли пользователь управлять пользователями
 */
function canManageUsers() {
    return in_array(currentRole(), ['admin']);
}

/**
 * Проверяет, может ли пользователь просматривать пользователей
 */
function canViewUsers() {
    return in_array(currentRole(), ['admin', 'director', 'head_teacher']);
}

/**
 * Проверяет, может ли пользователь управлять расписанием
 */
function canManageSchedule() {
    return in_array(currentRole(), ['admin', 'head_teacher']);
}

/**
 * Проверяет, может ли пользователь выставлять оценки
 */
function canManageGrades() {
    return in_array(currentRole(), ['admin', 'teacher', 'class_teacher']);
}

/**
 * Проверяет, может ли пользователь отмечать посещаемость
 */
function canManageAttendance() {
    return in_array(currentRole(), ['admin', 'teacher', 'class_teacher']);
}

/**
 * Проверяет, может ли пользователь просматривать отчёты
 */
function canViewReports() {
    return in_array(currentRole(), ['admin', 'director', 'head_teacher', 'class_teacher']);
}

/**
 * Проверяет, может ли пользователь отправлять сообщения
 */
function canSendMessages() {
    return in_array(currentRole(), ['admin', 'director', 'head_teacher', 'class_teacher', 'teacher', 'parent']);
}

/**
 * Получает teacher_id текущего пользователя (если учитель)
 */
function getCurrentTeacherId() {
    if (!isTeacher()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM teachers WHERE user_id = :user_id");
    $stmt->execute([':user_id' => currentUserId()]);
    $teacher = $stmt->fetch();
    return $teacher ? $teacher['id'] : null;
}

/**
 * Получает student_id текущего пользователя (если ученик)
 */
function getCurrentStudentId() {
    if (!isStudent()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM students WHERE user_id = :user_id");
    $stmt->execute([':user_id' => currentUserId()]);
    $student = $stmt->fetch();
    return $student ? $student['id'] : null;
}

/**
 * Получает ID детей текущего родителя
 */
function getParentChildrenIds() {
    if (!isParent()) return [];
    $db = getDB();
    $stmt = $db->prepare("
        SELECT ps.student_id 
        FROM parent_student ps 
        WHERE ps.parent_user_id = :user_id
    ");
    $stmt->execute([':user_id' => currentUserId()]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Получает class_id класса, которым руководит текущий пользователь
 */
function getClassTeacherClassId() {
    if (!isClassTeacher()) return null;
    $db = getDB();
    $stmt = $db->prepare("
        SELECT c.id FROM classes c
        JOIN teachers t ON c.class_teacher_id = t.id
        WHERE t.user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute([':user_id' => currentUserId()]);
    $class = $stmt->fetch();
    return $class ? $class['id'] : null;
}