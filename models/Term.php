<?php
/**
 * Модель учебных периодов (четвертей / семестров)
 */
class Term {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Получить все периоды за год
     */
    public function getAll($year = null) {
        $sql = "SELECT * FROM terms";
        $params = [];

        if ($year) {
            $sql .= " WHERE year = :year";
            $params[':year'] = (int)$year;
        }

        $sql .= " ORDER BY start_date";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Получить период по ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM terms WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch();
    }

    /**
     * Получить текущий период
     */
    public function getCurrent() {
        $stmt = $this->db->query("SELECT * FROM terms WHERE is_current = 1 LIMIT 1");
        return $stmt->fetch();
    }

    /**
 * Проверяет, есть ли уже периоды для заданного учебного года
 */
public function existsForYear($year) {
    $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM terms WHERE year = :year");
    $stmt->execute([':year' => (int)$year]);
    $row = $stmt->fetch();
    return !empty($row['cnt']) && (int)$row['cnt'] > 0;
}

/**
 * Создаёт 4 четверти для учебного года
 * year = год начала учебного года, например 2025 => 2025/2026
 */
public function createAcademicYearTerms($year) {
    $year = (int)$year;

    if ($this->existsForYear($year)) {
        throw new Exception('Периоды для этого учебного года уже существуют');
    }

    // Снимаем текущий флаг со старых периодов
    $this->db->exec("UPDATE terms SET is_current = 0");

    $stmt = $this->db->prepare("
        INSERT INTO terms (name, start_date, end_date, year, is_current)
        VALUES (:name, :start_date, :end_date, :year, :is_current)
    ");

    $terms = [
        ['name' => 'I четверть',   'start' => $year . '-09-01',     'end' => $year . '-10-27',     'current' => 0],
        ['name' => 'II четверть',  'start' => $year . '-11-06',     'end' => $year . '-12-29',     'current' => 0],
        ['name' => 'III четверть', 'start' => ($year + 1) . '-01-09','end' => ($year + 1) . '-03-23','current' => 0],
        ['name' => 'IV четверть',  'start' => ($year + 1) . '-04-02','end' => ($year + 1) . '-05-25','current' => 0],
    ];

    foreach ($terms as $t) {
        $stmt->execute([
            ':name' => $t['name'],
            ':start_date' => $t['start'],
            ':end_date' => $t['end'],
            ':year' => $year,
            ':is_current' => $t['current'],
        ]);
    }

    return true;
}

/**
 * Получить периоды конкретного учебного года
 */
public function getByAcademicYear($year) {
    $stmt = $this->db->prepare("
        SELECT *
        FROM terms
        WHERE year = :year
        ORDER BY start_date
    ");
    $stmt->execute([':year' => (int)$year]);
    return $stmt->fetchAll();
}

/**
 * Возвращает список учебных лет из таблицы terms
 */
public function getAvailableYears() {
    $stmt = $this->db->query("
        SELECT DISTINCT year
        FROM terms
        ORDER BY year DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
}