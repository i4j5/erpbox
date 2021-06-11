<?php

namespace App\Actions\WordTemplate;

use \PhpOffice\PhpWord\TemplateProcessor;

class CreateNewDocument
{

    private $templatePath;

    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
    }
    
    public function execute($pathToSave, $data = [])
    {

        $document = new TemplateProcessor($this->templatePath);

        //$stringVariables = [];
        //$imageVariables = [];

        foreach ($data as $variable) {

            if (isset($variable['type'])) {

                if ($variable['type'] === 'string') {
                    // $stringVariables[$variable['name']] = $variable['value'];
                    $document->setValue($variable['name'], $variable['value']);
                }

                if ($variable['type'] === 'image') {
                    //$imageVariables[$variable['name']] = $variable['value'];
                    $document->setImageValue($variable['name'], $variable['value']);
                }

                if ($variable['type'] === 'block') {
                    $document->cloneBlock($variable['name'], 0, true, false, $variable['value']);
                }

                if ($variable['type'] === 'table') {
                    $document->cloneRowAndSetValues($variable['index'], $variable['value']);
                }

            }
        }

        // Заполняем строковые значения 
        // $document->setValues($stringVariables );


        $document->saveAs($pathToSave);
    }
}
