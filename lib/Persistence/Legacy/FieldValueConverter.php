<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\Persistence\Legacy;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

use function array_key_exists;
use function json_decode;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

class FieldValueConverter implements Converter
{
    /**
     * @throws \JsonException
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
        if (empty($value->data['id'])) {
            $storageFieldValue->dataText = null;
        } else {
            $storageFieldValue->dataText = json_encode($value->data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        }

        $storageFieldValue->sortKeyString = (string) $value->sortKey;
    }

    /**
     * @throws \JsonException
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
        if ($value->dataText) {
            $fieldValue->data = json_decode($value->dataText, true, 512, JSON_THROW_ON_ERROR);
        } else {
            $fieldValue->data = null;
        }

        $fieldValue->sortKey = $value->sortKeyString;
    }

    /**
     * @throws \JsonException
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef): void
    {
        $storageDef->dataText5 = json_encode($fieldDef->fieldTypeConstraints->fieldSettings, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @throws \JsonException
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef): void
    {
        if (empty($storageDef->dataText5)) {
            return;
        }

        $settingsData = json_decode($storageDef->dataText5, true, 512, JSON_THROW_ON_ERROR);
        $fieldSettings = &$fieldDef->fieldTypeConstraints->fieldSettings;

        if (array_key_exists('allowedExternalVideoSource', $settingsData)) {
            $fieldSettings['allowedExternalVideoSource'] = $settingsData['allowedExternalVideoSource'];
        }
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}
