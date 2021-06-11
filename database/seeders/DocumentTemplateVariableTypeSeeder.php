<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTemplateVariableType;

class DocumentTemplateVariableTypeSeeder extends Seeder
{
    public function run()
    {
        DocumentTemplateVariableType::create([
            'name' => 'Строка',
            'description' => '',
            'key' => 'string',
        ]);

        DocumentTemplateVariableType::create([
            'name' => 'Талица',
            'description' => '',
            'key' => 'table',
        ]);

        DocumentTemplateVariableType::create([
            'name' => 'Блок',
            'description' => '',
            'key' => 'block',
        ]);

        DocumentTemplateVariableType::create([
            'name' => 'Изображение',
            'description' => '',
            'key' => 'image',
        ]);

        DocumentTemplateVariableType::create([
            'name' => 'Из числа в строку',
            'description' => '',
            'key' => 'number2string',
        ]);
    }
}