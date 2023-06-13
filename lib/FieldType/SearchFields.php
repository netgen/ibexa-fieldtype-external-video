<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\FieldType;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\Core\Search\FieldType\StringField;

use function is_string;

class SearchFields implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        $id = $field->value->data['id'] ?? null;

        if (is_string($id)) {
            return [
                new Search\Field(
                    'value',
                    $id,
                    new StringField(),
                ),
            ];
        }

        return [];
    }

    public function getIndexDefinition(): array
    {
        return [
            'value' => new StringField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return 'value';
    }

    public function getDefaultSortField(): string
    {
        return $this->getDefaultMatchField();
    }
}
