     <!-- ══════════════ SURVEY RESPONSES ══════════════ -->
      <?php if ($tab === 'surveys'): ?>
      <section class="dept-page-hero survey-hero">
        <div>
          <span class="dept-eyebrow"><i class="ti ti-clipboard-check"></i> Respondent Review</span>
          <h2>Survey Responses</h2>
          <p>Review submitted tracer-survey answers from <?= esc($collegeCode) ?> alumni. This list shows respondents only.</p>
        </div>
        <div class="response-total-badge"><strong><?= number_format($surveyPagination['total']) ?></strong><span>respondents</span></div>
      </section>

      <div class="dash-panel">
        <div class="survey-toolbar-head">
          <div><h3><i class="ti ti-list-details"></i> Alumni Responses</h3><p class="dash-sub">Showing <?= number_format($surveyPagination['from']) ?>–<?= number_format($surveyPagination['to']) ?> of <?= number_format($surveyPagination['total']) ?> respondents.</p></div>
        </div>
        <form method="get" class="dash-filterbar survey-filterbar-v2">
          <input type="hidden" name="tab" value="surveys">
          <div class="dash-field filter-search"><i class="ti ti-search"></i><input type="search" name="survey_q" placeholder="Search respondent..." value="<?= esc($surveyFilters['q']) ?>"></div>
          <select name="survey_batch"><option value="">All Batches</option><?php foreach ($batchYears as $by): ?><option value="<?= esc($by['batch_year']) ?>" <?= (string)$surveyFilters['batch_year']===(string)$by['batch_year']?'selected':'' ?>>Batch <?= esc($by['batch_year']) ?></option><?php endforeach; ?></select>
          <select name="survey_course"><option value="">All Programs</option><?php foreach ($courses as $c): ?><option value="<?= (int)$c['course_id'] ?>" <?= (string)$surveyFilters['course_id']===(string)$c['course_id']?'selected':'' ?>><?= esc($c['course_code']) ?></option><?php endforeach; ?></select>
          <select name="survey_status"><option value="">Completed &amp; Partial</option><option value="Completed" <?= $surveyFilters['status']==='Completed'?'selected':'' ?>>Completed</option><option value="Partial" <?= $surveyFilters['status']==='Partial'?'selected':'' ?>>Partial</option></select>
          <select name="survey_per_page"><option value="25" <?= $surveyPagination['per_page']==25?'selected':'' ?>>25 rows</option><option value="50" <?= $surveyPagination['per_page']==50?'selected':'' ?>>50 rows</option><option value="100" <?= $surveyPagination['per_page']==100?'selected':'' ?>>100 rows</option></select>
          <button class="btn-dash-primary" type="submit"><i class="ti ti-adjustments"></i> Apply</button>
          <a href="?tab=surveys" class="btn-dash-secondary"><i class="ti ti-refresh"></i> Reset</a>
        </form>

        <div class="dash-table-wrapper">
          <table class="dash-table response-table-v2">
            <thead><tr><th>Respondent</th><th>Program / Batch</th><th>Progress</th><th>Employment</th><th>Supporting Files</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($surveyResponses as $sr): $status=surveyStatusWithSubmission($sr['submission_status'] ?? null,$sr['answers_count'],$surveyQuestionTotal); $pct=min(100,(int)round(((int)$sr['answers_count']/max(1,$surveyQuestionTotal))*100)); ?>
              <tr>
                <td><div class="alumni-cell"><div class="alumni-mini-avatar"><?= esc(strtoupper(substr($sr['firstname'],0,1).substr($sr['lastname'],0,1))) ?></div><div><strong><?= esc($sr['lastname'].', '.$sr['firstname']) ?></strong><span><?= esc($sr['student_id']) ?></span></div></div></td>
                <td><strong><?= esc($sr['course_code']) ?></strong><div class="dash-sub">Batch <?= esc($sr['batch_year']) ?></div></td>
                <td><div class="response-progress"><div><span><?= statusBadge($status) ?></span><strong><?= (int)$sr['answers_count'] ?>/<?= $surveyQuestionTotal ?></strong></div><div class="survey-progress-track"><span style="width:<?= $pct ?>%"></span></div></div></td>
                <td><?= statusBadge($sr['employment_status'] ?? 'Unknown') ?></td>
                <td><div class="support-file-badges"><span class="<?= $sr['workplace_photo']?'has-file':'missing-file' ?>"><i class="ti <?= $sr['workplace_photo']?'ti-photo-check':'ti-photo-off' ?>"></i> Workplace</span><span class="<?= $sr['company_id_proof']?'has-file':'missing-file' ?>"><i class="ti <?= $sr['company_id_proof']?'ti-id-badge-2':'ti-id-off' ?>"></i> Company ID</span></div></td>
                <td><button class="btn-view-response" type="button" onclick="openModal('view-survey-<?= (int)$sr['graduate_id'] ?>')"><i class="ti ti-eye"></i> View Response</button></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$surveyResponses): ?><tr><td colspan="6" class="dash-empty"><i class="ti ti-clipboard-off"></i><strong>No respondents found</strong><span>No submitted survey responses match these filters.</span></td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($surveyPagination['pages'] > 1): ?>
        <nav class="dept-pagination" aria-label="Survey response pages">
          <a class="page-link <?= $surveyPagination['page']<=1?'disabled':'' ?>" href="<?= $surveyPagination['page']>1 ? esc(tabUrl('surveys',['survey_page'=>$surveyPagination['page']-1,'focus_id'=>null])) : '#' ?>"><i class="ti ti-chevron-left"></i> Previous</a>
          <div class="page-numbers"><?php $spStart=max(1,$surveyPagination['page']-2); $spEnd=min($surveyPagination['pages'],$surveyPagination['page']+2); for($sp=$spStart;$sp<=$spEnd;$sp++): ?><a href="<?= esc(tabUrl('surveys',['survey_page'=>$sp,'focus_id'=>null])) ?>" class="page-number <?= $sp===$surveyPagination['page']?'active':'' ?>"><?= $sp ?></a><?php endfor; ?></div>
          <a class="page-link <?= $surveyPagination['page']>=$surveyPagination['pages']?'disabled':'' ?>" href="<?= $surveyPagination['page']<$surveyPagination['pages'] ? esc(tabUrl('surveys',['survey_page'=>$surveyPagination['page']+1,'focus_id'=>null])) : '#' ?>">Next <i class="ti ti-chevron-right"></i></a>
        </nav>
        <?php endif; ?>
      </div>

      <?php foreach ($surveyResponses as $sr): $answers=$surveyResponseDetails[(int)$sr['graduate_id']] ?? []; $status=surveyStatusWithSubmission($sr['submission_status'] ?? null,$sr['answers_count'],$surveyQuestionTotal); ?>
      <div id="view-survey-<?= (int)$sr['graduate_id'] ?>" style="display:none;">
        <div class="survey-response-modal">
          <header class="survey-response-header">
            <div class="survey-response-avatar"><?= esc(strtoupper(substr($sr['firstname'],0,1).substr($sr['lastname'],0,1))) ?></div>
            <div><span class="dept-eyebrow">Tracer Survey Response</span><h2><?= esc($sr['firstname'].' '.$sr['lastname']) ?></h2><p><?= esc($sr['student_id']) ?> · <?= esc($sr['course_code']) ?> · Batch <?= esc($sr['batch_year']) ?></p></div>
            <div class="survey-response-status"><?= statusBadge($status) ?><strong><?= (int)$sr['answers_count'] ?>/<?= $surveyQuestionTotal ?></strong><span>answers saved</span></div>
          </header>
          <div class="response-detail-grid">
            <div><span>Program</span><strong><?= esc($sr['course_name']) ?></strong></div><div><span>Employment</span><strong><?= esc($sr['employment_status'] ?? 'Unknown') ?></strong></div><div><span>Workplace Photo</span><strong><?= $sr['workplace_photo']?'Submitted':'Not submitted' ?></strong></div><div><span>Company ID</span><strong><?= $sr['company_id_proof']?'Submitted':'Not submitted' ?></strong></div>
          </div>
          <div class="survey-answer-list">
            <div class="survey-answer-list-head"><h3><i class="ti ti-forms"></i> Submitted Answers</h3><span><?= count($answers) ?> saved response rows</span></div>
            <?php if ($answers): $answerIndex=0; foreach ($answers as $answerRow): $answerIndex++; list($questionLabel,$answerValue)=surveyAnswerPair($answerRow,$surveyQuestionMap,$answerIndex); ?>
              <div class="survey-answer-row"><div class="survey-question"><span><?= str_pad((string)$answerIndex,2,'0',STR_PAD_LEFT) ?></span><strong><?= esc($questionLabel) ?></strong></div><div class="survey-answer-value"><?= nl2br(esc($answerValue)) ?></div></div>
            <?php endforeach; else: ?>
              <div class="dash-empty">No saved answer rows were found for this respondent.</div>
            <?php endif; ?>
          </div>
          <div class="survey-modal-foot"><a class="btn-dash-secondary" href="survey-report-docx.php?graduate_id=<?= (int)$sr['graduate_id'] ?>&amp;include_employment=1"><i class="ti ti-file-type-docx"></i> Preview / Print DOCX Report</a><button class="btn-dash-primary" type="button" onclick="closeModal()"><i class="ti ti-check"></i> Done</button></div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?> 