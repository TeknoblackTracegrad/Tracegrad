<?php
/**
 * TRACEGRAD - Individual Survey Preview / Print-PDF
 * ------------------------------------------------------------
 * Keeps the browser preview and print/PDF layout visually aligned
 * with the official DOCX report.
 *
 * PHP compatibility: PHP 7.2+
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

$collegeId = (int)($_SESSION['admin_college_id'] ?? 0);
$graduateId = isset($_GET['graduate_id']) ? (int)$_GET['graduate_id'] : 0;
$includeEmployment = isset($_GET['include_employment'])
    ? ((int)$_GET['include_employment'] === 1)
    : true;
$format = strtolower(trim((string)($_GET['format'] ?? 'html')));

if (!in_array($format, ['html', 'pdf'], true)) {
    $format = 'html';
}

if ($collegeId <= 0 || $graduateId <= 0) {
    http_response_code(400);
    exit('Invalid report request.');
}

/* ============================================================
   HELPERS
============================================================ */
function tg_preview_esc($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function tg_preview_table_exists($pdo, $table)
{
    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA=DATABASE()
               AND TABLE_NAME=?"
        );
        $stmt->execute([$table]);
        return ((int)$stmt->fetchColumn()) > 0;
    } catch (Throwable $e) {
        return false;
    }
}

function tg_preview_column_exists($pdo, $table, $column)
{
    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA=DATABASE()
               AND TABLE_NAME=?
               AND COLUMN_NAME=?"
        );
        $stmt->execute([$table, $column]);
        return ((int)$stmt->fetchColumn()) > 0;
    } catch (Throwable $e) {
        return false;
    }
}

function tg_preview_status_class($value)
{
    $value = strtolower(trim((string)$value));

    if (in_array($value, ['completed', 'employed', 'submitted', 'active', 'yes'], true)) {
        return 'status-good';
    }

    if (in_array($value, ['partial', 'pending', 'underemployed'], true)) {
        return 'status-warning';
    }

    if (in_array($value, ['not started', 'not submitted', 'unemployed', 'missing'], true)) {
        return 'status-danger';
    }

    return '';
}

function tg_preview_data_uri($mime, $bytes)
{
    if ($bytes === false || $bytes === null || $bytes === '') {
        return '';
    }

    return 'data:' . $mime . ';base64,' . base64_encode($bytes);
}

/* ============================================================
   OFFICIAL TEMPLATE ARTWORK
============================================================ */
$templatePath = __DIR__ . '/assets/templates/survey-report-template.docx';

if (!is_file($templatePath)) {
    http_response_code(500);
    exit(
        'Official report template not found. Expected: ' .
        'assets/templates/survey-report-template.docx'
    );
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    exit('The PHP ZIP extension is required to read the official report template.');
}

$templateZip = new ZipArchive();
$templateOpen = $templateZip->open($templatePath);

if ($templateOpen !== true) {
    http_response_code(500);
    exit('Unable to open the official Word report template.');
}

/*
 * These are the three institutional images contained in the supplied
 * template:
 *   image2.jpeg - ISUFST logo
 *   image1.png  - Bagong Pilipinas logo
 *   image3.jpeg - institutional accreditation / values footer
 */
$leftLogoUri = tg_preview_data_uri(
    'image/jpeg',
    $templateZip->getFromName('word/media/image2.jpeg')
);
$rightLogoUri = tg_preview_data_uri(
    'image/png',
    $templateZip->getFromName('word/media/image1.png')
);
$footerArtworkUri = tg_preview_data_uri(
    'image/jpeg',
    $templateZip->getFromName('word/media/image3.jpeg')
);

$templateZip->close();

if ($leftLogoUri === '' || $rightLogoUri === '' || $footerArtworkUri === '') {
    http_response_code(500);
    exit('The official Word template is missing one or more institutional images.');
}

/* ============================================================
   COLLEGE
============================================================ */
$stmt = $pdo->prepare(
    "SELECT college_name, college_code
     FROM colleges
     WHERE college_id=?
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
   ALUMNI - DEPARTMENT SCOPE
============================================================ */
$stmt = $pdo->prepare(
    "SELECT g.*, c.course_code, c.course_name
     FROM graduates g
     JOIN courses c ON c.course_id=g.course_id
     WHERE g.graduate_id=?
       AND c.college_id=?
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
     JOIN survey_questions q
       ON q.question_id=sa.question_id
     LEFT JOIN survey_question_options qo
       ON qo.option_id=sa.option_id
     LEFT JOIN survey_categories sc
       ON sc.category_id=q.category_id
     WHERE sa.graduate_id=?
     ORDER BY
       COALESCE(sc.display_order,0),
       COALESCE(q.display_order,q.question_id),
       sa.answer_id"
);
$stmt->execute([$graduateId]);
$answers = $stmt->fetchAll();

/* ============================================================
   SURVEY STATUS
============================================================ */
$questionTotal = 0;

try {
    if (tg_preview_column_exists($pdo, 'survey_questions', 'status')) {
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
     WHERE graduate_id=?"
);
$stmt->execute([$graduateId]);
$answerQuestionCount = (int)$stmt->fetchColumn();

$submissionStatus = '';

if (tg_preview_table_exists($pdo, 'survey_submissions')) {
    try {
        $stmt = $pdo->prepare(
            "SELECT status
             FROM survey_submissions
             WHERE graduate_id=?
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

if ($includeEmployment && tg_preview_table_exists($pdo, 'employment')) {
    try {
        if (tg_preview_table_exists($pdo, 'companies')) {
            $stmt = $pdo->prepare(
                "SELECT e.*, comp.company_name, comp.industry, comp.company_address
                 FROM employment e
                 LEFT JOIN companies comp
                   ON comp.company_id=e.company_id
                 WHERE e.graduate_id=?
                 ORDER BY e.employment_id DESC
                 LIMIT 1"
            );
        } else {
            $stmt = $pdo->prepare(
                "SELECT e.*
                 FROM employment e
                 WHERE e.graduate_id=?
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

/* Group answers exactly like the DOCX report. */
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

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TRACEGRAD - <?= tg_preview_esc($fullName) ?> Survey Report</title>

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

* {
    box-sizing:border-box;
}

html,
body {
    margin:0;
    padding:0;
    background:#e9edf2;
    color:var(--text);
    font-family:Arial, Helvetica, sans-serif;
    font-size:10pt;
    line-height:1.35;
}

.preview-toolbar {
    position:sticky;
    top:0;
    z-index:100;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:10px 18px;
    background:#17365d;
    color:#fff;
    box-shadow:0 4px 14px rgba(0,0,0,.12);
}

.preview-toolbar-copy strong,
.preview-toolbar-copy span {
    display:block;
}

.preview-toolbar-copy strong {
    font-size:13px;
}

.preview-toolbar-copy span {
    margin-top:2px;
    color:#dce7f5;
    font-size:10px;
}

.preview-toolbar-actions {
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.preview-toolbar button,
.preview-toolbar a {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:7px 12px;
    border:1px solid rgba(255,255,255,.22);
    border-radius:6px;
    background:rgba(255,255,255,.1);
    color:#fff;
    font:700 11px Arial, sans-serif;
    text-decoration:none;
    cursor:pointer;
}

.preview-toolbar button:hover,
.preview-toolbar a:hover {
    background:rgba(255,255,255,.18);
}

.report-page {
    position:relative;
    width:210mm;
    min-height:297mm;
    margin:20px auto;
    padding:38mm 17mm 27mm;
    background:#fff;
    box-shadow:0 8px 35px rgba(15,23,42,.14);
}

.official-header {
    position:absolute;
    left:17mm;
    right:17mm;
    top:7mm;
    height:28mm;
    display:grid;
    grid-template-columns:31mm 1fr 29mm;
    align-items:center;
    gap:3mm;
    padding-bottom:2.5mm;
    border-bottom:1.2px solid #2d77aa;
}

.official-header-logo-left {
    width:29mm;
    height:20mm;
    object-fit:contain;
    justify-self:start;
}

.official-header-logo-right {
    width:25mm;
    height:19mm;
    object-fit:contain;
    justify-self:end;
}

.official-header-copy {
    text-align:center;
    line-height:1.22;
}

.official-header-copy .republic {
    font-size:8.5pt;
}

.official-header-copy .university {
    margin-top:1mm;
    color:#176ba0;
    font-size:9.5pt;
    font-weight:700;
    white-space:nowrap;
}

.official-header-copy .office {
    margin-top:.5mm;
    color:#111;
    font-size:10.5pt;
    font-weight:700;
}

.official-header-copy .contact,
.official-header-copy .website {
    margin-top:.5mm;
    color:#222;
    font-size:7.8pt;
}

.official-footer {
    position:absolute;
    left:11mm;
    right:11mm;
    bottom:6mm;
    height:19mm;
    display:flex;
    align-items:flex-end;
    justify-content:center;
}

.official-footer img {
    width:100%;
    max-height:18mm;
    object-fit:contain;
}

.report-title {
    margin:0;
    color:var(--navy);
    text-align:center;
    font-size:14pt;
    font-weight:700;
    letter-spacing:.01em;
}

.report-subtitle {
    margin:1mm 0 5mm;
    color:#666;
    text-align:center;
    font-size:10pt;
}

.report-section {
    margin-top:5mm;
    break-inside:auto;
}

.report-section-title {
    margin:0 0 2mm;
    padding:0 0 1.5mm;
    border-bottom:2px solid #2d77aa;
    color:var(--navy);
    font-size:10.5pt;
    font-weight:700;
}

.report-table {
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    font-size:8.7pt;
}

.report-table th,
.report-table td {
    padding:2.2mm 2.5mm;
    border:1px solid var(--border);
    vertical-align:top;
    overflow-wrap:anywhere;
    word-break:normal;
}

.report-table th {
    width:20%;
    background:#f1f5f9;
    color:var(--label);
    text-align:left;
    font-weight:700;
}

.report-table .section-cell {
    padding:2mm 2.5mm;
    background:var(--light-blue);
    color:var(--navy);
    font-weight:700;
    text-transform:uppercase;
}

.report-table .column-head {
    background:var(--blue);
    color:#fff;
    text-align:left;
    font-weight:700;
}

.report-table .column-head.center,
.report-table td.center {
    text-align:center;
}

.report-table tr {
    break-inside:avoid;
    page-break-inside:avoid;
}

.report-info-table col.label-a { width:19%; }
.report-info-table col.value-a { width:31%; }
.report-info-table col.label-b { width:18%; }
.report-info-table col.value-b { width:32%; }

.answers-table col.no { width:7%; }
.answers-table col.question { width:42%; }
.answers-table col.response { width:51%; }

.answer-category {
    margin-top:3mm;
}

.status-good {
    color:var(--good);
    font-weight:700;
}

.status-warning {
    color:var(--warning);
    font-weight:700;
}

.status-danger {
    color:var(--danger);
    font-weight:700;
}

.document-control {
    margin:5mm 0 0;
    color:#777;
    text-align:center;
    font-size:7.5pt;
}

.no-data {
    padding:4mm;
    border:1px solid var(--soft-border);
    background:#fafbfc;
    color:#777;
    text-align:center;
    font-size:8.5pt;
}

@media print {
    @page {
        size:A4 portrait;
        margin:38mm 17mm 27mm 17mm;
    }

    html,
    body {
        background:#fff;
    }

    body {
        margin:0;
        padding:0;
    }

    .preview-toolbar {
        display:none !important;
    }

    .report-page {
        width:auto;
        min-height:0;
        margin:0;
        padding:0;
        box-shadow:none;
    }

    /*
     * Fixed header/footer repeat on every printed page in modern browsers,
     * visually matching the institutional Word template.
     */
    .official-header {
        position:fixed;
        left:0;
        right:0;
        top:-31mm;
        height:28mm;
    }

    .official-footer {
        position:fixed;
        left:-6mm;
        right:-6mm;
        bottom:-22mm;
        height:19mm;
    }

    .report-title {
        margin-top:0;
    }

    .report-section-title,
    .answer-category {
        break-after:avoid;
        page-break-after:avoid;
    }

    thead {
        display:table-header-group;
    }

    tfoot {
        display:table-footer-group;
    }

    a {
        color:inherit;
        text-decoration:none;
    }
}

@media screen and (max-width:900px) {
    body {
        background:#fff;
    }

    .preview-toolbar {
        position:relative;
        align-items:flex-start;
        flex-direction:column;
    }

    .report-page {
        width:100%;
        min-height:0;
        margin:0;
        padding:34mm 10px 28mm;
        box-shadow:none;
    }

    .official-header {
        left:10px;
        right:10px;
        grid-template-columns:70px 1fr 65px;
        gap:4px;
    }

    .official-header-logo-left {
        width:68px;
    }

    .official-header-logo-right {
        width:58px;
    }

    .official-header-copy .university {
        font-size:7.5pt;
        white-space:normal;
    }

    .official-header-copy .office {
        font-size:8pt;
    }

    .official-header-copy .contact,
    .official-header-copy .website,
    .official-header-copy .republic {
        font-size:6.5pt;
    }

    .official-footer {
        left:8px;
        right:8px;
    }
}
</style>
</head>

<body>

<div class="preview-toolbar">
    <div class="preview-toolbar-copy">
        <strong>TRACEGRAD Official Report Preview</strong>
        <span>Browser preview and print/PDF use the same document layout as the official DOCX report.</span>
    </div>

    <div class="preview-toolbar-actions">
        <a href="survey-report-docx.php?graduate_id=<?= (int)$graduateId ?>&amp;include_employment=<?= $includeEmployment ? '1' : '0' ?>">
            Download DOCX
        </a>
        <button type="button" onclick="window.print()">Print / Save PDF</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>
</div>

<main class="report-page">

    <header class="official-header">
        <img
            class="official-header-logo-left"
            src="<?= tg_preview_esc($leftLogoUri) ?>"
            alt="ISUFST"
        >

        <div class="official-header-copy">
            <div class="republic">Republic of the Philippines</div>
            <div class="university">ILOILO STATE UNIVERSITY OF FISHERIES SCIENCE AND TECHNOLOGY</div>
            <div class="office">ADMISSION AND STUDENT RECORDS OFFICE</div>
            <div class="contact">San Enrique, Iloilo | Email: sanenriquecampus@gmail.com</div>
            <div class="website">Website: www.isufst.edu.ph | Contact No: (033) 327-3405</div>
        </div>

        <img
            class="official-header-logo-right"
            src="<?= tg_preview_esc($rightLogoUri) ?>"
            alt="Bagong Pilipinas"
        >
    </header>

    <footer class="official-footer">
        <img
            src="<?= tg_preview_esc($footerArtworkUri) ?>"
            alt="ISUFST institutional recognitions and core values"
        >
    </footer>

    <h1 class="report-title">GRADUATE TRACER SURVEY REPORT</h1>
    <div class="report-subtitle">Individual Alumni Response</div>

    <!-- Report information - same structure as DOCX -->
    <table class="report-table report-info-table">
        <colgroup>
            <col class="label-a">
            <col class="value-a">
            <col class="label-b">
            <col class="value-b">
        </colgroup>
        <tbody>
            <tr>
                <th>Report Type</th>
                <td>Individual Survey Response</td>
                <th>Generated</th>
                <td><?= tg_preview_esc($generatedAt) ?></td>
            </tr>
            <tr>
                <th>Department</th>
                <td><?= tg_preview_esc($collegeName) ?></td>
                <th>Survey Status</th>
                <td class="<?= tg_preview_esc(tg_preview_status_class($surveyStatus)) ?>"><?= tg_preview_esc($surveyStatus) ?></td>
            </tr>
        </tbody>
    </table>

    <section class="report-section">
        <h2 class="report-section-title">I. ALUMNI PROFILE</h2>

        <table class="report-table report-info-table">
            <colgroup>
                <col class="label-a">
                <col class="value-a">
                <col class="label-b">
                <col class="value-b">
            </colgroup>
            <tbody>
                <tr>
                    <th>Student ID</th>
                    <td><?= tg_preview_esc($alumni['student_id']) ?></td>
                    <th>Batch Year</th>
                    <td><?= tg_preview_esc($alumni['batch_year']) ?></td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td><?= tg_preview_esc($fullName) ?></td>
                    <th>Sex</th>
                    <td><?= tg_preview_esc($alumni['sex']) ?></td>
                </tr>
                <tr>
                    <th>Program</th>
                    <td><?= tg_preview_esc(trim((string)$alumni['course_code'] . ' - ' . (string)$alumni['course_name'])) ?></td>
                    <th>Email</th>
                    <td><?= tg_preview_esc($email) ?></td>
                </tr>
                <tr>
                    <th>Mobile Number</th>
                    <td><?= tg_preview_esc($mobile) ?></td>
                    <th>Latin Honor</th>
                    <td><?= tg_preview_esc($latinHonor) ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="report-section">
        <h2 class="report-section-title">II. RESPONSE SUMMARY</h2>

        <table class="report-table report-info-table">
            <colgroup>
                <col class="label-a">
                <col class="value-a">
                <col class="label-b">
                <col class="value-b">
            </colgroup>
            <tbody>
                <tr>
                    <th>Survey Status</th>
                    <td class="<?= tg_preview_esc(tg_preview_status_class($surveyStatus)) ?>"><?= tg_preview_esc($surveyStatus) ?></td>
                    <th>Completion</th>
                    <td><?= (int)$answerQuestionCount ?> / <?= max(1, (int)$questionTotal) ?> (<?= (int)$completionPercent ?>%)</td>
                </tr>
                <tr>
                    <th>Employment</th>
                    <td class="<?= tg_preview_esc(tg_preview_status_class($employmentStatus)) ?>"><?= tg_preview_esc($employmentStatus) ?></td>
                    <th>Workplace Photo</th>
                    <td class="<?= tg_preview_esc(tg_preview_status_class($workplaceStatus)) ?>"><?= tg_preview_esc($workplaceStatus) ?></td>
                </tr>
                <tr>
                    <th>Company ID</th>
                    <td class="<?= tg_preview_esc(tg_preview_status_class($companyIdStatus)) ?>"><?= tg_preview_esc($companyIdStatus) ?></td>
                    <th>Program / Batch</th>
                    <td><?= tg_preview_esc((string)$alumni['course_code'] . ' / ' . (string)$alumni['batch_year']) ?></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="report-section">
        <h2 class="report-section-title">III. SUBMITTED SURVEY RESPONSES</h2>

        <?php if (!$answers): ?>
            <div class="no-data">No saved survey-answer rows were found for this respondent.</div>
        <?php else: ?>
            <?php $answerNumber = 0; ?>
            <?php foreach ($groupedAnswers as $category => $categoryAnswers): ?>
                <table class="report-table answers-table answer-category">
                    <colgroup>
                        <col class="no">
                        <col class="question">
                        <col class="response">
                    </colgroup>
                    <thead>
                        <tr>
                            <th colspan="3" class="section-cell"><?= tg_preview_esc(strtoupper($category)) ?></th>
                        </tr>
                        <tr>
                            <th class="column-head center">No.</th>
                            <th class="column-head">Survey Question</th>
                            <th class="column-head">Submitted Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categoryAnswers as $answer): ?>
                            <?php
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
                            ?>
                            <tr>
                                <td class="center"><?= (int)$answerNumber ?></td>
                                <td><strong><?= tg_preview_esc($question) ?></strong></td>
                                <td><?= nl2br(tg_preview_esc($answerText)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <?php if ($includeEmployment): ?>
        <section class="report-section">
            <h2 class="report-section-title">IV. EMPLOYMENT INFORMATION</h2>

            <?php if (!$employment): ?>
                <div class="no-data">No employment information is currently available for this alumni record.</div>
            <?php else: ?>
                <table class="report-table">
                    <colgroup>
                        <col style="width:28%">
                        <col style="width:72%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Employment Status</th>
                            <td class="<?= tg_preview_esc(tg_preview_status_class($employment['employment_status'] ?? 'Unknown')) ?>">
                                <?= tg_preview_esc($employment['employment_status'] ?? 'Unknown') ?>
                            </td>
                        </tr>

                        <?php if (!empty($employment['company_name'])): ?>
                            <tr><th>Company</th><td><?= tg_preview_esc($employment['company_name']) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($employment['employer_name'])): ?>
                            <tr><th>Employer</th><td><?= tg_preview_esc($employment['employer_name']) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($employment['position_title'])): ?>
                            <tr><th>Position</th><td><?= tg_preview_esc($employment['position_title']) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($employment['industry'])): ?>
                            <tr><th>Industry</th><td><?= tg_preview_esc($employment['industry']) ?></td></tr>
                        <?php elseif (!empty($employment['employment_sector'])): ?>
                            <tr><th>Employment Sector</th><td><?= tg_preview_esc($employment['employment_sector']) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($employment['work_location'])): ?>
                            <tr><th>Work Location</th><td><?= tg_preview_esc($employment['work_location']) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($employment['job_related_to_course'])): ?>
                            <tr><th>Job-Course Alignment</th><td><?= tg_preview_esc($employment['job_related_to_course']) ?></td></tr>
                        <?php endif; ?>

                        <?php if (isset($employment['monthly_salary']) && $employment['monthly_salary'] !== null && $employment['monthly_salary'] !== ''): ?>
                            <tr><th>Monthly Salary</th><td>PHP <?= number_format((float)$employment['monthly_salary'], 2) ?></td></tr>
                        <?php endif; ?>

                        <?php if (!empty($employment['company_address'])): ?>
                            <tr><th>Company Address</th><td><?= tg_preview_esc($employment['company_address']) ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <p class="document-control">
        System-generated by TRACEGRAD. This report contains personal and alumni information intended for authorized institutional use.
    </p>
</main>

<?php if ($format === 'pdf'): ?>
<script>
window.addEventListener('load', function () {
    setTimeout(function () {
        window.print();
    }, 350);
});
</script>
<?php endif; ?>

</body>
</html>