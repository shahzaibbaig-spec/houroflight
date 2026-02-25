@extends('layouts.app')

@section('content')
<section class="overflow-hidden rounded-2xl bg-[#1d8cf8] text-white">
    <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2 lg:items-center">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/80">Hour of Light</p>
            <h1 class="mt-3 text-4xl font-extrabold leading-none sm:text-6xl">AI Lesson<br>Planner</h1>
            <p class="mt-4 text-sm text-white/90 sm:text-base">Single lesson, PBL, custom files, quiz, slides, and exports.</p>
        </div>
    </div>
</section>

<section class="mt-6 grid gap-6 lg:grid-cols-3">
    <article class="hol-panel lg:col-span-1">
        <div id="planner-alert" class="hidden rounded-xl border px-4 py-3 text-sm"></div>
        <div class="mt-2 grid grid-cols-3 gap-2 rounded-xl bg-[#efefe7] p-1">
            <button id="tab-single" class="planner-tab rounded-lg bg-white px-2 py-2 text-xs font-bold uppercase shadow">Single</button>
            <button id="tab-pbl" class="planner-tab rounded-lg px-2 py-2 text-xs font-bold uppercase text-slate-600">PBL</button>
            <button id="tab-custom" class="planner-tab rounded-lg px-2 py-2 text-xs font-bold uppercase text-slate-600">Custom</button>
        </div>
        <form id="planner-form" class="mt-4 space-y-4">
            @csrf
            <input id="plan_type" type="hidden" value="single">
            <div>
                <label class="hol-label">Grade</label>
                <select id="grade" class="hol-input" required></select>
            </div>
            <div id="single-fields" class="space-y-4">
                <div>
                    <label class="hol-label">Subject</label>
                    <select id="subject" class="hol-input"></select>
                </div>
                <div>
                    <label class="hol-label">Topic</label>
                    <select id="topic" class="hol-input"></select>
                </div>
                <div id="topic-other-wrap" class="hidden">
                    <label class="hol-label">Custom Topic</label>
                    <input id="topic_other" class="hol-input" placeholder="Enter your own topic">
                </div>
            </div>
            <div id="pbl-fields" class="hidden space-y-4">
                <div><label class="hol-label">PBL Theme</label><input id="pbl_theme" class="hol-input"></div>
                <div>
                    <label class="hol-label">Integrated Subjects</label>
                    <select id="pbl_subjects" multiple class="hol-input min-h-[120px]">
                        <option>Science</option><option>Mathematics</option><option>English</option><option>Computer Studies</option><option>Social Studies</option>
                    </select>
                </div>
            </div>
            <div id="custom-fields" class="hidden space-y-3">
                <div><label class="hol-label">Upload Files</label><input id="custom_files" type="file" multiple accept=".txt,.md,.csv,image/*" class="hol-input"></div>
                <ul id="file-list" class="space-y-1 text-xs text-slate-700"></ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="hol-label">Duration</label>
                    <select id="duration" class="hol-input"></select>
                </div>
                <div>
                    <label class="hol-label">Language</label>
                    <select id="language" class="hol-input"></select>
                </div>
            </div>
            <div><label class="hol-label">Objectives (Optional)</label><textarea id="objectives" class="hol-input min-h-24"></textarea></div>
            <button id="generate-btn" class="hol-btn-primary w-full">Generate Plan</button>
        </form>
        <div class="mt-5 border-t border-black/10 pt-4">
            <h3 class="text-sm font-extrabold uppercase">History</h3>
            <div id="history-list" class="mt-2 space-y-2"></div>
        </div>
    </article>

    <article class="hol-panel lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-2xl font-extrabold">Output</h2>
            <div class="flex gap-2">
                <button id="copy-btn" class="hidden rounded-lg bg-[#1d8cf8] px-3 py-2 text-xs font-bold uppercase text-white">Copy</button>
                <button id="word-btn" class="hidden rounded-lg bg-[#ff4d00] px-3 py-2 text-xs font-bold uppercase text-white">Word</button>
                <button id="pdf-btn" class="hidden rounded-lg border border-black/15 bg-white px-3 py-2 text-xs font-bold uppercase">PDF</button>
            </div>
        </div>
        <div id="loading" class="mt-4 hidden rounded-xl bg-[#efefe7] px-4 py-3 text-sm">Generating...</div>
        <div id="plan-output" class="hol-plan-output mt-4 hidden max-h-[520px] overflow-auto rounded-xl border border-black/10 bg-white p-4 text-sm"></div>
        <div id="empty" class="mt-4 rounded-xl border border-dashed border-black/20 bg-[#efefe7] p-6 text-sm">No plan generated yet.</div>

        <div id="tools" class="mt-6 hidden space-y-6">
            <section class="rounded-xl border border-black/10 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-extrabold">Quiz Generator</h3>
                    <div class="flex gap-2">
                        <select id="quiz_type" class="hol-input py-2"></select>
                        <button id="quiz-btn" class="hol-btn-secondary">Generate Quiz</button>
                    </div>
                </div>
                <pre id="quiz-output" class="mt-3 hidden max-h-[260px] overflow-auto whitespace-pre-wrap rounded-xl border border-black/10 bg-[#f8fafc] p-4 text-sm"></pre>
            </section>

        </div>
    </article>
</section>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
(() => {
    const R = { g: '{{ route('ai.lesson-planner.generate') }}', q: '{{ route('ai.lesson-planner.generate-quiz') }}' };
    const S = { type: 'single', files: [], plan: '', history: [] };
    const DURATIONS = ['30', '40', '45', '60', '90'];
    const PBL_DURATIONS = ['1 Week', '2 Weeks', '3 Weeks', '1 Month', '6 Weeks', 'Full Semester'];
    const LANGUAGES = ['English', 'Urdu'];
    const QUIZ_TYPES = ['Multiple Choice', 'True / False', 'Fill in the Blanks', 'Short Answer'];
    const CURRICULUM = [
        { id: 'pg', name: 'Play Group', subjects: [{ name: 'English', topics: ['Introduction to Alphabets'] }, { name: 'Urdu', topics: ['Introduction to Haroof-e-Tahaji'] }, { name: 'Maths', topics: ['Counting 1-10'] }, { name: 'General Knowledge', topics: ['Colors and Shapes'] }, { name: 'Islamiat', topics: ['Basic Kalimas'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'nursery', name: 'Nursery', subjects: [{ name: 'English', topics: ['Introduction to Alphabets'] }, { name: 'Urdu', topics: ['Introduction to Haroof-e-Tahaji'] }, { name: 'Maths', topics: ['Counting 1-10'] }, { name: 'General Knowledge', topics: ['Colors and Shapes'] }, { name: 'Islamiat', topics: ['Basic Kalimas'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'prep', name: 'Prep', subjects: [{ name: 'English', topics: ['Introduction to Alphabets'] }, { name: 'Urdu', topics: ['Introduction to Haroof-e-Tahaji'] }, { name: 'Maths', topics: ['Counting 1-10'] }, { name: 'General Knowledge', topics: ['Colors and Shapes'] }, { name: 'Islamiat', topics: ['Basic Kalimas'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-1', name: 'Class 1', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'General Science', topics: ['Living and Non-living things'] }, { name: 'Social Studies', topics: ['My Community'] }, { name: 'Islamiat', topics: ['Pillars of Islam'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-2', name: 'Class 2', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'General Science', topics: ['Living and Non-living things'] }, { name: 'Social Studies', topics: ['My Community'] }, { name: 'Islamiat', topics: ['Pillars of Islam'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-3', name: 'Class 3', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'General Science', topics: ['Living and Non-living things'] }, { name: 'Social Studies', topics: ['My Community'] }, { name: 'Islamiat', topics: ['Pillars of Islam'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-4', name: 'Class 4', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'General Science', topics: ['Living and Non-living things'] }, { name: 'Social Studies', topics: ['My Community'] }, { name: 'Islamiat', topics: ['Pillars of Islam'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-5', name: 'Class 5', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'General Science', topics: ['Living and Non-living things'] }, { name: 'Social Studies', topics: ['My Community'] }, { name: 'Islamiat', topics: ['Pillars of Islam'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-6', name: 'Class 6', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'Science', topics: ['Cells and Tissues'] }, { name: 'History', topics: ['Ancient Civilizations'] }, { name: 'Geography', topics: ['World Climates'] }, { name: 'Social Studies', topics: ['Mughal Empire'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-7', name: 'Class 7', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'Science', topics: ['Cells and Tissues'] }, { name: 'History', topics: ['Ancient Civilizations'] }, { name: 'Geography', topics: ['World Climates'] }, { name: 'Social Studies', topics: ['Mughal Empire'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-8', name: 'Class 8', subjects: [{ name: 'English', topics: ['Nouns and Verbs'] }, { name: 'Urdu', topics: ['Wahid Jama'] }, { name: 'Maths', topics: ['Addition and Subtraction'] }, { name: 'Science', topics: ['Cells and Tissues'] }, { name: 'History', topics: ['Ancient Civilizations'] }, { name: 'Geography', topics: ['World Climates'] }, { name: 'Social Studies', topics: ['Mughal Empire'] }, { name: 'Computer Science', topics: ['Parts of a Computer'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-9', name: 'Class 9', subjects: [{ name: 'English', topics: ['Tenses and Grammar'] }, { name: 'Urdu', topics: ['Poetry (Ghazal and Nazm)'] }, { name: 'Maths', topics: ['Algebra and Geometry'] }, { name: 'Islamiat', topics: ['Life of the Prophet (PBUH)'] }, { name: 'Pakistan Studies', topics: ['Ideology of Pakistan'] }, { name: 'Physics', topics: ['Laws of Motion'] }, { name: 'Chemistry', topics: ['Periodic Table'] }, { name: 'Biology', topics: ['Human Anatomy'] }, { name: 'Computer Science', topics: ['Programming Basics'] }, { name: 'History', topics: ['World Wars'] }, { name: 'Geography', topics: ['Plate Tectonics'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-10', name: 'Class 10', subjects: [{ name: 'English', topics: ['Tenses and Grammar'] }, { name: 'Urdu', topics: ['Poetry (Ghazal and Nazm)'] }, { name: 'Maths', topics: ['Algebra and Geometry'] }, { name: 'Islamiat', topics: ['Life of the Prophet (PBUH)'] }, { name: 'Pakistan Studies', topics: ['Ideology of Pakistan'] }, { name: 'Physics', topics: ['Laws of Motion'] }, { name: 'Chemistry', topics: ['Periodic Table'] }, { name: 'Biology', topics: ['Human Anatomy'] }, { name: 'Computer Science', topics: ['Programming Basics'] }, { name: 'History', topics: ['World Wars'] }, { name: 'Geography', topics: ['Plate Tectonics'] }, { name: 'P.E (Physical Education)', topics: ['Cricket', 'Football', 'Table Tennis', 'Badminton', 'Athletics', 'Basketball'] }] },
        { id: 'grade-11', name: 'Class 11', subjects: [{ name: 'English', topics: ['Tenses and Grammar'] }, { name: 'Urdu', topics: ['Poetry (Ghazal and Nazm)'] }, { name: 'Maths', topics: ['Algebra and Geometry'] }, { name: 'Physics', topics: ['Laws of Motion'] }, { name: 'Chemistry', topics: ['Periodic Table'] }, { name: 'Biology', topics: ['Human Anatomy'] }, { name: 'Computer Science', topics: ['Programming Basics'] }] },
        { id: 'grade-12', name: 'Class 12', subjects: [{ name: 'English', topics: ['Tenses and Grammar'] }, { name: 'Urdu', topics: ['Poetry (Ghazal and Nazm)'] }, { name: 'Maths', topics: ['Algebra and Geometry'] }, { name: 'Physics', topics: ['Laws of Motion'] }, { name: 'Chemistry', topics: ['Periodic Table'] }, { name: 'Biology', topics: ['Human Anatomy'] }, { name: 'Computer Science', topics: ['Programming Basics'] }] }
    ];
    const $ = (id) => document.getElementById(id);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const ui = {
        alert: $('planner-alert'), form: $('planner-form'), loading: $('loading'), output: $('plan-output'), empty: $('empty'), tools: $('tools'),
        single: $('single-fields'), pbl: $('pbl-fields'), custom: $('custom-fields'), files: $('custom_files'), fileList: $('file-list'),
        copy: $('copy-btn'), word: $('word-btn'), pdf: $('pdf-btn'), history: $('history-list'), quizOut: $('quiz-output')
    };

    const alert = (m, ok = false) => { ui.alert.textContent = m; ui.alert.className = 'rounded-xl border px-4 py-3 text-sm ' + (ok ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700'); ui.alert.classList.remove('hidden'); };
    const clearAlert = () => { ui.alert.classList.add('hidden'); ui.alert.textContent = ''; };
    const post = async (u, b) => { const r = await fetch(u, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(b) }); return { r, p: await r.json() }; };
    const errors = (p) => Object.values(p?.errors || {}).flat().join(' ');

    const fillSelect = (id, options, placeholder = '') => {
        const el = $(id);
        if (!el) return;
        el.innerHTML = '';
        if (placeholder !== '') {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder;
            el.appendChild(opt);
        }
        options.forEach((option) => {
            const opt = document.createElement('option');
            if (typeof option === 'string') {
                opt.value = option;
                opt.textContent = option;
            } else {
                opt.value = option.id;
                opt.textContent = option.name;
            }
            el.appendChild(opt);
        });
    };

    const currentGrade = () => CURRICULUM.find((g) => g.id === $('grade').value) || null;
    const currentSubject = () => {
        const grade = currentGrade();
        if (!grade) return null;
        return grade.subjects.find((s) => s.name === $('subject').value) || null;
    };

    const refreshSubjectTopic = () => {
        const grade = currentGrade();
        const subjects = grade ? grade.subjects.map((s) => s.name) : [];
        fillSelect('subject', subjects, 'Select Subject');
        fillSelect('topic', [], 'Select Topic');
        refreshPblSubjects();
    };

    const refreshTopics = () => {
        const subject = currentSubject();
        const topics = subject ? subject.topics : [];
        fillSelect('topic', topics, 'Select Topic');
        const otherOption = document.createElement('option');
        otherOption.value = '__other__';
        otherOption.textContent = 'Other...';
        $('topic').appendChild(otherOption);
    };

    const refreshPblSubjects = () => {
        const grade = currentGrade();
        const target = $('pbl_subjects');
        target.innerHTML = '';
        (grade ? grade.subjects : []).forEach((subject) => {
            const opt = document.createElement('option');
            opt.value = subject.name;
            opt.textContent = subject.name;
            target.appendChild(opt);
        });
    };

    const refreshDurationOptions = () => {
        fillSelect('duration', S.type === 'pbl' ? PBL_DURATIONS : DURATIONS, 'Select Duration');
        if (S.type === 'pbl') {
            $('duration').value = PBL_DURATIONS[0];
        } else {
            $('duration').value = '40';
        }
    };

    const topicOtherWrap = $('topic-other-wrap');
    const topicOtherInput = $('topic_other');

    const getTopicValue = () => {
        const topic = $('topic').value;
        if (topic === '__other__') {
            return topicOtherInput.value.trim();
        }
        return topic.trim();
    };

    const renderTopicOther = () => {
        const topic = $('topic').value;
        topicOtherWrap.classList.toggle('hidden', topic !== '__other__');
    };

    const setType = (t) => {
        S.type = t; $('plan_type').value = t;
        ui.single.classList.toggle('hidden', t !== 'single');
        ui.pbl.classList.toggle('hidden', t !== 'pbl');
        ui.custom.classList.toggle('hidden', t !== 'custom');
        refreshDurationOptions();
        [['tab-single', 'single'], ['tab-pbl', 'pbl'], ['tab-custom', 'custom']].forEach(([id, v]) => {
            $(id).className = 'planner-tab rounded-lg px-2 py-2 text-xs font-bold uppercase ' + (v === t ? 'bg-white text-black shadow' : 'text-slate-600');
        });
    };

    const escapeHtml = (str) => String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

    const showPlan = (text) => {
        S.plan = text || '';
        if (S.plan.toLowerCase().includes('<table')) {
            ui.output.innerHTML = S.plan;
        } else {
            ui.output.innerHTML = '<pre class="whitespace-pre-wrap text-sm leading-7">' + escapeHtml(S.plan) + '</pre>';
        }
        const on = !!S.plan;
        ui.output.classList.toggle('hidden', !on); ui.empty.classList.toggle('hidden', on); ui.tools.classList.toggle('hidden', !on);
        ui.copy.classList.toggle('hidden', !on); ui.word.classList.toggle('hidden', !on); ui.pdf.classList.toggle('hidden', !on);
    };

    const saveHistory = (meta, plan) => { S.history = [{ id: Date.now(), meta, plan }].concat(S.history).slice(0, 12); localStorage.setItem('hol_history', JSON.stringify(S.history)); renderHistory(); };
    const renderHistory = () => {
        ui.history.innerHTML = '';
        if (!S.history.length) { ui.history.innerHTML = '<p class="text-xs text-slate-500">No history yet.</p>'; return; }
        S.history.forEach((h) => { const b = document.createElement('button'); b.className = 'w-full rounded-lg border border-black/10 bg-white px-3 py-2 text-left text-xs text-slate-700'; b.textContent = h.meta.type + ' | ' + h.meta.grade + ' | ' + new Date(h.id).toLocaleString(); b.onclick = () => showPlan(h.plan); ui.history.appendChild(b); });
    };

    const readFile = (f) => new Promise((res, rej) => { const r = new FileReader(); r.onload = () => res(r.result); r.onerror = () => rej(); f.type.startsWith('image/') ? r.readAsDataURL(f) : r.readAsText(f); });
    ui.files.onchange = async (e) => {
        const files = Array.from(e.target.files || []); S.files = []; ui.fileList.innerHTML = '';
        for (const f of files) { const c = await readFile(f); S.files.push({ name: f.name, type: f.type.startsWith('image/') ? 'image' : 'text', mime_type: f.type || null, content: c }); const li = document.createElement('li'); li.textContent = f.name; ui.fileList.appendChild(li); }
    };

    ui.form.onsubmit = async (e) => {
        e.preventDefault(); clearAlert(); ui.loading.classList.remove('hidden');
        try {
            const body = {
                plan_type: S.type,
                grade: currentGrade() ? currentGrade().name : $('grade').value.trim(),
                subject: $('subject').value.trim(),
                topic: getTopicValue(),
                duration: $('duration').value.trim(), language: $('language').value, objectives: $('objectives').value.trim(),
                pbl_theme: $('pbl_theme').value.trim(), pbl_subjects: Array.from($('pbl_subjects').selectedOptions).map((o) => o.value), custom_files: S.files
            };
            const { r, p } = await post(R.g, body);
            if (!r.ok) { alert(errors(p) || p.message || 'Failed to generate plan.'); return; }
            showPlan(p.lesson_plan || ''); saveHistory({ type: S.type, grade: body.grade }, p.lesson_plan || ''); alert('Lesson plan generated.', true);
        } catch { alert('Network error.'); } finally { ui.loading.classList.add('hidden'); }
    };

    $('quiz-btn').onclick = async () => {
        clearAlert(); if (!S.plan) return alert('Generate a plan first.');
        $('quiz-btn').disabled = true; $('quiz-btn').textContent = 'Generating...';
        try { const { r, p } = await post(R.q, { lesson_plan: ui.output.innerText, quiz_type: $('quiz_type').value }); if (!r.ok) return alert(errors(p) || p.message || 'Failed quiz.'); ui.quizOut.textContent = p.quiz || ''; ui.quizOut.classList.remove('hidden'); }
        catch { alert('Network error on quiz.'); } finally { $('quiz-btn').disabled = false; $('quiz-btn').textContent = 'Generate Quiz'; }
    };

    ui.copy.onclick = async () => { if (!S.plan) return; try { await navigator.clipboard.writeText(S.plan); alert('Copied.', true); } catch { alert('Copy failed.'); } };
    ui.word.onclick = () => {
        if (!S.plan) return;
        const html = `
            <html>
                <head>
                    <meta charset="utf-8">
                    <style>
                        @page { size: A4; margin: 12mm; }
                        body { font-family: Arial, sans-serif; line-height: 1.45; color: #111827; }
                        h1,h2,h3,h4 { margin: 0 0 8px; }
                        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 10px; }
                        th, td { border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; word-break: break-word; }
                        th { background: #f1f5f9; font-weight: 700; text-align: left; }
                        tr { page-break-inside: avoid; }
                    </style>
                </head>
                <body>${ui.output.innerHTML}</body>
            </html>
        `;
        const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
        const u = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = u;
        a.download = 'lesson-plan.doc';
        a.click();
        URL.revokeObjectURL(u);
    };
    ui.pdf.onclick = async () => {
        if (!S.plan || !window.html2canvas || !window.jspdf) return;
        const t = document.createElement('div');
        t.style.cssText = 'position:fixed;left:-9999px;top:0;width:960px;padding:24px;background:#fff;color:#111827;font-family:Arial,sans-serif;line-height:1.45';
        t.innerHTML = ui.output.innerHTML;
        document.body.appendChild(t);
        try {
            const c = await window.html2canvas(t, { scale: 2, useCORS: true });
            const pdf = new window.jspdf.jsPDF('p', 'mm', 'a4');
            const margin = 10;
            const pageWidthMm = 210;
            const pageHeightMm = 297;
            const contentWidthMm = pageWidthMm - (margin * 2);
            const contentHeightMm = pageHeightMm - (margin * 2);
            const pageHeightPx = Math.floor((c.width * contentHeightMm) / contentWidthMm);

            let yOffset = 0;
            let pageIndex = 0;
            while (yOffset < c.height) {
                const sliceHeightPx = Math.min(pageHeightPx, c.height - yOffset);
                const pageCanvas = document.createElement('canvas');
                pageCanvas.width = c.width;
                pageCanvas.height = sliceHeightPx;
                const ctx = pageCanvas.getContext('2d');
                ctx.drawImage(c, 0, yOffset, c.width, sliceHeightPx, 0, 0, c.width, sliceHeightPx);

                const imgData = pageCanvas.toDataURL('image/png');
                const renderedHeightMm = (sliceHeightPx * contentWidthMm) / c.width;

                if (pageIndex > 0) {
                    pdf.addPage();
                }
                pdf.addImage(imgData, 'PNG', margin, margin, contentWidthMm, renderedHeightMm);

                yOffset += sliceHeightPx;
                pageIndex++;
            }

            pdf.save('lesson-plan.pdf');
        } finally {
            document.body.removeChild(t);
        }
    };

    $('tab-single').onclick = () => setType('single'); $('tab-pbl').onclick = () => setType('pbl'); $('tab-custom').onclick = () => setType('custom');
    $('grade').onchange = () => refreshSubjectTopic();
    $('subject').onchange = () => refreshTopics();
    $('topic').onchange = () => renderTopicOther();

    fillSelect('grade', CURRICULUM, 'Select Grade');
    fillSelect('language', LANGUAGES);
    fillSelect('quiz_type', QUIZ_TYPES);
    $('language').value = 'English';
    $('quiz_type').value = 'Multiple Choice';
    $('grade').value = 'grade-5';
    refreshSubjectTopic();
    $('subject').value = 'General Science';
    refreshTopics();
    $('topic').value = 'Living and Non-living things';
    renderTopicOther();

    try { S.history = JSON.parse(localStorage.getItem('hol_history') || '[]'); } catch { S.history = []; }
    renderHistory();
    setType('single');
})();
</script>
@endsection
