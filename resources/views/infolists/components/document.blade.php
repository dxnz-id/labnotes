{{-- filepath: /home/dxnz/Documents/labnotes/resources/views/infolists/components/document.blade.php --}}
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @if(is_array($getState()))
        @foreach($getState() as $item)
            <li class="flex w-full gap-2 m-0 p-0">
                <a class="mb-4 w-full fi-in-repeatable-item flex rounded-xl bg-non m-4 p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10 hover:shadow-lg hover:ring-gray-950/10 dark:hover:ring-white/20 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300"
                    href="{{ env('APP_URL') . '/storage/' . str_replace('\\', '/', $item) }}" target="_blank">
                    {{ basename($item) }}
                </a>
            </li>
        @endforeach
    @else
        <div class="flex w-full text-center justify-center">
            <p>No documents uploaded.</p>
        </div>
    @endif
</x-dynamic-component>