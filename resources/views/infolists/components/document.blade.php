{{-- filepath: /home/dxnz/Documents/labnotes/resources/views/infolists/components/document.blade.php --}}
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @if(is_array($getState()))
            @foreach($getState() as $item)
                <a href="{{ env('APP_URL') . '/storage/' . str_replace('\\', '/', $item) }}">
                    {{ basename($item) }}
                </a>
            @endforeach
        @else
            {{ $getState() }}
        @endif
    </div>
</x-dynamic-component>