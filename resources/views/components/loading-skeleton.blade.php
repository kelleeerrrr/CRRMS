@props(['count' => 1, 'height' => 'h-4'])

@for($i = 0; $i < $count; $i++)
    <div class="{{ $height }} bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
@endfor
