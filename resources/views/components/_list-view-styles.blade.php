{{--
    List-view styles partial
    ========================
    @include('partials._list-view-styles')
    یا @push('styles') ... @endpush در layout اضافه کنید
--}}
<style>
    /* ══════════════════════════════════════════════
   LIST-VIEW — row layout overrides
   ══════════════════════════════════════════════ */

    /* Container override when list mode is active */
    .items-list-mode {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    /* Each card in list mode */
    .items-list-mode .list-card {
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        align-items: center;
        gap: 0 1.25rem;
        padding: 0.5rem 1rem;
        border-radius: 0.875rem;
        transition: all .22s cubic-bezier(.4, 0, .2, 1);
    }

    /* Checkbox column */
    .bulk-checkbox-col {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ── Custom checkbox ── */
    .bulk-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 1.125rem;
        height: 1.125rem;
        border: 2px solid #4b5563;
        border-radius: .35rem;
        background: transparent;
        cursor: pointer;
        transition: all .18s;
        flex-shrink: 0;
        position: relative;
    }

    .bulk-checkbox:hover {
        border-color: #7c3aed;
        background: rgba(124, 58, 237, .08);
    }

    .bulk-checkbox:checked {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
        border-color: #7c3aed;
        box-shadow: 0 0 10px rgba(124, 58, 237, .35);
    }

    .bulk-checkbox:checked::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 12 10' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.5 5L4.5 8L10.5 2' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 68%;
    }

    /* ── Main content area in list mode ── */
    .list-main-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        min-width: 0;
        flex-wrap: wrap;
    }

    .list-title-group {
        min-width: 0;
        flex-shrink: 0;
        width: 13rem;
    }

    .list-title {
        font-size: .9375rem;
        font-weight: 700;
        color: #1c1917;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color .18s;
    }

    .list-subtitle {
        font-size: .72rem;
        color: #6b7280;
        margin-top: .1rem;
    }

    .list-meta-group {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
        flex: 1;
        min-width: 0;
    }

    .list-meta-item {
        display: flex;
        align-items: center;
        gap: .35rem;
        font-size: .78rem;
        white-space: nowrap;
        color: #9ca3af;
    }

    .list-meta-item svg {
        flex-shrink: 0;
        color: #7c3aed;
    }

    .list-meta-val {
        color: #292524;
        font-weight: 500;
    }

    /* ── Approval pills row ── */
    .list-approvals {
        display: flex;
        gap: .4rem;
        flex-shrink: 0;
    }

    /* ── Right-side actions ── */
    .list-actions {
        display: flex;
        align-items: center;
        gap: .4rem;
        flex-shrink: 0;
    }

    /* ── Selected state ── */
    .bulk-item-selected.list-card {
        background: rgba(124, 58, 237, .08) !important;
        border-color: rgba(124, 58, 237, .3) !important;
        box-shadow: 0 0 0 1px rgba(124, 58, 237, .2), inset 0 0 20px rgba(124, 58, 237, .04);
    }

    /* ── Grid mode: hide checkboxes by default (shown only in list mode) ── */
    .view-grid-mode .bulk-checkbox-col {
        display: none;
    }

    /* In grid mode, bulk-checkbox-col is hidden; show it in list mode only via parent class */
    .view-list-mode .bulk-checkbox-col {
        display: flex;
    }

    /* Override اختصاصی برای WorkRequest که بج‌های اضافه (نوع/مرحله) داره */
    .wr-list-card .list-title-group {
        width: 20rem;
        flex-shrink: 0;
    }

    .wr-list-card .list-subtitle {
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .wr-list-card .list-main-content {
        flex-wrap: nowrap;
    }

    .wr-list-card .list-meta-group {
        flex: 0 1 auto;
        gap: 0.75rem;
        overflow: hidden;
    }

    .wr-list-card .list-meta-item {
        font-size: 0.8rem;
    }

    /* ══════════════════════════════════════════════
   RESPONSIVE
   ══════════════════════════════════════════════ */
    @media (max-width: 768px) {
        .items-list-mode .list-card {
            grid-template-columns: auto 1fr auto;
            row-gap: .5rem;
        }

        .list-meta-group {
            display: none;
            /* hide meta on small screens, rely on show page */
        }

        .list-approvals {
            display: none;
        }
    }
</style>