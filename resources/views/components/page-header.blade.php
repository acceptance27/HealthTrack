{{--
    Standard page title block.

        <x-page-header title="Patients" subtitle="Everyone registered at the centre.">
            <x-slot:aside><span class="ht-pill">128 total</span></x-slot:aside>
        </x-page-header>
--}}
@props(['title', 'subtitle' => null])

<section class="ht-page-header">
    <div>
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>

    @isset($aside)
        <div class="flex items-center gap-2">{{ $aside }}</div>
    @endisset
</section>
