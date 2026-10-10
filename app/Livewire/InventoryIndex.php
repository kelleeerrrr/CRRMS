<?php

namespace App\Livewire;

use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $equipment_id = '';
    public $location = '';
    public $condition = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'equipment_id' => ['except' => ''],
        'location' => ['except' => ''],
        'condition' => ['except' => ''],
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
        $this->reset(['search', 'equipment_id', 'location', 'condition']);
        $this->resetPage();
    }

    #[Computed]
    public function inventory()
    {
        $query = Inventory::with(['equipment', 'countedBy']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('location', 'like', '%' . $this->search . '%')
                    ->orWhereHas('equipment', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Equipment filter
        if ($this->equipment_id) {
            $query->where('equipment_id', $this->equipment_id);
        }

        // Location filter
        if ($this->location) {
            $query->where('location', 'like', '%' . $this->location . '%');
        }

        // Condition filter
        if ($this->condition) {
            $query->where('condition', $this->condition);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    #[Computed]
    public function equipment()
    {
        return \App\Models\Equipment::where('is_active', true)->get();
    }

    public function render()
    {
        return view('livewire.inventory-index', [
            'inventory' => $this->inventory,
            'equipment' => $this->equipment,
        ]);
    }
}
