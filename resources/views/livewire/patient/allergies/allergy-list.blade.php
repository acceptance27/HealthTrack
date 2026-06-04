<x-patient-page title="Medication Allergies" description="Your allergy record and reaction history.">
    <x-record-list title="Medication Allergies" :records="$records" date-field="recorded_at" primary-field="allergen" secondary-field="reaction" />
</x-patient-page>
