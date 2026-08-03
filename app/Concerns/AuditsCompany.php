<?php

namespace App\Concerns;

/**
 * log can be scoped per company like the rest of the app. (NFR-1, F-101)
 *
 * transformAudit() is the hook owen-it/laravel-auditing exposes for extra
 * columns - see Contracts\Auditable. Models whose company is not a direct
 * column override auditCompanyId() instead of this method.
 */
trait AuditsCompany
{
    public function transformAudit(array $data): array
    {
        $data['company_id'] = $this->auditCompanyId();

        return $data;
    }

    protected function auditCompanyId(): ?int
    {
        return $this->company_id;
    }
}
