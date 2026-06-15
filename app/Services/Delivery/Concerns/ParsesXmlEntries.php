<?php

namespace App\Services\Delivery\Concerns;

trait ParsesXmlEntries
{
    protected function parseXmlEntries(string $body): array
    {
        if ($body === '') {
            return [];
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if ($xml === false) {
            return [];
        }

        $entries = [];

        foreach ($xml->Entry as $entry) {
            $row = [];
            foreach ($entry->children() as $child) {
                $row[$child->getName()] = trim((string) $child);
            }
            if ($row !== []) {
                $entries[] = $row;
            }
        }

        return $entries;
    }
}
