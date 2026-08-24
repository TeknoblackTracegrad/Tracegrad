<?php
/**
 * TRACEGRAD — Super Admin Institutional Report Export
 * ------------------------------------------------------------
 * PHP 7.2+
 *
 * Supported templates:
 * - department_summary
 * - employment
 * - roster
 *
 * Supported formats:
 * - html  : browser preview
 * - pdf   : print-ready HTML + browser print dialog
 * - csv   : CSV download
 *
 * Browser preview / Print-PDF reuse the SAME institutional
 * header/footer artwork as the Department Admin reports.
 */

require_once __DIR__ . '/config.php';


/* ============================================================
   AUTHORIZATION
============================================================ */

if (
    empty($_SESSION['admin_id'])
    ||
    (int)($_SESSION['role_id'] ?? 0) !== 1
) {
    header('Location: admin-login.php');
    exit;
}


/* ============================================================
   HELPERS
============================================================ */

function saReportEsc($value)
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


function saCsvSafe($value)
{
    $text =
        (string)($value ?? '');

    if (
        $text !== ''
        &&
        in_array(
            $text[0],
            ['=', '+', '-', '@'],
            true
        )
    ) {
        return "'" . $text;
    }

    return $text;
}


function saSafeFilename($value)
{
    $value =
        preg_replace(
            '/[^A-Za-z0-9_\-]+/',
            '_',
            (string)$value
        );

    $value =
        trim(
            $value,
            '_'
        );

    return
        $value !== ''
            ? $value
            : 'TRACEGRAD_Report';
}


function saDataUri($mime, $bytes)
{
    if (
        $bytes === false
        ||
        $bytes === null
        ||
        $bytes === ''
    ) {
        return '';
    }

    return
        'data:' .
        $mime .
        ';base64,' .
        base64_encode($bytes);
}


function saOfficialArtwork()
{
    $result = [
        'left' => '',
        'right' => '',
        'footer' => '',
        'ready' => false,
    ];

    $templatePath =
        __DIR__ .
        '/assets/templates/survey-report-template.docx';

    if (
        !is_file($templatePath)
        ||
        !class_exists('ZipArchive')
    ) {
        return $result;
    }

    $zip =
        new ZipArchive();

    $opened =
        $zip->open(
            $templatePath
        );

    if ($opened !== true) {
        return $result;
    }

    /*
     * These are the same institutional images used by the
     * Department Admin report preview:
     *
     * image2.jpeg = ISUFST logo
     * image1.png  = Bagong Pilipinas
     * image3.jpeg = institutional footer artwork
     */
    $result['left'] =
        saDataUri(
            'image/jpeg',
            $zip->getFromName(
                'word/media/image2.jpeg'
            )
        );

    $result['right'] =
        saDataUri(
            'image/png',
            $zip->getFromName(
                'word/media/image1.png'
            )
        );

    $result['footer'] =
        saDataUri(
            'image/jpeg',
            $zip->getFromName(
                'word/media/image3.jpeg'
            )
        );

    $zip->close();

    $result['ready'] =
        $result['left'] !== ''
        &&
        $result['right'] !== ''
        &&
        $result['footer'] !== '';

    return $result;
}


function saSurveyStatus($answerCount, $questionCount)
{
    $answerCount =
        (int)$answerCount;

    $questionCount =
        (int)$questionCount;

    if ($answerCount <= 0) {
        return 'Not Started';
    }

    if (
        $questionCount > 0
        &&
        $answerCount >= $questionCount
    ) {
        return 'Completed';
    }

    return 'Partial';
}


function saRate($numerator, $denominator)
{
    $numerator =
        (int)$numerator;

    $denominator =
        (int)$denominator;

    if ($denominator <= 0) {
        return 0;
    }

    return
        (int)round(
            ($numerator / $denominator) * 100
        );
}


/* ============================================================
   INPUT
============================================================ */

$template =
    strtolower(
        trim(
            (string)(
                $_GET['template']
                ?? 'department_summary'
            )
        )
    );


/* Backward compatibility with the older Super Admin form. */
if ($template === 'ched_tracer') {
    $template = 'department_summary';
}


$allowedTemplates = [
    'department_summary',
    'employment',
    'roster',
];


if (
    !in_array(
        $template,
        $allowedTemplates,
        true
    )
) {
    $template =
        'department_summary';
}


$format =
    strtolower(
        trim(
            (string)(
                $_GET['format']
                ?? 'html'
            )
        )
    );


if (
    !in_array(
        $format,
        ['html', 'pdf', 'csv'],
        true
    )
) {
    $format =
        'html';
}


$collegeId =
    isset($_GET['college_id'])
    &&
    $_GET['college_id'] !== ''
        ? (int)$_GET['college_id']
        : 0;


$batchYear =
    isset($_GET['batch_year'])
    &&
    $_GET['batch_year'] !== ''
        ? (int)$_GET['batch_year']
        : 0;


if (
    $batchYear < 1900
    ||
    $batchYear > 2100
) {
    $batchYear = 0;
}


/* ============================================================
   VALIDATE / RESOLVE DEPARTMENT SCOPE
============================================================ */

$scopeDepartmentName =
    'All Departments';

$scopeDepartmentCode =
    'ALL';


if ($collegeId > 0) {

    $collegeStmt =
        $pdo->prepare(
            "SELECT
                college_id,
                college_code,
                college_name
             FROM colleges
             WHERE college_id = ?
             LIMIT 1"
        );

    $collegeStmt->execute([
        $collegeId
    ]);

    $collegeRow =
        $collegeStmt->fetch();

    if (!$collegeRow) {

        $collegeId = 0;

    } else {

        $scopeDepartmentName =
            (string)$collegeRow['college_name'];

        $scopeDepartmentCode =
            (string)$collegeRow['college_code'];
    }
}


/* ============================================================
   SURVEY QUESTION COUNT
============================================================ */

try {

    $questionCount =
        (int)$pdo->query(
            "SELECT COUNT(*)
             FROM survey_questions
             WHERE status='Active'"
        )->fetchColumn();

} catch (Throwable $e) {

    $questionCount = 0;
}


/* ============================================================
   REPORT DATA
============================================================ */

$reportTitle = '';
$reportSubtitle = '';
$columns = [];
$rows = [];
$rawDepartmentSummary = [];


/* ------------------------------------------------------------
   1. DEPARTMENT SUMMARY
------------------------------------------------------------ */
if ($template === 'department_summary') {

    $reportTitle =
        'INSTITUTIONAL DEPARTMENT SUMMARY';

    $reportSubtitle =
        $collegeId > 0
            ? $scopeDepartmentName
            : 'All Active Departments';


    $graduateJoin =
        "LEFT JOIN graduates g
            ON g.course_id = c.course_id";

    $params = [];


    if ($batchYear > 0) {

        $graduateJoin .=
            " AND g.batch_year = ?";

        $params[] =
            $batchYear;
    }


    $where =
        "WHERE col.status = 'Active'";


    if ($collegeId > 0) {

        $where .=
            " AND col.college_id = ?";

        $params[] =
            $collegeId;
    }


    $sql = "
        SELECT
            col.college_id,
            col.college_code,
            col.college_name,

            (
                SELECT COUNT(*)
                FROM courses pc
                WHERE pc.college_id = col.college_id
                  AND pc.status = 'Active'
            ) AS program_count,

            COUNT(DISTINCT g.graduate_id)
                AS total_alumni,

            COUNT(
                DISTINCT
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM employment ew
                        WHERE ew.graduate_id = g.graduate_id
                          AND ew.employment_status IN (
                              'Employed',
                              'Self-Employed',
                              'Freelancer'
                          )
                    )
                    THEN g.graduate_id
                END
            ) AS working_alumni,

            COUNT(
                DISTINCT
                CASE
                    WHEN COALESCE(sa.answer_count, 0) > 0
                    THEN g.graduate_id
                END
            ) AS survey_responded,

            COUNT(
                DISTINCT
                CASE
                    WHEN " . (int)$questionCount . " > 0
                     AND COALESCE(sa.answer_count, 0) >= " . (int)$questionCount . "
                    THEN g.graduate_id
                END
            ) AS survey_completed,

            COUNT(DISTINCT g.batch_year)
                AS batch_count

        FROM colleges col

        LEFT JOIN courses c
            ON c.college_id = col.college_id

        $graduateJoin

        LEFT JOIN (
            SELECT
                graduate_id,
                COUNT(DISTINCT question_id)
                    AS answer_count
            FROM survey_answers
            GROUP BY graduate_id
        ) sa
            ON sa.graduate_id = g.graduate_id

        $where

        GROUP BY
            col.college_id,
            col.college_code,
            col.college_name

        ORDER BY
            col.college_name ASC
    ";


    $stmt =
        $pdo->prepare(
            $sql
        );

    $stmt->execute(
        $params
    );

    $rawDepartmentSummary =
        $stmt->fetchAll();


    $columns = [
        'Department Code',
        'Department',
        'Active Programs',
        'Alumni',
        'Working Alumni',
        'Employment Rate',
        'Survey Responded',
        'Survey Completed',
        'Survey Completion Rate',
        'Batches Represented',
    ];


    foreach (
        $rawDepartmentSummary
        as $row
    ) {

        $total =
            (int)$row['total_alumni'];

        $working =
            (int)$row['working_alumni'];

        $completed =
            (int)$row['survey_completed'];


        $rows[] = [
            $row['college_code'],
            $row['college_name'],
            (int)$row['program_count'],
            $total,
            $working,
            saRate(
                $working,
                $total
            ) . '%',
            (int)$row['survey_responded'],
            $completed,
            saRate(
                $completed,
                $total
            ) . '%',
            (int)$row['batch_count'],
        ];
    }


/* ------------------------------------------------------------
   2. EMPLOYMENT OUTCOMES
------------------------------------------------------------ */
} elseif ($template === 'employment') {

    $reportTitle =
        'INSTITUTIONAL EMPLOYMENT OUTCOMES';

    $reportSubtitle =
        $collegeId > 0
            ? $scopeDepartmentName
            : 'All Departments';


    $where = [];
    $params = [];


    if ($collegeId > 0) {
        $where[] =
            'col.college_id = ?';
        $params[] =
            $collegeId;
    }


    if ($batchYear > 0) {
        $where[] =
            'g.batch_year = ?';
        $params[] =
            $batchYear;
    }


    $whereSql =
        $where
            ? ' WHERE ' . implode(
                ' AND ',
                $where
            )
            : '';


    $sql = "
        SELECT
            g.student_id,

            CONCAT(
                g.lastname,
                ', ',
                g.firstname
            ) AS alumni_name,

            c.course_code,
            c.course_major,

            col.college_code,
            col.college_name,

            g.batch_year,

            COALESCE(
                e.employment_status,
                'No employment record'
            ) AS employment_status,

            COALESCE(
                NULLIF(e.employer_name, ''),
                comp.company_name,
                '—'
            ) AS company,

            COALESCE(
                e.position_title,
                '—'
            ) AS position_title,

            e.monthly_salary,

            COALESCE(
                NULLIF(e.work_region, ''),
                e.work_location,
                '—'
            ) AS work_location

        FROM graduates g

        JOIN courses c
            ON c.course_id = g.course_id

        JOIN colleges col
            ON col.college_id = c.college_id

        LEFT JOIN employment e
            ON e.employment_id = (
                SELECT MAX(e2.employment_id)
                FROM employment e2
                WHERE e2.graduate_id = g.graduate_id
            )

        LEFT JOIN companies comp
            ON comp.company_id = e.company_id

        $whereSql

        ORDER BY
            col.college_name ASC,
            g.batch_year DESC,
            g.lastname ASC,
            g.firstname ASC
    ";


    $stmt =
        $pdo->prepare(
            $sql
        );

    $stmt->execute(
        $params
    );

    $data =
        $stmt->fetchAll();


    $columns = [
        'Student ID',
        'Alumni',
        'Program',
        'Department',
        'Batch',
        'Employment Status',
        'Company / Employer',
        'Position',
        'Monthly Salary',
        'Work Location',
    ];


    foreach ($data as $row) {

        $program =
            (string)$row['course_code'];

        if (
            trim(
                (string)($row['course_major'] ?? '')
            ) !== ''
        ) {
            $program .=
                ' — ' .
                trim(
                    (string)$row['course_major']
                );
        }


        $rows[] = [
            $row['student_id'],
            $row['alumni_name'],
            $program,
            $row['college_code'],
            $row['batch_year'],
            $row['employment_status'],
            $row['company'],
            $row['position_title'],
            $row['monthly_salary'] !== null
                ? number_format(
                    (float)$row['monthly_salary'],
                    2,
                    '.',
                    ''
                )
                : '—',
            $row['work_location'],
        ];
    }


/* ------------------------------------------------------------
   3. INSTITUTIONAL ROSTER
------------------------------------------------------------ */
} else {

    $reportTitle =
        'INSTITUTIONAL ALUMNI ROSTER';

    $reportSubtitle =
        $collegeId > 0
            ? $scopeDepartmentName
            : 'All Departments';


    $where = [];
    $params = [];


    if ($collegeId > 0) {
        $where[] =
            'col.college_id = ?';
        $params[] =
            $collegeId;
    }


    if ($batchYear > 0) {
        $where[] =
            'g.batch_year = ?';
        $params[] =
            $batchYear;
    }


    $whereSql =
        $where
            ? ' WHERE ' . implode(
                ' AND ',
                $where
            )
            : '';


    $sql = "
        SELECT
            g.student_id,

            CONCAT(
                g.lastname,
                ', ',
                g.firstname
            ) AS alumni_name,

            c.course_code,
            c.course_major,

            col.college_code,
            col.college_name,

            g.batch_year,

            COALESCE(
                sa.answer_count,
                0
            ) AS answer_count,

            COALESCE(
                (
                    SELECT e3.employment_status
                    FROM employment e3
                    WHERE e3.graduate_id = g.graduate_id
                    ORDER BY e3.employment_id DESC
                    LIMIT 1
                ),
                'No employment record'
            ) AS employment_status

        FROM graduates g

        JOIN courses c
            ON c.course_id = g.course_id

        JOIN colleges col
            ON col.college_id = c.college_id

        LEFT JOIN (
            SELECT
                graduate_id,
                COUNT(DISTINCT question_id)
                    AS answer_count
            FROM survey_answers
            GROUP BY graduate_id
        ) sa
            ON sa.graduate_id = g.graduate_id

        $whereSql

        ORDER BY
            col.college_name ASC,
            g.batch_year DESC,
            g.lastname ASC,
            g.firstname ASC
    ";


    $stmt =
        $pdo->prepare(
            $sql
        );

    $stmt->execute(
        $params
    );

    $data =
        $stmt->fetchAll();


    $columns = [
        'Student ID',
        'Alumni',
        'Program',
        'Department',
        'Batch',
        'Survey Status',
        'Answers',
        'Employment Status',
    ];


    foreach ($data as $row) {

        $program =
            (string)$row['course_code'];

        if (
            trim(
                (string)($row['course_major'] ?? '')
            ) !== ''
        ) {
            $program .=
                ' — ' .
                trim(
                    (string)$row['course_major']
                );
        }


        $rows[] = [
            $row['student_id'],
            $row['alumni_name'],
            $program,
            $row['college_code'],
            $row['batch_year'],
            saSurveyStatus(
                $row['answer_count'],
                $questionCount
            ),
            (int)$row['answer_count'],
            $row['employment_status'],
        ];
    }
}


/* ============================================================
   CSV OUTPUT
============================================================ */

$scopeLabel =
    $collegeId > 0
        ? $scopeDepartmentCode
        : 'All_Departments';


$batchLabel =
    $batchYear > 0
        ? 'Batch_' . $batchYear
        : 'All_Batches';


$filenameBase =
    saSafeFilename(
        'TRACEGRAD_' .
        $reportTitle .
        '_' .
        $scopeLabel .
        '_' .
        $batchLabel
    );


if ($format === 'csv') {

    if (ob_get_length()) {
        ob_clean();
    }

    header(
        'Content-Type: text/csv; charset=utf-8'
    );

    header(
        'Content-Disposition: attachment; filename="' .
        $filenameBase .
        '.csv"'
    );

    /*
     * UTF-8 BOM helps Excel display names and special characters.
     */
    echo "\xEF\xBB\xBF";

    $out =
        fopen(
            'php://output',
            'w'
        );

    if ($out === false) {
        http_response_code(500);
        exit(
            'Unable to create CSV output.'
        );
    }


    $safeColumns = [];

    foreach ($columns as $column) {
        $safeColumns[] =
            saCsvSafe($column);
    }

    fputcsv(
        $out,
        $safeColumns
    );


    foreach ($rows as $row) {

        $safeRow = [];

        foreach ($row as $value) {
            $safeRow[] =
                saCsvSafe($value);
        }

        fputcsv(
            $out,
            $safeRow
        );
    }

    fclose($out);
    exit;
}


/* ============================================================
   OFFICIAL ARTWORK
============================================================ */

$artwork =
    saOfficialArtwork();

/* Exact Department Admin header/footer is mandatory. */
if (!$artwork['ready']) {
    http_response_code(500);
    exit(
        'Official ISUFST report header/footer could not be loaded. ' .
        'Make sure assets/templates/survey-report-template.docx exists ' .
        'and the PHP ZIP extension is enabled.'
    );
}


/* ============================================================
   PRINT / PREVIEW SUMMARY VALUES
============================================================ */

$generatedAt =
    date(
        'F d, Y g:i A'
    );

$batchDisplay =
    $batchYear > 0
        ? 'Batch ' . $batchYear
        : 'All Batches';


$totalResultRows =
    count($rows);


/*
 * KPI values displayed at the beginning of the Department Summary
 * report. These come from exactly the same filtered rows used by
 * the table.
 */
$summaryTotalAlumni = 0;
$summaryWorking = 0;
$summaryResponded = 0;
$summaryCompleted = 0;
$summaryPrograms = 0;


if ($template === 'department_summary') {

    foreach (
        $rawDepartmentSummary
        as $summaryRow
    ) {

        $summaryTotalAlumni +=
            (int)$summaryRow['total_alumni'];

        $summaryWorking +=
            (int)$summaryRow['working_alumni'];

        $summaryResponded +=
            (int)$summaryRow['survey_responded'];

        $summaryCompleted +=
            (int)$summaryRow['survey_completed'];

        $summaryPrograms +=
            (int)$summaryRow['program_count'];
    }
}


/* ============================================================
   HTML TABLE
============================================================ */

$tableHtml =
    '<div class="table-wrap">' .
    '<table class="report-table">' .
    '<thead><tr>';


foreach ($columns as $column) {

    $tableHtml .=
        '<th>' .
        saReportEsc($column) .
        '</th>';
}


$tableHtml .=
    '</tr></thead><tbody>';


if ($rows) {

    foreach ($rows as $row) {

        $tableHtml .=
            '<tr>';

        foreach ($row as $value) {

            $cellText =
                (string)$value;

            $cellClass =
                '';

            $lower =
                strtolower(
                    trim(
                        $cellText
                    )
                );

            if (
                in_array(
                    $lower,
                    [
                        'completed',
                        'employed',
                        'self-employed',
                        'freelancer',
                    ],
                    true
                )
            ) {
                $cellClass =
                    ' status-good';
            } elseif (
                in_array(
                    $lower,
                    [
                        'partial',
                        'underemployed',
                    ],
                    true
                )
            ) {
                $cellClass =
                    ' status-warning';
            } elseif (
                in_array(
                    $lower,
                    [
                        'not started',
                        'unemployed',
                        'no employment record',
                    ],
                    true
                )
            ) {
                $cellClass =
                    ' status-danger';
            }

            $tableHtml .=
                '<td class="' .
                $cellClass .
                '">' .
                saReportEsc($cellText) .
                '</td>';
        }

        $tableHtml .=
            '</tr>';
    }

} else {

    $tableHtml .=
        '<tr>' .
        '<td colspan="' .
        max(
            1,
            count($columns)
        ) .
        '" class="empty-cell">' .
        'No records match the selected report filters.' .
        '</td>' .
        '</tr>';
}


$tableHtml .=
    '</tbody></table></div>';


/* ============================================================
   SUMMARY BLOCK
============================================================ */

$summaryHtml = '';


if ($template === 'department_summary') {

    $summaryHtml =
        '<section class="summary-grid">' .

        '<div class="summary-card">' .
            '<span>Departments</span>' .
            '<strong>' .
                number_format(
                    count(
                        $rawDepartmentSummary
                    )
                ) .
            '</strong>' .
        '</div>' .

        '<div class="summary-card">' .
            '<span>Programs</span>' .
            '<strong>' .
                number_format(
                    $summaryPrograms
                ) .
            '</strong>' .
        '</div>' .

        '<div class="summary-card">' .
            '<span>Alumni</span>' .
            '<strong>' .
                number_format(
                    $summaryTotalAlumni
                ) .
            '</strong>' .
        '</div>' .

        '<div class="summary-card">' .
            '<span>Working Alumni</span>' .
            '<strong>' .
                number_format(
                    $summaryWorking
                ) .
            '</strong>' .
            '<small>' .
                saRate(
                    $summaryWorking,
                    $summaryTotalAlumni
                ) .
                '% of alumni' .
            '</small>' .
        '</div>' .

        '<div class="summary-card">' .
            '<span>Survey Responded</span>' .
            '<strong>' .
                number_format(
                    $summaryResponded
                ) .
            '</strong>' .
        '</div>' .

        '<div class="summary-card">' .
            '<span>Survey Completed</span>' .
            '<strong>' .
                number_format(
                    $summaryCompleted
                ) .
            '</strong>' .
            '<small>' .
                saRate(
                    $summaryCompleted,
                    $summaryTotalAlumni
                ) .
                '% of alumni' .
            '</small>' .
        '</div>' .

        '</section>';
}


/* ============================================================
   OFFICIAL HEADER / FOOTER HTML
============================================================ */

$leftLogoHtml =
    '<img class="official-header-logo-left" src="' .
    saReportEsc($artwork['left']) .
    '" alt="ISUFST">';

$rightLogoHtml =
    '<img class="official-header-logo-right" src="' .
    saReportEsc($artwork['right']) .
    '" alt="Bagong Pilipinas">';

$footerHtml =
    '<img src="' .
    saReportEsc($artwork['footer']) .
    '" alt="ISUFST institutional recognitions and core values">';


/* ============================================================
   PAGE MODE — EXACTLY MATCH DEPARTMENT ADMIN
============================================================ */
$isWideReport = false;
$pageSize = 'A4 portrait';


/* ============================================================
   HTML OUTPUT
============================================================ */

if (ob_get_length()) {
    ob_clean();
}


header(
    'Content-Type: text/html; charset=utf-8'
);

header(
    'Content-Disposition: inline; filename="' .
    $filenameBase .
    '.html"'
);


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>
<title><?= saReportEsc($reportTitle) ?></title>

<style>
:root {
    --navy:#17365d;
    --blue:#4472c4;
    --light-blue:#d9eaf7;
    --border:#cfd8e3;
    --soft-border:#e6ebf0;
    --label:#44546a;
    --text:#222;
    --muted:#666;
    --good:#237044;
    --warning:#986b00;
    --danger:#a83232;
}
* { box-sizing:border-box; }
html, body {
    margin:0; padding:0; background:#e9edf2; color:var(--text);
    font-family:Arial, Helvetica, sans-serif; font-size:10pt; line-height:1.35;
}
.preview-toolbar {
    position:sticky; top:0; z-index:100; display:flex; align-items:center;
    justify-content:space-between; gap:12px; padding:10px 18px;
    background:#17365d; color:#fff; box-shadow:0 4px 14px rgba(0,0,0,.12);
}
.preview-toolbar-copy strong,.preview-toolbar-copy span { display:block; }
.preview-toolbar-copy strong { font-size:13px; }
.preview-toolbar-copy span { margin-top:2px; color:#dce7f5; font-size:10px; }
.preview-toolbar-actions { display:flex; flex-wrap:wrap; gap:8px; }
.preview-toolbar button,.preview-toolbar a {
    display:inline-flex; align-items:center; justify-content:center; min-height:34px;
    padding:7px 12px; border:1px solid rgba(255,255,255,.22); border-radius:6px;
    background:rgba(255,255,255,.1); color:#fff; font:700 11px Arial,sans-serif;
    text-decoration:none; cursor:pointer;
}
.preview-toolbar button:hover,.preview-toolbar a:hover { background:rgba(255,255,255,.18); }
.report-page {
    position:relative; width:210mm; min-height:297mm; margin:20px auto;
    padding:38mm 17mm 27mm; background:#fff; box-shadow:0 8px 35px rgba(15,23,42,.14);
}
.official-header {
    position:absolute; left:17mm; right:17mm; top:7mm; height:28mm;
    display:grid; grid-template-columns:31mm 1fr 29mm; align-items:center; gap:3mm;
    padding-bottom:2.5mm; border-bottom:1.2px solid #2d77aa;
}
.official-header-logo-left { width:29mm; height:20mm; object-fit:contain; justify-self:start; }
.official-header-logo-right { width:25mm; height:19mm; object-fit:contain; justify-self:end; }
.official-header-copy { text-align:center; line-height:1.22; }
.official-header-copy .republic { font-size:8.5pt; }
.official-header-copy .university {
    margin-top:1mm; color:#176ba0; font-size:9.5pt; font-weight:700; white-space:nowrap;
}
.official-header-copy .office { margin-top:.5mm; color:#111; font-size:10.5pt; font-weight:700; }
.official-header-copy .contact,.official-header-copy .website {
    margin-top:.5mm; color:#222; font-size:7.8pt;
}
.official-footer {
    position:absolute; left:11mm; right:11mm; bottom:6mm; height:19mm;
    display:flex; align-items:flex-end; justify-content:center;
}
.official-footer img { width:100%; max-height:18mm; object-fit:contain; }
.report-title {
    margin:0; color:var(--navy); text-align:center; font-size:14pt; font-weight:700; letter-spacing:.01em;
}
.report-subtitle { margin:1mm 0 5mm; color:#666; text-align:center; font-size:10pt; }
.report-meta { width:100%; margin-bottom:4mm; border-collapse:collapse; table-layout:fixed; font-size:8.5pt; }
.report-meta td { width:25%; padding:2.2mm 2.5mm; border:1px solid var(--border); background:#f8fafc; vertical-align:top; }
.report-meta span,.report-meta strong { display:block; }
.report-meta span { color:var(--label); font-size:7.2pt; font-weight:700; }
.report-meta strong { margin-top:.6mm; color:#222; font-size:8.3pt; }
.summary-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:2mm; margin:0 0 4mm; }
.summary-card { min-width:0; padding:2.2mm 2.5mm; border:1px solid var(--border); background:#f8fafc; }
.summary-card span,.summary-card strong,.summary-card small { display:block; }
.summary-card span { color:var(--label); font-size:7pt; font-weight:700; }
.summary-card strong { margin-top:.7mm; color:var(--navy); font-size:12pt; }
.summary-card small { margin-top:.4mm; color:var(--muted); font-size:7pt; }
.table-wrap { width:100%; overflow:visible; }
.report-table { width:100%; border-collapse:collapse; table-layout:auto; font-size:7.3pt; }
.report-table th,.report-table td {
    padding:1.6mm 1.8mm; border:1px solid var(--border); vertical-align:top;
    overflow-wrap:anywhere; word-break:normal;
}
.report-table th { background:#f1f5f9; color:var(--label); text-align:left; font-weight:700; }
.report-table tbody tr:nth-child(even) { background:#fbfcfd; }
.report-table td.status-good { color:var(--good); font-weight:700; }
.report-table td.status-warning { color:var(--warning); font-weight:700; }
.report-table td.status-danger { color:var(--danger); font-weight:700; }
.empty-cell { padding:4mm !important; background:#fafbfc; color:#777; text-align:center; font-size:8.5pt; }
.document-note { margin:5mm 0 0; color:#777; text-align:center; font-size:7.5pt; }
.document-note strong { color:#555; }
.generated-note { margin:2mm 0 0; color:#888; text-align:center; font-size:7pt; }
@media print {
    @page { size:A4 portrait; margin:38mm 17mm 27mm 17mm; }
    html,body { background:#fff; }
    body { margin:0; padding:0; }
    .preview-toolbar { display:none !important; }
    .report-page { width:auto; min-height:0; margin:0; padding:0; box-shadow:none; }
    .official-header { position:fixed; left:0; right:0; top:-31mm; height:28mm; }
    .official-footer { position:fixed; left:-6mm; right:-6mm; bottom:-22mm; height:19mm; }
    .report-title { margin-top:0; }
    thead { display:table-header-group; }
    tfoot { display:table-footer-group; }
    .summary-card,.document-note,.generated-note,.report-table tr { break-inside:avoid; page-break-inside:avoid; }
    a { color:inherit; text-decoration:none; }
}
@media screen and (max-width:900px) {
    body { background:#fff; }
    .preview-toolbar { position:relative; align-items:flex-start; flex-direction:column; }
    .report-page { width:100%; min-height:0; margin:0; padding:34mm 10px 28mm; box-shadow:none; }
    .official-header { left:10px; right:10px; grid-template-columns:70px 1fr 65px; gap:4px; }
    .official-header-logo-left { width:68px; }
    .official-header-logo-right { width:58px; }
    .official-header-copy .university { font-size:7.5pt; white-space:normal; }
    .official-header-copy .office { font-size:8pt; }
    .official-header-copy .contact,.official-header-copy .website,.official-header-copy .republic { font-size:6.5pt; }
    .official-footer { left:8px; right:8px; }
    .summary-grid { grid-template-columns:1fr; }
    .report-meta,.report-meta tbody,.report-meta tr,.report-meta td { display:block; width:100%; }
    .report-meta td { border-bottom:0; }
    .report-meta td:last-child { border-bottom:1px solid var(--border); }
}
</style>
</head>

<body>

<div class="preview-toolbar no-print">
    <div class="preview-toolbar-copy">
        <strong>TRACEGRAD Official Report Preview</strong>
        <span>Browser preview and print/PDF use the same document layout as the official DOCX report.</span>
    </div>

    <div class="preview-toolbar-actions">

        <a
            href="admin-report-export.php?<?= saReportEsc(
                http_build_query(
                    array_merge(
                        $_GET,
                        ['format' => 'csv']
                    )
                )
            ) ?>"
        >
            Download CSV
        </a>

        <button
            type="button"
            class="primary-action"
            onclick="window.print()"
        >
            Print / Save PDF
        </button>

        <button
            type="button"
            onclick="window.close()"
        >
            Close
        </button>

    </div>
</div>


<main class="report-page">

    <header class="official-header">
        <?= $leftLogoHtml ?>

        <div class="official-header-copy">
            <div class="republic">Republic of the Philippines</div>
            <div class="university">ILOILO STATE UNIVERSITY OF FISHERIES SCIENCE AND TECHNOLOGY</div>
            <div class="office">ADMISSION AND STUDENT RECORDS OFFICE</div>
            <div class="contact">San Enrique, Iloilo | Email: sanenriquecampus@gmail.com</div>
            <div class="website">Website: www.isufst.edu.ph | Contact No: (033) 327-3405</div>
        </div>

        <?= $rightLogoHtml ?>
    </header>

    <footer class="official-footer">
        <?= $footerHtml ?>
    </footer>


    <h1 class="report-title">
        <?= saReportEsc($reportTitle) ?>
    </h1>

    <div class="report-subtitle">
        <?= saReportEsc($reportSubtitle) ?>
    </div>


    <table class="report-meta">
        <tbody>
            <tr>
                <td>
                    <span>Generated</span>
                    <strong><?= saReportEsc($generatedAt) ?></strong>
                </td>

                <td>
                    <span>Department Scope</span>
                    <strong>
                        <?= saReportEsc(
                            $collegeId > 0
                                ? $scopeDepartmentCode . ' — ' . $scopeDepartmentName
                                : 'All Departments'
                        ) ?>
                    </strong>
                </td>

                <td>
                    <span>Graduate Batch</span>
                    <strong><?= saReportEsc($batchDisplay) ?></strong>
                </td>

                <td>
                    <span>Records / Rows</span>
                    <strong><?= number_format($totalResultRows) ?></strong>
                </td>
            </tr>
        </tbody>
    </table>


    <?= $summaryHtml ?>


    <?= $tableHtml ?>


    <div class="document-note">
        <strong>Document Control:</strong>
        This report was generated by the TRACEGRAD system for
        authorized institutional alumni and tracer-study reporting.
        Results reflect records currently stored in the TRACEGRAD
        database and the filters selected by the Super Administrator.
        Please handle personal information in accordance with applicable
        institutional privacy and records-management policies.
    </div>


    <div class="generated-note">
        Generated by TRACEGRAD System ·
        <?= saReportEsc(date('Y-m-d H:i:s')) ?>
    </div>

</main>


<?php if ($format === 'pdf'): ?>
<script>
window.addEventListener(
    'load',
    function () {
        setTimeout(
            function () {
                window.print();
            },
            250
        );
    }
);
</script>
<?php endif; ?>

</body>
</html>