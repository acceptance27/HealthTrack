<x-patient-page title="Medical History" description="Your personal medical history records and condition notes.">
    <x-record-list title="Personal Medical History" :records="$records" date-field="recorded_at" primary-field="condition" secondary-field="details" />
</x-patient-page>
