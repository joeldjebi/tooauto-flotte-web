<style>
    .dealer-detail-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .dealer-detail-hero,
    .dealer-detail-card,
    .dealer-detail-table-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
    }

    .dealer-detail-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 18px;
        position: relative;
        overflow: hidden;
    }

    .dealer-detail-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .dealer-detail-hero > * {
        position: relative;
        z-index: 1;
    }

    .dealer-detail-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .dealer-detail-title {
        color: #130d0d;
        font-size: 24px;
        font-weight: 900;
        margin: 0;
    }

    .dealer-detail-copy {
        color: #64748b;
        font-size: 14px;
        margin: 6px 0 0;
    }

    .dealer-detail-actions {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .dealer-detail-btn {
        align-items: center;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        color: #334155;
        display: inline-flex;
        font-size: 13px;
        font-weight: 850;
        gap: 8px;
        min-height: 40px;
        padding: 9px 12px;
    }

    .dealer-detail-btn.primary {
        background: #efc242;
        border-color: #efc242;
        color: #130d0d;
    }

    .dealer-detail-btn:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .dealer-detail-btn.primary:hover {
        background: #d8ab28;
        border-color: #d8ab28;
        color: #130d0d;
    }

    .dealer-detail-stats {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-bottom: 18px;
    }

    .dealer-detail-stat {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
        min-height: 92px;
        padding: 16px;
    }

    .dealer-detail-stat span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .dealer-detail-stat strong {
        color: #130d0d;
        display: block;
        font-size: 24px;
        font-weight: 950;
        margin-top: 8px;
    }

    .dealer-detail-tools {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .dealer-detail-search {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        display: flex;
        gap: 10px;
        max-width: 430px;
        padding: 0 12px;
        width: 100%;
    }

    .dealer-detail-search i {
        color: #64748b;
    }

    .dealer-detail-search input {
        border: 0;
        color: #130d0d;
        font-size: 13px;
        min-height: 42px;
        outline: 0;
        width: 100%;
    }

    .dealer-detail-chip {
        align-items: center;
        background: #130d0d;
        border-radius: 999px;
        color: #ffffff;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        min-height: 36px;
        padding: 8px 12px;
        white-space: nowrap;
    }

    .dealer-detail-table-card {
        overflow: hidden;
    }

    .dealer-detail-table-card .card-header {
        align-items: center;
        background: transparent;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        padding: 16px 18px;
    }

    .dealer-detail-table-card .card-title {
        color: #130d0d;
        font-size: 16px;
        font-weight: 900;
    }

    .dealer-detail-table {
        margin: 0;
    }

    .dealer-detail-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e5eaf1;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .dealer-detail-table tbody td {
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .dealer-detail-name {
        color: #130d0d;
        display: block;
        font-size: 14px;
        font-weight: 900;
    }

    .dealer-detail-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 3px;
    }

    .dealer-detail-thumb {
        background: #f1f5f9;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        height: 56px;
        object-fit: cover;
        width: 76px;
    }

    .dealer-detail-price {
        color: #130d0d;
        font-size: 14px;
        font-weight: 950;
        white-space: nowrap;
    }

    .dealer-detail-mini {
        color: #64748b;
        font-size: 12px;
        margin: 0;
    }

    .dealer-detail-pill {
        border-radius: 999px;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        padding: 6px 10px;
        white-space: nowrap;
    }

    .dealer-detail-pill.waiting {
        background: #fffbeb;
        color: #b45309;
    }

    .dealer-detail-pill.success {
        background: #ecfdf5;
        color: #047857;
    }

    .dealer-detail-pill.danger {
        background: #fff1f2;
        color: #be123c;
    }

    .dealer-detail-pill.info {
        background: #eff6ff;
        color: #2563eb;
    }

    .dealer-detail-row-actions {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .dealer-detail-action {
        align-items: center;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        color: #334155;
        display: inline-flex;
        font-size: 12px;
        font-weight: 850;
        gap: 6px;
        min-height: 34px;
        padding: 7px 9px;
    }

    .dealer-detail-action:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .dealer-detail-action.primary {
        background: #efc242;
        border-color: #efc242;
        color: #130d0d;
    }

    .dealer-detail-action.primary:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .dealer-detail-action.danger:hover {
        background: #dc2626;
        border-color: #dc2626;
    }

    .dealer-detail-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: #64748b;
        padding: 28px;
        text-align: center;
    }

    .dealer-detail-modal {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .dealer-detail-modal .modal-header {
        background: #130d0d;
        border: 0;
        color: #ffffff;
        padding: 18px 22px;
    }

    .dealer-detail-modal .btn-close {
        filter: invert(1);
    }

    .dealer-detail-modal .modal-body {
        background: #f8fafc;
        padding: 20px 22px;
    }

    .dealer-detail-modal-section {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        padding: 16px;
    }

    .dealer-detail-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dealer-detail-field {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 12px;
    }

    .dealer-detail-field span {
        color: #64748b;
        display: block;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .dealer-detail-field strong {
        color: #130d0d;
        display: block;
        font-size: 14px;
        font-weight: 900;
        margin-top: 4px;
    }

    .dealer-detail-gallery {
        background: #f8fafc;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        height: 420px;
        overflow: hidden;
    }

    .dealer-detail-gallery img {
        height: 420px;
        object-fit: contain;
        width: 100%;
    }

    .dealer-detail-no-result {
        display: none;
        margin-top: 12px;
    }

    @media (max-width: 991px) {
        .dealer-detail-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
        }

        .dealer-detail-actions,
        .dealer-detail-tools {
            align-items: stretch;
            flex-direction: column;
            width: 100%;
        }

        .dealer-detail-btn,
        .dealer-detail-search {
            max-width: none;
            width: 100%;
        }

        .dealer-detail-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .dealer-detail-stats,
        .dealer-detail-grid {
            grid-template-columns: 1fr;
        }

        .dealer-detail-gallery,
        .dealer-detail-gallery img {
            height: 280px;
        }
    }
</style>
