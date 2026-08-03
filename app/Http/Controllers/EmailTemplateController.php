<?php

namespace App\Http\Controllers;

use App\Enums\EmailTemplateType;
use App\Models\EmailTemplate;
use Illuminate\http\Request;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->role === 'administrator' , 403);
        $user = $request->user();
        $companies = $user->companies()->orderBy('name')->get();
        $company = $companies->firstWhere('id', session('active_company_id')) ?? $companies->first();
        abort_if(! $company, 404, 'Nu există companie disponibilă.');
        $defaults = config('email_templates.defaults', []);
        $variables = config('email_templates.variables', []);
        $existing = EmailTemplate::query()
        ->where('company_id',$company->id)
        ->get()
        ->keyBy(fn (EmailTemplate $t) => $t->type->value);

        $types = [
            EmailTemplateType::InvoiceIssued,
            EmailTemplateType::DueReminder,
            EmailTemplateType::OverdueAlert,
        ];
        $templates = [];
        foreach ($types as $type){
            $row = $existing->get($type->value);
            $templates[] = [
                'type' => $type->value,
                'label' => $type->label(),
                'subject' =>$row?->subject ?? ($defaults[$type->value]['subject'] ?? ''),
                'body' => $row?->body ?? ($defaults[$type->value]['body'] ?? ''),
            ];
        }
        return view('administrator.settings.email-templates',[
            'user' => $user,
            'companies' => $companies,
            'company' => $company,
            'templates' => $templates,
            'variables' => $variables,
        ]);
    }
    public function update(Request $request, string $type)
{
    abort_unless($request->user()?->role === 'administrator', 403);
    $allowedTypes = array_map(fn ($c) => $c->value, EmailTemplateType::cases());
    $validated = $request->validate([
        'company_id' => ['required', 'integer', 'exists:companies,id'],
        'type' => ['required', Rule::in($allowedTypes)],
        'subject' => ['required', 'string', 'max:255'],
        'body' => ['required', 'string'],
    ]);
    abort_unless($validated['type'] === $type, 422, 'Tip șablon invalid.');
    abort_unless(
        $request->user()->companies()->whereKey($validated['company_id'])->exists(),
        403
    );

    EmailTemplate::updateOrCreate(
        ['company_id' => $validated['company_id'], 'type' => $type],
        ['subject' => $validated['subject'], 'body' => $validated['body']]
    );

    return back()->with('success', 'Șablonul a fost salvat.');
}
}