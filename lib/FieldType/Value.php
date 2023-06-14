<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\FieldType;

use Ibexa\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    public string $id;
    public string $source;

    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        string $id = '',
        string $source = Type::SOURCE_CLOUDFLARE
    ) {
        $this->id = $id;
        $this->source = $source;
    }

    public function __toString()
    {
        return $this->id;
    }
}
