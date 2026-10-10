<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $role)) }} Dashboard</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
    </x-slot>

    @switch($role)
    @case('super_admin')
        @include('dashboard.roles.super-admin')
    @break

    @case('admin')
        @include('dashboard.roles.admin')
    @break

    @case('school_officer')
        @include('dashboard.roles.school-officer')
    @break

    @case('user')
        @include('dashboard.roles.student')
    @break

    @default
        @include('dashboard.roles.student')
@endswitch
</x-app-layout>
