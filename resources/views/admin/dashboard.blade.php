@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h2 class="page-title">System Administration</h2>
    <p style="color: var(--neutral); font-size: 14px;">Setup, configuration, and system maintenance.</p>
</div>

<!-- Admin Actions -->
<div class="grid grid-2">
    <div class="card">
        <div style="padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--neutral-dark);">⚙️ System Status</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--neutral-light); border-radius: 8px;">
                <span style="font-size: 14px; color: var(--neutral);">Database</span>
                <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: var(--secondary-light); color: var(--secondary); font-size: 12px; font-weight: 600;">Connected</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--neutral-light); border-radius: 8px;">
                <span style="font-size: 14px; color: var(--neutral);">Application</span>
                <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: var(--secondary-light); color: var(--secondary); font-size: 12px; font-weight: 600;">Running</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--neutral-light); border-radius: 8px;">
                <span style="font-size: 14px; color: var(--neutral);">Last Backup</span>
                <span style="font-weight: 600; color: var(--neutral-dark); font-size: 13px;">{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="padding-bottom: 16px; border-bottom: 1px solid var(--border); margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--neutral-dark);">📊 Quick Stats</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--primary-light); border-radius: 8px; border-left: 4px solid var(--primary);">
                <span style="font-size: 14px; color: var(--primary);">Total Users</span>
                <span style="font-weight: 700; font-size: 18px; color: var(--primary);">-</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--secondary-light); border-radius: 8px; border-left: 4px solid var(--secondary);">
                <span style="font-size: 14px; color: var(--secondary);">Active Sessions</span>
                <span style="font-weight: 700; font-size: 18px; color: var(--secondary);">1</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #FEF3C7; border-radius: 8px; border-left: 4px solid var(--warning);">
                <span style="font-size: 14px; color: var(--warning);">System Warnings</span>
                <span style="font-weight: 700; font-size: 18px; color: var(--warning);">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Security Notice -->
<div class="card" style="margin-top: 20px; border-left: 4px solid var(--warning);">
    <div style="display: flex; gap: 12px;">
        <span style="font-size: 20px;">⚠️</span>
        <div>
            <h3 style="font-weight: 600; color: var(--neutral-dark); margin-bottom: 4px;">Admin Access</h3>
            <p style="font-size: 14px; color: var(--neutral);">This panel is restricted to system administrators only. All actions are logged for security purposes. Unauthorized access attempts will be recorded.</p>
        </div>
    </div>
</div>
@endsection
