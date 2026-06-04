@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', '')

@section('content')
<div class="space-y-4">
    <livewire:midwife.patients.patients-table />
</div>
@endsection
