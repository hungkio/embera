<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class WordTemplateService
{
    public function generateHtmlFromDocx(string $templatePath, array $data): string
    {
        $processor = new TemplateProcessor($templatePath);

        foreach ($data as $key => $value) {
            $processor->setValue($key, $value);
        }

        $tempPath = storage_path('app/temp_' . uniqid() . '.docx');
        $processor->saveAs($tempPath);

        return $this->convertToHtml($tempPath);
    }

    public function convertToHtml(string $path): string
    {
        $phpWord = IOFactory::load($path, 'Word2007');
        $writer = IOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
