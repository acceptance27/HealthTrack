@props(['title' => 'Patient Portal', 'description' => 'Your personal records and appointments.'])

<style>
    .patient-dashboard {
        --ev-bg: #f6f1e8;
        --ev-bg-deep: #e8e0d1;
        --ev-surface: #ffffff;
        --ev-surface-muted: #f2ede4;
        --ev-ink: #1f2421;
        --ev-ink-soft: #3f473f;
        --ev-accent: #0f6b5f;
        --ev-accent-strong: #0c4f46;
        --ev-accent-warm: #d0742a;
        --ev-border: rgba(31, 36, 33, 0.12);
        --ev-shadow: 0 12px 30px rgba(15, 20, 16, 0.08);
        position: relative;
        display: grid;
        gap: 16px;
        margin: -8px;
        padding: 16px;
        overflow: hidden;
        border-radius: 20px;
        color: var(--ev-ink);
        font-family: "Trebuchet MS", "Gill Sans", "Candara", sans-serif;
        background:
            radial-gradient(circle at top right, #f3dcc8, transparent 44%),
            radial-gradient(circle at 12% 18%, rgba(15, 107, 95, 0.12), transparent 36%),
            linear-gradient(140deg, var(--ev-bg) 0%, var(--ev-bg-deep) 100%);
    }

    .patient-dashboard * {
        box-sizing: border-box;
    }

    .mw-card {
        background: var(--ev-surface);
        border: 1px solid var(--ev-border);
        border-radius: 16px;
        box-shadow: var(--ev-shadow);
    }

    .mw-page-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 18px;
    }

    .mw-page-header h2 {
        margin: 0 0 4px;
        font-family: "Palatino Linotype", "Book Antiqua", serif;
        font-size: 24px;
        line-height: 1.1;
        color: var(--ev-ink);
    }

    .mw-page-header p,
    .mw-muted {
        color: var(--ev-ink-soft);
    }

    .mw-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: fit-content;
        padding: 6px 12px;
        border: 1px solid var(--ev-border);
        border-radius: 999px;
        background: var(--ev-surface);
        color: var(--ev-accent-strong);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mw-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .mw-metric {
        padding: 16px;
    }

    .mw-metric h3 {
        margin: 0 0 6px;
        color: var(--ev-ink-soft);
        font-size: 12px;
        font-weight: 700;
    }

    .mw-metric p {
        margin: 0;
        color: var(--ev-ink);
        font-size: 24px;
        font-weight: 800;
    }

    .mw-metric .mw-warm {
        color: var(--ev-accent-warm);
    }

    .mw-metric .mw-green {
        color: var(--ev-accent);
    }

    .mw-metric .mw-red {
        color: #b2453e;
    }

    .mw-two-col {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 14px;
    }

    .mw-panel {
        padding: 16px;
    }

    .mw-panel h3 {
        margin: 0 0 10px;
        color: var(--ev-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .mw-status-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
        background: var(--ev-surface);
        border: 1px solid var(--ev-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .mw-status-table th,
    .mw-status-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--ev-border);
        border-right: 1px solid var(--ev-border);
        text-align: left;
    }

    .mw-status-table th:last-child,
    .mw-status-table td:last-child {
        border-right: none;
    }

    .mw-status-table tr:last-child td {
        border-bottom: none;
    }

    .mw-status-table th {
        background: var(--ev-surface-muted);
        color: var(--ev-ink-soft);
        font-weight: 800;
    }

    .mw-status-table tbody tr:nth-child(even) {
        background: rgba(242, 237, 228, 0.4);
    }

    .mw-status-table tbody tr:hover {
        background: rgba(15, 107, 95, 0.04);
    }

    .mw-rag-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
        margin-bottom: 10px;
        padding: 10px 16px;
        border-radius: 999px;
        background: rgba(47, 143, 91, 0.2);
        color: #18623b;
        font-weight: 800;
    }

    .mw-empty-state {
        display: grid;
        place-content: center;
        min-height: 100px;
        border: 1px dashed var(--ev-border);
        border-radius: 14px;
        background: var(--ev-surface-muted);
        color: var(--ev-ink-soft);
        font-size: 13px;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .mw-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mw-two-col {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .patient-dashboard {
            margin: -12px;
            padding: 16px;
            border-radius: 18px;
        }

        .mw-page-header {
            flex-direction: column;
        }

        .mw-summary-grid {
            grid-template-columns: 1fr;
        }

        .mw-pill {
            width: 100%;
        }
    }
</style>

<div class="patient-dashboard">
    <section class="mw-card mw-page-header">
        <div>
            <h2>{{ $title }}</h2>
            <p>{{ $description }}</p>
        </div>
        <span class="mw-pill">Patient Portal</span>
    </section>

    {{ $slot }}
</div>
