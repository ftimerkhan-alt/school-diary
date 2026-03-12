<?php
/**
 * Контроллер дашборда
 */
class DashboardController {
    
    public function index() {
        $role = currentRole();
        $pageTitle = 'Главная панель';
        
        switch ($role) {
            case 'admin':
                $this->adminDashboard();
                break;
            case 'director':
                $this->directorDashboard();
                break;
            case 'head_teacher':
                $this->headTeacherDashboard();
                break;
            case 'class_teacher':
                $this->classTeacherDashboard();
                break;
            case 'teacher':
                $this->teacherDashboard();
                break;
            case 'student':
                $this->studentDashboard();
                break;
            case 'parent':
                $this->parentDashboard();
                break;
            default:
                redirect('login');
        }
    }
    
    private function adminDashboard() {
        $pageTitle = 'Панель администратора';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Subject.php';
        
        $userModel = new User();
        $classModel = new ClassModel();
        $subjectModel = new Subject();
        
        $stats = $userModel->getStatistics();
        $classes = $classModel->getAll(currentAcademicYear());
        $subjects = $subjectModel->getAll();
        $totalUsers = $userModel->countAll();
        
        // Подсчёт по ролям
        $roleCounts = [];
        foreach ($stats as $s) {
            $roleCounts[$s['name']] = $s['count'];
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/admin.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    private function directorDashboard() {
        $pageTitle = 'Панель директора';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Grade.php';
        
        $userModel = new User();
        $classModel = new ClassModel();
        $teacherModel = new Teacher();
        $gradeModel = new GradeModel();
        
        $stats = $userModel->getStatistics();
        $classes = $classModel->getAll(currentAcademicYear());
        $teachers = $teacherModel->getAllWithWorkload();
        
        $roleCounts = [];
        foreach ($stats as $s) {
            $roleCounts[$s['name']] = $s['count'];
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/director.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    private function headTeacherDashboard() {
        $pageTitle = 'Панель завуча';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Grade.php';
        require_once __DIR__ . '/../models/Attendance.php';
        
        $classModel = new ClassModel();
        $teacherModel = new Teacher();
        $gradeModel = new GradeModel();
        $attendanceModel = new AttendanceModel();
        
        $classes = $classModel->getAll(currentAcademicYear());
        $teachers = $teacherModel->getAllWithWorkload();
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/head_teacher.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    private function classTeacherDashboard() {
        $pageTitle = 'Панель классного руководителя';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/ClassModel.php';
        require_once __DIR__ . '/../models/Grade.php';
        require_once __DIR__ . '/../models/Attendance.php';
        require_once __DIR__ . '/../models/Schedule.php';
        
        $teacherModel = new Teacher();
        $studentModel = new Student();
        $classModel = new ClassModel();
        $gradeModel = new GradeModel();
        $attendanceModel = new AttendanceModel();
        $scheduleModel = new ScheduleModel();
        
        $teacher = $teacherModel->findByUserId(currentUserId());
        $classId = getClassTeacherClassId();
        $class = $classId ? $classModel->findById($classId) : null;
        $students = $classId ? $studentModel->getByClassId($classId) : [];
        $todaySchedule = [];
        
        if ($classId) {
            $schedule = $scheduleModel->getByClassStructured($classId);
            $todayDow = date('N'); // 1=Пн..7=Вс
            $todaySchedule = $schedule[$todayDow] ?? [];
        }
        
        $classStats = $classId ? $attendanceModel->getClassStats($classId) : null;
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/class_teacher.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    private function teacherDashboard() {
        $pageTitle = 'Панель учителя';
        require_once __DIR__ . '/../models/Teacher.php';
        require_once __DIR__ . '/../models/Schedule.php';
        
        $teacherModel = new Teacher();
        $scheduleModel = new ScheduleModel();
        
        $teacher = $teacherModel->findByUserId(currentUserId());
        $subjects = $teacher ? $teacherModel->getSubjects($teacher['id']) : [];
        $classes = $teacher ? $teacherModel->getClasses($teacher['id']) : [];
        
        $todaySchedule = [];
        if ($teacher) {
            $schedule = $scheduleModel->getByTeacherStructured($teacher['id']);
            $todayDow = date('N');
            $todaySchedule = $schedule[$todayDow] ?? [];
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/teacher.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    private function studentDashboard() {
        $pageTitle = 'Панель ученика';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/Grade.php';
        require_once __DIR__ . '/../models/Attendance.php';
        require_once __DIR__ . '/../models/Schedule.php';
        
        $studentModel = new Student();
        $gradeModel = new GradeModel();
        $attendanceModel = new AttendanceModel();
        $scheduleModel = new ScheduleModel();
        
        $student = $studentModel->findByUserId(currentUserId());
        $recentGrades = $student ? $gradeModel->getByStudent($student['id']) : [];
        $recentGrades = array_slice($recentGrades, 0, 10);
        $attendanceStats = $student ? $attendanceModel->getStudentStats($student['id']) : null;
        
        $todaySchedule = [];
        if ($student) {
            $schedule = $scheduleModel->getByClassStructured($student['class_id']);
            $todayDow = date('N');
            $todaySchedule = $schedule[$todayDow] ?? [];
        }
        
        // Средние баллы по предметам
        $gradesBySubject = $student ? $gradeModel->getByStudentGrouped($student['id']) : [];
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/student.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    
    private function parentDashboard() {
        $pageTitle = 'Панель родителя';
        require_once __DIR__ . '/../models/Student.php';
        require_once __DIR__ . '/../models/Grade.php';
        require_once __DIR__ . '/../models/Attendance.php';
        
        $studentModel = new Student();
        $gradeModel = new GradeModel();
        $attendanceModel = new AttendanceModel();
        
        $children = $studentModel->getChildrenByParentId(currentUserId());
        
        $childrenData = [];
        foreach ($children as $child) {
            $recentGrades = $gradeModel->getByStudent($child['id']);
            $recentGrades = array_slice($recentGrades, 0, 5);
            $attendanceStats = $attendanceModel->getStudentStats($child['id']);
            $childrenData[] = [
                'info' => $child,
                'recent_grades' => $recentGrades,
                'attendance' => $attendanceStats,
            ];
        }
        
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/parent.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}