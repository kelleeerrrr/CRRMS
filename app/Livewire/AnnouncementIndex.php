<?php

namespace App\Livewire;

use App\Models\Announcement;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $audience = '';
    public $is_pinned = '';
    public $sortField = 'published_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'audience' => ['except' => ''],
        'is_pinned' => ['except' => ''],
        'sortField' => ['except' => 'published_at'],
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
        $this->reset(['search', 'category', 'audience', 'is_pinned']);
        $this->resetPage();
    }

    #[Computed]
    public function announcements()
    {
        $query = Announcement::with(['createdBy', 'attachments']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->audience) {
            $query->where('audience', $this->audience);
        }

        if ($this->is_pinned !== '') {
            $query->where('is_pinned', $this->is_pinned);
        }

        // Only show published announcements
        $query->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(20);
    }

    public function render()
    {
        return view('livewire.announcement-index', [
            'announcements' => $this->announcements,
        ]);
    }
}
