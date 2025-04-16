<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
        <thead class="bg-gray-50 dark:bg-white/5">
            <tr class="text-left">
                <th class="fi-ta-header-cell px-4 py-2 font-semibold text-gray-600 dark:text-gray-400">Name</th>
                <th class="fi-ta-header-cell px-4 py-2 font-semibold text-gray-600 dark:text-gray-400">Email</th>
                <th class="fi-ta-header-cell px-4 py-2 font-semibold text-gray-600 dark:text-gray-400">Phone Number</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
            @foreach($entry->getState() as $person)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800"> <!-- Perubahan utama di sini -->
                    <td class="fi-ta-cell px-4 py-2 text-gray-900 dark:text-gray-200">{{ $person['name'] }}</td>
                    <td class="fi-ta-cell px-4 py-2 text-gray-900 dark:text-gray-200">{{ $person['email'] }}</td>
                    <td class="fi-ta-cell px-4 py-2 text-gray-900 dark:text-gray-200">{{ $person['phone_number'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-dynamic-component>