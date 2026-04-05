<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceOption;
use App\Models\ServiceOptionValue;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::updateOrCreate(
            ['service_name' => 'Dental Cleaning'],
            [
                'description' => 'Professional cleaning for healthier teeth and gums.',
                'estimated_duration_minutes' => 60,
                'estimated_price' => 1200,
                'is_active' => true,
            ]
        );

        Service::updateOrCreate(
            ['service_name' => 'Consultation'],
            [
                'description' => 'General dental consultation and treatment planning.',
                'estimated_duration_minutes' => 30,
                'estimated_price' => 500,
                'is_active' => true,
            ]
        );

        $braces = Service::updateOrCreate(
            ['service_name' => 'Braces'],
            [
                'description' => 'Orthodontic consultation, new braces, or adjustment.',
                'estimated_duration_minutes' => 120,
                'estimated_price' => 3000,
                'is_active' => true,
            ]
        );

        $question1 = ServiceOption::updateOrCreate(
            ['service_id' => $braces->service_id, 'option_name' => 'Braces Type'],
            [
                'option_type' => 'radio',
                'is_required' => true,
            ]
        );

        foreach (['New Braces', 'Adjustment'] as $index => $label) {
            ServiceOptionValue::updateOrCreate(
                ['option_id' => $question1->option_id, 'value_label' => $label],
                ['sort_order' => $index + 1]
            );
        }

        $question2 = ServiceOption::updateOrCreate(
            ['service_id' => $braces->service_id, 'option_name' => 'Teeth Coverage'],
            [
                'option_type' => 'radio',
                'is_required' => true,
            ]
        );

        foreach (['Upper', 'Lower', 'Both'] as $index => $label) {
            ServiceOptionValue::updateOrCreate(
                ['option_id' => $question2->option_id, 'value_label' => $label],
                ['sort_order' => $index + 1]
            );
        }

        $question3 = ServiceOption::updateOrCreate(
            ['service_id' => $braces->service_id, 'option_name' => 'Rubber / Chain Color'],
            [
                'option_type' => 'select',
                'is_required' => false,
            ]
        );

        foreach (['Red', 'Blue', 'Green', 'Pink', 'None'] as $index => $label) {
            ServiceOptionValue::updateOrCreate(
                ['option_id' => $question3->option_id, 'value_label' => $label],
                ['sort_order' => $index + 1]
            );
        }

        ServiceOption::updateOrCreate(
            ['service_id' => $braces->service_id, 'option_name' => 'Extra Notes'],
            [
                'option_type' => 'textarea',
                'is_required' => false,
            ]
        );
    }
}
