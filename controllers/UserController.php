<?php
/**
 * Контроллер управления пользователями
 */
class UserController {
    
    private $userModel;
    private $teacherModel;
    private $studentModel;
    private $classModel;
    private $subjectModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Subject.php';
        
        $this->userModel = new User();
        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
        $this->subjectModel = new Subject();
    }
    
    /**
     * Список пользователей
     */
    public function index() {
        // Проверка прав: только admin, director, head_teacher
        requireRole(['admin', 'director', 'head_teacher']);
        
        $pageTitle = 'Управление пользователями';
        
        $filters = [
    'role' => get('role', ''),
    'search' => get('search', ''),
    'class_id' => (int)get('class_id', 0),
];
        
        $page = max(1, (int)get('page', 1));
        $perPage = 20;
        
        $totalUsers = $this->userModel->countAll($filters);
        $pagination = paginate($totalUsers, $perPage, $page);
        $users = $this->userModel->getAll($filters, $perPage, $pagination['offset']);
        $roles = $this->userModel->getRoles();
        $classes = $this->classModel->getAll(currentAcademicYear());
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/users/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Форма создания пользователя
     */
    public function create() {
        requireRole(['admin']);
        
        $pageTitle = 'Создание пользователя';
        $roles = $this->userModel->getRoles();
        $classes = $this->classModel->getAll(currentAcademicYear());
        $subjects = $this->subjectModel->getAll();
        $students = $this->studentModel->getAll();
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/users/create.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Сохранение нового пользователя
     */
    public function store() {
        requireRole(['admin']);
        validateCSRF();
        
        $data = [
            'login'     => post('login'),
            'password'  => post('password'),
            'full_name' => post('full_name'),
            'email'     => post('email'),
            'phone'     => post('phone'),
            'role_id'   => (int)post('role_id'),
        ];
        
        // Валидация
        $errors = [];
        if (empty($data['login'])) $errors[] = 'Логин обязателен';
        if (empty($data['password'])) $errors[] = 'Пароль обязателен';
        if (strlen($data['password']) < 3) $errors[] = 'Пароль слишком короткий';
        if (empty($data['full_name'])) $errors[] = 'ФИО обязательно';
        if (empty($data['role_id'])) $errors[] = 'Выберите роль';
        if (!$this->userModel->isLoginUnique($data['login'])) $errors[] = 'Логин уже занят';
        if (!empty($data['email']) && !isValidEmail($data['email'])) $errors[] = 'Некорректный email';
        
        if (!empty($errors)) {
            setFlash('error', implode('. ', $errors));
            redirect('users/create');
        }
        
        $db = getDB();
        
        try {
            $db->beginTransaction();
            
            // Создаём пользователя
            $userId = $this->userModel->create($data);
            
            // Получаем роль
            $roles = $this->userModel->getRoles();
            $roleName = '';
            foreach ($roles as $r) {
                if ($r['id'] == $data['role_id']) {
                    $roleName = $r['name'];
                    break;
                }
            }
            
            // Дополнительные действия в зависимости от роли
            if (in_array($roleName, ['teacher', 'class_teacher'])) {
                $isClassTeacher = ($roleName === 'class_teacher') ? 1 : 0;
                $teacherId = $this->teacherModel->create($userId, $isClassTeacher);
                
                // Назначение классного руководства
                if ($isClassTeacher) {
                    $classId = (int)post('class_teacher_class_id');
                    if ($classId) {
                        $this->classModel->update($classId, ['class_teacher_id' => $teacherId]);
                    }
                }
            }
            
            if ($roleName === 'student') {
                $classId = (int)post('student_class_id');
                if ($classId) {
                    $this->studentModel->create($userId, $classId);
                } else {
                    throw new Exception('Для ученика необходимо выбрать класс');
                }
            }
            
            if ($roleName === 'parent') {
                $childrenIds = $_POST['children'] ?? [];
                $relationships = $_POST['relationships'] ?? [];
                
                foreach ($childrenIds as $i => $studentId) {
                    $studentId = (int)$studentId;
                    if ($studentId > 0) {
                        $rel = $relationships[$i] ?? 'Родитель';
                        $this->studentModel->addParent($studentId, $userId, $rel, $i === 0);
                    }
                }
            }
            
            $db->commit();
            setFlash('success', 'Пользователь успешно создан');
            redirect('users');
            
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('error', 'Ошибка создания: ' . $e->getMessage());
            redirect('users/create');
        }
    }
    
    /**
     * Форма редактирования пользователя
     */
    public function edit($id){
        requireRole(['admin']);
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Пользователь не найден');
            redirect('users');
        }
        
        $pageTitle = 'Редактирование: ' . $user['full_name'];
        $roles = $this->userModel->getRoles();
        $classes = $this->classModel->getAll(currentAcademicYear());
        $subjects = $this->subjectModel->getAll();
        $allStudents = $this->studentModel->getAll();
        
        // Доп. данные в зависимости от роли
        $teacher = null;
        $student = null;
        $parentChildren = [];
        $currentClassTeacherClassId = null;

        if ($teacher) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM classes WHERE class_teacher_id = :tid LIMIT 1");
            $stmt->execute([':tid' => $teacher['id']]);
            $row = $stmt->fetch();
            $currentClassTeacherClassId = $row ? (int)$row['id'] : null;
        }
        
        if (in_array($user['role_name'], ['teacher', 'class_teacher'])) {
            $teacher = $this->teacherModel->findByUserId($user['id']);
        }
        
        if ($user['role_name'] === 'student') {
            $student = $this->studentModel->findByUserId($user['id']);
        }
        
        if ($user['role_name'] === 'parent') {
            $parentChildren = $this->studentModel->getChildrenByParentId($user['id']);
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/users/edit.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Обновление пользователя
     */
    public function update($id) {
        requireRole(['admin']);
        validateCSRF();
        
        $user = $this->userModel->findById($id);
        if (!$user) {
            setFlash('error', 'Пользователь не найден');
            redirect('users');
        }
        
        $data = [
            'full_name' => post('full_name'),
            'email'     => post('email'),
            'phone'     => post('phone'),
            'role_id'   => (int)post('role_id'),
            'is_active' => (int)post('is_active', 0),
        ];
        
        // Пароль — только если заполнен
        $newPassword = post('password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 3) {
                setFlash('error', 'Пароль слишком короткий');
                redirect('users/edit/' . $id);
            }
            $data['password'] = $newPassword;
        }
        
        // Логин
        $newLogin = post('login');
        if ($newLogin && $newLogin !== $user['login']) {
            if (!$this->userModel->isLoginUnique($newLogin, $id)) {
                setFlash('error', 'Логин уже занят');
                redirect('users/edit/' . $id);
            }
            $data['login'] = $newLogin;
        }
        
        // Валидация
        if (empty($data['full_name'])) {
            setFlash('error', 'ФИО обязательно');
            redirect('users/edit/' . $id);
        }
        
        $db = getDB();
        
        try {
            $db->beginTransaction();
            
            $this->userModel->update($id, $data);
            
            // Получаем новую роль
            $roles = $this->userModel->getRoles();
            $newRoleName = '';
            foreach ($roles as $r) {
                if ($r['id'] == $data['role_id']) {
                    $newRoleName = $r['name'];
                    break;
                }
            }

            // ===== Обработка назначения класса классному руководителю =====
if ($newRoleName === 'class_teacher') {

    // 1) гарантируем, что есть запись в teachers
    $teacher = $this->teacherModel->findByUserId($id);
    if (!$teacher) {
        $teacherId = $this->teacherModel->create($id, true);
        $teacher = $this->teacherModel->findById($teacherId);
    }

    // 2) помечаем как классный руководитель
    $db->prepare("UPDATE teachers SET is_class_teacher = 1 WHERE user_id = :uid")
       ->execute([':uid' => $id]);

    $teacher = $this->teacherModel->findByUserId($id);
    $teacherId = (int)$teacher['id'];

    // 3) снимаем классное руководство с других классов этого учителя (если было)
    $db->prepare("UPDATE classes SET class_teacher_id = NULL WHERE class_teacher_id = :tid")
       ->execute([':tid' => $teacherId]);

    // 4) назначаем выбранный класс
    $classId = (int)post('class_teacher_class_id', 0);

    if ($classId > 0) {
        // Проверка: выбранный класс не должен быть закреплён за другим учителем
        $stmt = $db->prepare("SELECT class_teacher_id FROM classes WHERE id = :cid LIMIT 1");
        $stmt->execute([':cid' => $classId]);
        $cls = $stmt->fetch();

        if ($cls && !empty($cls['class_teacher_id']) && (int)$cls['class_teacher_id'] !== $teacherId) {
            throw new Exception('Этот класс уже закреплён за другим классным руководителем');
        }

        $db->prepare("UPDATE classes SET class_teacher_id = :tid WHERE id = :cid")
           ->execute([':tid' => $teacherId, ':cid' => $classId]);
    } else {
    }
}
            
            // Обработка смены роли на ученика
            if ($newRoleName === 'student') {
                $student = $this->studentModel->findByUserId($id);
                $classId = (int)post('student_class_id');
                if ($classId) {
                    if ($student) {
                        $this->studentModel->updateClass($student['id'], $classId);
                    } else {
                        $this->studentModel->create($id, $classId);
                    }
                }
            }
            
            // Обработка роли родителя
            if ($newRoleName === 'parent') {
                $this->studentModel->removeParentLinks($id);
                $childrenIds = $_POST['children'] ?? [];
                $relationships = $_POST['relationships'] ?? [];
                
                foreach ($childrenIds as $i => $studentId) {
                    $studentId = (int)$studentId;
                    if ($studentId > 0) {
                        $rel = $relationships[$i] ?? 'Родитель';
                        $this->studentModel->addParent($studentId, $id, $rel, $i === 0);
                    }
                }
            }
            
            $db->commit();
            setFlash('success', 'Пользователь обновлён');
            redirect('users');
            
        } catch (Exception $e) {
            $db->rollBack();
            setFlash('error', 'Ошибка: ' . $e->getMessage());
            redirect('users/edit/' . $id);
        }
    }
    
    /**
     * Удаление пользователя
     */
    public function delete($id) {
        requireRole(['admin']);
        validateCSRF();
        
        // Нельзя удалить себя
        if ($id == currentUserId()) {
            setFlash('error', 'Нельзя удалить собственный аккаунт');
            redirect('users');
        }
        
        try {
            $this->userModel->delete($id);
            setFlash('success', 'Пользователь удалён');
        } catch (Exception $e) {
            setFlash('error', 'Ошибка удаления: ' . $e->getMessage());
        }
        
        redirect('users');
    }
    
    /**
     * Страница назначения предметов учителю
     */
    public function teacherSubjects($teacherId) {
        requireRole(['admin', 'head_teacher']);
        
        $teacher = $this->teacherModel->findById($teacherId);
        if (!$teacher) {
            setFlash('error', 'Учитель не найден');
            redirect('users');
        }
        
        $pageTitle = 'Предметы и классы: ' . $teacher['full_name'];
        $subjects = $this->subjectModel->getAll();
        $classes = $this->classModel->getAll(currentAcademicYear());
        $teacherSubjects = $this->teacherModel->getSubjects($teacherId);
        $teacherClassSubjects = $this->teacherModel->getClassSubjects($teacherId);
        
        $teacherSubjectIds = array_column($teacherSubjects, 'id');
        
        // Создаём карту class_subject для отмеченных
        $classSubjectMap = [];
        foreach ($teacherClassSubjects as $tcs) {
            $classSubjectMap[$tcs['subject_id'] . '_' . $tcs['class_id']] = true;
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/users/teacher-subjects.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Сохранение предметов учителя
     */
    public function saveTeacherSubjects($teacherId) {
        requireRole(['admin', 'head_teacher']);
        validateCSRF();
        
        $teacher = $this->teacherModel->findById($teacherId);
        if (!$teacher) {
            setFlash('error', 'Учитель не найден');
            redirect('users');
        }
        
        $subjectIds = $_POST['subjects'] ?? [];
        $classSubjects = [];
        
        // Парсим class_subject чекбоксы
        foreach ($_POST as $key => $val) {
            if (preg_match('/^cs_(\d+)_(\d+)$/', $key, $m)) {
                $classSubjects[] = [
                    'subject_id' => (int)$m[1],
                    'class_id'   => (int)$m[2],
                ];
            }
        }
        
        try {
            $this->teacherModel->setSubjects($teacherId, $subjectIds);
            $this->teacherModel->setClassSubjects($teacherId, $classSubjects);
            
            setFlash('success', 'Предметы и классы обновлены');
        } catch (Exception $e) {
            setFlash('error', 'Ошибка: ' . $e->getMessage());
        }
        
        redirect('users/teacher-subjects/' . $teacherId);
    }
        /**
     * Страница управления классами и предметами
     */
    public function classes() {
        requireRole(['admin', 'head_teacher']);
        
        $pageTitle = 'Классы и предметы';
        $classes = $this->classModel->getAll();
        $subjects = $this->subjectModel->getAll();
        $teachers = $this->teacherModel->getAll();
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/users/classes.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Добавление класса
     */
    public function addClass() {
        requireRole(['admin', 'head_teacher']);
        validateCSRF();
        
        $name = post('class_name');
        $year = (int)post('class_year', currentAcademicYear());
        $teacherId = post('class_teacher_id') ? (int)post('class_teacher_id') : null;
        
        if (empty($name)) {
            setFlash('error', 'Введите название класса');
            redirect('users/classes');
        }
        
        try {
            $this->classModel->create($name, $year, $teacherId);
            setFlash('success', 'Класс «' . $name . '» создан');
        } catch (Exception $e) {
            setFlash('error', 'Ошибка: такой класс уже существует или произошла ошибка БД');
        }
        
        redirect('users/classes');
    }
    
    /**
     * Удаление класса
     */
    public function deleteClass($id) {
        requireRole(['admin']);
        validateCSRF();
        
        try {
            // Проверяем, есть ли ученики в классе
            $count = $this->studentModel->countByClass($id);
            if ($count > 0) {
                setFlash('error', 'Нельзя удалить класс, в котором есть ученики (' . $count . ' чел.)');
            } else {
                $this->classModel->delete($id);
                setFlash('success', 'Класс удалён');
            }
        } catch (Exception $e) {
            setFlash('error', 'Ошибка удаления: ' . $e->getMessage());
        }
        
        redirect('users/classes');
    }
    
    /**
     * Добавление предмета
     */
    public function addSubject() {
        requireRole(['admin', 'head_teacher']);
        validateCSRF();
        
        $name = post('subject_name');
        
        if (empty($name)) {
            setFlash('error', 'Введите название предмета');
            redirect('users/classes');
        }
        
        try {
            $this->subjectModel->create($name);
            setFlash('success', 'Предмет «' . $name . '» добавлен');
        } catch (Exception $e) {
            setFlash('error', 'Ошибка: такой предмет уже существует');
        }
        
        redirect('users/classes');
    }
    
    /**
     * Удаление предмета
     */
    public function deleteSubject($id) {
        requireRole(['admin']);
        validateCSRF();
        
        try {
            $this->subjectModel->delete($id);
            setFlash('success', 'Предмет удалён');
        } catch (Exception $e) {
            setFlash('error', 'Нельзя удалить предмет: он используется в системе');
        }
        
        redirect('users/classes');
    }
}