<?php

namespace App\Livewire;

use App\Models\MaintenanceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $status = '';
    public $priority = '';
    public $assigned_to = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'status' => ['except' => ''],
        'priority' => ['except' => ''],
        'assigned_to' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        // Subscribe to Echo channel for real-time updates
        $this->dispatch('echo:subscribe', ['userId' => Auth::id()]);
    }

    #[On('maintenance-request-status-changed')]
    public function onMaintenanceRequestStatusChanged($event)
    {
        // Refresh maintenance logs when status changes
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'type', 'status', 'priority', 'assigned_to']);
        $this->resetPage();
    }

    #[Computed]
    public function maintenanceLogs()
    {
        $query = MaintenanceLog::with(['maintainable', 'assignedTo', 'createdBy']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        if ($this->assigned_to) {
            $query->where('assigned_to', $this->assigned_to);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(20);
    }

    #[Computed]
    public function technicians()
    {
        return \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'technician');
        })->get();
    }

    public function render()
    {
        return view('livewire.maintenance-index', [
            'maintenanceLogs' => $this->maintenanceLogs,
            'technicians' => $this->technicians,
        ]);
    }
}
