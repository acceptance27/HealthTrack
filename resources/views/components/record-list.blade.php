@props(['title', 'records', 'dateField', 'primaryField', 'secondaryField'])

<div class="space-y-4">
    <h1 class="text-2xl font-semibold">{{ $title }}</h1>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Record</th>
                    <th class="px-4 py-3">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="border-t border-slate-200">
                        <td class="px-4 py-3">{{ $record->{$dateField}->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $record->{$primaryField} }}</td>
                        <td class="px-4 py-3">{{ $record->{$secondaryField} }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $records->links() }}
</div>
