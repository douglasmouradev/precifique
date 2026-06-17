/**
 * Assistente de IA do painel tenant — JS puro.
 */
export function initTenantAiAssistant() {
    const panel = document.getElementById('tenant-ai-assistant');
    const openButton = document.querySelector('[data-ai-open]');

    if (!panel || !openButton) {
        return;
    }

    const closeButton = panel.querySelector('[data-ai-close]');
    const input = panel.querySelector('[data-ai-question]');
    const sendButton = panel.querySelector('[data-ai-send]');
    const answer = panel.querySelector('[data-ai-answer]');
    const loading = panel.querySelector('[data-ai-loading]');
    const sendLabel = panel.querySelector('[data-ai-send-label]');
    const sendBusy = panel.querySelector('[data-ai-send-busy]');

    const chatUrl = panel.dataset.chatUrl || '';
    const csrf = panel.dataset.csrf || '';
    const noAnswer = panel.dataset.noAnswer || 'Sem resposta.';
    const errorMessage = panel.dataset.error || 'Erro ao consultar a IA.';

    let isOpen = false;
    let isLoading = false;

    const setOpen = (next) => {
        isOpen = next;
        panel.classList.toggle('hidden', !isOpen);
        panel.setAttribute('aria-hidden', String(!isOpen));

        if (!isOpen && input) {
            input.blur();
        }
    };

    const setLoading = (next) => {
        isLoading = next;

        if (loading) {
            loading.classList.toggle('hidden', !isLoading);
        }

        if (sendLabel) {
            sendLabel.classList.toggle('hidden', isLoading);
        }

        if (sendBusy) {
            sendBusy.classList.toggle('hidden', !isLoading);
        }

        if (input) {
            input.disabled = isLoading;
        }

        if (sendButton) {
            sendButton.disabled = isLoading;
        }
    };

    const showAnswer = (text) => {
        if (!answer) {
            return;
        }

        if (text) {
            answer.textContent = text;
            answer.classList.remove('hidden');
        } else {
            answer.textContent = '';
            answer.classList.add('hidden');
        }
    };

    const sendQuestion = async () => {
        if (!input || !chatUrl || isLoading) {
            return;
        }

        const question = input.value.trim();
        if (!question) {
            return;
        }

        setLoading(true);

        try {
            const response = await fetch(chatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ question }),
            });

            if (!response.ok) {
                throw new Error('request failed');
            }

            const data = await response.json();
            showAnswer(data.answer || noAnswer);
        } catch (_) {
            window.toast?.error(errorMessage);
        } finally {
            setLoading(false);
        }
    };

    openButton.addEventListener('click', (event) => {
        event.preventDefault();
        setOpen(!isOpen);
    });

    closeButton?.addEventListener('click', (event) => {
        event.preventDefault();
        setOpen(false);
    });

    sendButton?.addEventListener('click', (event) => {
        event.preventDefault();
        sendQuestion();
    });

    input?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !isLoading) {
            event.preventDefault();
            sendQuestion();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            setOpen(false);
        }
    });
}
