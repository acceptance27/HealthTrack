@php
    $barangayName = Auth::user()->barangay->name ?? 'Unknown';
    $lowStockTotal = $lowStockItems ?? $lowStockCount ?? 0;
@endphp

<div class="max-w-7xl mx-auto p-6">
    <header class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Midwife Dashboard</h1>
            <p class="text-sm text-gray-500">BI-style overview for {{ now()->format('F d, Y') }} — Barangay {{ $barangayName }}.</p>

        </div>
        <div class="flex items-center gap-3">
            <button class="px-3 py-1 text-sm bg-amber-500 text-white rounded">Refresh</button>
            <a href="{{ route('midwife.patients') }}" class="text-sm text-amber-600 hover:underline">Manage Patients</a>
        </div>
    </header>

    <!-- KPI row: four equal cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-700 font-bold">P</div>
            <div>
                <div class="text-xs text-gray-500">Total Patients</div>
                <div class="text-2xl font-extrabold">{{ $patientsCount ?? 0 }}</div>
            </div>
        </div>

        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600 font-bold">A</div>
            <div>
                <div class="text-xs text-gray-500">Appointments Today</div>
                <div class="text-2xl font-extrabold text-green-600">{{ $appointmentsToday ?? 0 }}</div>
            </div>
        </div>

        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 font-bold">F</div>
            <div>
                <div class="text-xs text-gray-500">Pending Follow-ups</div>
                <div class="text-2xl font-extrabold text-amber-600">{{ $pendingFollowUps ?? 0 }}</div>
            </div>
        </div>

        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 font-bold">I</div>
            <div>
                <div class="text-xs text-gray-500">Low Stock Items</div>
                <div class="text-2xl font-extrabold text-red-600">{{ $lowStockTotal }}</div>
            </div>
        </div>
    </section>

    <!-- Charts row: main KPI chart + two small widgets -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card p-4 lg:col-span-2">
            <h3 class="text-lg font-medium mb-3">Patient Coverage (7 days)</h3>
            <div class="h-56 bg-gradient-to-b from-white to-gray-50 rounded border flex items-center justify-center text-gray-400">[Chart placeholder]</div>
        </div>

        <div class="card p-4">
            <h3 class="text-lg font-medium mb-3">Inventory Trend</h3>
            <div class="h-56 bg-gradient-to-b from-white to-gray-50 rounded border flex items-center justify-center text-gray-400">[Sparkline]</div>
        </div>
    </section>

    <!-- Details row: snapshot table + alerts/actions -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="card p-4 lg:col-span-2">
            <h3 class="text-lg font-medium mb-3">Clinic Snapshot</h3>
            <table class="mw-status-table">
                <tbody>
                    <tr>
                        <th>Registered Patients</th>
                        <td class="text-right">{{ $patientsCount ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Scheduled Today</th>
                        <td class="text-right">{{ $appointmentsToday ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Follow-up Queue</th>
                        <td class="text-right">{{ $pendingFollowUps ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Inventory Alerts</th>
                        <td class="text-right">{{ $lowStockTotal }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <aside class="card p-4">
            <h3 class="text-lg font-medium mb-3">Alerts & Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('midwife.inventory') }}" class="block p-3 bg-red-50 text-red-700 rounded border">{{ $lowStockTotal > 0 ? $lowStockTotal . ' items need restock' : 'No critical alerts' }}</a>
                <a href="{{ route('midwife.appointments') }}" class="block p-3 bg-white rounded border">Open Appointments</a>
                <a href="{{ route('midwife.patients') }}" class="block p-3 bg-white rounded border">Manage Patients</a>
            </div>
        </aside>
    </section>

    <!-- Bottom row: recent lists -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card p-4">
            <h3 class="text-lg font-medium mb-3">Recent Appointments</h3>
            @if(!empty($recentAppointments ?? []))
                <ul class="divide-y">
                    @foreach($recentAppointments as $appt)
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <div class="text-sm font-medium">{{ $appt->patient_name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ optional($appt->scheduled_at)->format('M d, H:i') }}</div>
                            </div>
                            <div class="text-sm text-gray-600">{{ $appt->status ?? '' }}</div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-gray-500">No appointments scheduled for today.</div>
            @endif
        </div>

        <div class="card p-4">
            <h3 class="text-lg font-medium mb-3">Inventory Status</h3>
            <div class="text-sm text-gray-600">{{ $lowStockTotal > 0 ? $lowStockTotal . ' item(s) need restocking.' : 'No critical low stock items at the moment.' }}</div>

            <div class="mt-4">
                <a href="{{ route('midwife.inventory') }}" class="text-sm text-amber-600 hover:underline">Open Inventory</a>
            </div>
        </div>
    </section>
</div>
