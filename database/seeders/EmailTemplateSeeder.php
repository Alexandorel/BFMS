<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = config('email_templates.defaults', []);
        Company::query()->select('id')->chunkById(100, function($companies) use ($defaults){
            foreach($companies as $company){
                foreach($defaults as $type => $template){
                    EmailTemplate::query()->updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'type' => $type,
                        ],
                        [
                            'subject' => $template['subject'],
                            'body' => $template['body'],
                        ]
                    );
                }
            }
        });
    }
}
