<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideoBundle\Form\Field;

use Ibexa\Contracts\Core\Repository\FieldType;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Value;
use Symfony\Component\Form\DataTransformerInterface;

class FieldValueTransformer implements DataTransformerInterface
{
    private FieldType $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function transform($value): ?array
    {
        if (!$value instanceof Value) {
            return null;
        }

        return [
            'id' => $value->id,
            'source' => $value->source,
        ];
    }

    public function reverseTransform($value): ?Value
    {
        return new Value(
            $value['id'] === null ? "" : $value['id'],
            $value['source'],
        );
    }
}
