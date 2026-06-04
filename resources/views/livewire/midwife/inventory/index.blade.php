@extends('layouts.app')

@section('title', 'Inventory')
@section('page-title', '')
@section('content')
<div class="space-y-4">
    <livewire:midwife.inventory.inventory-table />
</div>
@endsection
