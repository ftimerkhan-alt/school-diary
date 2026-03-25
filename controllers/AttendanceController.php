<?php
/**
 * Контроллер посещаемости
 */
class AttendanceController {
    
    private $attendanceModel;
    private $teacherModel;
    private $studentModel;
    private $classModel;
    private $subjectModel;
    private $scheduleModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Attendance.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Subject.php';
        require_once __DIR__ . '/../models/Schedule.php';
        
        $this->attendanceModel = new AttendanceModel();
        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
        $this->subjectModel = new Subject();
        $this->scheduleModel = new ScheduleModel();
    }
    
    /**
     * Отметка посещаемости
     */
    public function mark() {
        requireRole(['admin', 'teacher', 'class_teacher', 'head_teacher']);
        
        $pageTitle = 'Посещаемость';
        $role = currentRole();
        $teacher = null;
        $teacherId = null;
        $availableSubjects = [];
        $availableClasses = [];
        
        if (in_array($role, ['teacher', 'class_teacher', 'head_teacher'])) {
            $teacher = $this->teacherModel->findByUserId(currentUserId());
            if ($teacher) {
                $teacherId = $teacher['id'];
                $availableSubjects = $this->teacherModel->getSubjects($teacherId);
                $availableClasses = $this->teacherModel->getClasses($teacherId);
            }
        } elseif (in_array($role, ['admin'])) {
            $availableSubjects = $this->subjectModel->getAll();
            $availableClasses = $this->classModel->getAll(currentAcademicYear());
        }
        
        // Для классного руководителя — добавляем его класс
        if ($role === 'class_teacher') {
            $classId = getClassTeacherClassId();
            if ($classId) {
                $existingIds = array_column($availableClasses, 'id');
                if (!in_array($classId, $existingIds)) {
                    $cls = $this->classModel->findById($classId);
                    if ($cls) $availableClasses[] = $cls;
                }
                $classSubjects = $this->classModel->getSubjects($classId);
                $existingSubjIds = array_column($availableSubjects, 'id');
                foreach ($classSubjects as $cs) {
                    if (!in_array($cs['id'], $existingSubjIds)) {
                        $availableSubjects[] = $cs;
                    }
                }
            }
        }
        
        $selectedClassId = (int)get('class_id', 0);
        $selectedSubjectId = (int)get('subject_id', 0);
        $selectedDate = get('date', date('Y-m-d'));
        
        $students = [];
        $attendanceData = [];
        
        if ($selectedClassId && $selectedSubjectId && $selectedDate) {
            $attendanceData = $this->attendanceModel->getByClassSubjectDate(
                $selectedClassId, $selectedSubjectId, $selectedDate
            );
        }
        
        $canEdit = false;
        if ($role === 'admin' || $role === 'head_teacher') {
            $canEdit = true;
        } elseif (in_array($role, ['teacher', 'class_teacher']) && $teacher && $selectedSubjectId && $selectedClassId) {
            $canEdit = $this->teacherModel->teachesSubjectInClass($teacherId, $selectedSubjectId, $selectedClassId);
            // Классный руководитель может отмечать свой класс
            if ($role === 'class_teacher' && $selectedClassId == getClassTeacherClassId()) {
                $canEdit = true;
            }
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/attendance/mark.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Сохранение посещаемости
     */
    public function store() {
        requireRole(['admin', 'teacher', 'class_teacher', 'head_teacher']);
        validateCSRF();
        
        $classId = (int)post('class_id');
        $subjectId = (int)post('subject_id');
        $date = post('date');
        $statuses = $_POST['status'] ?? [];
        $comments = $_POST['comment'] ?? [];
        $lessonOrder = (int)post('lesson_order', 0);

        $teacher = $this->teacherModel->findByUserId(currentUserId());
$teacherId = $teacher ? (int)$teacher['id'] : null;

$dayOfWeek = (int)date('N', strtotime($date)); // 1=Пн ... 7=Вс
$classSchedule = $this->scheduleModel->getByClass($classId);

$hasLesson = false;
foreach ($classSchedule as $lesson) {
    if (
        (int)$lesson['subject_id'] === $subjectId &&
        (int)$lesson['day_of_week'] === $dayOfWeek
    ) {
        // для админа не важно кто учитель, для учителя/классного руководителя важно
        if (isAdmin() || !$teacherId || (int)$lesson['teacher_id'] === $teacherId) {
            $hasLesson = true;
            break;
        }
    }
}

if (!$hasLesson) {
    setFlash('error', 'На выбранную дату у класса нет данного урока');
    redirect("attendance/mark?class_id={$classId}&subject_id={$subjectId}&date={$date}");
}
        
        if (!$classId || !$subjectId || !$date) {
            setFlash('error', 'Заполните все обязательные поля');
            redirect('attendance/mark');
        }
        
        $teacher = $this->teacherModel->findByUserId(currentUserId());
        $markedBy = $teacher ? $teacher['id'] : null;
        
        $count = 0;
        foreach ($statuses as $studentId => $status) {
            $studentId = (int)$studentId;
            $comment = $comments[$studentId] ?? null;
            
            if (in_array($status, ['present', 'absent', 'late', 'excused'])) {
                $this->attendanceModel->save($studentId, $subjectId, $date, $status, $comment, $markedBy, $lessonOrder);
                $count++;
            }
        }
        
        setFlash('success', "Посещаемость сохранена для {$count} " . plural($count, 'ученика', 'учеников', 'учеников'));
        redirect("attendance/mark?class_id={$classId}&subject_id={$subjectId}&date={$date}");
    }
    
    /**
     * Моя посещаемость (для ученика и родителя)
     */
    public function myAttendance() {
        requireRole(['student', 'parent']);
        
        $pageTitle = 'Посещаемость';
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
                $childIds = array_column($children, 'id');
                if (in_array($selectedStudentId, $childIds)) {
                    $studentId = $selectedStudentId;
                    $student = $this->studentModel->findById($studentId);
                }
            } elseif (!empty($children)) {
                $studentId = $children[0]['id'];
                $student = $this->studentModel->findById($studentId);
            }
            $pageTitle = 'Посещаемость ребёнка';
        }
        
        $attendanceRecords = [];
        $stats = null;
        $statsBySubject = [];
        
        if ($studentId) {
            $attendanceRecords = $this->attendanceModel->getByStudent($studentId);
            $stats = $this->attendanceModel->getStudentStats($studentId);
            
            // Группировка по предметам для статистики
            $subjectIds = array_unique(array_column($attendanceRecords, 'subject_id'));
            foreach ($subjectIds as $sid) {
                $subjectRecords = array_filter($attendanceRecords, function($r) use ($sid) {
                    return $r['subject_id'] == $sid;
                });
                $first = reset($subjectRecords);
                $total = count($subjectRecords);
                $present = count(array_filter($subjectRecords, function($r) { return $r['status'] === 'present'; }));
                $statsBySubject[] = [
                    'name' => $first['subject_name'],
                    'total' => $total,
                    'present' => $present,
                    'percent' => $total > 0 ? round(($present / $total) * 100) : 0,
                ];
            }
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/attendance/my-attendance.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}