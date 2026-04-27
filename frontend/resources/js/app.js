const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function el(html) {
  const t = document.createElement('template');
  t.innerHTML = html.trim();
  return t.content.firstElementChild;
}

function escapeHtml(str) {
  return String(str)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

async function api(path, { method = 'GET', body } = {}) {
  const headers = { 'Accept': 'application/json' };
  if (method !== 'GET' && method !== 'HEAD') {
    headers['X-CSRF-TOKEN'] = csrfToken;
  }
  if (body !== undefined) headers['Content-Type'] = 'application/json';

  const resp = await fetch(path, {
    method,
    headers,
    credentials: 'same-origin',
    body: body !== undefined ? JSON.stringify(body) : undefined,
  });

  const text = await resp.text();
  let json = null;
  try { json = text ? JSON.parse(text) : null; } catch (_) {}

  return { ok: resp.ok, status: resp.status, json, text };
}

function apiError(r) {
  return r?.json?.error || `Error ${r?.status ?? '???'}`;
}

async function apiList(path) {
  const r = await api(path);
  if (!r.ok) return { ok: false, status: r.status, error: apiError(r), items: [] };
  if (!Array.isArray(r.json)) return { ok: false, status: r.status, error: 'Unexpected response', items: [] };
  return { ok: true, status: r.status, error: '', items: r.json };
}

function navigate(path, { replace = false } = {}) {
  if (!path.startsWith('/')) path = `/${path}`;
  const current = window.location.pathname + window.location.search;
  if (current === path) {
    boot();
    return;
  }
  if (replace) history.replaceState({}, '', path);
  else history.pushState({}, '', path);
  boot();
}

function getRoute() {
  const p = window.location.pathname || '/';
  const path = p.length > 1 && p.endsWith('/') ? p.slice(0, -1) : p;
  return path === '/' ? '/dashboard' : path;
}

function layout({ title, content, notice }) {
  return el(`
    <div class="min-h-screen">
      <div class="border-b bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded bg-zinc-900"></div>
            <div class="leading-tight">
              <div class="text-sm font-semibold">School Client</div>
              <div class="text-xs text-zinc-500">Laravel SPA</div>
            </div>
          </div>
          <nav class="flex items-center gap-2 text-sm">
            <a class="rounded px-2 py-1 hover:bg-zinc-100" href="/dashboard">Dashboard</a>
            <form method="POST" action="/logout">
              <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}" />
              <button class="rounded bg-zinc-900 px-3 py-1.5 text-white hover:bg-zinc-800" type="submit">Logout</button>
            </form>
          </nav>
        </div>
      </div>

      <div class="mx-auto max-w-6xl px-4 py-6">
        <div class="mb-4 flex items-baseline justify-between gap-3">
          <h1 class="text-lg font-semibold">${escapeHtml(title)}</h1>
          <div id="authBadge" class="text-xs text-zinc-500"></div>
        </div>
        ${notice ? `<div class="mb-4 rounded border border-zinc-200 bg-white px-3 py-2 text-sm">${escapeHtml(notice)}</div>` : ''}
        <div class="rounded border border-zinc-200 bg-white p-4">${content}</div>
      </div>
    </div>
  `);
}

async function updateAuthBadge(root) {
  const badge = root.querySelector('#authBadge');
  if (!badge) return;
  const r = await api('/client-api/session');
  const isAuth = !!r.json?.authenticated;
  const hasBackend = !!r.json?.backendToken;
  badge.textContent = isAuth ? (hasBackend ? 'Authenticated' : 'Logged in (no backend token)') : 'Not authenticated';
}

function table(items, columns) {
  const header = columns.map((c) => `<th class="border-b px-3 py-2 text-left text-xs font-medium text-zinc-500">${escapeHtml(c.label)}</th>`).join('');
  const rows = items.map((it) => {
    const tds = columns.map((c) => `<td class="border-b px-3 py-2 text-sm">${c.render(it)}</td>`).join('');
    return `<tr>${tds}</tr>`;
  }).join('');
  return `
    <div class="overflow-x-auto">
      <table class="w-full border-separate border-spacing-0">
        <thead><tr>${header}</tr></thead>
        <tbody>${rows || `<tr><td class="px-3 py-6 text-sm text-zinc-500" colspan="${columns.length}">No data</td></tr>`}</tbody>
      </table>
    </div>
  `;
}

function actionBtn(label, variant = 'secondary') {
  const cls = variant === 'danger'
    ? 'rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-500'
    : variant === 'primary'
      ? 'rounded bg-zinc-900 px-2 py-1 text-xs text-white hover:bg-zinc-800'
      : 'rounded border border-zinc-300 px-2 py-1 text-xs hover:bg-zinc-50';
  return `<button data-action="${escapeHtml(label)}" class="${cls}" type="button">${escapeHtml(label)}</button>`;
}

function ensureYmd(value, fallback = '') {
  const v = String(value ?? '').trim();
  if (/^\\d{4}-\\d{2}-\\d{2}$/.test(v)) return v;
  return fallback;
}

async function pageDashboard() {
  const r = await api('/client-api/session');
  const isAuth = !!r.json?.authenticated;
  const hasBackend = !!r.json?.backendToken;
  const user = r.json?.user;

  const [courses, teachers, students, subjects] = await Promise.all([
    apiList('/client-api/courses'),
    apiList('/client-api/teachers'),
    apiList('/client-api/students'),
    apiList('/client-api/subjects'),
  ]);

  const errors = [courses, teachers, students, subjects].filter((x) => !x.ok).map((x) => x.error);
  const notice = errors.length ? errors[0] : (!hasBackend ? 'Missing backend token. Logout and login again.' : '');

  const stats = [
    { label: 'Courses', path: '/courses', count: courses.items.length },
    { label: 'Teachers', path: '/teachers', count: teachers.items.length },
    { label: 'Students', path: '/students', count: students.items.length },
    { label: 'Subjects', path: '/subjects', count: subjects.items.length },
  ];

  const content = `
    <div class="space-y-3 text-sm">
      <div>Auth status: <span class="font-medium">${isAuth ? 'Authenticated' : 'Not authenticated'}</span></div>
      ${user ? `<div>User: <span class="font-medium">${escapeHtml(user.email)}</span></div>` : ''}

      <div class="grid gap-2 pt-1 md:grid-cols-3">
        <a class="rounded bg-zinc-900 px-3 py-2 text-center text-sm font-medium text-white hover:bg-zinc-800" href="/students">Mostrar alumnos</a>
        <a class="rounded bg-zinc-900 px-3 py-2 text-center text-sm font-medium text-white hover:bg-zinc-800" href="/teachers">Mostrar teachers</a>
        <a class="rounded bg-zinc-900 px-3 py-2 text-center text-sm font-medium text-white hover:bg-zinc-800" href="/subjects">Mostrar subjects</a>
      </div>

      <div class="grid gap-3 pt-2 md:grid-cols-2">
        ${stats.map((s) => `
          <a class="rounded border border-zinc-200 bg-white px-3 py-2 hover:bg-zinc-50" href="${escapeHtml(s.path)}">
            <div class="text-xs text-zinc-500">${escapeHtml(s.label)}</div>
            <div class="text-lg font-semibold">${escapeHtml(String(s.count))}</div>
          </a>
        `).join('')}
      </div>
    </div>
  `;
  return layout({ title: 'Dashboard', content, notice });
}

async function pageCourses() {
  const r = await apiList('/client-api/courses');
  const items = r.items;

  const content = `
    <form id="createCourse" class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-8">
      <input name="name" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" placeholder="Course name" required />
      <input name="startDate" type="date" class="rounded border border-zinc-300 px-3 py-2 text-sm" required />
      <input name="endDate" type="date" class="rounded border border-zinc-300 px-3 py-2 text-sm" required />
      <input name="description" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" placeholder="Description (optional)" />
      <button class="rounded bg-zinc-900 px-3 py-2 text-sm text-white hover:bg-zinc-800 md:col-span-2" type="submit">Create</button>
    </form>
    ${table(items, [
      { label: 'ID', render: (c) => `<span class="font-mono text-xs">${escapeHtml(c.id)}</span>` },
      { label: 'Name', render: (c) => escapeHtml(c.name) },
      { label: 'Description', render: (c) => escapeHtml(String(c.description || '').slice(0, 60)) },
      { label: 'Dates', render: (c) => `${escapeHtml(c.startDate)} → ${escapeHtml(c.endDate)}` },
      { label: 'Active', render: (c) => c.isActive ? '<span class="text-xs text-green-700">Yes</span>' : '<span class="text-xs text-zinc-500">No</span>' },
      { label: 'Actions', render: (c) => `${actionBtn('Edit','secondary')} ${actionBtn('Delete','danger')}` },
    ])}
  `;

  const root = layout({ title: 'Courses', content, notice: r.ok ? '' : r.error });
  await updateAuthBadge(root);

  root.querySelector('#createCourse').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const payload = Object.fromEntries(fd.entries());
    const cr = await api('/client-api/courses', { method: 'POST', body: payload });
    if (!cr.ok) alert(cr.json?.error || `Error ${cr.status}`);
    navigate('/courses', { replace: true });
  });

  root.querySelectorAll('tbody tr').forEach((tr, idx) => {
    const item = items[idx];
    if (!item) return;
    tr.querySelectorAll('button[data-action]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const act = btn.getAttribute('data-action');
        if (act === 'Edit') {
          const name = prompt('Course name:', item.name);
          if (name === null) return;
          const startDate = prompt('Start date (YYYY-MM-DD):', ensureYmd(item.startDate));
          if (startDate === null) return;
          const endDate = prompt('End date (YYYY-MM-DD):', ensureYmd(item.endDate));
          if (endDate === null) return;
          const description = prompt('Description (optional):', item.description || '');
          if (description === null) return;
          const ur = await api(`/client-api/courses/${item.id}`, { method: 'PUT', body: { name, startDate, endDate, description } });
          if (!ur.ok) alert(ur.json?.error || `Error ${ur.status}`);
          navigate('/courses', { replace: true });
        }
        if (act === 'Delete') {
          if (!confirm('Delete course?')) return;
          const dr = await api(`/client-api/courses/${item.id}`, { method: 'DELETE' });
          if (!dr.ok) alert(dr.json?.error || `Error ${dr.status}`);
          navigate('/courses', { replace: true });
        }
      });
    });
  });

  return root;
}

async function pageTeachers() {
  const r = await apiList('/client-api/teachers');
  const items = r.items;
  const subj = await apiList('/client-api/subjects');
  const subjects = subj.items;

  const subjectNameById = new Map(subjects.map((s) => [String(s.id), String(s.name)]));

  const subjectOptions = subjects.map((s) => `<option value="${escapeHtml(s.id)}">${escapeHtml(s.name)} (${escapeHtml(s.id)})</option>`).join('');

  const content = `
    <form id="createTeacher" class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-5">
      <input name="name" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" placeholder="Teacher name" required />
      <input name="email" type="email" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" placeholder="teacher@example.com" required />
      <button class="rounded bg-zinc-900 px-3 py-2 text-sm text-white hover:bg-zinc-800" type="submit">Create</button>
    </form>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
      <div class="text-sm text-zinc-600">
        Assign/Unassign requires an existing subject.
      </div>
      <select id="assignSubjectId" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2">
        <option value="">Select subject...</option>
        ${subjectOptions}
      </select>
    </div>

    ${table(items, [
      { label: 'ID', render: (t) => `<span class="font-mono text-xs">${escapeHtml(t.id)}</span>` },
      { label: 'Name', render: (t) => escapeHtml(t.name) },
      { label: 'Email', render: (t) => escapeHtml(t.email) },
      { label: 'Subjects', render: (t) => {
        const ids = Array.isArray(t.subjectIds) ? t.subjectIds.map((x) => String(x)) : [];
        if (ids.length === 0) return '<span class="text-xs text-zinc-500">None</span>';
        const names = ids.map((id) => subjectNameById.get(id) || id);
        return `<span class="text-xs">${escapeHtml(names.join(', '))}</span>`;
      } },
      { label: 'Actions', render: () => `${actionBtn('Edit')} ${actionBtn('Assign','primary')} ${actionBtn('Unassign')} ${actionBtn('Delete','danger')}` },
    ])}
  `;

  const notice = !r.ok ? r.error : (!subj.ok ? subj.error : '');
  const root = layout({ title: 'Teachers', content, notice });
  await updateAuthBadge(root);

  root.querySelector('#createTeacher').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const payload = Object.fromEntries(fd.entries());
    const cr = await api('/client-api/teachers', { method: 'POST', body: payload });
    if (!cr.ok) alert(cr.json?.error || `Error ${cr.status}`);
    navigate('/teachers', { replace: true });
  });

  root.querySelectorAll('tbody tr').forEach((tr, idx) => {
    const item = items[idx];
    if (!item) return;
    tr.querySelectorAll('button[data-action]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const act = btn.getAttribute('data-action');
        if (act === 'Edit') {
          const name = prompt('Teacher name:', item.name);
          if (name === null) return;
          const email = prompt('Teacher email:', item.email);
          if (email === null) return;
          const ur = await api(`/client-api/teachers/${item.id}`, { method: 'PUT', body: { name, email } });
          if (!ur.ok) alert(ur.json?.error || `Error ${ur.status}`);
          navigate('/teachers', { replace: true });
        }
        if (act === 'Assign') {
          const subjectId = root.querySelector('#assignSubjectId').value;
          if (!subjectId) { alert('Select a subject first'); return; }
          const ar = await api(`/client-api/teachers/${item.id}/assign`, { method: 'POST', body: { subjectId } });
          if (!ar.ok) alert(ar.json?.error || `Error ${ar.status}`);
          navigate('/subjects');
        }
        if (act === 'Unassign') {
          const subjectId = root.querySelector('#assignSubjectId').value;
          if (!subjectId) { alert('Select a subject first'); return; }
          const ar = await api(`/client-api/teachers/${item.id}/unassign`, { method: 'POST', body: { subjectId } });
          if (!ar.ok) alert(ar.json?.error || `Error ${ar.status}`);
          navigate('/subjects');
        }
        if (act === 'Delete') {
          if (!confirm('Delete teacher?')) return;
          const dr = await api(`/client-api/teachers/${item.id}`, { method: 'DELETE' });
          if (!dr.ok) alert(dr.json?.error || `Error ${dr.status}`);
          navigate('/teachers', { replace: true });
        }
      });
    });
  });

  return root;
}

async function pageStudents() {
  const r = await apiList('/client-api/students');
  const items = r.items;
  const cr = await apiList('/client-api/courses');
  const courses = cr.items;

  const courseNameById = new Map(courses.map((c) => [String(c.id), String(c.name)]));

  const courseOptions = courses.map((c) => `<option value="${escapeHtml(c.id)}">${escapeHtml(c.name)} (${escapeHtml(c.id)})</option>`).join('');

  const content = `
    <form id="createStudent" class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-5">
      <input name="name" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" placeholder="Student name" required />
      <input name="email" type="email" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" placeholder="student@example.com" required />
      <button class="rounded bg-zinc-900 px-3 py-2 text-sm text-white hover:bg-zinc-800" type="submit">Create</button>
    </form>

    <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
      <div class="text-sm text-zinc-600">
        Enroll requires an existing course.
      </div>
      <select id="enrollCourseId" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2">
        <option value="">Select course...</option>
        ${courseOptions}
      </select>
    </div>

    ${table(items, [
      { label: 'ID', render: (s) => `<span class="font-mono text-xs">${escapeHtml(s.id)}</span>` },
      { label: 'Name', render: (s) => escapeHtml(s.name) },
      { label: 'Email', render: (s) => escapeHtml(s.email) },
      { label: 'Courses', render: (s) => {
        const ids = Array.isArray(s.activeCourseIds) ? s.activeCourseIds.map((x) => String(x)) : [];
        if (ids.length === 0) return '<span class="text-xs text-zinc-500">None</span>';
        const names = ids.map((id) => courseNameById.get(id) || id);
        return `<span class="text-xs">${escapeHtml(names.join(', '))}</span>`;
      } },
      { label: 'Actions', render: () => `${actionBtn('Edit')} ${actionBtn('Enroll','primary')} ${actionBtn('Delete','danger')}` },
    ])}
  `;

  const notice = !r.ok ? r.error : (!cr.ok ? cr.error : '');
  const root = layout({ title: 'Students', content, notice });
  await updateAuthBadge(root);

  root.querySelector('#createStudent').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const payload = Object.fromEntries(fd.entries());
    const res = await api('/client-api/students', { method: 'POST', body: payload });
    if (!res.ok) alert(res.json?.error || `Error ${res.status}`);
    navigate('/students', { replace: true });
  });

  root.querySelectorAll('tbody tr').forEach((tr, idx) => {
    const item = items[idx];
    if (!item) return;
    tr.querySelectorAll('button[data-action]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const act = btn.getAttribute('data-action');
        if (act === 'Edit') {
          const name = prompt('Student name:', item.name);
          if (name === null) return;
          const email = prompt('Student email:', item.email);
          if (email === null) return;
          const ur = await api(`/client-api/students/${item.id}`, { method: 'PUT', body: { name, email } });
          if (!ur.ok) alert(ur.json?.error || `Error ${ur.status}`);
          navigate('/students', { replace: true });
        }
        if (act === 'Enroll') {
          const courseId = root.querySelector('#enrollCourseId').value;
          if (!courseId) { alert('Select a course first'); return; }
          const er = await api(`/client-api/students/${item.id}/enroll`, { method: 'POST', body: { courseId } });
          if (!er.ok) alert(er.json?.error || `Error ${er.status}`);
          alert('Enrolled');
        }
        if (act === 'Delete') {
          if (!confirm('Delete student?')) return;
          const dr = await api(`/client-api/students/${item.id}`, { method: 'DELETE' });
          if (!dr.ok) alert(dr.json?.error || `Error ${dr.status}`);
          navigate('/students', { replace: true });
        }
      });
    });
  });

  return root;
}

async function pageSubjects() {
  const r = await apiList('/client-api/subjects');
  const items = r.items;
  const cr = await apiList('/client-api/courses');
  const courses = cr.items;

  const courseOptions = courses.map((c) => `<option value="${escapeHtml(c.id)}">${escapeHtml(c.name)} (${escapeHtml(c.id)})</option>`).join('');

  const content = `
    <form id="createSubject" class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-6">
      <input name="name" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-3" placeholder="Subject name" required />
      <select name="courseId" class="rounded border border-zinc-300 px-3 py-2 text-sm md:col-span-2" required>
        <option value="">Select course...</option>
        ${courseOptions}
      </select>
      <button class="rounded bg-zinc-900 px-3 py-2 text-sm text-white hover:bg-zinc-800" type="submit">Create</button>
    </form>

    ${table(items, [
      { label: 'ID', render: (s) => `<span class="font-mono text-xs">${escapeHtml(s.id)}</span>` },
      { label: 'Name', render: (s) => escapeHtml(s.name) },
      { label: 'Course', render: (s) => `<span class="font-mono text-xs">${escapeHtml(s.courseId)}</span>` },
      { label: 'Teachers', render: (s) => Array.isArray(s.teacherIds) && s.teacherIds.length
        ? `<span class="font-mono text-xs">${escapeHtml(s.teacherIds.join(', '))}</span>`
        : '<span class="text-xs text-zinc-500">None</span>' },
      { label: 'Actions', render: () => `${actionBtn('Edit')} ${actionBtn('Delete','danger')}` },
    ])}
  `;

  const notice = !r.ok ? r.error : (!cr.ok ? cr.error : '');
  const root = layout({ title: 'Subjects', content, notice });
  await updateAuthBadge(root);

  root.querySelector('#createSubject').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const payload = Object.fromEntries(fd.entries());
    const res = await api('/client-api/subjects', { method: 'POST', body: payload });
    if (!res.ok) alert(res.json?.error || `Error ${res.status}`);
    navigate('/subjects', { replace: true });
  });

  root.querySelectorAll('tbody tr').forEach((tr, idx) => {
    const item = items[idx];
    if (!item) return;
    tr.querySelectorAll('button[data-action]').forEach((btn) => {
      btn.addEventListener('click', async () => {
        const act = btn.getAttribute('data-action');
        if (act === 'Edit') {
          const name = prompt('Subject name:', item.name);
          if (name === null) return;
          const courseId = prompt('Course id:', item.courseId);
          if (courseId === null) return;
          const ur = await api(`/client-api/subjects/${item.id}`, { method: 'PUT', body: { name, courseId } });
          if (!ur.ok) alert(ur.json?.error || `Error ${ur.status}`);
          navigate('/subjects', { replace: true });
        }
        if (act === 'Delete') {
          if (!confirm('Delete subject?')) return;
          const dr = await api(`/client-api/subjects/${item.id}`, { method: 'DELETE' });
          if (!dr.ok) alert(dr.json?.error || `Error ${dr.status}`);
          navigate('/subjects', { replace: true });
        }
      });
    });
  });

  return root;
}

async function getSession() {
  return await api('/client-api/session');
}

async function render() {
  const route = getRoute();

  const s = await getSession();
  const authed = !!s.json?.authenticated;
  if (!s.ok || !authed) {
    window.location.href = '/login';
    return el('<div></div>');
  }

  if (!s.json?.backendToken) {
    return layout({
      title: 'Dashboard',
      notice: 'Missing backend token. Logout and login again.',
      content: '<div class="text-sm text-zinc-600">No es pot carregar dades del backend sense el token.</div>',
    });
  }

  if (route === '/dashboard') return await pageDashboard();
  if (route === '/courses') return await pageCourses();
  if (route === '/teachers') return await pageTeachers();
  if (route === '/students') return await pageStudents();
  if (route === '/subjects') return await pageSubjects();

  history.replaceState({}, '', '/dashboard');
  return await pageDashboard();
}

function mount(app, node) {
  app.innerHTML = '';
  app.appendChild(node);
}

async function boot() {
  if (window.location.pathname === '/') {
    history.replaceState({}, '', '/dashboard');
  }
  const app = document.getElementById('app');
  if (!app) return;
  mount(app, await render());
}

window.addEventListener('popstate', boot);

document.addEventListener('click', (e) => {
  if (e.defaultPrevented) return;
  if (e.button !== 0) return;
  if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

  const a = e.target?.closest?.('a');
  if (!a) return;
  if (a.target && a.target !== '_self') return;

  const href = a.getAttribute('href');
  if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

  const url = new URL(href, window.location.href);
  if (url.origin !== window.location.origin) return;

  e.preventDefault();
  navigate(url.pathname + url.search);
});

boot();
//
