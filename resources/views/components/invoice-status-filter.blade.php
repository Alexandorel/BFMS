@props([
    'target',
    'compact' => false,
    'disabled' => false,
])

<label class="relative w-full sm:w-auto">
    <span class="sr-only">Filtrează după stare</span>
    <select data-invoice-status-filter data-filter-target="{{ $target }}"
            @disabled($disabled)
            class="{{ $compact ? 'ui-toolbar-select !h-10 text-xs' : 'form-input min-w-44 pr-9' }}">
        <option value="">Toate stările</option>
        @foreach (\App\Enums\InvoiceStatus::cases() as $status)
            <option value="{{ $status->value }}">{{ $status->label() }}</option>
        @endforeach
    </select>
    <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-slate-400"
         fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
</label>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-invoice-status-filter]').forEach(function (select) {
                select.addEventListener('change', function () {
                    const target = document.getElementById(this.dataset.filterTarget);
                    if (! target) return;

                    const selectedStatus = this.value;
                    const rows = target.querySelectorAll('[data-invoice-status]');
                    let visibleRows = 0;

                    rows.forEach(function (row) {
                        const visible = ! selectedStatus || row.dataset.invoiceStatus === selectedStatus;
                        row.classList.toggle('hidden', ! visible);
                        if (visible) visibleRows++;
                    });

                    target.querySelector('[data-filter-empty]')?.classList.toggle('hidden', visibleRows > 0);
                });
            });
        });
    </script>
@endonce
