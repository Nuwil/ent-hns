@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports')

@push('styles')
    <style>
        .report-controls {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
        }

        #writtenReport {
            font-family: 'Georgia', 'Times New Roman', serif;
            color: #1a1a2e;
            line-height: 1.75;
        }

        .wr-page {
            background: #fff;
            max-width: 860px;
            margin: 0 auto 32px;
            padding: 56px 64px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .wr-cover {
            text-align: center;
            padding: 80px 64px;
            border-top: 6px solid #2563eb;
        }

        .wr-cover-logo {
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        .wr-cover-title {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .wr-cover-subtitle {
            font-size: 16px;
            color: #475569;
            margin-bottom: 40px;
        }

        .wr-cover-meta {
            display: inline-block;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 32px;
            font-size: 13px;
            color: #64748b;
            line-height: 2;
        }

        .wr-h1 {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 6px;
            margin: 0 0 16px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .wr-h2 {
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 24px 0 8px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .wr-p {
            font-size: 13.5px;
            margin: 0 0 12px;
            color: #334155;
            text-align: justify;
        }

        .wr-ul {
            margin: 0 0 12px;
            padding-left: 20px;
            font-size: 13.5px;
            color: #334155;
        }

        .wr-ul li {
            margin-bottom: 5px;
        }

        .wr-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: #e2e8f0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin: 16px 0;
        }

        .wr-kpi-cell {
            background: #f8fafc;
            padding: 14px 16px;
            text-align: center;
        }

        .wr-kpi-val {
            font-size: 26px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .wr-kpi-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .wr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin: 12px 0 16px;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .wr-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px 12px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
        }

        .wr-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        .wr-table tr:last-child td {
            border-bottom: none;
        }

        .wr-table tr.repeat-row {
            background: #eff6ff;
        }

        .wr-table tr.repeat-row td {
            color: #1d4ed8;
            font-weight: 600;
        }

        .wr-table .vc {
            font-weight: 700;
            font-size: 13px;
        }

        .wr-callout {
            border-left: 3px solid #2563eb;
            background: #eff6ff;
            padding: 10px 14px;
            border-radius: 0 6px 6px 0;
            font-size: 13px;
            color: #1d4ed8;
            margin: 8px 0;
        }

        .wr-callout.warn {
            border-color: #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

        .wr-callout.success {
            border-color: #16a34a;
            background: #f0fdf4;
            color: #14532d;
        }

        .wr-footer {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .wr-divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">

        {{-- Date Validation Modal --}}
        <div class="modal fade" id="dateValidationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom:1px solid #fee2e2;background:#fff9f9;">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>Invalid Date Range
                        </h5>
                    </div>
                    <div class="modal-body py-4 px-4">
                        <p class="mb-0" style="font-size:14px;color:#334155;line-height:1.6;">
                            The <strong>Start Date</strong> cannot be later than the <strong>End Date</strong>. Please
                            select a valid date range.
                        </p>
                    </div>
                    <div class="modal-footer" style="border-top:1px solid #f1f5f9;">
                        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Controls --}}
        <div class="report-controls">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-primary btn-sm" id="btnWeekly" onclick="setType('weekly')">
                    <i class="bi bi-calendar-week me-1"></i>Weekly
                </button>
                <button class="btn btn-outline-secondary btn-sm" id="btnMonthly" onclick="setType('monthly')">
                    <i class="bi bi-calendar-month me-1"></i>Monthly
                </button>
            </div>
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div>
                    <label class="form-label fw-semibold small mb-1">Start Date</label>
                    <input type="date" id="startDate" class="form-control form-control-sm" style="min-width:150px">
                </div>
                <div>
                    <label class="form-label fw-semibold small mb-1">End Date</label>
                    <input type="date" id="endDate" class="form-control form-control-sm" style="min-width:150px">
                </div>
                <button class="btn btn-primary btn-sm" onclick="generateReport()" id="generateBtn">
                    <i class="bi bi-arrow-clockwise me-1"></i>Generate
                </button>
            </div>
        </div>

        <div id="reportLoading" style="display:none" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <div class="text-muted small mt-2">Generating report…</div>
        </div>

        <div id="reportOutput" style="display:none">
            <div class="d-flex gap-2 flex-wrap mb-4">
                <button class="btn btn-danger btn-sm" onclick="exportPDF()" id="pdfBtn">
                    <i class="bi bi-file-pdf me-1"></i>Save as PDF
                </button>
            </div>
            <div id="writtenReport"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        const reportDataUrl = '{{ route("admin.reports.data") }}';
        let currentType = 'weekly';
        let lastData = null;
        const clinicName = 'ENT Clinic Online';

        (function () {
            const t = new Date(), m = new Date(t);
            m.setDate(t.getDate() - t.getDay() + 1);
            document.getElementById('endDate').value = fmt(t);
            document.getElementById('startDate').value = fmt(m);
        })();

        function fmt(d) { return d.toISOString().slice(0, 10); }

        function setType(type) {
            currentType = type;
            document.getElementById('btnWeekly').className = type === 'weekly' ? 'btn btn-primary btn-sm' : 'btn btn-outline-secondary btn-sm';
            document.getElementById('btnMonthly').className = type === 'monthly' ? 'btn btn-primary btn-sm' : 'btn btn-outline-secondary btn-sm';
            const t = new Date();
            if (type === 'weekly') { const m = new Date(t); m.setDate(t.getDate() - t.getDay() + 1); document.getElementById('startDate').value = fmt(m); document.getElementById('endDate').value = fmt(t); }
            else { document.getElementById('startDate').value = fmt(new Date(t.getFullYear(), t.getMonth(), 1)); document.getElementById('endDate').value = fmt(t); }
        }

        async function generateReport() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            if (!start || !end) { showDateModal(); return; }
            if (new Date(start) > new Date(end)) { showDateModal(); return; }

            const btn = document.getElementById('generateBtn');
            btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating…';
            document.getElementById('reportLoading').style.display = 'block';
            document.getElementById('reportOutput').style.display = 'none';
            try {
                const res = await fetch(`${reportDataUrl}?${new URLSearchParams({ type: currentType, start, end })}`);
                if (!res.ok) throw new Error(res.status);
                lastData = await res.json();
                document.getElementById('reportOutput').style.display = 'block';
                renderWrittenReport(lastData);
            } catch (e) { alert('Failed to generate report. Please try again.'); console.error(e); }
            finally { document.getElementById('reportLoading').style.display = 'none'; btn.disabled = false; btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Generate'; }
        }

        function showDateModal() {
            new bootstrap.Modal(document.getElementById('dateValidationModal')).show();
        }

        function renderWrittenReport(d) {
            const tl = currentType === 'weekly' ? 'Weekly' : 'Monthly';
            const generated = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            const topC = d.complaints.length > 0 ? d.complaints[0] : null;
            const topR = (d.repeatLeaderboard || []).length > 0 ? d.repeatLeaderboard[0] : null;
            const rPct = d.totalPatients > 0 ? Math.round(d.repeatPatients / d.totalPatients * 100) : 0;
            const canPct = d.apptTotal > 0 ? Math.round(d.apptCancelled / d.apptTotal * 100) : 0;
            const comPct = d.apptTotal > 0 ? Math.round(d.apptCompleted / d.apptTotal * 100) : 0;
            const gStr = Object.entries(d.genderDist).map(([g, c]) => c + ' ' + g).join(', ');
            const topAge = Object.entries(d.ageGroups).sort((a, b) => b[1] - a[1])[0];
            const growthNarr = d.consultationGrowth !== null ? 'Compared to the previous comparable period (' + d.prevConsultations + ' consultations), visit volume has ' + (d.consultationGrowth > 0 ? 'increased' : d.consultationGrowth < 0 ? 'decreased' : 'remained stable') + ' by ' + Math.abs(d.consultationGrowth) + '%.' : '';

            const exec = 'This ' + tl.toLowerCase() + ' report covers the period from ' + d.period.start + ' to ' + d.period.end + '. During this time, ' + clinicName + ' recorded a total of ' + d.totalConsultations + ' consultation' + (d.totalConsultations !== 1 ? 's' : '') + ' across ' + d.totalPatients + ' patient' + (d.totalPatients !== 1 ? 's' : '') + ', of whom ' + d.newPatients + ' were newly registered and ' + d.repeatPatients + ' (' + rPct + '%) were returning patients with prior visit history. ' + (topC ? 'The most frequently cited chief complaint was "' + topC.complaint + '", accounting for ' + topC.count + ' case' + (topC.count !== 1 ? 's' : '') + ' (' + topC.pct + '% of total consultations). ' : '') + growthNarr + ' A total of ' + d.apptTotal + ' appointment' + (d.apptTotal !== 1 ? 's were' : ' was') + ' scheduled, with a ' + comPct + '% completion rate.';

            const trends = 'Patient volume totaled ' + d.totalPatients + ' unique individuals. ' + (topAge && topAge[1] > 0 ? 'The ' + topAge[0] + ' age group was the largest demographic (' + topAge[1] + ' patients). ' : '') + (gStr ? 'Gender distribution: ' + gStr + '. ' : '') + d.repeatPatients + ' patients (' + rPct + '%) had more than one recorded visit, indicating ' + (rPct > 60 ? 'strong continuity of care and patient retention.' : rPct > 30 ? 'a healthy balance between new and returning patients.' : 'a predominantly new patient base during this period.');

            const cNarr = d.complaints.length > 0 ? 'The top chief complaint' + (d.complaints.length > 1 ? 's were' : ' was') + ' ' + d.complaints.slice(0, 3).map(c => '"' + c.complaint + '" (' + c.count + ' cases)').join(', ') + '. ' + (topC && topC.trend === 'up' ? '"' + topC.complaint + '" showed an upward trend vs prior period (' + topC.prev + ' → ' + topC.count + ' cases), suggesting a possible seasonal factor.' : topC && topC.trend === 'down' ? '"' + topC.complaint + '" showed a declining trend from the prior period (' + topC.prev + ' → ' + topC.count + ' cases).' : '') : 'No chief complaint data was recorded for this period.';

            const rNarr = d.repeatPatients > 0 && topR ? d.repeatPatients + ' patient' + (d.repeatPatients !== 1 ? 's' : '') + ' recorded more than one visit (' + rPct + '% of total patient traffic). The highest-frequency patient was ' + topR.name + ' with ' + topR.visit_count + ' visits. High repeat-visit rates may indicate ongoing treatment plans, chronic ENT conditions, or strong patient trust.' : 'No patients recorded more than one visit during this period. All encounters were single-visit consultations.';

            const recs = [];
            if (canPct > 20) recs.push('Address the ' + canPct + '% appointment cancellation rate through automated reminders or improved scheduling flexibility.');
            if (rPct < 20 && d.totalPatients > 5) recs.push('Consider implementing a follow-up care program to improve patient retention and continuity of care.');
            if (topC && topC.trend === 'up') recs.push('Prepare additional resources for "' + topC.complaint + '" cases, which are trending upward.');
            if (d.consultationGrowth !== null && d.consultationGrowth > 20) recs.push('Review staffing and scheduling capacity to support the increased patient volume.');
            if (recs.length === 0) recs.push('Continue current operational practices — key metrics are within normal range.');
            recs.push('Maintain accurate and complete visit documentation to support future trend analysis.');
            recs.push('Review and update the appointment scheduling system to minimize no-show rates.');

            const ageRows = Object.entries(d.ageGroups).map(([g, c]) => '<tr><td>' + g + ' years</td><td class="vc">' + c + '</td><td>' + (d.totalPatients > 0 ? Math.round(c / d.totalPatients * 100) : 0) + '%</td></tr>').join('');
            const genderRows = Object.entries(d.genderDist).map(([g, c]) => '<tr><td>' + g.charAt(0).toUpperCase() + g.slice(1) + '</td><td class="vc">' + c + '</td><td>' + (d.totalPatients > 0 ? Math.round(c / d.totalPatients * 100) : 0) + '%</td></tr>').join('');
            const cmpRows = d.complaints.length === 0 ? '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:16px;">No complaints recorded.</td></tr>' : d.complaints.map((c, i) => '<tr><td style="color:#94a3b8;">' + (i + 1) + '</td><td style="font-weight:600;">' + esc(c.complaint) + '</td><td class="vc">' + c.count + '</td><td>' + c.pct + '%</td><td>' + (c.trend === 'up' ? '↑ Up from ' + c.prev : c.trend === 'down' ? '↓ Down from ' + c.prev : '— No change') + '</td></tr>').join('');
            const diagRows = d.recurringIllnesses && d.recurringIllnesses.length > 0 ? d.recurringIllnesses.map((r, i) => '<tr><td style="color:#94a3b8;">' + (i + 1) + '</td><td style="font-weight:600;">' + esc(r.diagnosis) + '</td><td class="vc">' + r.count + '</td></tr>').join('') : '';
            const patRows = (d.allPatientsTable || []).length === 0 ? '<tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:16px;">No patient data recorded.</td></tr>' : (d.allPatientsTable || []).map((p, i) => '<tr class="' + (p.is_repeat ? 'repeat-row' : '') + '"><td style="color:' + (p.is_repeat ? '#1d4ed8' : '#94a3b8') + ';">' + (i + 1) + '</td><td style="font-weight:600;">' + esc(p.name) + '</td><td>' + esc(p.gender.charAt(0).toUpperCase() + p.gender.slice(1)) + '</td><td>' + p.age + '</td><td class="vc" style="color:' + (p.is_repeat ? '#1d4ed8' : 'inherit') + ';">' + p.visit_count + (p.is_repeat ? ' <span style="font-size:10px;background:#dbeafe;color:#1d4ed8;border-radius:10px;padding:1px 6px;font-weight:600;">Repeat</span>' : '') + '</td></tr>').join('');
            const insRows = (d.insights || []).length === 0 ? '<p class="wr-p">No significant insights detected for this period.</p>' : (d.insights || []).map(i => '<div class="wr-callout ' + (i.type === 'warning' ? 'warn' : i.type === 'success' ? 'success' : '') + '">' + esc(i.text) + '</div>').join('');

            document.getElementById('writtenReport').innerHTML =
                '<div class="wr-page wr-cover">' +
                '<div class="wr-cover-logo">' + esc(clinicName) + '</div>' +
                '<div class="wr-cover-title">' + tl + ' Clinical Report</div>' +
                '<div class="wr-cover-subtitle">Ear, Nose &amp; Throat — Administrative &amp; Clinical Summary</div>' +
                '<div class="wr-cover-meta"><strong>Reporting Period:</strong> ' + d.period.start + ' — ' + d.period.end + '<br><strong>Report Type:</strong> ' + tl + ' Report<br><strong>Date Generated:</strong> ' + generated + '<br><strong>Prepared By:</strong> ' + clinicName + ' — Administration System</div>' +
                '<div class="wr-footer" style="margin-top:60px;">CONFIDENTIAL — FOR ADMINISTRATIVE USE ONLY</div>' +
                '</div>' +
                '<div class="wr-page">' +
                '<div class="wr-h1">Executive Summary</div>' +
                '<p class="wr-p">' + esc(exec) + '</p>' +
                '<hr class="wr-divider">' +
                '<div class="wr-h1">Key Statistics</div>' +
                '<div class="wr-kpi-grid">' +
                '<div class="wr-kpi-cell"><div class="wr-kpi-val">' + d.totalConsultations + '</div><div class="wr-kpi-label">Total Consultations</div></div>' +
                '<div class="wr-kpi-cell"><div class="wr-kpi-val">' + d.totalPatients + '</div><div class="wr-kpi-label">Patients Seen</div></div>' +
                '<div class="wr-kpi-cell"><div class="wr-kpi-val">' + d.newPatients + '</div><div class="wr-kpi-label">New Patients</div></div>' +
                '<div class="wr-kpi-cell"><div class="wr-kpi-val">' + d.repeatPatients + '</div><div class="wr-kpi-label">Repeat Patients</div></div>' +
                '<div class="wr-kpi-cell"><div class="wr-kpi-val">' + d.apptTotal + '</div><div class="wr-kpi-label">Appointments</div></div>' +
                '<div class="wr-kpi-cell"><div class="wr-kpi-val">' + comPct + '%</div><div class="wr-kpi-label">Completion Rate</div></div>' +
                '</div>' +
                (d.consultationGrowth !== null ? '<div class="wr-h2">Period-over-Period Comparison</div><p class="wr-p">Previous period: <strong>' + d.prevConsultations + '</strong> → Current: <strong>' + d.totalConsultations + '</strong> <span style="font-weight:700;color:' + (d.consultationGrowth >= 0 ? '#16a34a' : '#dc2626') + ';">(' + (d.consultationGrowth >= 0 ? '+' : '') + d.consultationGrowth + '%)</span></p>' : '') +
                '<div class="wr-footer">Page 1 &nbsp;·&nbsp; ' + clinicName + ' ' + tl + ' Report &nbsp;·&nbsp; ' + generated + '</div>' +
                '</div>' +
                '<div class="wr-page">' +
                '<div class="wr-h1">Patient Trends</div>' +
                '<p class="wr-p">' + esc(trends) + '</p>' +
                '<div class="wr-h2">Age Distribution</div>' +
                '<table class="wr-table"><thead><tr><th>Age Group</th><th>Count</th><th>% of Total</th></tr></thead><tbody>' + ageRows + '</tbody></table>' +
                '<div class="wr-h2">Gender Distribution</div>' +
                '<table class="wr-table"><thead><tr><th>Gender</th><th>Count</th><th>% of Total</th></tr></thead><tbody>' + genderRows + '</tbody></table>' +
                '<hr class="wr-divider">' +
                '<div class="wr-h1">Chief Complaint Analysis</div>' +
                '<p class="wr-p">' + esc(cNarr) + '</p>' +
                '<div class="wr-h2">Top Chief Complaints</div>' +
                '<table class="wr-table"><thead><tr><th>#</th><th>Chief Complaint</th><th>Cases</th><th>%</th><th>vs Prior</th></tr></thead><tbody>' + cmpRows + '</tbody></table>' +
                (diagRows ? '<div class="wr-h2">Top Recurring Diagnoses</div><table class="wr-table"><thead><tr><th>#</th><th>Diagnosis</th><th>Cases</th></tr></thead><tbody>' + diagRows + '</tbody></table>' : '') +
                '<div class="wr-footer">Page 2 &nbsp;·&nbsp; ' + clinicName + ' ' + tl + ' Report &nbsp;·&nbsp; ' + generated + '</div>' +
                '</div>' +
                '<div class="wr-page">' +
                '<div class="wr-h1">Repeat Patient Analysis</div>' +
                '<p class="wr-p">' + esc(rNarr) + '</p>' +
                '<div class="wr-h2">Patient Visit Summary <span style="font-size:10px;font-weight:400;color:#64748b;text-transform:none;letter-spacing:0;">— Blue rows = repeat patients (&gt;1 visit)</span></div>' +
                '<table class="wr-table"><thead><tr><th>Rank</th><th>Patient Name</th><th>Gender</th><th>Age</th><th>No. of Visits</th></tr></thead><tbody>' + patRows + '</tbody></table>' +
                '<hr class="wr-divider">' +
                '<div class="wr-h1">Appointment Summary</div>' +
                '<table class="wr-table"><thead><tr><th>Status</th><th>Count</th><th>%</th></tr></thead><tbody>' +
                '<tr><td>Completed</td><td class="vc">' + d.apptCompleted + '</td><td>' + comPct + '%</td></tr>' +
                '<tr><td>Cancelled</td><td class="vc">' + d.apptCancelled + '</td><td>' + canPct + '%</td></tr>' +
                '<tr><td>Pending</td><td class="vc">' + d.apptPending + '</td><td>' + (d.apptTotal > 0 ? Math.round(d.apptPending / d.apptTotal * 100) : 0) + '%</td></tr>' +
                '<tr style="font-weight:700;border-top:2px solid #e2e8f0;"><td>Total</td><td class="vc">' + d.apptTotal + '</td><td>100%</td></tr>' +
                '</tbody></table>' +
                (canPct > 20 ? '<div class="wr-callout warn"><strong>Note:</strong> Cancellation rate of ' + canPct + '% exceeds the recommended 20% threshold.</div>' : '') +
                '<div class="wr-footer">Page 3 &nbsp;·&nbsp; ' + clinicName + ' ' + tl + ' Report &nbsp;·&nbsp; ' + generated + '</div>' +
                '</div>' +
                '<div class="wr-page">' +
                '<div class="wr-h1">Predictive Insights</div>' + insRows +
                '<hr class="wr-divider">' +
                '<div class="wr-h1">Recommendations</div>' +
                '<ul class="wr-ul">' + recs.map(r => '<li>' + esc(r) + '</li>').join('') + '</ul>' +
                '<hr class="wr-divider">' +
                '<div class="wr-h1">Conclusion</div>' +
                '<p class="wr-p">This ' + tl.toLowerCase() + ' report for ' + clinicName + ' covering ' + d.period.start + ' to ' + d.period.end + ' reflects ' + (d.totalConsultations > 0 ? 'an active clinical period with ' + d.totalConsultations + ' consultation' + (d.totalConsultations !== 1 ? 's' : '') + ' across ' + d.totalPatients + ' patient' + (d.totalPatients !== 1 ? 's' : '') + '.' : 'minimal clinical activity for the selected period.') + ' ' + (d.repeatPatients > 0 ? 'Patient retention remains a positive indicator, with ' + rPct + '% of patients having returned for follow-up care.' : '') + ' ' + (topC ? 'The predominant clinical concern was "' + esc(topC.complaint) + '", which should be monitored in future reporting cycles.' : '') + ' Continued attention to appointment adherence, documentation completeness, and follow-up care scheduling will be essential for maintaining the quality of service delivery.</p>' +
                '<p class="wr-p" style="color:#64748b;font-size:12px;font-style:italic;margin-top:20px;">This report was automatically generated by the ' + clinicName + ' Management System on ' + generated + '. Data is derived from recorded visits and appointments within the selected reporting window.</p>' +
                '<div class="wr-footer">Page 4 &nbsp;·&nbsp; ' + clinicName + ' ' + tl + ' Report &nbsp;·&nbsp; ' + generated + '</div>' +
                '</div>';
        }

        function esc(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }

        // ── PDF: render full report as one canvas, then slice into A4 pages ──
        async function exportPDF() {
            if (!lastData) return;
            const btn = document.getElementById('pdfBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Building PDF…';
            try {
                const { jsPDF } = window.jspdf;
                const container = document.getElementById('writtenReport');

                // Temporarily expand all .wr-page elements for full capture
                const pages = [...container.querySelectorAll('.wr-page')];
                const savedStyles = pages.map(p => ({ margin: p.style.marginBottom, shadow: p.style.boxShadow, radius: p.style.borderRadius }));
                pages.forEach(p => { p.style.marginBottom = '0'; p.style.boxShadow = 'none'; p.style.borderRadius = '0'; });

                // Render entire container as one tall canvas
                const canvas = await html2canvas(container, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    windowWidth: container.scrollWidth,
                    scrollX: 0,
                    scrollY: 0,
                });

                // Restore styles
                pages.forEach((p, i) => { p.style.marginBottom = savedStyles[i].margin; p.style.boxShadow = savedStyles[i].shadow; p.style.borderRadius = savedStyles[i].radius; });

                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pageW = pdf.internal.pageSize.getWidth();   // 210mm
                const pageH = pdf.internal.pageSize.getHeight();  // 297mm
                const margin = 12; // mm
                const usableW = pageW - margin * 2;
                const usableH = pageH - margin * 2;

                // Scale: canvas pixels → mm on page
                const imgW = usableW;
                const scale = imgW / canvas.width; // mm per pixel
                const imgH = canvas.height * scale; // total height in mm

                let yOffset = 0; // current position in mm along the full canvas
                let pageNum = 0;

                while (yOffset < imgH) {
                    if (pageNum > 0) pdf.addPage();

                    // Slice height in pixels
                    const sliceH = usableH / scale; // pixels to capture for one page
                    const srcY = yOffset / scale;   // pixel row to start from

                    // Create a slice canvas
                    const sliceCanvas = document.createElement('canvas');
                    sliceCanvas.width = canvas.width;
                    sliceCanvas.height = Math.min(sliceH, canvas.height - srcY);
                    const ctx = sliceCanvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, sliceCanvas.width, sliceCanvas.height);
                    ctx.drawImage(canvas, 0, srcY, canvas.width, sliceCanvas.height, 0, 0, canvas.width, sliceCanvas.height);

                    const sliceImg = sliceCanvas.toDataURL('image/jpeg', 0.92);
                    const sliceImgH = sliceCanvas.height * scale;
                    pdf.addImage(sliceImg, 'JPEG', margin, margin, usableW, sliceImgH);

                    yOffset += usableH;
                    pageNum++;
                }

                const tl = currentType === 'weekly' ? 'Weekly' : 'Monthly';
                pdf.save('ENT_' + tl + '_Report_' + lastData.period.start.replace(/,/g, '').replace(/ /g, '_') + '.pdf');
            } catch (e) {
                console.error(e);
                alert('PDF export failed. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-file-pdf me-1"></i>Save as PDF';
            }
        }
    </script>
@endpush

.wr-page{background:#fff;max-width:860px;margin:0 auto 32px;padding:56px 64px;border:1px solid
#e2e8f0;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.06);}
.wr-cover{text-align:center;padding:80px 64px;border-top:6px solid #2563eb;}
.wr-cover-logo{font-size:13px;font-weight:700;color:#2563eb;letter-spacing:2px;text-transform:uppercase;margin-bottom:40px;}
.wr-cover-title{font-size:32px;font-weight:800;color:#1e293b;margin-bottom:12px;line-height:1.2;}
.wr-cover-subtitle{font-size:16px;color:#475569;margin-bottom:40px;}
.wr-cover-meta{display:inline-block;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:16px
32px;font-size:13px;color:#64748b;line-height:2;}
.wr-h1{font-size:20px;font-weight:800;color:#1e293b;border-bottom:2px solid #2563eb;padding-bottom:6px;margin:0 0
16px;font-family:'Segoe UI',system-ui,sans-serif;}
.wr-h2{font-size:14px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:0.8px;margin:24px 0
8px;font-family:'Segoe UI',system-ui,sans-serif;}
.wr-p{font-size:13.5px;margin:0 0 12px;color:#334155;text-align:justify;}
.wr-ul{margin:0 0 12px;padding-left:20px;font-size:13.5px;color:#334155;}
.wr-ul li{margin-bottom:5px;}
.wr-kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:#e2e8f0;border:1px solid
#e2e8f0;border-radius:8px;overflow:hidden;margin:16px 0;}
.wr-kpi-cell{background:#f8fafc;padding:14px 16px;text-align:center;}
.wr-kpi-val{font-size:26px;font-weight:800;color:#1e293b;line-height:1;font-family:'Segoe UI',system-ui,sans-serif;}
.wr-kpi-label{font-size:11px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:4px;font-family:'Segoe
UI',system-ui,sans-serif;}
.wr-table{width:100%;border-collapse:collapse;font-size:12.5px;margin:12px 0 16px;font-family:'Segoe
UI',system-ui,sans-serif;}
.wr-table
th{background:#f1f5f9;color:#475569;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;padding:8px
12px;text-align:left;border-bottom:2px solid #e2e8f0;}
.wr-table td{padding:8px 12px;border-bottom:1px solid #f1f5f9;color:#334155;vertical-align:middle;}
.wr-table tr:last-child td{border-bottom:none;}
.wr-table tr.repeat-row{background:#eff6ff;}
.wr-table tr.repeat-row td{color:#1d4ed8;font-weight:600;}
.wr-table .vc{font-weight:700;font-size:13px;}
.wr-callout{border-left:3px solid #2563eb;background:#eff6ff;padding:10px 14px;border-radius:0 6px 6px
0;font-size:13px;color:#1d4ed8;margin:8px 0;}
.wr-callout.warn{border-color:#f59e0b;background:#fffbeb;color:#92400e;}
.wr-callout.success{border-color:#16a34a;background:#f0fdf4;color:#14532d;}
.wr-footer{text-align:center;font-size:11px;color:#94a3b8;margin-top:32px;padding-top:12px;border-top:1px solid
#e2e8f0;font-family:'Segoe UI',system-ui,sans-serif;}
.wr-divider{border:none;border-top:1px solid #e2e8f0;margin:20px 0;}
@media print{
*{-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important;}
.app-sidebar,.app-topnav,.no-print,.report-controls{display:none!important;}
.app-body{padding:0!important;}
.app-panel{margin:0!important;padding:0!important;}
.app-content{overflow:visible!important;padding:0!important;}
.page-content{padding:0!important;margin:0!important;}
#reportOutput{display:block!important;}
#writtenReport{font-size:12pt;}
.wr-page{max-width:100%;margin:0;padding:20mm 18mm;border:none;border-radius:0;box-shadow:none;page-break-after:always;}
.wr-page:last-child{page-break-after:avoid;}
.wr-cover{padding:30mm 18mm;}
.wr-h1{font-size:16pt;}
.wr-h2{font-size:10pt;}
.wr-p,.wr-ul{font-size:10.5pt;}
.wr-kpi-val{font-size:20pt;}
.wr-table{font-size:9.5pt;}
.wr-table th{font-size:8pt;}
.no-break{page-break-inside:avoid;}
@page{size:A4 portrait;margin:15mm 12mm;}
}
</style>
@endpush

@section('content')