const form = document.querySelector('[data-inventory-search]');
const results = document.querySelector('[data-inventory-results]');
const header = document.querySelector('[data-inventory-header]');

if (form && results && header) {
    let timer;
    let pendingRequest;

    async function updateResults() {
        const url = new URL(form.action);
        for (const [name, value] of new FormData(form)) {
            if (value !== '') url.searchParams.set(name, value);
        }

        const request = new AbortController();
        pendingRequest = request;
        try {
            const response = await fetch(url, { signal: request.signal });
            if (!response.ok || response.redirected) throw new Error('Inventory request failed');

            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (request.signal.aborted) return;

            const nextResults = page.querySelector('[data-inventory-results]');
            const nextHeader = page.querySelector('[data-inventory-header]');
            if (!nextResults || !nextHeader) throw new Error('Inventory response is incomplete');

            results.replaceChildren(...nextResults.childNodes);
            header.replaceChildren(...nextHeader.childNodes);
            window.history.replaceState(window.history.state, '', url);
        } catch (error) {
            if (!request.signal.aborted) window.location.assign(url);
        } finally {
            if (pendingRequest === request && !request.signal.aborted) {
                results.setAttribute('aria-busy', 'false');
            }
        }
    }

    function scheduleUpdate(delay) {
        clearTimeout(timer);
        pendingRequest?.abort();
        results.setAttribute('aria-busy', 'true');
        timer = setTimeout(updateResults, delay);
    }

    form.addEventListener('input', (event) => {
        if (event.target.matches('input[type="search"]') && !event.isComposing) {
            scheduleUpdate(300);
        }
    });
    form.addEventListener('change', (event) => {
        if (event.target.matches('select')) scheduleUpdate(0);
    });
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        scheduleUpdate(0);
    });
}
