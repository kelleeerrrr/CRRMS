<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityReservationController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\EquipmentBorrowRequestController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ResourceRequestController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\MyLendingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // My Lending
    Route::prefix('my-lending')->name('my-lending.')->group(function () {
        Route::get('/requests', [MyLendingController::class, 'requests'])->name('requests');
        Route::get('/borrowing', [MyLendingController::class, 'borrowing'])->name('borrowing');
    });

    // Facilities
    Route::prefix('facilities')->name('facilities.')->group(function () {
        Route::get('/', [FacilityController::class, 'index'])->name('index');
        Route::get('/create', [FacilityController::class, 'create'])->name('create');
        Route::post('/', [FacilityController::class, 'store'])->name('store');
        
        // Facility Reservations
        Route::prefix('reservations')->name('reservations.')->group(function () {
            Route::get('/', [FacilityReservationController::class, 'index'])->name('index');
            Route::get('/create', [FacilityReservationController::class, 'create'])->name('create');
            Route::post('/', [FacilityReservationController::class, 'store'])->name('store');
            Route::get('/calendar', [FacilityReservationController::class, 'calendar'])->name('calendar');
            Route::get('/{reservation}', [FacilityReservationController::class, 'show'])->name('show');
            Route::get('/{reservation}/edit', [FacilityReservationController::class, 'edit'])->name('edit');
            Route::put('/{reservation}', [FacilityReservationController::class, 'update'])->name('update');
            Route::delete('/{reservation}', [FacilityReservationController::class, 'destroy'])->name('destroy');
            Route::post('/{reservation}/approve', [FacilityReservationController::class, 'approve'])->name('approve');
            Route::post('/{reservation}/reject', [FacilityReservationController::class, 'reject'])->name('reject');
            Route::post('/{reservation}/check-in', [FacilityReservationController::class, 'checkIn'])->name('check-in');
            Route::post('/{reservation}/check-out', [FacilityReservationController::class, 'checkOut'])->name('check-out');
        });
    });

    // Equipment
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/create', [EquipmentController::class, 'create'])->name('create');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        
        // Equipment Borrow Requests
        Route::prefix('borrow-requests')->name('borrow-requests.')->group(function () {
            Route::get('/', [EquipmentBorrowRequestController::class, 'index'])->name('index');
            Route::get('/create', [EquipmentBorrowRequestController::class, 'create'])->name('create');
            Route::post('/', [EquipmentBorrowRequestController::class, 'store'])->name('store');
            Route::get('/{borrowRequest}', [EquipmentBorrowRequestController::class, 'show'])->name('show');
            Route::get('/{borrowRequest}/edit', [EquipmentBorrowRequestController::class, 'edit'])->name('edit');
            Route::put('/{borrowRequest}', [EquipmentBorrowRequestController::class, 'update'])->name('update');
            Route::delete('/{borrowRequest}', [EquipmentBorrowRequestController::class, 'destroy'])->name('destroy');
            Route::post('/{borrowRequest}/approve', [EquipmentBorrowRequestController::class, 'approve'])->name('approve');
            Route::post('/{borrowRequest}/reject', [EquipmentBorrowRequestController::class, 'reject'])->name('reject');
            Route::post('/{borrowRequest}/check-out', [EquipmentBorrowRequestController::class, 'checkOut'])->name('check-out');
            Route::post('/{borrowRequest}/return', [EquipmentBorrowRequestController::class, 'return'])->name('return');
        });
    });

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::post('/{inventory}/stock-count', [InventoryController::class, 'stockCount'])->name('stock-count');
        Route::get('/transfers', [InventoryController::class, 'transfers'])->name('transfers');
        Route::get('/{inventory}/create-transfer', [InventoryController::class, 'createTransfer'])->name('create-transfer');
        Route::post('/{inventory}/store-transfer', [InventoryController::class, 'storeTransfer'])->name('store-transfer');
        Route::post('/transfers/{transfer}/complete', [InventoryController::class, 'completeTransfer'])->name('complete-transfer');
        Route::post('/transfers/{transfer}/cancel', [InventoryController::class, 'cancelTransfer'])->name('cancel-transfer');
    });

    // Resource Requests
    Route::prefix('resource-requests')->name('resource-requests.')->group(function () {
        Route::get('/', [ResourceRequestController::class, 'index'])->name('index');
        Route::post('/{request}/approve', [ResourceRequestController::class, 'approve'])->name('approve');
        Route::post('/{request}/decline', [ResourceRequestController::class, 'decline'])->name('decline');
    });

    // Building Management
    Route::prefix('buildings')->name('buildings.')->group(function () {
        Route::get('/', [BuildingController::class, 'index'])->name('index');
        Route::get('/create', [BuildingController::class, 'create'])->name('create');
        Route::post('/', [BuildingController::class, 'store'])->name('store');
        Route::get('/directory', [BuildingController::class, 'directory'])->name('directory');
        Route::get('/{building}', [BuildingController::class, 'show'])->name('show');
        Route::get('/{building}/edit', [BuildingController::class, 'edit'])->name('edit');
        Route::put('/{building}', [BuildingController::class, 'update'])->name('update');
        Route::delete('/{building}', [BuildingController::class, 'destroy'])->name('destroy');
        
        // Floor routes
        Route::get('/{building}/floors/create', [BuildingController::class, 'createFloor'])->name('floors.create');
        Route::post('/{building}/floors', [BuildingController::class, 'storeFloor'])->name('floors.store');
        Route::get('/{building}/floors/{floor}/edit', [BuildingController::class, 'editFloor'])->name('floors.edit');
        Route::put('/{building}/floors/{floor}', [BuildingController::class, 'updateFloor'])->name('floors.update');
        Route::delete('/{building}/floors/{floor}', [BuildingController::class, 'deleteFloor'])->name('floors.delete');
        
        // Room routes
        Route::get('/{building}/floors/{floor}/rooms/create', [BuildingController::class, 'createRoom'])->name('rooms.create');
        Route::post('/{building}/floors/{floor}/rooms', [BuildingController::class, 'storeRoom'])->name('rooms.store');
        Route::get('/{building}/floors/{floor}/rooms/{room}/edit', [BuildingController::class, 'editRoom'])->name('rooms.edit');
        Route::put('/{building}/floors/{floor}/rooms/{room}', [BuildingController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/{building}/floors/{floor}/rooms/{room}', [BuildingController::class, 'deleteRoom'])->name('rooms.delete');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])->name('permissions');
        Route::post('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('update-permissions');
        Route::get('/{user}/roles', [UserController::class, 'roles'])->name('roles');
        Route::post('/{user}/roles', [UserController::class, 'updateRole'])->name('update-role');
        Route::post('/{user}/remove-role', [UserController::class, 'removeRole'])->name('remove-role');
    });

    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/create', [ChatController::class, 'create'])->name('create');
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
        Route::get('/{chat}/edit', [ChatController::class, 'edit'])->name('edit');
        Route::put('/{chat}', [ChatController::class, 'update'])->name('update');
        Route::delete('/{chat}', [ChatController::class, 'destroy'])->name('destroy');
        Route::post('/{chat}/send-message', [ChatController::class, 'sendMessage'])->name('send-message');
        Route::post('/messages/{message}/mark-seen', [ChatController::class, 'markAsSeen'])->name('mark-seen');
        Route::post('/{chat}/typing', [ChatController::class, 'markTyping'])->name('typing');
        Route::post('/{chat}/add-participant', [ChatController::class, 'addParticipant'])->name('add-participant');
        Route::post('/{chat}/remove-participant', [ChatController::class, 'removeParticipant'])->name('remove-participant');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-send', [NotificationController::class, 'bulkSend'])->name('bulk-send');
        Route::post('/cleanup', [NotificationController::class, 'cleanup'])->name('cleanup');
    });

    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::get('/{maintenanceLog}', [MaintenanceController::class, 'show'])->name('show');
        Route::get('/{maintenanceLog}/edit', [MaintenanceController::class, 'edit'])->name('edit');
        Route::put('/{maintenanceLog}', [MaintenanceController::class, 'update'])->name('update');
        Route::delete('/{maintenanceLog}', [MaintenanceController::class, 'destroy'])->name('destroy');
        Route::post('/{maintenanceLog}/complete', [MaintenanceController::class, 'complete'])->name('complete');
        Route::post('/{maintenanceLog}/cancel', [MaintenanceController::class, 'cancel'])->name('cancel');
        Route::post('/{maintenanceLog}/assign-technician', [MaintenanceController::class, 'assignTechnician'])->name('assign-technician');
    });

    // Service Requests
    Route::prefix('service-requests')->name('service-requests.')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'index'])->name('index');
        Route::get('/create', [ServiceRequestController::class, 'create'])->name('create');
        Route::post('/', [ServiceRequestController::class, 'store'])->name('store');
        Route::get('/{serviceRequest}', [ServiceRequestController::class, 'show'])->name('show');
        Route::get('/{serviceRequest}/edit', [ServiceRequestController::class, 'edit'])->name('edit');
        Route::put('/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('update');
        Route::delete('/{serviceRequest}', [ServiceRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{serviceRequest}/complete', [ServiceRequestController::class, 'complete'])->name('complete');
        Route::post('/{serviceRequest}/cancel', [ServiceRequestController::class, 'cancel'])->name('cancel');
        Route::post('/{serviceRequest}/assign-staff', [ServiceRequestController::class, 'assignStaff'])->name('assign-staff');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/reservations', [ReportController::class, 'reservations'])->name('reservations');
        Route::post('/export-reservations', [ReportController::class, 'exportReservations'])->name('export-reservations');
        Route::get('/equipment', [ReportController::class, 'equipment'])->name('equipment');
        Route::post('/export-equipment', [ReportController::class, 'exportEquipment'])->name('export-equipment');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::post('/export-inventory', [ReportController::class, 'exportInventory'])->name('export-inventory');
        Route::get('/usage', [ReportController::class, 'usage'])->name('usage');
        Route::post('/export-usage', [ReportController::class, 'exportUsage'])->name('export-usage');
        Route::get('/department', [ReportController::class, 'department'])->name('department');
        Route::post('/export-department', [ReportController::class, 'exportDepartment'])->name('export-department');
    });

    // Activity Logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
        Route::post('/cleanup', [ActivityLogController::class, 'cleanup'])->name('cleanup');
    });

    // Audit Trail
    Route::prefix('audit-trails')->name('audit-trails.')->group(function () {
        Route::get('/', [AuditTrailController::class, 'index'])->name('index');
        Route::get('/{auditTrail}', [AuditTrailController::class, 'show'])->name('show');
        Route::post('/cleanup', [AuditTrailController::class, 'cleanup'])->name('cleanup');
    });

    // Announcements
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}', [AnnouncementController::class, 'show'])->name('show');
        Route::get('/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::post('/{announcement}/pin', [AnnouncementController::class, 'pin'])->name('pin');
        Route::post('/{announcement}/unpin', [AnnouncementController::class, 'unpin'])->name('unpin');
    });

    // Calendar
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::get('/create', [CalendarController::class, 'create'])->name('create');
        Route::post('/', [CalendarController::class, 'store'])->name('store');
        Route::get('/{event}', [CalendarController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [CalendarController::class, 'edit'])->name('edit');
        Route::put('/{event}', [CalendarController::class, 'update'])->name('update');
        Route::delete('/{event}', [CalendarController::class, 'destroy'])->name('destroy');
        Route::get('/data', [CalendarController::class, 'calendarData'])->name('data');
    });

    // Global Search
    Route::prefix('search')->name('global-search.')->group(function () {
        Route::get('/', [GlobalSearchController::class, 'index'])->name('index');
        Route::get('/api', [GlobalSearchController::class, 'search'])->name('api');
    });

    // Resources (User-owned lending)
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [ResourceController::class, 'index'])->name('index');
        Route::get('/create', [ResourceController::class, 'create'])->name('create');
        Route::post('/', [ResourceController::class, 'store'])->name('store');
        Route::get('/my-lending', [ResourceController::class, 'myLending'])->name('my-lending');
        Route::get('/my-borrowing', [ResourceController::class, 'myBorrowing'])->name('my-borrowing');
        Route::get('/{resource}', [ResourceController::class, 'show'])->name('show');
        Route::get('/{resource}/edit', [ResourceController::class, 'edit'])->name('edit');
        Route::put('/{resource}', [ResourceController::class, 'update'])->name('update');
        Route::delete('/{resource}', [ResourceController::class, 'destroy'])->name('destroy');
    });

    // Resource Borrow Requests
    Route::prefix('resource-borrow-requests')->name('resource-borrow-requests.')->group(function () {
        Route::post('/', [ResourceController::class, 'storeBorrowRequest'])->name('store');
        Route::get('/{request}/edit', [ResourceController::class, 'editBorrowRequest'])->name('edit');
        Route::get('/{request}', [ResourceController::class, 'showBorrowRequest'])->name('show');
        Route::put('/{request}', [ResourceController::class, 'updateBorrowRequest'])->name('update');
        Route::delete('/{request}', [ResourceController::class, 'destroyBorrowRequest'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
