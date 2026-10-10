<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $role = '';
    public $department_id = '';
    public $organization_id = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
        'department_id' => ['except' => ''],
        'organization_id' => ['except' => ''],
        'sortField' => ['except' => 'name'],
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
        $this->reset(['search', 'role', 'department_id', 'organization_id']);
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        $query = User::with(['department', 'organization', 'roles']);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('student_id', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_id', 'like', '%' . $this->search . '%');
            });
        }

        // Role filter
        if ($this->role) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        // Department filter
        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
        }

        // Organization filter
        if ($this->organization_id) {
            $query->where('organization_id', $this->organization_id);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    #[Computed]
    public function departments()
    {
        return \App\Models\Department::all();
    }

    #[Computed]
    public function organizations()
    {
        return \App\Models\Organization::all();
    }

    #[Computed]
    public function roles()
    {
        return \Spatie\Permission\Models\Role::all();
    }

    public function render()
    {
        return view('livewire.user-index', [
            'users' => $this->users,
            'departments' => $this->departments,
            'organizations' => $this->organizations,
            'roles' => $this->roles,
        ]);
    }
}
