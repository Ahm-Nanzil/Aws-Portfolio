<?php
require __DIR__ . '/../includes/auth.php';
require_login();
$userId = effective_user_id();
$base = base_path();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($base . '/programs.php');
}
csrf_check();

$uniId = (int)($_POST['university_id'] ?? 0);
$progId = (int)($_POST['id'] ?? 0);
$section = trim($_POST['section'] ?? 'overview');

$uni = get_university($userId, $uniId);
if ($uni === null) {
    flash('danger', 'University not found.');
    redirect($base . '/universities.php');
}

$defaultRedirect = $base . '/university.php?id=' . $uniId;
$isNew = ($progId === 0);

if (!$isNew) {
    $existing = get_program($userId, $progId);
    if ($existing === null) {
        flash('danger', 'Program not found.');
        redirect($defaultRedirect);
    }
}

function txt($key) { return trim($_POST[$key] ?? ''); }

switch ($section) {
    case 'overview':
        $overview = [
            'name' => txt('name'),
            'degree' => txt('degree'),
            'subject' => txt('subject'),
            'department' => txt('department'),
            'faculty' => txt('faculty'),
            'website' => txt('website'),
            'description' => txt('description'),
            'studyLocation' => txt('studyLocation'),
            'duration' => txt('duration'),
            'ects' => txt('ects'),
            'studyMode' => txt('studyMode') ?: 'Full-time',
            'intake' => txt('intake') ?: 'Winter',
        ];
        if ($overview['name'] === '') {
            flash('danger', 'Program name is required.');
            redirect($isNew ? $defaultRedirect : ($base . '/program.php?id=' . $progId));
        }
        if (!is_valid_url($overview['website'])) {
            flash('danger', 'Please enter a valid program website URL.');
            redirect($isNew ? $defaultRedirect : ($base . '/program.php?id=' . $progId));
        }
        if ($isNew) {
            $newId = create_program($userId, $uniId, $overview);
            flash('success', 'Program "' . $overview['name'] . '" added.');
            redirect($base . '/program.php?id=' . $newId);
        }
        update_program_overview($userId, $progId, $overview);
        flash('success', 'Program updated (Overview).');
        redirect($base . '/program.php?id=' . $progId . '&tab=overview');
        break;

    case 'language':
        $data = [
            'teachingLanguage' => txt('teachingLanguage'),
            'englishTaught' => txt('englishTaught') ?: 'Unknown',
            'englishPercentage' => txt('englishPercentage'),
            'germanRequirement' => txt('germanRequirement'),
            'englishRequirement' => txt('englishRequirement'),
            'ieltsRequired' => txt('ieltsRequired') ?: 'Unknown',
            'ieltsMin' => txt('ieltsMin'),
            'toeflRequired' => txt('toeflRequired') ?: 'Unknown',
            'toeflMin' => txt('toeflMin'),
            'otherTests' => txt('otherTests'),
            'moiAccepted' => txt('moiAccepted') ?: 'Unknown',
            'moiDetails' => txt('moiDetails'),
        ];
        update_program_section($userId, $progId, 'language', $data);
        flash('success', 'Program updated (Language).');
        redirect($base . '/program.php?id=' . $progId . '&tab=language');
        break;

    case 'fees':
        $data = [
            'tuitionFee' => txt('tuitionFee'),
            'semesterContribution' => txt('semesterContribution'),
            'applicationFee' => txt('applicationFee'),
            'uniAssistFee' => txt('uniAssistFee'),
            'otherFees' => txt('otherFees'),
            'feeNotes' => txt('feeNotes'),
        ];
        update_program_section($userId, $progId, 'fees', $data);
        flash('success', 'Program updated (Fees).');
        redirect($base . '/program.php?id=' . $progId . '&tab=fees');
        break;

    case 'application':
        $url = txt('url');
        $portal = txt('portal');
        if (!is_valid_url($url) || !is_valid_url($portal)) {
            flash('danger', 'Please enter valid URLs for the application section.');
            redirect($base . '/program.php?id=' . $progId . '&tab=application');
        }
        $data = [
            'method' => txt('method') ?: 'Direct',
            'portal' => $portal,
            'url' => $url,
            'startDate' => txt('startDate'),
            'deadline' => txt('deadline'),
            'winterDeadline' => txt('winterDeadline'),
            'summerDeadline' => txt('summerDeadline'),
            'intlDeadline' => txt('intlDeadline'),
            'otherInfo' => txt('otherInfo'),
        ];
        update_program_section($userId, $progId, 'application', $data);
        flash('success', 'Program updated (Deadlines).');
        redirect($base . '/program.php?id=' . $progId . '&tab=application');
        break;

    case 'admission':
        $data = [
            'requiredDegree' => txt('requiredDegree'),
            'requiredMajor' => txt('requiredMajor'),
            'minGpa' => txt('minGpa'),
            'requiredEcts' => txt('requiredEcts'),
            'csEcts' => txt('csEcts'),
            'mathEcts' => txt('mathEcts'),
            'programmingEcts' => txt('programmingEcts'),
            'otherEcts' => txt('otherEcts'),
            'workExperience' => txt('workExperience') ?: 'Unknown',
            'greRequired' => txt('greRequired') ?: 'Unknown',
            'entranceExam' => txt('entranceExam') ?: 'Unknown',
            'interview' => txt('interview') ?: 'Unknown',
            'otherAcademic' => txt('otherAcademic'),
            'additionalReq' => txt('additionalReq'),
        ];
        update_program_section($userId, $progId, 'admission', $data);
        flash('success', 'Program updated (Admission).');
        redirect($base . '/program.php?id=' . $progId . '&tab=admission');
        break;

    case 'personal':
        $data = [
            'eligibility' => txt('eligibility') ?: 'Unknown',
            'priority' => txt('priority') ?: 'Medium',
            'applicationStatus' => txt('applicationStatus') ?: 'Not Started',
            'notes' => txt('notes'),
            'questions' => txt('questions'),
            'lastChecked' => txt('lastChecked'),
        ];
        update_program_section($userId, $progId, 'personal', $data);
        flash('success', 'Program updated (Notes).');
        redirect($base . '/program.php?id=' . $progId . '&tab=personal');
        break;

    default:
        flash('danger', 'Unknown form section.');
        redirect($defaultRedirect);
}
