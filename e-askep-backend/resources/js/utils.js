/**
 * Generate route URL (mirrors Laravel route() helper).
 * Since we're using Inertia, we typically use the href prop directly.
 * This is a simple helper for static route construction.
 */
export function route(name, params = {}) {
    const routes = {
        'welcome':            '/',
        'login':              '/login',
        'register':           '/register',
        'logout':             '/logout',
        'dosen.dashboard':    '/dosen/dashboard',
        'dosen.review':       (p) => `/dosen/review/${p.uuid}`,
        'mahasiswa.dashboard':'/mahasiswa/dashboard',
        'mahasiswa.create':   '/mahasiswa/kasus/baru',
        'mahasiswa.show':     (p) => `/mahasiswa/kasus/${p.uuid}`,
        'admin.dashboard':    '/admin/dashboard',
        'admin.courses':      '/admin/courses',
        'admin.groups':       '/admin/groups',
        'admin.master3s':     '/admin/master-3s',
    };

    const r = routes[name];
    if (typeof r === 'function') return r(params);
    return r || '/';
}

/**
 * Format triage category label
 */
export function triageLabel(cat) {
    const map = {
        merah: { label: 'MERAH', bg: 'bg-red-100', text: 'text-red-700', dot: 'bg-red-500' },
        kuning: { label: 'KUNING', bg: 'bg-yellow-100', text: 'text-yellow-700', dot: 'bg-yellow-400' },
        hijau: { label: 'HIJAU', bg: 'bg-green-100', text: 'text-green-700', dot: 'bg-green-500' },
        hitam: { label: 'HITAM', bg: 'bg-gray-200', text: 'text-gray-800', dot: 'bg-gray-800' },
    };
    return map[cat] || map.hijau;
}

/**
 * Format session status label
 */
export function statusLabel(status) {
    const map = {
        draft: { label: 'Draft', bg: 'bg-slate-100', text: 'text-slate-600' },
        submitted: { label: 'Menunggu Telaah', bg: 'bg-blue-100', text: 'text-blue-700' },
        need_revision: { label: 'Revisi', bg: 'bg-orange-100', text: 'text-orange-700' },
        approved_graded: { label: 'Disetujui & Dinilai', bg: 'bg-emerald-100', text: 'text-emerald-700' },
    };
    return map[status] || map.draft;
}

/**
 * Format date to Indonesian locale
 */
export function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
}

export function formatDateTime(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}
