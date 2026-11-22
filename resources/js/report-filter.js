const form = document.querySelector('[data-report-filter]');
const results = document.querySelector('[data-report-results]');
const status = document.querySelector('[data-report-filter-status]');

if (form && results && status) {
    const type = form.elements.namedItem('type');
    let appliedType = type.value;
    let pendingRequest;

    async function updateReports(url) {
        pendingRequest?.abort();
        const request = new AbortController();
        pendingRequest = request;
        results.setAttribute('aria-busy', 'true');
        status.textContent = 'Loading reports…';

        try {
            const response = await fetch(url, {
                headers: { 'X-Report-Results': '1' },
                signal: request.signal,
            });
            if (!response.ok || response.redirected) throw new Error('Reports request failed');

            const html = await response.text();
            if (request.signal.aborted) return;

            results.innerHTML = html;
            appliedType = url.searchParams.get('type') ?? '';
            type.value = appliedType;
            window.history.replaceState(window.history.state, '', url);
            status.textContent = 'Reports updated.';
        } catch (error) {
            if (request.signal.aborted) return;

            type.value = appliedType;
            status.textContent = 'Could not update reports. Please try again.';
        } finally {
            if (pendingRequest === request) {
                results.setAttribute('aria-busy', 'false');
            }
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const url = new URL(form.action);
        if (type.value) url.searchParams.set('type', type.value);
        updateReports(url);
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('[data-report-pagination] a[href]');
        if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        event.preventDefault();
        updateReports(new URL(link.href));
    });
}
