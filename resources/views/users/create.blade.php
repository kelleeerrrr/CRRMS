<x-slot name="title">Add User</x-slot>

<x-slot name="breadcrumbs">
    {{ [
        ['title' => 'Users', 'url' => route('users.index')],
        ['title' => 'Add', 'url' => null],
    ] }}
</x-slot>

<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add User</h1>
    <p class="text-gray-600 dark:text-gray-400">Create a new user account</p>
</x-slot>

<x-card>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input
                type="text"
                name="name"
                label="Full Name"
                placeholder="John Doe"
                required
                :value="old('name')"
                :error="$errors->first('name')"
            />
            
            <x-input
                type="email"
                name="email"
                label="Email Address"
                placeholder="john@example.com"
                required
                :value="old('email')"
                :error="$errors->first('email')"
            />
            
            <x-input
                type="password"
                name="password"
                label="Password"
                placeholder="••••••••"
                required
                :value="old('password')"
                :error="$errors->first('password')"
            />
            
            <x-input
                type="password"
                name="password_confirmation"
                label="Confirm Password"
                placeholder="••••••••"
                required
                :value="old('password_confirmation')"
                :error="$errors->first('password_confirmation')"
            />
            
            <x-input
                type="text"
                name="student_id"
                label="Student ID (Optional)"
                placeholder="e.g., 20240001"
                :value="old('student_id')"
                :error="$errors->first('student_id')"
            />
            
            <x-input
                type="text"
                name="employee_id"
                label="Employee ID (Optional)"
                placeholder="e.g., EMP001"
                :value="old('employee_id')"
                :error="$errors->first('employee_id')"
            />
            
            <x-input
                type="text"
                name="phone"
                label="Phone Number (Optional)"
                placeholder="e.g., +1234567890"
                :value="old('phone')"
                :error="$errors->first('phone')"
            />
            
            <x-select
                name="department_id"
                label="Department (Optional)"
                :options="['' => 'Select department'] + $departments->pluck('name', 'id')->toArray()"
                :value="old('department_id')"
            />
            
            <x-select
                name="organization_id"
                label="Organization (Optional)"
                :options="['' => 'Select organization'] + $organizations->pluck('name', 'id')->toArray()"
                :value="old('organization_id')"
            />
            
            <x-select
                name="role"
                label="Role (Optional)"
                :options="['' => 'Select role'] + $roles->pluck('name', 'name')->toArray()"
                :value="old('role')"
            />
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Create User
            </button>
        </div>
    </form>
</x-card>
