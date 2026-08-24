<?php
/**
 * TRACEGRAD - Report Export Router
 * ------------------------------------------------------------
 * Keeps existing report-export.php links working.
 *
 * format=html -> report-preview.php
 * format=pdf  -> report-preview.php (auto print)
 * format=docx -> report-docx.php
 * format=csv  -> CSV export
 *
 * PHP 7.2+
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$format = strtolower(trim((string)($_GET['format'] ?? 'html')));

if ($format === 'html' || $format === 'pdf') {
    require __DIR__ . '/report-preview.php';
    exit;
}

if ($format === 'docx') {
    require __DIR__ . '/report-docx.php';
    exit;
}

if ($format !== 'csv') {
    http_response_code(400);
    exit('Unsupported report format.');
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/dept-admin-dashboard/reports/report-data.php';


if (empty($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}
if ((int)($_SESSION['role_id'] ?? 0) !== 2) {
    header('Location: admin-dashboard.php');
    exit;
}
$collegeId = (int)($_SESSION['admin_college_id'] ?? 0);
if ($collegeId <= 0) {
    http_response_code(403);
    exit('Your Department Admin account is not assigned to a college.');
}


$request = tg_report_request($_GET);

try {
    $package = tg_report_build($pdo, $collegeId, $request);
} catch (Throwable $e) {
    http_response_code(400);
    exit($e->getMessage());
}

$filename = tg_report_filename($package, 'csv');

while (ob_get_level() > 0) {
    @ob_end_clean();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

/* UTF-8 BOM for Excel */
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

if ($request['template'] === 'individual_survey') {
    $alumni = $package['data']['alumni'];

    fputcsv($out, ['TRACEGRAD INDIVIDUAL SURVEY REPORT']);
    fputcsv($out, ['Department', $package['college']['college_name']]);
    fputcsv($out, ['Student ID', $alumni['student_id']]);
    fputcsv($out, ['Name', tg_report_full_name($alumni)]);
    fputcsv($out, ['Program', $alumni['course_code'] . ' - ' . $alumni['course_name']]);
    fputcsv($out, ['Batch Year', $alumni['batch_year']]);
    fputcsv($out, ['Survey Status', $alumni['survey_status']]);
    fputcsv($out, []);
    fputcsv($out, ['Category', 'Question', 'Answer']);

    foreach ($package['data']['answers'] as $answer) {
        fputcsv($out, [
            $answer['category_name'] ?? 'General',
            $answer['question'] ?? '',
            tg_report_answer_value($answer)
        ]);
    }

} elseif ($request['template'] === 'batch_survey_summary') {
    fputcsv($out, ['TRACEGRAD BATCH SURVEY SUMMARY']);
    fputcsv($out, ['Department', $package['college']['college_name']]);
    fputcsv($out, ['Batch Year', $package['data']['batch_year']]);
    fputcsv($out, []);
    fputcsv($out, [
        'Name',
        'Student ID',
        'Program',
        'Batch',
        'Survey Status',
        'Answers',
        'Question Total',
        'Completion %',
        'Employment Status'
    ]);

    foreach ($package['data']['rows'] as $row) {
        fputcsv($out, [
            $row['lastname'] . ', ' . $row['firstname'],
            $row['student_id'],
            $row['course_code'],
            $row['batch_year'],
            $row['survey_status'],
            $row['answers_count'],
            $package['question_total'],
            $row['completion_percent'],
            $row['employment_status']
        ]);
    }

} else {
    fputcsv($out, ['TRACEGRAD FULL ROSTER + SURVEY']);
    fputcsv($out, ['Department', $package['college']['college_name']]);
    fputcsv($out, [
        'Name',
        'Student ID',
        'Program',
        'Batch',
        'Survey Status',
        'Answers',
        'Question Total',
        'Completion %',
        'Employment Status',
        'Position',
        'Monthly Salary'
    ]);

    foreach ($package['data']['rows'] as $row) {
        fputcsv($out, [
            $row['lastname'] . ', ' . $row['firstname'],
            $row['student_id'],
            $row['course_code'],
            $row['batch_year'],
            $row['survey_status'],
            $row['answers_count'],
            $package['question_total'],
            $row['completion_percent'],
            $row['employment_status'],
            $row['position_title'] ?? '',
            $row['monthly_salary'] ?? ''
        ]);
    }
}

fclose($out);
exit;
