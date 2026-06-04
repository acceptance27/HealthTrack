@props(['title', 'records', 'primary', 'date', 'secondary' => null, 'value' => null, 'unit' => null, 'range' => null, 'label' => 'Record'])

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="m-0">{{ $title }}</h3>
        <span class="mw-pill">{{ $records->count() }} Total</span>
    </div>

    <div class="overflow-x-auto">
        <table class="mw-status-table">
            <thead>
                <tr>
                    <th>{{ $label }}</th>
                    @if($value)
                        <th>Result</th>
                    @endif
                    @if($secondary)
                        <th>Details</th>
                    @endif
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td class="font-bold text-[var(--ev-accent-strong)]">
                            {{ $record->{$primary} }}
                            @if(isset($record->severity))
                                <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full border border-[var(--ev-border)] bg-white mw-muted">
                                    {{ $record->severity }}
                                </span>
                            @endif
                            @if($range && isset($record->{$range}))
                                <div class="text-[10px] font-normal mw-muted mt-0.5">Ref: {{ $record->{$range} }}</div>
                            @endif
                        </td>
                        @if($value)
                            <td>
                                <span class="font-bold">{{ $record->{$value} }}</span>
                                <span class="text-[10px] mw-muted ml-0.5">{{ $record->{$unit} ?? '' }}</span>
                            </td>
                        @endif
                        @if($secondary)
                            <td class="text-xs leading-relaxed max-w-md">
                                {{ Str::limit($record->{$secondary}, 100) }}
                            </td>
                        @endif
                        <td class="whitespace-nowrap">{{ $record->{$date}->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center mw-muted">
                            No {{ strtolower($title) }} found for this patient.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
