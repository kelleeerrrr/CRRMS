<?php

namespace App\Livewire;

use App\Services\GlobalSearchService;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];
    public $isOpen = false;
    public $selectedTypes = ['users', 'buildings', 'facilities', 'equipment', 'reservations'];

    protected GlobalSearchService $globalSearchService;

    public function boot(GlobalSearchService $globalSearchService)
    {
        $this->globalSearchService = $globalSearchService;
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $this->results = $this->globalSearchService->search($this->query, ['types' => $this->selectedTypes]);
    }

    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->query = '';
        $this->results = [];
    }

    public function toggleType($type)
    {
        if (in_array($type, $this->selectedTypes)) {
            $this->selectedTypes = array_diff($this->selectedTypes, [$type]);
        } else {
            $this->selectedTypes[] = $type;
        }

        if (!empty($this->query)) {
            $this->updatedQuery();
        }
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
