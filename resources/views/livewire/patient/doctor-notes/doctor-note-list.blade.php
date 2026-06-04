<x-patient-page title="Doctor Notes" description="Your personal notes from medical providers.">
    <x-record-list title="Doctor Notes" :records="$records" date-field="noted_at" primary-field="title" secondary-field="note" />
</x-patient-page>
