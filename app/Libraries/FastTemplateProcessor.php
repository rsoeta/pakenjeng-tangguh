<?php

namespace App\Libraries;

use PhpOffice\PhpWord\Escaper\Xml;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class FastTemplateProcessor extends TemplateProcessor
{
    public function cloneRowAndSetValues($search, $values): void
    {
        $this->cloneRow($search, count($values));

        $xmlEscaper = Settings::isOutputEscapingEnabled() ? new Xml() : null;
        $replacements = [];

        foreach ($values as $rowKey => $rowData) {
            $rowNumber = $rowKey + 1;
            foreach ($rowData as $macro => $replace) {
                $replace = static::ensureUtf8Encoded($replace);
                if ($xmlEscaper !== null) {
                    $replace = $xmlEscaper->escape($replace);
                }

                $replacements[static::ensureMacroCompleted($macro . '#' . $rowNumber)] =
                    $this->replaceCarriageReturns($replace);
            }
        }

        $this->tempDocumentMainPart = strtr($this->tempDocumentMainPart, $replacements);
    }
}
