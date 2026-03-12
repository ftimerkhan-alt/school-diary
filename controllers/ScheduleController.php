<?php
/**
 * Контроллер расписания
 */
class ScheduleController {
    
    private $scheduleModel;
    private $teacherModel;
    private $studentModel;
    private $classModel;
    private $subjectModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Schedule.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Subject.php';
        
        $this->scheduleModel = new ScheduleModel();
        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
        $this->subjectModel = new Subject();
    }
    
    /**
     * Просмотр расписания (для любого класса)
     */
    public function view() {
        requireAuth();
        
        $pageTitle = 'Расписание';
        $classes = $this->classModel->getAll(currentAcademicYear());
        $selectedClassId = (int)get('class_id', 0);

         // Ограничение: классный руководитель видит ТОЛЬКО свой класс
    if (isClassTeacher()) {
        $myClassId = getClassTeacherClassId();
        if (!$myClassId) {
            setFlash('error', 'Вам не назначен класс');
            redirect('dashboard');
        }

        // Принудительно ставим класс классного руководителя
        $selectedClassId = (int)$myClassId;

        // И ограничиваем список классов одним (для UI)
        $classes = array_values(array_filter($classes, function($c) use ($myClassId) {
            return (int)$c['id'] === (int)$myClassId;
        }));
    }
        
        // Автовыбор класса для классного руководителя
        //if (!$selectedClassId && isClassTeacher()) {
        //    $selectedClassId = getClassTeacherClassId() ?: 0;
        //}
        
        // Автовыбор для ученика
        if (!$selectedClassId && isStudent()) {
            $student = $this->studentModel->findByUserId(currentUserId());
            if ($student) $selectedClassId = $student['class_id'];
        }
        
        if (!$selectedClassId && !empty($classes)) {
            $selectedClassId = $classes[0]['id'];
        }
        
        $schedule = [];
        $className = '';
        if ($selectedClassId) {
            $schedule = $this->scheduleModel->getByClassStructured($selectedClassId);
            $cls = $this->classModel->findById($selectedClassId);
            $className = $cls ? $cls['name'] : '';
        }
        
        $currentLesson = ScheduleModel::getCurrentLesson();
        $todayDow = (int)date('N');
        $lessonTimes = ScheduleModel::getLessonTimes();
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/schedule/view.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Моё расписание (для учителя/ученика/родителя)
     */
    public function mySchedule() {
        requireAuth();
        
        $pageTitle = 'Моё расписание';
        $role = currentRole();
        $schedule = [];
        $title = '';
        
        if (in_array($role, ['teacher', 'class_teacher'])) {
            $teacher = $this->teacherModel->findByUserId(currentUserId());
            if ($teacher) {
                $schedule = $this->scheduleModel->getByTeacherStructured($teacher['id']);
                $title = 'Расписание учителя';
            }
        } elseif ($role === 'student') {
            $student = $this->studentModel->findByUserId(currentUserId());
            if ($student) {
                $schedule = $this->scheduleModel->getByClassStructured($student['class_id']);
                $title = 'Расписание класса ' . $student['class_name'];
            }
        } elseif ($role === 'parent') {
            $children = $this->studentModel->getChildrenByParentId(currentUserId());
            $selectedClassId = (int)get('class_id', 0);
            
            if (!$selectedClassId && !empty($children)) {
                $selectedClassId = $children[0]['class_id'];
            }
            
            if ($selectedClassId) {
                $schedule = $this->scheduleModel->getByClassStructured($selectedClassId);
                $cls = $this->classModel->findById($selectedClassId);
                $title = 'Расписание класса ' . ($cls ? $cls['name'] : '');
            }
        }
        
        $currentLesson = ScheduleModel::getCurrentLesson();
        $todayDow = (int)date('N');
        $lessonTimes = ScheduleModel::getLessonTimes();
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/schedule/my-schedule.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Редактор расписания
     */
    public function edit() {
        requireRole(['admin', 'head_teacher']);
        
        $pageTitle = 'Редактор расписания';
        $classes = $this->classModel->getAll(currentAcademicYear());
        $subjects = $this->subjectModel->getAll();
        $teachers = $this->teacherModel->getAll();
        
        $selectedClassId = (int)get('class_id', 0);
        if (!$selectedClassId && !empty($classes)) {
            $selectedClassId = $classes[0]['id'];
        }
        
        $schedule = [];
        if ($selectedClassId) {
            $schedule = $this->scheduleModel->getByClassStructured($selectedClassId);
        }
        
        $lessonTimes = ScheduleModel::getLessonTimes();
        $days = [1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота'];
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/schedule/edit.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Добавление урока в расписание
     */
    public function store() {
        requireRole(['admin', 'head_teacher']);
        validateCSRF();
        
        $data = [
            'class_id'     => (int)post('class_id'),
            'subject_id'   => (int)post('subject_id'),
            'teacher_id'   => (int)post('teacher_id'),
            'day_of_week'  => (int)post('day_of_week'),
            'lesson_order' => (int)post('lesson_order'),
            'time_start'   => post('time_start'),
            'time_end'     => post('time_end'),
            'room'         => post('room', ''),
        ];
        
        if (!$data['class_id'] || !$data['subject_id'] || !$data['teacher_id']) {
            setFlash('error', 'Заполните все обязательные поля');
            redirect('schedule/edit?class_id=' . $data['class_id']);
        }
        
        $result = $this->scheduleModel->addLesson($data);
        
        if ($result['success']) {
            setFlash('success', 'Урок добавлен в расписание');
        } else {
            setFlash('error', $result['error']);
        }
        
        redirect('schedule/edit?class_id=' . $data['class_id']);
    }
    
    /**
     * Удаление урока
     */
    public function deleteLesson() {
        requireRole(['admin', 'head_teacher']);
        validateCSRF();
        
        $id = (int)post('id');
        $classId = (int)post('class_id');
        
        if ($id) {
            $this->scheduleModel->deleteLesson($id);
            setFlash('success', 'Урок удалён из расписания');
        }
        
        redirect('schedule/edit?class_id=' . $classId);
    }
    
    /**
     * Копирование расписания
     */
    public function copy() {
        requireRole(['admin', 'head_teacher']);
        validateCSRF();
        
        $fromClassId = (int)post('from_class_id');
        $toClassId = (int)post('to_class_id');
        
        if ($fromClassId && $toClassId && $fromClassId !== $toClassId) {
            $this->scheduleModel->copySchedule($fromClassId, $toClassId);
            setFlash('success', 'Расписание скопировано');
            redirect('schedule/edit?class_id=' . $toClassId);
        } else {
            setFlash('error', 'Выберите разные классы');
            redirect('schedule/edit?class_id=' . $toClassId);
        }
    }
}