<?php
/**
 * TRACEGRAD V2 - Individual Survey DOCX Report
 * ------------------------------------------------------------
 * Generates an organized Word report from the official ISUFST
 * header/footer DOCX template supplied by the user.
 *
 * PHP: 7.2+
 * Required extension: zip (ZipArchive)
 *
 * Recommended template location:
 *   assets/templates/survey-report-template.docx
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

/* ============================================================
   AUTHORIZATION
============================================================ */
if (empty($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

if ((int)($_SESSION['role_id'] ?? 0) !== 2) {
    header('Location: admin-dashboard.php');
    exit;
}

$adminId = (int)$_SESSION['admin_id'];
$collegeId = (int)($_SESSION['admin_college_id'] ?? 0);
$graduateId = isset($_GET['graduate_id']) ? (int)$_GET['graduate_id'] : 0;
$includeEmployment = !isset($_GET['include_employment']) || (int)$_GET['include_employment'] === 1;

if ($collegeId <= 0) {
    http_response_code(403);
    exit('Your Department Admin account is not assigned to a college.');
}

if ($graduateId <= 0) {
    http_response_code(400);
    exit('Invalid alumni record.');
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    exit(
        'DOCX export requires the PHP ZIP extension. ' .
        'Enable extension=zip in XAMPP php.ini, restart Apache, and try again.'
    );
}

/* ============================================================
   OFFICIAL WORD TEMPLATE
============================================================ */
$templateCandidates = [
    __DIR__ . '/assets/templates/survey-report-template.docx',
    __DIR__ . '/survey-report-template.docx',
    __DIR__ . '/header and footer format.docx'
];

$templatePath = '';
foreach ($templateCandidates as $candidate) {
    if (is_file($candidate)) {
        $templatePath = $candidate;
        break;
    }
}

if ($templatePath === '') {
    http_response_code(500);
    exit(
        'Survey report template was not found. Place survey-report-template.docx ' .
        'inside assets/templates/ and try again.'
    );
}

/* ============================================================
   DATABASE HELPERS
============================================================ */
function tg_docx_table_exists($pdo, $table)
{
    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?"
        );
        $stmt->execute([$table]);
        return ((int)$stmt->fetchColumn()) > 0;
    } catch (Throwable $e) {
        return false;
    }
}

function tg_docx_column_exists($pdo, $table, $column)
{
    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?"
        );
        $stmt->execute([$table, $column]);
        return ((int)$stmt->fetchColumn()) > 0;
    } catch (Throwable $e) {
        return false;
    }
}

/* ============================================================
   XML / WORD HELPERS
============================================================ */
function tg_docx_xml($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function tg_docx_clean_text($value)
{
    $value = (string)$value;
    $value = str_replace(["\r\n", "\r"], "\n", $value);
    return preg_replace('/[^\P{C}\n\t]/u', '', $value);
}

function tg_docx_run($text, $bold = false, $size = 18, $color = '000000', $font = 'Arial')
{
    $text = tg_docx_clean_text($text);
    $parts = explode("\n", $text);
    $xml = '';

    foreach ($parts as $index => $part) {
        if ($index > 0) {
            $xml .= '<w:r><w:br/></w:r>';
        }

        $xml .= '<w:r><w:rPr>' .
            '<w:rFonts w:ascii="' . tg_docx_xml($font) . '" w:hAnsi="' . tg_docx_xml($font) . '"/>' .
            '<w:sz w:val="' . (int)$size . '"/><w:szCs w:val="' . (int)$size . '"/>' .
            ($bold ? '<w:b/>' : '') .
            '<w:color w:val="' . tg_docx_xml($color) . '"/>' .
            '</w:rPr><w:t xml:space="preserve">' . tg_docx_xml($part) . '</w:t></w:r>';
    }

    return $xml;
}

function tg_docx_paragraph(
    $text = '',
    $bold = false,
    $size = 18,
    $color = '000000',
    $align = '',
    $before = 0,
    $after = 60,
    $keepNext = false,
    $bottomBorder = ''
) {
    $pPr = '<w:pPr>';

    if ($align !== '') {
        $pPr .= '<w:jc w:val="' . tg_docx_xml($align) . '"/>';
    }

    $pPr .= '<w:spacing w:before="' . (int)$before . '" w:after="' . (int)$after . '"/>';

    if ($keepNext) {
        $pPr .= '<w:keepNext/>';
    }

    if ($bottomBorder !== '') {
        $pPr .= '<w:pBdr><w:bottom w:val="single" w:sz="8" w:space="4" w:color="' .
            tg_docx_xml($bottomBorder) . '"/></w:pBdr>';
    }

    $pPr .= '</w:pPr>';

    return '<w:p>' . $pPr . tg_docx_run($text, $bold, $size, $color) . '</w:p>';
}

function tg_docx_section_title($text)
{
    return tg_docx_paragraph($text, true, 20, '17365D', '', 110, 55, true, '5B9BD5');
}

function tg_docx_cell($contentXml, $width, $shade = '', $gridSpan = 0, $border = 'D9E2F3')
{
    $tcPr = '<w:tcPr><w:tcW w:w="' . (int)$width . '" w:type="dxa"/>';

    if ($gridSpan > 0) {
        $tcPr .= '<w:gridSpan w:val="' . (int)$gridSpan . '"/>';
    }

    if ($shade !== '') {
        $tcPr .= '<w:shd w:fill="' . tg_docx_xml($shade) . '"/>';
    }

    $tcPr .= '<w:vAlign w:val="center"/>';

    if ($border !== '') {
        $tcPr .= '<w:tcBorders>' .
            '<w:top w:val="single" w:sz="4" w:color="' . tg_docx_xml($border) . '"/>' .
            '<w:left w:val="single" w:sz="4" w:color="' . tg_docx_xml($border) . '"/>' .
            '<w:bottom w:val="single" w:sz="4" w:color="' . tg_docx_xml($border) . '"/>' .
            '<w:right w:val="single" w:sz="4" w:color="' . tg_docx_xml($border) . '"/>' .
            '</w:tcBorders>';
    }

    $tcPr .= '</w:tcPr>';

    return '<w:tc>' . $tcPr . $contentXml . '</w:tc>';
}

function tg_docx_row($cells, $repeatHeader = false, $cantSplit = true)
{
    $trPr = '<w:trPr>' .
        ($repeatHeader ? '<w:tblHeader/>' : '') .
        ($cantSplit ? '<w:cantSplit/>' : '') .
        '</w:trPr>';

    return '<w:tr>' . $trPr . implode('', $cells) . '</w:tr>';
}

function tg_docx_table($rows, $widths)
{
    $grid = '';
    foreach ($widths as $width) {
        $grid .= '<w:gridCol w:w="' . (int)$width . '"/>';
    }

    return '<w:tbl>' .
        '<w:tblPr>' .
            '<w:tblW w:w="0" w:type="auto"/>' .
            '<w:tblLayout w:type="fixed"/>' .
            '<w:tblCellMar>' .
                '<w:top w:w="45" w:type="dxa"/>' .
                '<w:left w:w="90" w:type="dxa"/>' .
                '<w:bottom w:w="45" w:type="dxa"/>' .
                '<w:right w:w="90" w:type="dxa"/>' .
            '</w:tblCellMar>' .
        '</w:tblPr>' .
        '<w:tblGrid>' . $grid . '</w:tblGrid>' .
        implode('', $rows) .
        '</w:tbl>';
}

function tg_docx_label_cell($label, $width)
{
    return tg_docx_cell(
        tg_docx_paragraph($label, true, 16, '44546A', '', 0, 0),
        $width,
        'F2F6FA'
    );
}

function tg_docx_value_cell($value, $width, $bold = false, $color = '000000')
{
    return tg_docx_cell(
        tg_docx_paragraph($value, $bold, 16, $color, '', 0, 0),
        $width
    );
}

function tg_docx_status_color($status)
{
    $status = strtolower(trim((string)$status));

    if (in_array($status, ['completed', 'employed', 'submitted', 'active', 'yes'], true)) {
        return '237044';
    }

    if (in_array($status, ['partial', 'pending', 'underemployed'], true)) {
        return '986B00';
    }

    if (in_array($status, ['missing', 'not submitted', 'unemployed', 'not started'], true)) {
        return 'A83232';
    }

    return '000000';
}

function tg_docx_safe_filename($name)
{
    $name = preg_replace('/[^A-Za-z0-9._-]+/', '_', (string)$name);
    $name = trim($name, '._-');
    return $name !== '' ? $name : 'TRACEGRAD_Survey_Report';
}

/* ============================================================
   COLLEGE
============================================================ */
$stmt = $pdo->prepare(
    "SELECT college_name, college_code
     FROM colleges
     WHERE college_id = ?
     LIMIT 1"
);
$stmt->execute([$collegeId]);
$college = $stmt->fetch();

if (!$college) {
    http_response_code(404);
    exit('Assigned college was not found.');
}

$collegeName = (string)$college['college_name'];
$collegeCode = (string)$college['college_code'];

/* ============================================================
   ALUMNI - DEPARTMENT SCOPE ENFORCED
============================================================ */
$stmt = $pdo->prepare(
    "SELECT g.*, c.course_code, c.course_name
     FROM graduates g
     JOIN courses c ON c.course_id = g.course_id
     WHERE g.graduate_id = ?
       AND c.college_id = ?
     LIMIT 1"
);
$stmt->execute([$graduateId, $collegeId]);
$alumni = $stmt->fetch();

if (!$alumni) {
    http_response_code(404);
    exit('Alumni record was not found in your assigned department.');
}

/* ============================================================
   SURVEY ANSWERS
============================================================ */
$stmt = $pdo->prepare(
    "SELECT
        sa.*,
        q.question,
        q.question_type,
        qo.option_text,
        sc.category_name,
        sc.display_order AS category_order,
        q.display_order AS question_order
     FROM survey_answers sa
     JOIN survey_questions q ON q.question_id = sa.question_id
     LEFT JOIN survey_question_options qo ON qo.option_id = sa.option_id
     LEFT JOIN survey_categories sc ON sc.category_id = q.category_id
     WHERE sa.graduate_id = ?
     ORDER BY
       COALESCE(sc.display_order, 0),
       COALESCE(q.display_order, q.question_id),
       sa.answer_id"
);
$stmt->execute([$graduateId]);
$answers = $stmt->fetchAll();

/* ============================================================
   SURVEY COUNTS / STATUS
============================================================ */
$questionTotal = 0;
try {
    if (tg_docx_column_exists($pdo, 'survey_questions', 'status')) {
        $questionTotal = (int)$pdo->query(
            "SELECT COUNT(*) FROM survey_questions WHERE status='Active'"
        )->fetchColumn();
    } else {
        $questionTotal = (int)$pdo->query(
            "SELECT COUNT(*) FROM survey_questions"
        )->fetchColumn();
    }
} catch (Throwable $e) {
    $questionTotal = 0;
}

$stmt = $pdo->prepare(
    "SELECT COUNT(DISTINCT question_id)
     FROM survey_answers
     WHERE graduate_id = ?"
);
$stmt->execute([$graduateId]);
$answerQuestionCount = (int)$stmt->fetchColumn();

$submissionStatus = '';
if (tg_docx_table_exists($pdo, 'survey_submissions')) {
    try {
        $stmt = $pdo->prepare(
            "SELECT status
             FROM survey_submissions
             WHERE graduate_id = ?
             LIMIT 1"
        );
        $stmt->execute([$graduateId]);
        $submissionStatus = (string)$stmt->fetchColumn();
    } catch (Throwable $e) {
        $submissionStatus = '';
    }
}

if ($submissionStatus === 'Submitted') {
    $surveyStatus = 'Completed';
} elseif ($submissionStatus === 'Draft') {
    $surveyStatus = 'Partial';
} elseif ($answerQuestionCount <= 0) {
    $surveyStatus = 'Not started';
} elseif ($questionTotal > 0 && $answerQuestionCount >= $questionTotal) {
    $surveyStatus = 'Completed';
} else {
    $surveyStatus = 'Partial';
}

$completionPercent = $questionTotal > 0
    ? min(100, (int)round(($answerQuestionCount / $questionTotal) * 100))
    : 0;

/* ============================================================
   LATEST EMPLOYMENT
============================================================ */
$employment = null;

if ($includeEmployment && tg_docx_table_exists($pdo, 'employment')) {
    try {
        if (tg_docx_table_exists($pdo, 'companies')) {
            $stmt = $pdo->prepare(
                "SELECT e.*, comp.company_name, comp.industry, comp.company_address
                 FROM employment e
                 LEFT JOIN companies comp ON comp.company_id = e.company_id
                 WHERE e.graduate_id = ?
                 ORDER BY e.employment_id DESC
                 LIMIT 1"
            );
        } else {
            $stmt = $pdo->prepare(
                "SELECT e.*
                 FROM employment e
                 WHERE e.graduate_id = ?
                 ORDER BY e.employment_id DESC
                 LIMIT 1"
            );
        }

        $stmt->execute([$graduateId]);
        $employment = $stmt->fetch();
    } catch (Throwable $e) {
        $employment = null;
    }
}

/* ============================================================
   DISPLAY VALUES
============================================================ */
$fullName = trim(
    (string)($alumni['firstname'] ?? '') . ' ' .
    (string)($alumni['middlename'] ?? '') . ' ' .
    (string)($alumni['lastname'] ?? '') . ' ' .
    (string)($alumni['suffix'] ?? '')
);

$email = trim((string)($alumni['personal_email'] ?? ''));
if ($email === '') {
    $email = trim((string)($alumni['student_email'] ?? ''));
}
if ($email === '') {
    $email = 'Not provided';
}

$mobile = trim((string)($alumni['mobile_number'] ?? ''));
if ($mobile === '') {
    $mobile = 'Not provided';
}

$latinHonor = trim((string)($alumni['latin_honor'] ?? ''));
if ($latinHonor === '') {
    $latinHonor = 'None';
}

$workplaceStatus = !empty($alumni['workplace_photo']) ? 'Submitted' : 'Not submitted';
$companyIdStatus = !empty($alumni['company_id_proof']) ? 'Submitted' : 'Not submitted';
$employmentStatus = $employment && !empty($employment['employment_status'])
    ? (string)$employment['employment_status']
    : 'No employment record';
$generatedAt = date('F d, Y h:i A');

/* ============================================================
   BUILD PROFESSIONAL REPORT BODY
============================================================ */
$body = '';

$body .= tg_docx_paragraph(
    'GRADUATE TRACER SURVEY REPORT',
    true,
    22,
    '17365D',
    'center',
    80,
    30
);
$body .= tg_docx_paragraph(
    'Individual Alumni Response',
    false,
    18,
    '666666',
    'center',
    0,
    70
);

/* Report information */
$rows = [];
$rows[] = tg_docx_row([
    tg_docx_cell(tg_docx_paragraph('Report Type', true, 16, '44546A', '', 0, 0), 1800, 'D9EAF7'),
    tg_docx_value_cell('Individual Survey Response', 3000),
    tg_docx_cell(tg_docx_paragraph('Generated', true, 16, '44546A', '', 0, 0), 1500, 'D9EAF7'),
    tg_docx_value_cell($generatedAt, 3000)
]);
$rows[] = tg_docx_row([
    tg_docx_cell(tg_docx_paragraph('Department', true, 16, '44546A', '', 0, 0), 1800, 'D9EAF7'),
    tg_docx_value_cell($collegeName, 3000),
    tg_docx_cell(tg_docx_paragraph('Survey Status', true, 16, '44546A', '', 0, 0), 1500, 'D9EAF7'),
    tg_docx_value_cell($surveyStatus, 3000, true, tg_docx_status_color($surveyStatus))
]);
$body .= tg_docx_table($rows, [1800, 3000, 1500, 3000]);

/* I. Alumni profile */
$body .= tg_docx_section_title('I. ALUMNI PROFILE');
$rows = [];
$profileRows = [
    ['Student ID', (string)$alumni['student_id'], 'Batch Year', (string)$alumni['batch_year']],
    ['Full Name', $fullName, 'Sex', (string)$alumni['sex']],
    ['Program', trim((string)$alumni['course_code'] . ' - ' . (string)$alumni['course_name']), 'Email', $email],
    ['Mobile Number', $mobile, 'Latin Honor', $latinHonor]
];
foreach ($profileRows as $row) {
    $rows[] = tg_docx_row([
        tg_docx_label_cell($row[0], 1700),
        tg_docx_value_cell($row[1], 3100),
        tg_docx_label_cell($row[2], 1500),
        tg_docx_value_cell($row[3], 3000)
    ]);
}
$body .= tg_docx_table($rows, [1700, 3100, 1500, 3000]);

/* II. Response summary */
$body .= tg_docx_section_title('II. RESPONSE SUMMARY');
$rows = [];
$summaryRows = [
    ['Survey Status', $surveyStatus, 'Completion', $answerQuestionCount . ' / ' . max(1, $questionTotal) . ' (' . $completionPercent . '%)'],
    ['Employment', $employmentStatus, 'Workplace Photo', $workplaceStatus],
    ['Company ID', $companyIdStatus, 'Program / Batch', (string)$alumni['course_code'] . ' / ' . (string)$alumni['batch_year']]
];
foreach ($summaryRows as $index => $row) {
    $rows[] = tg_docx_row([
        tg_docx_label_cell($row[0], 1700),
        tg_docx_value_cell($row[1], 3100, $index === 0, $index === 0 ? tg_docx_status_color($row[1]) : '000000'),
        tg_docx_label_cell($row[2], 1500),
        tg_docx_value_cell($row[3], 3000, false, tg_docx_status_color($row[3]))
    ]);
}
$body .= tg_docx_table($rows, [1700, 3100, 1500, 3000]);

/* III. Survey responses - one organized table per category */
$body .= tg_docx_section_title('III. SUBMITTED SURVEY RESPONSES');

if (!$answers) {
    $body .= tg_docx_paragraph(
        'No saved survey-answer rows were found for this respondent.',
        false,
        16,
        '777777',
        '',
        0,
        60
    );
} else {
    $groupedAnswers = [];
    foreach ($answers as $answer) {
        $category = trim((string)($answer['category_name'] ?? ''));
        if ($category === '') {
            $category = 'General';
        }
        if (!isset($groupedAnswers[$category])) {
            $groupedAnswers[$category] = [];
        }
        $groupedAnswers[$category][] = $answer;
    }

    $answerNumber = 0;
    foreach ($groupedAnswers as $category => $categoryAnswers) {
        $rows = [];
        $rows[] = tg_docx_row([
            tg_docx_cell(
                tg_docx_paragraph(strtoupper($category), true, 16, '17365D', '', 0, 0),
                9300,
                'D9EAF7',
                3
            )
        ]);
        $rows[] = tg_docx_row([
            tg_docx_cell(tg_docx_paragraph('No.', true, 15, 'FFFFFF', 'center', 0, 0), 500, '4472C4'),
            tg_docx_cell(tg_docx_paragraph('Survey Question', true, 15, 'FFFFFF', '', 0, 0), 3900, '4472C4'),
            tg_docx_cell(tg_docx_paragraph('Submitted Response', true, 15, 'FFFFFF', '', 0, 0), 4900, '4472C4')
        ], true);

        foreach ($categoryAnswers as $answer) {
            $answerNumber++;

            $question = trim((string)($answer['question'] ?? ''));
            if ($question === '') {
                $question = 'Question #' . (int)($answer['question_id'] ?? $answerNumber);
            }

            $answerText = trim((string)($answer['option_text'] ?? ''));
            if ($answerText === '') {
                $answerText = trim((string)($answer['answer_text'] ?? ''));
            }
            if ($answerText === '') {
                $answerText = 'No response';
            }

            $rows[] = tg_docx_row([
                tg_docx_cell(tg_docx_paragraph((string)$answerNumber, false, 15, '333333', 'center', 0, 0), 500),
                tg_docx_cell(tg_docx_paragraph($question, true, 15, '333333', '', 0, 0), 3900),
                tg_docx_cell(tg_docx_paragraph($answerText, false, 15, '333333', '', 0, 0), 4900)
            ], false, false);
        }

        $body .= tg_docx_table($rows, [500, 3900, 4900]);
        $body .= tg_docx_paragraph('', false, 10, 'FFFFFF', '', 0, 20);
    }
}

/* IV. Employment */
if ($includeEmployment) {
    $body .= tg_docx_section_title('IV. EMPLOYMENT INFORMATION');

    if (!$employment) {
        $body .= tg_docx_paragraph(
            'No employment information is currently available for this alumni record.',
            false,
            16,
            '777777',
            '',
            0,
            60
        );
    } else {
        $employmentRows = [];
        $employmentRows[] = ['Employment Status', (string)($employment['employment_status'] ?? 'Unknown')];

        if (!empty($employment['company_name'])) {
            $employmentRows[] = ['Company', (string)$employment['company_name']];
        }
        if (!empty($employment['employer_name'])) {
            $employmentRows[] = ['Employer', (string)$employment['employer_name']];
        }
        if (!empty($employment['position_title'])) {
            $employmentRows[] = ['Position', (string)$employment['position_title']];
        }
        if (!empty($employment['industry'])) {
            $employmentRows[] = ['Industry', (string)$employment['industry']];
        } elseif (!empty($employment['employment_sector'])) {
            $employmentRows[] = ['Employment Sector', (string)$employment['employment_sector']];
        }
        if (!empty($employment['work_location'])) {
            $employmentRows[] = ['Work Location', (string)$employment['work_location']];
        }
        if (!empty($employment['job_related_to_course'])) {
            $employmentRows[] = ['Job-Course Alignment', (string)$employment['job_related_to_course']];
        }
        if (
            isset($employment['monthly_salary']) &&
            $employment['monthly_salary'] !== null &&
            $employment['monthly_salary'] !== ''
        ) {
            $employmentRows[] = [
                'Monthly Salary',
                'PHP ' . number_format((float)$employment['monthly_salary'], 2)
            ];
        }
        if (!empty($employment['company_address'])) {
            $employmentRows[] = ['Company Address', (string)$employment['company_address']];
        }

        $rows = [];
        foreach ($employmentRows as $employmentRow) {
            $rows[] = tg_docx_row([
                tg_docx_label_cell($employmentRow[0], 2200),
                tg_docx_value_cell($employmentRow[1], 7100)
            ]);
        }
        $body .= tg_docx_table($rows, [2200, 7100]);
    }
}

$body .= tg_docx_paragraph(
    'System-generated by TRACEGRAD. This report contains personal and alumni information intended for authorized institutional use.',
    false,
    14,
    '777777',
    'center',
    100,
    0
);

/* ============================================================
   COPY TEMPLATE AND REPLACE ONLY THE DOCUMENT BODY
   Header/footer XML and all institutional graphics remain intact.
============================================================ */
$tmpBase = tempnam(sys_get_temp_dir(), 'tracegrad_survey_');
if ($tmpBase === false) {
    http_response_code(500);
    exit('Unable to create temporary Word report.');
}

$tmpDocx = $tmpBase . '.docx';
@unlink($tmpBase);

if (!copy($templatePath, $tmpDocx)) {
    http_response_code(500);
    exit('Unable to copy the official Word report template.');
}

$zip = new ZipArchive();
$openResult = $zip->open($tmpDocx);
if ($openResult !== true) {
    @unlink($tmpDocx);
    http_response_code(500);
    exit('Unable to open the official Word report template.');
}

$documentXml = $zip->getFromName('word/document.xml');
if ($documentXml === false) {
    $zip->close();
    @unlink($tmpDocx);
    http_response_code(500);
    exit('The Word template is missing word/document.xml.');
}

if (!preg_match('/<w:sectPr>.*?<\/w:sectPr>/s', $documentXml, $sectionMatch)) {
    $zip->close();
    @unlink($tmpDocx);
    http_response_code(500);
    exit('The Word template does not contain a valid section definition.');
}

$sectionProperties = $sectionMatch[0];

/*
 * Keep the uploaded header/footer exactly as supplied, but give the report body
 * enough safe space below/above them and slightly wider table margins.
 */
$sectionProperties = preg_replace(
    '/<w:pgMar[^>]*\/>/',
    '<w:pgMar w:top="1950" w:right="1000" w:bottom="1450" w:left="1000" w:header="720" w:footer="720" w:gutter="0"/>',
    $sectionProperties
);

$newBodyXml = '<w:body>' . $body . $sectionProperties . '</w:body>';
$updatedDocumentXml = preg_replace_callback(
    '/<w:body>.*?<\/w:body>/s',
    function () use ($newBodyXml) {
        return $newBodyXml;
    },
    $documentXml,
    1
);

if ($updatedDocumentXml === null || $updatedDocumentXml === $documentXml) {
    $zip->close();
    @unlink($tmpDocx);
    http_response_code(500);
    exit('The survey report body could not be inserted into the Word template.');
}

if (!$zip->addFromString('word/document.xml', $updatedDocumentXml)) {
    $zip->close();
    @unlink($tmpDocx);
    http_response_code(500);
    exit('The Word report could not be written.');
}

$zip->close();

if (!is_file($tmpDocx) || filesize($tmpDocx) <= 0) {
    @unlink($tmpDocx);
    http_response_code(500);
    exit('The Word report could not be generated.');
}

/* ============================================================
   DOWNLOAD
============================================================ */
$filename = tg_docx_safe_filename(
    (string)$alumni['lastname'] . '_' .
    (string)$alumni['firstname'] . '_' .
    (string)$alumni['student_id'] . '_Tracer_Survey_Report'
) . '.docx';

while (ob_get_level() > 0) {
    @ob_end_clean();
}

@ini_set('zlib.output_compression', 'Off');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmpDocx));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($tmpDocx);
@unlink($tmpDocx);
exit;
