<style>
    .fleet-module-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .fleet-module-hero {
        align-items: center;
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 18px;
    }

    .fleet-module-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .fleet-module-title {
        color: #130d0d;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 0;
        margin: 0;
    }

    .fleet-module-copy {
        color: #64748b;
        font-size: 14px;
        margin: 6px 0 0;
        max-width: 760px;
    }

    .fleet-module-action {
        align-items: center;
        background: #efc242;
        border: 0;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
    }

    .fleet-module-action:hover {
        background: #d9ad32;
        color: #130d0d;
    }

    .fleet-stat-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-bottom: 18px;
    }

    .fleet-stat-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        padding: 16px;
    }

    .fleet-stat-card span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .fleet-stat-card strong {
        color: #130d0d;
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
        margin-top: 8px;
    }

    .fleet-filter-card,
    .fleet-table-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
    }

    .fleet-filter-card {
        margin-bottom: 18px;
        padding: 14px;
    }

    .fleet-filter-card .form-control {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .fleet-table-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eef2f7;
        padding: 16px 18px;
    }

    .fleet-table-card .card-title {
        color: #130d0d;
        font-size: 16px;
        font-weight: 900;
    }

    .fleet-table {
        margin: 0;
    }

    .fleet-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e5eaf1;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .fleet-table tbody td {
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .fleet-table tbody tr:hover {
        background: #f8fafc;
    }

    .fleet-vehicle-pill {
        background: rgba(239, 194, 66, 0.22);
        border-radius: 999px;
        color: #130d0d;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        padding: 6px 10px;
        white-space: nowrap;
    }

    .fleet-actions {
        display: inline-flex;
        gap: 6px;
    }

    .fleet-actions .btn {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        height: 34px;
        justify-content: center;
        width: 34px;
    }

    .fleet-empty-state {
        color: #64748b;
        padding: 34px 12px;
        text-align: center;
    }

    .fleet-module-modal {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .fleet-module-modal .modal-header {
        background: #130d0d;
        border: 0;
        color: #ffffff;
        padding: 18px 22px;
    }

    .fleet-module-modal .modal-title {
        font-size: 18px;
        font-weight: 900;
    }

    .fleet-module-modal .btn-close {
        filter: invert(1);
    }

    .fleet-module-modal .modal-body {
        background: #f8fafc;
        padding: 20px 22px;
    }

    .fleet-form-section {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        margin-bottom: 14px;
        padding: 16px;
    }

    .fleet-form-section-title {
        color: #130d0d;
        display: block;
        font-size: 13px;
        font-weight: 900;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .fleet-module-modal .form-label {
        color: #475569;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .fleet-module-modal .form-control {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .fleet-module-modal textarea.form-control {
        min-height: 92px;
    }

    .fleet-module-modal .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e5eaf1;
        padding: 14px 22px;
    }

    @media (max-width: 991px) {
        .fleet-module-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 14px;
        }

        .fleet-stat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .fleet-stat-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
