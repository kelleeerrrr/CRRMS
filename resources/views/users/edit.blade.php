<x-slot name="title">Edit User</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Users', 'url' => route('users.index')],
        ['title' => 'Edit', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
    <p class="text-gray-600 dark:text-gray-400">Update user information</p>
</x-slot>

<x-card>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input
                type="text"
                name="name"
                label="Full Name"
                placeholder="John Doe"
                required
                :value="old('name', $user->name)"
                :error="$errors->first('name')"
            />
            
            <x-input
                type="email"
                name="email"
                label="Email Address"
                placeholder="john@example.com"
                required
                :value="old('email', $user->email)"
                :error="$errors->first('email')"
            />
            
            <x-input
                type="password"
                name="password"
                label="Password (Leave blank to keep current)"
                placeholder="••••••••"
                :value="old('password')"
                :error="$errors->first('password')"
            />
            
            <x-input
                type="password"
                name="password_confirmation"
                label="Confirm Password"
                placeholder="••••••••"
                :value="old('password_confirmation')"
                :error="$errors->first('password_confirmation')"
            />
            
            <x-input
                type="text"
                name="student_id"
                label="Student ID (Optional)"
                placeholder="e.g., 20240001"
                :value="old('student_id', $user->student_id)"
                :error="$errors->first('student_id')"
            />
            
            <x-input
                type="text"
                name="employee_id"
                label="Employee ID (Optional)"
                placeholder="e.g., EMP001"
                :value="old('employee_id', $user->employee_id)"
                :error="$errors->first('employee_id')"
            />
            
            <x-input
                type="text"
                name="phone"
                label="Phone Number (Optional)"
                placeholder="e.g., +1234567890"
                :value="old('phone', $user->phone)"
                :error="$errors->first('phone')"
            />
            
            <x-select
                name="department_id"
                label="Department (Optional)"
                :options="['' => 'Select department'] + $departments->pluck('name', 'id')->toArray()"
                :value="old('department_id', $user->department_id)"
            />
            
            <x-select
                name="organization_id"
                label="Organization (Optional)"
                :options="['' => 'Select organization'] + $organizations->pluck('name', 'id')->toArray()"
                :value="old('organization_id', $user->organization_id)"
            />
            
            <x-select
                name="role"
                label="Role (Optional)"
                :options="['' => 'Select role'] + $roles->pluck('name', 'name')->toArray()"
                :value="old('role', $user->roles->first()?->name ?? '')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('users.show', $user) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update User
            </button>
        </div>
    </form>
</x-card>
