<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CalendarIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $event_type = '';
    public $related_model_type = '';
    public $sortField = 'start_date';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'event_type' => ['except' => ''],
        'related_model_type' => ['except' => ''],
        'sortField' => ['except' => 'start_date'],
        'sortDirection' => ['except' => 'asc'],
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
        $this->reset(['search', 'event_type', 'related_model_type']);
        $this->resetPage();
    }

    #[Computed]
    public function events()
    {
        $query = CalendarEvent::with(['createdBy']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->event_type) {
            $query->where('event_type', $this->event_type);
        }

        if ($this->related_model_type) {
            $query->where('related_model_type', $this->related_model_type);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.calendar-index', [
            'events' => $this->events,
        ]);
    }
}
