import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

// Initialize Laravel Echo with Reverb
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY ?? 'laravel-reverb-key',
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8081,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8081,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Listen for real-time events on authenticated user's private channel
if (window.Livewire) {
    window.Livewire.on('echo:subscribe', ({ userId }) => {
        window.Echo.private(`users.${userId}`)
            .listen('.FacilityReservationStatusChanged', (e) => {
                window.Livewire.dispatch('reservation-status-changed', e);
            })
            .listen('.EquipmentBorrowRequestStatusChanged', (e) => {
                window.Livewire.dispatch('borrow-request-status-changed', e);
            })
            .listen('.ServiceRequestStatusChanged', (e) => {
                window.Livewire.dispatch('service-request-status-changed', e);
            })
            .listen('.MaintenanceRequestStatusChanged', (e) => {
                window.Livewire.dispatch('maintenance-request-status-changed', e);
            })
            .listen('.NotificationCreated', (e) => {
                window.Livewire.dispatch('notification-created', e);
            });
    });
}

Alpine.start();
