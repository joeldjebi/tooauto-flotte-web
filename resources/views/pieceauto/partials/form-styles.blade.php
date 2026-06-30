<style>
    .piece-form-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .piece-form-hero,
    .piece-form-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
    }

    .piece-form-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 18px;
    }

    .piece-form-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .piece-form-title {
        color: #130d0d;
        font-size: 24px;
        font-weight: 900;
        margin: 0;
    }

    .piece-form-copy {
        color: #64748b;
        font-size: 14px;
        margin: 6px 0 0;
        max-width: 760px;
    }

    .piece-form-card {
        padding: 18px;
    }

    .piece-form-section {
        background: #f8fafc;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        margin-bottom: 14px;
        padding: 16px;
    }

    .piece-form-section-title {
        color: #130d0d;
        display: block;
        font-size: 13px;
        font-weight: 900;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .piece-form-card .form-label {
        color: #475569;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .piece-form-card .form-control,
    .piece-form-card .form-select {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .piece-upload-box {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 14px;
    }

    .piece-upload-box small {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 700;
        margin-top: 8px;
    }

    .piece-current-image {
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        display: block;
        max-height: 220px;
        object-fit: cover;
        width: 100%;
    }

    .piece-form-footer {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding-top: 4px;
    }

    .piece-form-primary,
    .piece-form-secondary {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        min-height: 42px;
        padding: 9px 14px;
    }

    .piece-form-primary {
        background: #efc242;
        border: 0;
        color: #130d0d;
    }

    .piece-form-secondary {
        background: #ffffff;
        border: 1px solid #dbe3ee;
        color: #334155;
    }

    @media (max-width: 767px) {
        .piece-form-hero,
        .piece-form-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
