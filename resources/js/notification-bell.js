/**
 * Sino de notificações do painel tenant — JS puro.
 */
function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

export function initNotificationBell() {
    document.querySelectorAll('[data-notification-bell]').forEach((root) => {
        const toggle = root.querySelector('[data-notification-toggle]');
        const panel = root.querySelector('[data-notification-panel]');
        const list = root.querySelector('[data-notification-list]');
        const badge = root.querySelector('[data-notification-badge]');
        const markAll = root.querySelector('[data-notification-mark-all]');

        if (!toggle || !panel || !list || !badge || !markAll) {
            return;
        }

        const indexUrl = root.dataset.indexUrl || '';
        const streamUrl = root.dataset.streamUrl || '';
        const readAllUrl = root.dataset.readAllUrl || '';
        const readUrlTemplate = root.dataset.readUrlTemplate || '';
        const csrf = root.dataset.csrf || '';
        const emptyLabel = root.dataset.empty || 'Nenhuma notificação.';

        let open = false;
        let unread = 0;
        let items = [];
        let loading = false;
        let eventSource = null;

        const headers = () => ({
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
        });

        const setOpen = (next) => {
            open = next;
            panel.classList.toggle('hidden', !open);
            toggle.setAttribute('aria-expanded', String(open));
            if (open) {
                fetchNotifications();
            }
        };

        const updateBadge = () => {
            if (unread > 0) {
                badge.textContent = unread > 9 ? '9+' : String(unread);
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        };

        const renderList = () => {
            if (items.length === 0) {
                list.innerHTML = `<p class="p-4 text-sm text-slate-500 text-center">${escapeHtml(emptyLabel)}</p>`;

                return;
            }

            list.innerHTML = items.map((item) => {
                const isRead = Boolean(item.read_at);
                const href = item.action_url || '#';
                const rowClass = isRead ? 'opacity-70' : 'bg-brand/5';

                return `
                    <a
                        href="${escapeHtml(href)}"
                        data-notification-item
                        data-notification-id="${escapeHtml(item.id)}"
                        data-notification-read="${isRead ? '1' : '0'}"
                        class="block px-4 py-3 border-b border-slate-50 hover:bg-slate-50 text-sm ${rowClass}"
                    >
                        <p class="font-semibold text-ink">${escapeHtml(item.title || '')}</p>
                        <p class="text-slate-500 text-xs mt-0.5 line-clamp-2">${escapeHtml(item.body || '')}</p>
                    </a>
                `;
            }).join('');
        };

        const fetchNotifications = async () => {
            if (!indexUrl || loading) {
                return;
            }

            loading = true;
            try {
                const response = await fetch(indexUrl, { headers: { Accept: 'application/json' } });
                if (!response.ok) {
                    return;
                }
                const data = await response.json();
                unread = data.unread_count ?? 0;
                items = data.notifications ?? [];
                updateBadge();
                renderList();
            } catch (_) {
                /* ignora */
            } finally {
                loading = false;
            }
        };

        const markRead = async (id) => {
            const url = readUrlTemplate.replace('__ID__', String(id));
            if (!url || url.includes('__ID__')) {
                return;
            }

            await fetch(url, {
                method: 'PATCH',
                headers: headers(),
            });
            await fetchNotifications();
        };

        const markAllRead = async () => {
            if (!readAllUrl) {
                return;
            }

            await fetch(readAllUrl, {
                method: 'POST',
                headers: headers(),
            });
            await fetchNotifications();
        };

        const connectStream = () => {
            if (!streamUrl || !window.EventSource) {
                return;
            }

            eventSource?.close();
            eventSource = new EventSource(streamUrl);
            eventSource.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    if (data.unread_count !== undefined) {
                        unread = data.unread_count;
                        updateBadge();
                    }
                } catch (_) {
                    /* ignora */
                }
            };
            eventSource.onerror = () => {
                eventSource?.close();
                window.setTimeout(connectStream, 5000);
            };
        };

        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            setOpen(!open);
        });

        markAll.addEventListener('click', async (event) => {
            event.preventDefault();
            event.stopPropagation();
            await markAllRead();
        });

        list.addEventListener('click', async (event) => {
            const link = event.target.closest('[data-notification-item]');
            if (!link || link.dataset.notificationRead === '1') {
                return;
            }

            const id = link.dataset.notificationId;
            if (!id) {
                return;
            }

            event.preventDefault();
            await markRead(id);

            const href = link.getAttribute('href');
            if (href && href !== '#') {
                window.location.assign(href);
            }
        });

        document.addEventListener('click', (event) => {
            if (!open) {
                return;
            }
            if (root.contains(event.target)) {
                return;
            }
            setOpen(false);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                eventSource?.close();
            } else {
                connectStream();
                fetchNotifications();
            }
        });

        fetchNotifications();
        connectStream();
    });
}
