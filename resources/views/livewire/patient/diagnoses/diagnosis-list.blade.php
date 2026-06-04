<x-patient-page title="Diagnoses" description="Your personal diagnosis history and recent condition summaries.">
    <x-record-list title="Diagnoses" :records="$records" date-field="diagnosed_at" primary-field="diagnosis" secondary-field="description" />
</x-patient-page>
