/* Portail du Togo — script principal */
(function () {
    'use strict';

    /* ---------- Menu mobile ---------- */
    var toggle = document.querySelector('[data-nav-toggle]');
    var nav = document.querySelector('[data-nav]');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        });
    }

    /* ---------- Bouton retour en haut ---------- */
    var toTop = document.querySelector('[data-top]');
    if (toTop) {
        toTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        window.addEventListener('scroll', function () {
            toTop.classList.toggle('is-visible', window.scrollY > 400);
        }, { passive: true });
    }

    /* ---------- Confirmations de suppression ---------- */
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!window.confirm(el.getAttribute('data-confirm') || 'Confirmer cette action ?')) {
                e.preventDefault();
            }
        });
    });

    /* ---------- Auto-submit des filtres ---------- */
    document.querySelectorAll('[data-autosubmit]').forEach(function (el) {
        el.addEventListener('change', function () {
            if (el.form) { el.form.submit(); }
        });
    });

    /* ---------- Copie de lien ---------- */
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy') || window.location.href;
            var done = function () {
                var old = btn.getAttribute('data-label') || btn.textContent;
                btn.setAttribute('data-label', old);
                btn.textContent = 'Lien copié !';
                setTimeout(function () { btn.textContent = old; }, 1800);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(done).catch(function () {});
            } else {
                var t = document.createElement('textarea');
                t.value = text; document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); done(); } catch (e) {}
                document.body.removeChild(t);
            }
        });
    });

    /* ---------- Partage natif ---------- */
    document.querySelectorAll('[data-share]').forEach(function (btn) {
        if (!navigator.share) { btn.hidden = true; return; }
        btn.addEventListener('click', function () {
            navigator.share({
                title: btn.getAttribute('data-share-title') || document.title,
                url: window.location.href
            }).catch(function () {});
        });
    });

    /* ---------- Recherche instantanée ---------- */
    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    document.querySelectorAll('form[data-search]').forEach(function (form) {
        var input = form.querySelector('input[type="search"]');
        var box = form.querySelector('.search__suggest');
        var endpoint = form.getAttribute('data-endpoint');
        if (!input || !box || !endpoint) { return; }

        var timer = null;
        var controller = null;
        var items = [];
        var active = -1;

        function close() {
            box.hidden = true;
            box.innerHTML = '';
            input.setAttribute('aria-expanded', 'false');
            items = []; active = -1;
        }

        function setActive(i) {
            var links = box.querySelectorAll('a');
            links.forEach(function (a) { a.classList.remove('is-active'); });
            active = i;
            if (i >= 0 && links[i]) {
                links[i].classList.add('is-active');
                links[i].scrollIntoView({ block: 'nearest' });
            }
        }

        function render(results, q) {
            if (!results.length) {
                box.innerHTML = '<div class="suggest-empty">Aucun résultat pour « ' + escapeHtml(q) + ' »</div>';
                box.hidden = false;
                input.setAttribute('aria-expanded', 'true');
                items = []; active = -1;
                return;
            }
            var html = results.map(function (r) {
                return '<a role="option" href="' + escapeHtml(r.url) + '">'
                    + '<span class="suggest-type">' + escapeHtml(r.type_label || r.type) + '</span>'
                    + '<span>' + escapeHtml(r.label) + '</span></a>';
            }).join('');
            box.innerHTML = html;
            box.hidden = false;
            input.setAttribute('aria-expanded', 'true');
            items = results; active = -1;
        }

        input.addEventListener('input', function () {
            var q = input.value.trim();
            if (timer) { clearTimeout(timer); }
            if (q.length < 2) { close(); return; }
            timer = setTimeout(function () {
                if (controller) { controller.abort(); }
                controller = new AbortController();
                fetch(endpoint + '?q=' + encodeURIComponent(q), { signal: controller.signal })
                    .then(function (r) { return r.ok ? r.json() : { results: [] }; })
                    .then(function (data) { render(data.results || [], q); })
                    .catch(function () {});
            }, 180);
        });

        input.addEventListener('keydown', function (e) {
            if (box.hidden) { return; }
            var links = box.querySelectorAll('a');
            if (e.key === 'ArrowDown') {
                e.preventDefault(); setActive(active + 1 >= links.length ? 0 : active + 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault(); setActive(active - 1 < 0 ? links.length - 1 : active - 1);
            } else if (e.key === 'Enter') {
                if (active >= 0 && links[active]) { e.preventDefault(); links[active].click(); }
            } else if (e.key === 'Escape') {
                close();
            }
        });

        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) { close(); }
        });
        input.addEventListener('blur', function () { setTimeout(close, 200); });
    });

    /* ---------- Impression ---------- */
    document.querySelectorAll('[data-print]').forEach(function (btn) {
        btn.addEventListener('click', function () { window.print(); });
    });

    /* ---------- Année dynamique ---------- */
    document.querySelectorAll('[data-year]').forEach(function (el) {
        el.textContent = new Date().getFullYear();
    });
})();
