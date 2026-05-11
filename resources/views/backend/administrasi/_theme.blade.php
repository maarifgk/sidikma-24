<style>
    .admin-module-page {
        --module-primary: #12643a;
        --module-blue: #1d6fa5;
        --module-blue-dark: #114d76;
        --module-soft: #e8f4f1;
        --module-soft-blue: #edf6fc;
        --module-border: rgba(18, 100, 58, 0.12);
        --module-text: #163024;
        --module-muted: #6f7f77;
        position: relative;
    }

    .admin-module-page::before,
    .admin-module-page::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        z-index: 0;
    }

    .admin-module-page::before {
        width: 230px;
        height: 230px;
        top: -18px;
        right: 3%;
        background: rgba(29, 111, 165, 0.08);
    }

    .admin-module-page::after {
        width: 180px;
        height: 180px;
        bottom: 10px;
        left: 1%;
        background: rgba(18, 100, 58, 0.08);
    }

    .admin-module-page > * {
        position: relative;
        z-index: 1;
    }

    .admin-module-panel,
    .admin-module-info-card,
    .admin-module-table-card,
    .admin-module-page .container.mt-4,
    .admin-module-page .container {
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 40px rgba(21, 53, 40, 0.08);
    }

    .admin-module-panel {
        border: 0;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .admin-module-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.4rem 1.5rem 1rem !important;
        border-bottom: 1px solid rgba(18, 100, 58, 0.08);
        background:
            radial-gradient(circle at top right, rgba(29, 111, 165, 0.08), transparent 30%),
            radial-gradient(circle at bottom left, rgba(18, 100, 58, 0.08), transparent 24%),
            linear-gradient(180deg, #ffffff 0%, #fbfefd 100%);
    }

    .admin-module-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.76rem;
        margin-bottom: 0.8rem;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--module-soft) 0%, var(--module-soft-blue) 100%);
        color: var(--module-primary);
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .admin-module-title {
        margin: 0;
        color: var(--module-text);
        font-size: clamp(1.55rem, 2.1vw, 2.1rem);
        font-weight: 700;
        line-height: 1.18;
    }

    .admin-module-subtitle {
        margin: 0.35rem 0 0;
        color: var(--module-muted);
        font-size: 0.92rem;
        line-height: 1.65;
    }

    .admin-module-cta {
        border-radius: 16px !important;
        padding: 0.82rem 1.15rem !important;
        background: linear-gradient(135deg, var(--module-primary) 0%, var(--module-blue) 100%) !important;
        border: 0 !important;
        box-shadow: 0 14px 28px rgba(21, 53, 40, 0.12);
        font-weight: 700 !important;
        white-space: nowrap;
    }

    .admin-module-panel > .card-body {
        padding: 1.25rem 1.35rem 1.35rem;
    }

    .admin-module-info-card {
        border: 1px solid var(--module-border);
        overflow: hidden;
        margin-bottom: 0;
    }

    .admin-module-info-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        text-decoration: none;
        background: linear-gradient(135deg, #0f5a35 0%, #12643a 52%, #1d6fa5 100%) !important;
    }

    .admin-module-info-head h6 {
        margin: 0;
        color: #fff !important;
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.5;
    }

    .admin-module-info-card .card-body {
        padding: 1rem 1.1rem 1.1rem;
    }

    .admin-module-page .table {
        margin-bottom: 0;
        --bs-table-striped-bg: rgba(237, 246, 252, 0.42);
    }

    .admin-module-page .table > :not(caption) > * > * {
        padding: 0.88rem 0.95rem;
        vertical-align: middle;
        color: #31443a;
        border-bottom-color: rgba(18, 100, 58, 0.08);
    }

    .admin-module-page .table thead th {
        color: var(--module-text);
        font-size: 0.84rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background: rgba(232, 244, 241, 0.58);
        border-bottom: 1px solid rgba(18, 100, 58, 0.1);
    }

    .admin-module-table-card,
    .admin-module-page .container.mt-4,
    .admin-module-page .container {
        width: 100%;
        max-width: 100%;
        margin-top: 1.35rem !important;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(18, 100, 58, 0.08);
    }

    .admin-module-page .btn {
        border-radius: 12px;
        font-weight: 600;
    }

    .admin-module-page .btn-sm {
        border-radius: 10px;
    }

    .admin-module-page .btn-warning {
        color: #4f3b04;
        background: #f7d979;
        border-color: #f7d979;
    }

    .admin-module-page .btn-danger {
        background: #d9534f;
        border-color: #d9534f;
    }

    .admin-module-page .btn-success {
        background: #198754;
        border-color: #198754;
    }

    .admin-module-page .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-module-page .data-table td {
        padding: 0.9rem 0.95rem;
        color: #31443a;
        border-bottom: 1px solid rgba(18, 100, 58, 0.08);
        vertical-align: top;
    }

    .admin-module-page .data-table tr:last-child td {
        border-bottom: 0;
    }

    .admin-module-page .data-table .label {
        width: 42%;
        color: var(--module-text);
        font-weight: 600;
    }

    .admin-module-page .modal-content {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.18);
    }

    .admin-module-page hr {
        border-top-color: rgba(18, 100, 58, 0.1) !important;
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
    }

    @media (max-width: 767.98px) {
        .admin-module-header {
            flex-direction: column;
            align-items: stretch;
        }

        .admin-module-cta {
            width: 100%;
            text-align: center;
        }

        .admin-module-panel > .card-body,
        .admin-module-page .container.mt-4,
        .admin-module-page .container {
            padding: 1rem;
        }

        .admin-module-info-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
