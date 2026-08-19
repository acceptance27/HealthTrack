{{--
    Table + form for one clinical record type. Driven entirely by the entry
    for $type in config/healthtrack.php -- there is nothing type-specific here.
--}}
@php
    $definition = $this->definition;
    $fields = $definition['fields'];

    // The one column that identifies the row, plus any marked as a column.
    $primary = collect($fields)->search(fn ($f) => $f['primary'] ?? false);
    $extraColumns = collect($fields)->filter(fn ($f, $k) => ($f['column'] ?? false) && $k !== $primary);
@endphp

<div class="ht-panel">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <h2>{{ $definition['label'] }}</h2>

        <div class="flex items-center gap-2">
            <span class="ht-pill">{{ $totalRecords }} total</span>

            @if ($this->canManage)
                <button type="button" wire:click="toggleForm" class="ht-button">
                    {{ $showForm ? 'Cancel' : 'Add '.$definition['singular'] }}
                </button>
            @endif
        </div>
    </div>

    {{-- ---------------------------------------------------------------- --}}
    {{-- Entry form                                                        --}}
    {{-- ---------------------------------------------------------------- --}}
    @if ($showForm && $this->canManage)
        <form wire:submit="save" class="mb-4 grid gap-3 rounded-xl p-4"
              style="background: var(--color-surface-muted);">

            @foreach ($fields as $column => $field)
                <x-field
                    :name="$column"
                    :label="$field['label']"
                    :type="$field['type']"
                    :options="$field['options'] ?? []"
                    :wire="'form.'.$column"
                    :required="in_array('required', $field['rules'], true)"
                />
            @endforeach

            <x-field
                name="recordDate"
                :label="$definition['date_label']"
                type="date"
                wire="recordDate"
                :required="true"
            />

            <div class="flex gap-2">
                <button type="submit" class="ht-button" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Save {{ $definition['singular'] }}</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
                <button type="button" wire:click="toggleForm" class="ht-button ht-button-muted">
                    Cancel
                </button>
            </div>
        </form>
    @endif

    {{-- ---------------------------------------------------------------- --}}
    {{-- Records table                                                     --}}
    {{-- ---------------------------------------------------------------- --}}
    @if ($records->isEmpty())
        <div class="ht-empty">
            No {{ strtolower($definition['label']) }} recorded for this patient.
        </div>
    @else
        <div class="ht-table-scroll">
            <table class="ht-table">
                <thead>
                    <tr>
                        <th>{{ $fields[$primary]['label'] }}</th>
                        @foreach ($extraColumns as $column => $field)
                            <th>{{ $field['label'] }}</th>
                        @endforeach
                        <th>{{ $definition['date_label'] }}</th>
                        @if ($this->canManage)
                            <th><span class="sr-only">Actions</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr wire:key="{{ $type }}-{{ $record->id }}">
                            <td class="font-bold" style="color: var(--color-brand-strong);">
                                {{ $record->{$primary} }}
                            </td>

                            @foreach ($extraColumns as $column => $field)
                                <td>
                                    @if ($field['type'] === 'select')
                                        @if ($record->{$column})
                                            <span class="ht-pill">{{ $field['options'][$record->{$column}] ?? $record->{$column} }}</span>
                                        @else
                                            <span class="ht-muted">--</span>
                                        @endif
                                    @else
                                        {{ $record->{$column} ?: '--' }}
                                    @endif
                                </td>
                            @endforeach

                            <td class="whitespace-nowrap">
                                {{ $record->{$definition['date_field']}->format('M d, Y') }}
                            </td>

                            @if ($this->canManage)
                                <td>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $record->id }})"
                                        wire:confirm="Remove this {{ strtolower($definition['singular']) }}? This cannot be undone."
                                        class="ht-button ht-button-danger"
                                    >Remove</button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($totalRecords > $records->count())
            <button type="button" wire:click="showMore" class="ht-button ht-button-muted mt-3">
                Show more ({{ $totalRecords - $records->count() }} older)
            </button>
        @endif
    @endif
</div>
