<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();

$filename = 'german-university-research-' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');

$out = fopen('php://output', 'w');
// UTF-8 BOM so Excel opens accented characters (ö, ü, ß) correctly.
fwrite($out, "\xEF\xBB\xBF");

$headers = [
    'University', 'City', 'State', 'University Type', 'University Status',
    'Program', 'Degree', 'Subject', 'Intake', 'Study Mode', 'ECTS',
    'Teaching Language', 'English Taught', 'IELTS Required', 'IELTS Min',
    'TOEFL Required', 'TOEFL Min', 'MOI Accepted',
    'Tuition Fee', 'Semester Contribution', 'Application Fee', 'Uni-Assist Fee',
    'Application Method', 'Application Deadline', 'Winter Deadline', 'Summer Deadline',
    'Eligibility', 'Priority', 'Application Status', 'Last Checked',
];
fputcsv($out, $headers);

foreach (get_universities($userId) as $uni) {
    $programs = get_programs_for_university($userId, (int)$uni['id']);
    if (empty($programs)) {
        fputcsv($out, [
            $uni['name'], $uni['city'], $uni['state'], $uni['type'], $uni['status'],
            '', '', '', '', '', '',
            '', '', '', '',
            '', '', '',
            $uni['tuition_fee'], $uni['semester_contribution'], $uni['application_fee'], '',
            $uni['application_method'], '', '', '',
            '', '', '', '',
        ]);
        continue;
    }
    foreach ($programs as $p) {
        fputcsv($out, [
            $uni['name'], $uni['city'], $uni['state'], $uni['type'], $uni['status'],
            $p['name'], $p['degree'], $p['subject'], $p['intake'], $p['studyMode'], $p['ects'],
            $p['language']['teachingLanguage'], $p['language']['englishTaught'],
            $p['language']['ieltsRequired'], $p['language']['ieltsMin'],
            $p['language']['toeflRequired'], $p['language']['toeflMin'], $p['language']['moiAccepted'],
            $p['fees']['tuitionFee'], $p['fees']['semesterContribution'], $p['fees']['applicationFee'], $p['fees']['uniAssistFee'],
            $p['application']['method'], $p['application']['deadline'], $p['application']['winterDeadline'], $p['application']['summerDeadline'],
            $p['personal']['eligibility'], $p['personal']['priority'], $p['personal']['applicationStatus'], $p['personal']['lastChecked'],
        ]);
    }
}

fclose($out);
exit;
