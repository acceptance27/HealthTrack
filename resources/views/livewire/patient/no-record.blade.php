<div class="grid gap-4">
    <x-page-header title="No patient record found" />

    <div class="ht-panel">
        <div class="ht-empty">
            Your account is not linked to a patient record yet.
            <span class="mt-2 block text-xs">
                Please contact {{ config('healthtrack.centre.name') }} so a health worker can link it.
            </span>
        </div>
    </div>
</div>
