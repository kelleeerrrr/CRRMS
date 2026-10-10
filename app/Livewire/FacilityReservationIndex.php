<?php

namespace App\Livewire;

use App\Models\FacilityReservation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class FacilityReservationIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

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

    public function approve($reservationId)
    {
        $reservation = FacilityReservation::findOrFail($reservationId);
        
        if (Auth::user()->can('approve', $reservation)) {
            $reservation->update([
                'status' => 'approved',
                'approved_by' => Auth::user()->id,
                'approved_at' => now(),
            ]);
            
            $this->dispatch('notification', message: 'Reservation approved successfully!', type: 'success');
        }
    }

    public function reject($reservationId)
    {
        $reservation = FacilityReservation::findOrFail($reservationId);
        
        if (Auth::user()->can('approve', $reservation)) {
            $reservation->update([
                'status' => 'rejected',
                'approved_by' => Auth::user()->id,
                'approved_at' => now(),
                'rejection_reason' => 'Rejected by admin',
            ]);
            
            $this->dispatch('notification', message: 'Reservation rejected successfully!', type: 'success');
        }
    }

    #[Computed]
    public function reservations()
    {
        $query = FacilityReservation::with(['facility', 'user']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reservee_name', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('facility', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(12);
    }

    public function render()
    {
        return view('livewire.facility-reservation-index', [
            'reservations' => $this->reservations,
        ]);
    }
}
