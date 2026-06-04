<x-patient-page title="Lab Values" description="Your most recent lab results and reference ranges.">
    <div class="mw-card mw-panel">
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="mw-status-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Test</th>
                        <th>Value</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $record->tested_at->format('M d, Y') }}</td>
                            <td>{{ $record->test_name }}</td>
                            <td>{{ $record->value }} {{ $record->unit }}</td>
                            <td>{{ $record->reference_range }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $records->links() }}
    </div>
</x-patient-page>
