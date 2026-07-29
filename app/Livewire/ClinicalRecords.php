<?php

namespace App\Livewire;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Lists and edits one kind of clinical record for one patient.
 *
 * This single component replaces what used to be five near-identical pairs of
 * Livewire classes and Blade templates. Which fields it shows, how it
 * validates them and what it calls them all come from config/healthtrack.php:
 *
 *     <livewire:clinical-records :patient="$patient" type="diagnoses" />
 *     <livewire:clinical-records :patient="$patient" type="allergies" read-only />
 *
 * To add a field, or a whole new record type, edit that config file. You
 * should not need to touch this class. See DOCS/03-adding-a-record-type.md.
 */
class ClinicalRecords extends Component
{
    use AuthorizesRequests;

    public Patient $patient;

    /** A key from config('healthtrack.records'), e.g. "lab-values". */
    public string $type;

    /** True in the patient portal, where records are displayed but never edited. */
    public bool $readOnly = false;

    /** Form values, keyed by column name. Bound as form.<column> in Blade. */
    public array $form = [];

    /** The record's date, kept separate because every type has exactly one. */
    public string $recordDate = '';

    public bool $showForm = false;

    /** How many rows to display; the "Show more" button raises it. */
    public int $perPage = 10;

    public function mount(Patient $patient, string $type, bool $readOnly = false): void
    {
        abort_unless(array_key_exists($type, config('healthtrack.records')), 404);

        $this->patient = $patient;
        $this->type = $type;
        $this->readOnly = $readOnly;

        $this->resetForm();
    }

    /**
     * This record type's entry from config/healthtrack.php.
     *
     * #[Computed] makes it available as $this->definition both here and in
     * the Blade file, without recalculating on every access.
     */
    #[Computed]
    public function definition(): array
    {
        return config("healthtrack.records.{$this->type}");
    }

    /** The Eloquent class backing this record type. */
    private function modelClass(): string
    {
        return $this->definition['model'];
    }

    /** May the current user add and remove records of this type? */
    #[Computed]
    public function canManage(): bool
    {
        if ($this->readOnly) {
            return false;
        }

        return auth()->user()->can('create', $this->modelClass());
    }

    public function toggleForm(): void
    {
        $this->authorize('create', $this->modelClass());

        $this->showForm = ! $this->showForm;

        if (! $this->showForm) {
            $this->resetForm();
        }
    }

    public function save(): void
    {
        $this->authorize('create', $this->modelClass());

        $validated = $this->validate($this->rules())['form'];

        $this->modelClass()::create($validated + [
            'patient_id' => $this->patient->id,
            'created_by' => auth()->id(),
            $this->definition['date_field'] => $this->recordDate,
        ]);

        $this->resetForm();
        $this->showForm = false;

        $this->dispatch('record-saved', type: $this->type);
    }

    public function delete(int $id): void
    {
        $record = $this->modelClass()::where('patient_id', $this->patient->id)
            ->findOrFail($id);

        $this->authorize('delete', $record);

        $record->delete();

        $this->dispatch('record-saved', type: $this->type);
    }

    public function showMore(): void
    {
        $this->perPage += 10;
    }

    /**
     * Validation rules assembled from the field definitions.
     *
     * Produces e.g. ['form.diagnosis' => ['required','string','max:255'], ...]
     * plus a rule for the date, which every record type has.
     */
    protected function rules(): array
    {
        $rules = [];

        foreach ($this->definition['fields'] as $column => $field) {
            $rules["form.{$column}"] = $field['rules'];
        }

        $rules['recordDate'] = ['required', 'date', 'before_or_equal:today'];

        return $rules;
    }

    /** Friendly names so errors read "Diagnosis is required", not "form.diagnosis". */
    protected function validationAttributes(): array
    {
        $attributes = ['recordDate' => strtolower($this->definition['date_label'])];

        foreach ($this->definition['fields'] as $column => $field) {
            $attributes["form.{$column}"] = strtolower($field['label']);
        }

        return $attributes;
    }

    private function resetForm(): void
    {
        $this->form = array_fill_keys(
            array_keys($this->definition['fields']),
            ''
        );

        $this->recordDate = now()->format('Y-m-d');
        $this->resetValidation();
    }

    /** The rows to display, newest first. */
    private function records(): Collection
    {
        return $this->modelClass()::query()
            ->where('patient_id', $this->patient->id)
            ->orderByDesc($this->definition['date_field'])
            ->orderByDesc('id')
            ->limit($this->perPage)
            ->get();
    }

    public function render()
    {
        return view('livewire.clinical-records', [
            'records' => $this->records(),
            'totalRecords' => $this->modelClass()::where('patient_id', $this->patient->id)->count(),
        ]);
    }
}
