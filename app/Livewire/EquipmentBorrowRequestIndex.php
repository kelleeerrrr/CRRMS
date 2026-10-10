<?php

namespace App\Livewire;

use App\Models\EquipmentBorrowRequest;
use App\Services\EquipmentBorrowRequestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentBorrowRequestIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $rejectReason = '';
    public $rejectRequestId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $equipmentBorrowRequestService;

    public function boot()
    {
        $this->equipmentBorrowRequestService = app(EquipmentBorrowRequestService::class);
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
        $this->reset(['search', 'status']);
        $this->resetPage();
    }

    public function approve($requestId)
    {
        $request = EquipmentBorrowRequest::findOrFail($requestId);
        
        if (!Auth::user()->can('approve', $request)) {
            session()->flash('error', 'You do not have permission to approve this request.');
            return;
        }

        try {
            $this->equipmentBorrowRequestService->approveBorrowRequest($request, Auth::user());
            session()->flash('success', 'Borrow request approved successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    public function openRejectModal($requestId)
    {
        $this->rejectRequestId = $requestId;
        $this->rejectReason = '';
        $this->dispatch('open-modal', 'reject-modal');
    }

    public function reject()
    {
        $request = EquipmentBorrowRequest::findOrFail($this->rejectRequestId);
        
        if (!Auth::user()->can('reject', $request)) {
            session()->flash('error', 'You do not have permission to reject this request.');
            return;
        }

        try {
            $this->equipmentBorrowRequestService->rejectBorrowRequest($request, Auth::user(), $this->rejectReason);
            session()->flash('success', 'Borrow request rejected successfully.');
            $this->rejectRequestId = '';
            $this->rejectReason = '';
            $this->dispatch('close-modal', 'reject-modal');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }

    #[Computed]
    public function requests()
    {
        $query = EquipmentBorrowRequest::with(['user', 'items.equipment']);

        // School officers see all requests, regular users see only their own
        if (!Auth::user()->can('manage-equipment')) {
            $query->where('user_id', Auth::user()->id);
        }

        // Search by user name or equipment name
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('items.equipment', function ($equipmentQuery) {
                    $equipmentQuery->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    public function render()
    {
        return view('livewire.equipment-borrow-request-index', [
            'requests' => $this->requests,
        ]);
    }
}
