<?php
/**
 * Контроллер отчётов
 */
class ReportsController {
    
    private $gradeModel;
    private $attendanceModel;
    private $teacherModel;
    private $studentModel;
    private $classModel;
    private $subjectModel;
    private $termModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Grade.php';
        require_once __DIR__ . '/../models/Attendance.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Subject.php';
        require_once __DIR__ . '/../models/Term.php';
        
        $this->gradeModel = new GradeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->teacherModel = new Teacher();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
        $this->subjectModel = new Subject();
        $this->termModel = new Term();
    }
    
    /**
     * Главная страница отчётов
     */
    public function index() {
    requireRole(['admin', 'director', 'head_teacher', 'class_teacher']);
    $pageTitle = 'Отчёты и аналитика';

    $classes = $this->classModel->getAll(currentAcademicYear());

    // Классный руководитель видит только свой класс
    if (isClassTeacher()) {
        $myClassId = getClassTeacherClassId();
        $classes = array_values(array_filter($classes, function($c) use ($myClassId) {
            return (int)$c['id'] === (int)$myClassId;
        }));
    }

    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/reports/index.php';
    require __DIR__ . '/../views/layout/footer.php';
}
    
    /**
     * Отчёт по успеваемости
     */
    public function progress() {
    requireRole(['admin', 'director', 'head_teacher', 'class_teacher', 'teacher']);
    $pageTitle = 'Успеваемость';

    $classes = $this->classModel->getAll(currentAcademicYear());

    $selectedClassId = (int)get('class_id', 0);
    $selectedTermId = (int)get('term_id', 0);

    $availableYears = $this->termModel->getAvailableYears();
$selectedAcademicYear = (int)get('academic_year', currentAcademicYear());

if ($selectedAcademicYear <= 0 && !empty($availableYears)) {
    $selectedAcademicYear = (int)$availableYears[0];
}

$terms = $this->termModel->getByAcademicYear($selectedAcademicYear);

    // Классный руководитель может смотреть только свой класс
    if (isClassTeacher()) {
        $myClassId = getClassTeacherClassId() ?: 0;
        $selectedClassId = $myClassId;

        $classes = array_values(array_filter($classes, function($c) use ($myClassId) {
            return (int)$c['id'] === (int)$myClassId;
        }));
    }

    $selectedTerm = $selectedTermId ? $this->termModel->findById($selectedTermId) : null;
    $dateFrom = null;
    $dateTo = null;

    if ($selectedTerm) {
        $dateFrom = $selectedTerm['start_date'];
        $dateTo = $selectedTerm['end_date'];
    }

    $classInfo = null;
    $students = [];
    $subjectAverages = [];
    $gradeDistribution = [];
    $excellentCount = 0;
    $goodCount = 0;

    if ($selectedClassId) {
        $classInfo = $this->classModel->findById($selectedClassId);

        $students = $this->studentModel->getByClassId($selectedClassId);
        $subjectAverages = $this->gradeModel->classAveragesBySubjects($selectedClassId, $dateFrom, $dateTo);
        $gradeDistribution = $this->gradeModel->gradeDistribution($selectedClassId, null, $dateFrom, $dateTo);

        foreach ($students as &$st) {
            $avg = $this->studentModel->getAverageGradeByPeriod($st['id'], null, $dateFrom, $dateTo);
            $st['average'] = $avg;

            if ($avg !== null) {
                if ($avg >= 4.5) {
                    $excellentCount++;
                } elseif ($avg >= 3.5) {
                    $goodCount++;
                }
            }
        }
        unset($st);

        usort($students, function($a, $b) {
            return ($b['average'] ?? 0) <=> ($a['average'] ?? 0);
        });
    }

    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/reports/progress.php';
    require __DIR__ . '/../views/layout/footer.php';
}
    
    /**
     * Отчёт по посещаемости
     */
    public function attendanceReport() {
    requireRole(['admin', 'director', 'head_teacher', 'class_teacher']);
    $pageTitle = 'Отчёт по посещаемости';

    $classes = $this->classModel->getAll(currentAcademicYear());
    $availableYears = $this->termModel->getAvailableYears();

    $selectedClassId = (int)get('class_id', 0);
    $selectedAcademicYear = (int)get('academic_year', currentAcademicYear());
    $selectedTermId = (int)get('term_id', 0);

    $terms = $this->termModel->getByAcademicYear($selectedAcademicYear);

    // Классный руководитель может смотреть только свой класс
    if (isClassTeacher()) {
        $myClassId = getClassTeacherClassId() ?: 0;
        $selectedClassId = $myClassId;

        $classes = array_values(array_filter($classes, function($c) use ($myClassId) {
            return (int)$c['id'] === (int)$myClassId;
        }));
    }

    $selectedTerm = $selectedTermId ? $this->termModel->findById($selectedTermId) : null;
    $dateFrom = null;
    $dateTo = null;

    if ($selectedTerm) {
        $dateFrom = $selectedTerm['start_date'];
        $dateTo = $selectedTerm['end_date'];
    } else {
        // Если выбран "весь учебный год", ограничиваем рамками этого года
        $dateFrom = $selectedAcademicYear . '-09-01';
        $dateTo = ($selectedAcademicYear + 1) . '-08-31';
    }

    $classInfo = null;
    $classStats = null;
    $studentStats = [];
    $frequentAbsentees = [];

    if ($selectedClassId) {
        $classInfo = $this->classModel->findById($selectedClassId);
        $classStats = $this->attendanceModel->getClassStats($selectedClassId, $dateFrom, $dateTo);
        $studentStats = $this->attendanceModel->getClassStudentStatsByPeriod($selectedClassId, $dateFrom, $dateTo);
    }

    $frequentAbsentees = $this->attendanceModel->getFrequentAbsenteesByPeriod(3, $dateFrom, $dateTo);

    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/reports/attendance-report.php';
    require __DIR__ . '/../views/layout/footer.php';
}
    
    /**
     * Итоговые ведомости
     */
    public function finalReport() {
    requireRole(['admin', 'director', 'head_teacher', 'class_teacher']);
    $pageTitle = 'Итоговая ведомость';

    $classes = $this->classModel->getAll(currentAcademicYear());
    $availableYears = $this->termModel->getAvailableYears();

    $selectedClassId = (int)get('class_id', 0);
    $selectedAcademicYear = (int)get('academic_year', currentAcademicYear());
    $selectedTermId = (int)get('term_id', 0);

    if ($selectedAcademicYear <= 0 && !empty($availableYears)) {
        $selectedAcademicYear = (int)$availableYears[0];
    }

    $terms = $this->termModel->getByAcademicYear($selectedAcademicYear);

    if (isClassTeacher()) {
        $myClassId = getClassTeacherClassId() ?: 0;
        $selectedClassId = $myClassId;

        $classes = array_values(array_filter($classes, function($c) use ($myClassId) {
            return (int)$c['id'] === (int)$myClassId;
        }));
    }

    $selectedTerm = $selectedTermId ? $this->termModel->findById($selectedTermId) : null;
    $dateFrom = null;
    $dateTo = null;

    if ($selectedTerm) {
        $dateFrom = $selectedTerm['start_date'];
        $dateTo = $selectedTerm['end_date'];
    } else {
        $dateFrom = $selectedAcademicYear . '-09-01';
        $dateTo = ($selectedAcademicYear + 1) . '-08-31';
    }

    $classInfo = null;
    $students = [];
    $subjects = [];
    $gradesTable = [];

    if ($selectedClassId) {
        $classInfo = $this->classModel->findById($selectedClassId);
        $students = $this->studentModel->getByClassId($selectedClassId);
        $subjects = $this->classModel->getSubjects($selectedClassId);

        foreach ($students as $st) {
            $row = ['student' => $st, 'grades' => [], 'overall' => null];
            $allAvgs = [];

            foreach ($subjects as $subj) {
                $avg = $this->studentModel->getAverageGradeByPeriod($st['id'], $subj['id'], $dateFrom, $dateTo);
                $row['grades'][$subj['id']] = $avg;
                if ($avg !== null) {
                    $allAvgs[] = $avg;
                }
            }

            $row['overall'] = count($allAvgs) > 0 ? round(array_sum($allAvgs) / count($allAvgs), 2) : null;
            $gradesTable[] = $row;
        }
    }

    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . '/../views/reports/final.php';
    require __DIR__ . '/../views/layout/footer.php';
}
    
    /**
     * Нагрузка учителей
     */
    public function teachers() {
        requireRole(['admin', 'director', 'head_teacher']);
        $pageTitle = 'Нагрузка учителей';
        
        $teachers = $this->teacherModel->getAllWithWorkload();
        
        // Детальная нагрузка для каждого учителя
        $teacherDetails = [];
        foreach ($teachers as $t) {
            $workload = $this->teacherModel->getWorkload($t['id']);
            $classSubjects = $this->teacherModel->getClassSubjects($t['id']);
            $teacherDetails[$t['id']] = [
                'info' => $t,
                'workload' => $workload,
                'class_subjects' => $classSubjects,
            ];
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/reports/teachers.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    /**
     * Профиль ученика
     */
    public function studentProfile($studentId) {
        requireRole(['admin', 'director', 'head_teacher', 'class_teacher']);
        
        $student = $this->studentModel->findById($studentId);
        if (!$student) {
            setFlash('error', 'Ученик не найден');
            redirect('reports');
        }
        
        // Для классного руководителя — только свой класс
        if (isClassTeacher()) {
            $myClassId = getClassTeacherClassId();
            if ($student['class_id'] != $myClassId) {
                setFlash('error', 'Нет доступа к этому ученику');
                redirect('reports');
            }
        }
        
        $pageTitle = 'Профиль: ' . $student['full_name'];
        
        $gradesBySubject = $this->gradeModel->getByStudentGrouped($student['id']);
        $attendanceStats = $this->attendanceModel->getStudentStats($student['id']);
        $parents = $this->studentModel->getParents($student['id']);
        $recentGrades = $this->gradeModel->getByStudent($student['id']);
        
        // Средние баллы
        $subjectAvgs = [];
        foreach ($gradesBySubject as $subjId => $subj) {
            $vals = array_column($subj['grades'], 'grade');
            $subjectAvgs[] = [
                'name' => $subj['name'],
                'avg' => round(array_sum($vals) / count($vals), 2),
                'count' => count($vals),
            ];
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/reports/student-profile.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}