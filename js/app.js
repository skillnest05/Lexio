/* =====================================
   LEXIO — app.js
   Handles auth + email generation logic
===================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* ─────────── AUTH PAGES ─────────── */
    const loginForm    = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showRegister = document.getElementById('showRegister');
    const showLogin    = document.getElementById('showLogin');

    if (showRegister) {
        showRegister.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('loginBox').classList.add('d-none');
            document.getElementById('registerBox').classList.remove('d-none');
        });
    }

    if (showLogin) {
        showLogin.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('registerBox').classList.add('d-none');
            document.getElementById('loginBox').classList.remove('d-none');
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = loginForm.querySelector('button[type=submit]');
            const err = document.getElementById('loginError');
            setLoading(btn, true);
            err.style.display = 'none';

            const fd = new FormData(loginForm);
            fd.append('action', 'login');

            const data = await postJSON('api/auth.php', fd);
            setLoading(btn, false);

            if (data.success) {
                window.location.href = 'dashboard.php';
            } else {
                showAlert(err, data.message);
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = registerForm.querySelector('button[type=submit]');
            const err = document.getElementById('registerError');
            setLoading(btn, true);
            err.style.display = 'none';

            const fd = new FormData(registerForm);
            fd.append('action', 'register');

            const data = await postJSON('api/auth.php', fd);
            setLoading(btn, false);

            if (data.success) {
                showAlert(err, data.message, 'success');
                registerForm.reset();
                setTimeout(() => document.getElementById('showLogin').click(), 1500);
            } else {
                showAlert(err, data.message, 'error');
            }
        });
    }

    /* ─────────── DASHBOARD ─────────── */
    const generateBtn = document.getElementById('generateBtn');

    if (generateBtn) {
        // Tone chips
        document.querySelectorAll('.tone-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.tone-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                document.getElementById('selectedTone').value = chip.dataset.value;
            });
        });

        // Length chips
        document.querySelectorAll('.length-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.length-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                document.getElementById('selectedLength').value = chip.dataset.value;
            });
        });

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            const fd = new FormData();
            fd.append('action', 'logout');
            await postJSON('api/auth.php', fd);
            window.location.href = 'index.php';
        });

        // Load history on init
        loadHistory();

        // Generate email
        generateBtn.addEventListener('click', async () => {
            const description = document.getElementById('description').value.trim();
            const genError    = document.getElementById('genError');
            genError.style.display = 'none';

            if (!description) {
                showAlert(genError, 'Please describe what your email should say.', 'error');
                return;
            }

            const fd = new FormData();
            fd.append('sender_name',    document.getElementById('senderName').value.trim());
            fd.append('recipient_name', document.getElementById('recipientName').value.trim());
            fd.append('tone',           document.getElementById('selectedTone').value);
            fd.append('length',         document.getElementById('selectedLength').value);
            fd.append('description',    description);

            setLoading(generateBtn, true, 'Generating…');
            const data = await postJSON('api/generate_email.php', fd);
            setLoading(generateBtn, false, '<i class="bi bi-stars"></i> Generate Email');

            if (data.success) {
                // Populate output panel
                document.getElementById('subjectText').textContent  = data.subject || '(No subject generated)';
                document.getElementById('emailBodyOutput').value     = data.body;

                // Scroll to output on mobile
                document.getElementById('outputPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });

                loadHistory();
            } else {
                showAlert(genError, data.message || 'An error occurred. Please try again.', 'error');
            }
        });

        // Copy subject
        document.getElementById('copySubjectBtn').addEventListener('click', () => {
            const subject = document.getElementById('subjectText').textContent;
            copyToClipboard(subject, document.getElementById('copySubjectBtn'));
        });

        // Copy body
        document.getElementById('copyBodyBtn').addEventListener('click', () => {
            const body = document.getElementById('emailBodyOutput').value;
            copyToClipboard(body, document.getElementById('copyBodyBtn'));
        });

        // Copy full email (subject + body)
        document.getElementById('copyAllBtn').addEventListener('click', () => {
            const subject = document.getElementById('subjectText').textContent;
            const body    = document.getElementById('emailBodyOutput').value;
            const full    = `Subject: ${subject}\n\n${body}`;
            copyToClipboard(full, document.getElementById('copyAllBtn'));
        });
    }
});

/* ─────────── HISTORY ─────────── */
async function loadHistory() {
    const container = document.getElementById('historyContainer');
    if (!container) return;

    const data = await postJSON('api/fetch_history.php', new FormData());

    if (!data.success || data.history.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-envelope-open"></i>
                <p class="small mb-0">No drafts yet.<br>Generate your first email!</p>
            </div>`;
        return;
    }

    container.innerHTML = '';
    data.history.forEach(item => {
        const div = document.createElement('div');
        div.className = 'history-item';

        const date = new Date(item.created_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short' });
        const subject = item.subject_line || 'No subject';
        const to = item.recipient_name ? `To: ${item.recipient_name}` : '';

        div.innerHTML = `
            <div class="h-subject">${escapeHtml(subject)}</div>
            <div class="h-meta">
                <span class="tone-badge">${escapeHtml(item.tone)}</span>
                ${to ? `<span>${escapeHtml(to)}</span>` : ''}
                <span>${date}</span>
            </div>`;

        div.addEventListener('click', () => {
            document.getElementById('subjectText').textContent = subject;
            document.getElementById('emailBodyOutput').value   = item.generated_email;
            document.getElementById('senderName').value        = item.sender_name    || '';
            document.getElementById('recipientName').value     = item.recipient_name || '';
            document.getElementById('description').value       = item.prompt_text    || '';

            // Restore tone chip
            document.querySelectorAll('.tone-chip').forEach(c => {
                c.classList.toggle('active', c.dataset.value === item.tone);
            });
            document.getElementById('selectedTone').value = item.tone;

            // Restore length chip
            document.querySelectorAll('.length-chip').forEach(c => {
                c.classList.toggle('active', c.dataset.value === item.length);
            });
            document.getElementById('selectedLength').value = item.length;
        });

        container.appendChild(div);
    });
}

/* ─────────── HELPERS ─────────── */
async function postJSON(url, formData) {
    try {
        const res  = await fetch(url, { method: 'POST', body: formData });
        return await res.json();
    } catch {
        return { success: false, message: 'Network error. Please check your connection.' };
    }
}

function setLoading(btn, loading, label = null) {
    if (loading) {
        btn.disabled = true;
        btn.dataset.original = btn.innerHTML;
        btn.innerHTML = `<span class="spinner" style="display:inline-block"></span> ${label || 'Please wait…'}`;
    } else {
        btn.disabled = false;
        btn.innerHTML = label || btn.dataset.original;
    }
}

function showAlert(el, msg, type = 'error') {
    el.className = `alert-banner ${type}`;
    el.textContent = msg;
    el.style.display = 'block';
}

function copyToClipboard(text, btn) {
    if (!text.trim()) return;
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.innerHTML;
        btn.classList.add('success-flash');
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
        setTimeout(() => {
            btn.classList.remove('success-flash');
            btn.innerHTML = orig;
        }, 1800);
    });
}

function escapeHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
