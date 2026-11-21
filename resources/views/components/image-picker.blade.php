@props(['name', 'label', 'multiple' => false])

<label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $label }}</label>
<input type="file" id="{{ $name }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}" accept="image/*" @if($multiple) multiple @endif
    class="w-full cursor-pointer rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 file:mr-4 file:cursor-pointer file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-blue-700">
<x-input-error :messages="$errors->get($name)" class="mt-2" />
@foreach($errors->get($name . '.*') as $messages)
    <x-input-error :messages="$messages" class="mt-2" />
@endforeach
