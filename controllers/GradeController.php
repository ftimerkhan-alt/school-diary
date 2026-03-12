<?php
/**
 * Контроллер журнала оценок
 */
class GradeController {
    
    private $gradeModel;
    private $teacherModel;
    private $studentModel;
    private $classModel;
    private $subjectModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Grade.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Subject.php';
        
        $this->gradeModel = new GradeModel();
        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
        $this->subjectModel = new Subject();
    }
    
    /**
     * Журнал оценок
     */
    public function journal() {
        requireRole(['admin', 'director', 'head_teacher', 'class_teacher', 'teacher']);
        
        $pageTitle = 'Журнал оценок';
        
        $role = currentRole();
        $teacher = null;
        $teacherId = null;
        $availableSubjects = [];
        $availableClasses = [];
        
        // Определяем доступные предметы и классы
        if (in_array($role, ['teacher', 'class_teacher'])) {
            $teacher = $this->teacherModel->findByUserId(currentUserId());
            if ($teacher) {
                $teacherId = $teacher['id'];
                $availableSubjects = $this->teacherModel->getSubjects($teacherId);
                $availableClasses = $this->teacherModel->getClasses($teacherId);
            }
        } elseif (in_array($role, ['admin', 'director', 'head_teacher'])) {
            $availableSubjects = $this->subjectModel->getAll();
            $availableClasses = $this->classModel->getAll(currentAcademicYear());
        }
        
        // Для классного руководителя добавляем все предметы его класса
        if ($role === 'class_teacher') {
            $classId = getClassTeacherClassId();
            if ($classId) {
                $classSubjects = $this->classModel->getSubjects($classId);
                // Мержим, если класс не в списке
                $existingClassIds = array_column($availableClasses, 'id');
                if (!in_array($classId, $existingClassIds)) {
                    $cls = $this->classModel->findById($classId);
                    if ($cls) $availableClasses[] = $cls;
                }
            }
        }
        
        // Выбранные фильтры
        $selectedClassId = (int)get('class_id', 0);
        $selectedSubjectId = (int)get('subject_id', 0);
        $dateFrom = get('date_from', date('Y-m-01'));
        $dateTo = get('date_to', date('Y-m-t'));
        
        // Если учитель — подгружаем классы по предмету
        if ($teacherId && $selectedSubjectId) {
            $availableClasses = $this->teacherModel->getClassesBySubject($teacherId, $selectedSubjectId);
        }
        
        // Данные журнала
        $students = [];
        $dates = [];
        $gradesMap = [];
        $classAvg = null;
        
        if ($selectedClassId && $selectedSubjectId) {
            $students = $this->studentModel->getByClassId($selectedClassId);
            $dates = $this->gradeModel->getJournalDates($selectedClassId, $selectedSubjectId, $dateFrom, $dateTo);
            $grades = $this->gradeModel->getJournal($selectedClassId, $selectedSubjectId, $dateFrom, $dateTo);
            
            // Строим карту оценок: gradesMap[student_id][date] = [grade_data, ...]
            foreach ($grades as $g) {
                $gradesMap[$g['student_id']][$g['date']][] = $g;
            }
            
            $classAvg = $this->gradeModel->classAverage($selectedClassId, $selectedSubjectId, $dateFrom, $dateTo);
        }
        
        // Определяем, может ли текущий пользователь ставить оценки
        $canEdit = false;
        if ($role === 'admin') {
            $canEdit = true;
        } elseif (in_array($role, ['teacher', 'class_teacher']) && $teacher && $selectedSubjectId && $selectedClassId) {
            $canEdit = $this->teacherModel->teachesSubjectInClass($teacherId, $selectedSubjectId, $selectedClassId);
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/grades/journal.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Добавление оценки (AJAX)
     */
    public function store() {
        requireRole(['admin', 'teacher', 'class_teacher']);
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Метод не поддерживается']);
            exit;
        }
        
        $data = [
            'student_id' => (int)post('student_id'),
            'subject_id' => (int)post('subject_id'),
            'grade'      => (int)post('grade'),
            'date'       => post('date'),
            'comment'    => post('comment', ''),
            'grade_type' => post('grade_type', 'current'),
        ];
        
        // Определяем teacher_id
        $teacher = $this->teacherModel->findByUserId(currentUserId());
        if (!$teacher && !isAdmin()) {
            echo json_encode(['success' => false, 'error' => 'Вы не учитель']);
            exit;
        }
        
        if (isAdmin() && !$teacher) {
            // Админ без записи учителя — берём первого доступного
            $data['teacher_id'] = 1;
        } else {
            $data['teacher_id'] = $teacher['id'];
        }
        
        // Валидация
        if ($data['grade'] < 1 || $data['grade'] > 5) {
            echo json_encode(['success' => false, 'error' => 'Оценка должна быть от 1 до 5']);
            exit;
        }
        
        if (empty($data['date'])) {
            echo json_encode(['success' => false, 'error' => 'Укажите дату']);
            exit;
        }
        
        try {
            $id = $this->gradeModel->create($data);
            echo json_encode(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Обновление оценки (AJAX)
     */
    public function update() {
        requireRole(['admin', 'teacher', 'class_teacher']);
        
        header('Content-Type: application/json');
        
        $id = (int)post('id');
        $data = [];
        
        if (post('grade') !== null) $data['grade'] = (int)post('grade');
        if (post('comment') !== null) $data['comment'] = post('comment');
        if (post('grade_type') !== null) $data['grade_type'] = post('grade_type');
        
        if (isset($data['grade']) && ($data['grade'] < 1 || $data['grade'] > 5)) {
            echo json_encode(['success' => false, 'error' => 'Оценка должна быть от 1 до 5']);
            exit;
        }
        
        try {
            $this->gradeModel->update($id, $data);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Удаление оценки (AJAX)
     */
    public function delete() {
        requireRole(['admin', 'teacher', 'class_teacher']);
        
        header('Content-Type: application/json');
        
        $id = (int)post('id');
        
        try {
            $this->gradeModel->delete($id);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Мои оценки (для ученика и родителя)
     */
    public function myGrades() {
        requireRole(['student', 'parent']);
        
        $pageTitle = 'Мои оценки';
        $role = currentRole();
        
        $studentId = null;
        $student = null;
        $children = [];
        
        if ($role === 'student') {
            $student = $this->studentModel->findByUserId(currentUserId());
            if ($student) $studentId = $student['id'];
        } elseif ($role === 'parent') {
            $children = $this->studentModel->getChildrenByParentId(currentUserId());
            $selectedStudentId = (int)get('student_id', 0);
            
            if ($selectedStudentId) {
                // Проверяем, что это ребёнок текущего родителя
                $childIds = array_column($children, 'id');
                if (in_array($selectedStudentId, $childIds)) {
                    $studentId = $selectedStudentId;
                    $student = $this->studentModel->findById($studentId);
                }
            } elseif (!empty($children)) {
                $studentId = $children[0]['id'];
                $student = $this->studentModel->findById($studentId);
            }
            
            $pageTitle = 'Оценки ребёнка';
        }
        
        $gradesBySubject = [];
        $overallAvg = 0;
        
        if ($studentId) {
            $gradesBySubject = $this->gradeModel->getByStudentGrouped($studentId);
            
            $allGrades = [];
            foreach ($gradesBySubject as $subj) {
                foreach ($subj['grades'] as $g) $allGrades[] = $g['grade'];
            }
            $overallAvg = count($allGrades) > 0 ? round(array_sum($allGrades) / count($allGrades), 2) : 0;
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/grades/my-grades.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}