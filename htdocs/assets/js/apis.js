/**
 * apis.js — Modern Framework AJAX Helpers (Aether Catalyst)
 * =========================================================
 * Provides the ApiClient object that wraps fetch requests
 * following the /api/{namespace}/{method} routing pattern.
 */

const ApiClient = (() => {

    const getBase = () => {
        const base = window.APP_BASE_PATH || '/staff-verifier/htdocs/';
        return base.replace(/\/?$/, '/') + 'api/';
    };

    /**
     * Core fetch wrapper.
     * @param {'GET'|'POST'|'DELETE'} method
     * @param {string} namespace  API namespace folder (e.g. 'auth', 'verify')
     * @param {string} action     Action name (e.g. 'colleges', 'login', 'update')
     * @param {Object} [payload]  Data object
     * @returns {Promise<Object>} Parsed JSON response
     */
    async function _request(method, namespace, action, payload = {}) {
        let url = `${getBase()}${encodeURIComponent(namespace)}/${encodeURIComponent(action)}`;

        const options = {
            method,
            credentials: 'same-origin',
        };

        if (method === 'GET') {
            if (payload && Object.keys(payload).length > 0) {
                const params = new URLSearchParams(payload).toString();
                url += (url.includes('?') ? '&' : '?') + params;
            }
        } else if (method === 'POST') {
            const formData = new FormData();
            Object.entries(payload).forEach(([k, v]) => {
                if (v !== undefined && v !== null) {
                    formData.append(k, typeof v === 'object' ? JSON.stringify(v) : v);
                }
            });
            options.body = formData;
        }

        const response = await fetch(url, options);
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const err = new Error(data.message || data.error || `HTTP ${response.status}`);
            err.status = response.status;
            err.data   = data;
            throw err;
        }

        return data;
    }

    return {
        /** GET /api/{namespace}/{action} */
        get:    (ns, action, params = {}) => _request('GET',  ns, action, params),
        /** POST /api/{namespace}/{action} */
        post:   (ns, action, payload = {}) => _request('POST', ns, action, payload),
        /** DELETE /api/{namespace}/{action} */
        delete: (ns, action, payload = {}) => _request('DELETE', ns, action, payload),
    };
})();

/* ─── Auth Helpers ──────────────────────────────────────── */

async function apiLogin(user, password) {
    try {
        const data = await ApiClient.post('auth', 'login', { user, password });
        if (typeof toast !== 'undefined' && toast.success) {
            toast.success('Welcome back!', data.message || 'Logged in successfully.');
        }
        const redirect = window.APP_BASE_PATH || '/staff-verifier/htdocs/';
        setTimeout(() => { window.location.href = redirect; }, 600);
    } catch (err) {
        if (typeof toast !== 'undefined' && toast.error) {
            toast.error('Login Failed', err.message || 'Invalid credentials.');
        } else {
            alert(err.message || 'Invalid credentials.');
        }
    }
}

async function apiSignup(fields) {
    try {
        const data = await ApiClient.post('auth', 'signup', fields);
        if (typeof toast !== 'undefined' && toast.success) {
            toast.success('Account Created!', data.message || 'Please log in.');
        }
        const redirect = (window.APP_BASE_PATH || '/staff-verifier/htdocs/').replace(/\/?$/, '/') + 'login';
        setTimeout(() => { window.location.href = redirect; }, 800);
    } catch (err) {
        if (typeof toast !== 'undefined' && toast.error) {
            toast.error('Registration Failed', err.message || 'Try a different username.');
        } else {
            alert(err.message || 'Try a different username.');
        }
    }
}

/* ─── DOM-Ready Init ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const user     = document.getElementById('login-user').value.trim();
            const password = document.getElementById('login-password').value;
            apiLogin(user, password);
        });
    }

    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            apiSignup({
                username:      document.getElementById('signup-username').value.trim(),
                password:      document.getElementById('signup-password').value,
                email_address: document.getElementById('signup-email').value.trim(),
                phone:         document.getElementById('signup-phone').value.trim(),
            });
        });
    }
});
