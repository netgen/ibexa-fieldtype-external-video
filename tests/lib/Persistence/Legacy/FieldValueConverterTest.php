<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\Tests\Unit\Persistence\Legacy;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Type;
use Netgen\IbexaFieldTypeExternalVideo\Persistence\Legacy\FieldValueConverter;
use PHPUnit\Framework\TestCase;

/**
 * @group converter
 */
class FieldValueConverterTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\RelationConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new FieldValueConverter();
    }

    public function testToStorageFieldDefinition()
    {
        $fieldDefinition = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => new FieldTypeConstraints(
                    [
                        'fieldSettings' => [
                            'allowedExternalVideoSources' => [Type::SOURCE_CLOUDFLARE],
                        ],
                    ],
                ),
            ],
        );

        $expectedStorageFieldDefinition = new StorageFieldDefinition();
        $expectedStorageFieldDefinition->dataText5 =
            <<<'DATATEXT'
            {
                "allowedExternalVideoSources": [
                    "cloudflare"
                ]
            }
            DATATEXT;

        $actualStorageFieldDefinition = new StorageFieldDefinition();
        $this->converter->toStorageFieldDefinition($fieldDefinition, $actualStorageFieldDefinition);

        self::assertEquals(
            $expectedStorageFieldDefinition,
            $actualStorageFieldDefinition,
        );
    }

    public function testToFieldDefinition()
    {
        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 =
            <<<'DATATEXT'
            {
                "allowedExternalVideoSources": [
                    "cloudflare"
                ]
            }
            DATATEXT;

        $expectedFieldDefinition = new PersistenceFieldDefinition();
        $expectedFieldDefinition->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'allowedExternalVideoSources' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
        );

        $actualFieldDefinition = new PersistenceFieldDefinition();
        $this->converter->toFieldDefinition($storageFieldDefinition, $actualFieldDefinition);

        self::assertEquals($expectedFieldDefinition, $actualFieldDefinition);
    }

    public function testToFieldDefinitionWithDataText5Null()
    {
        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 = null;

        $expectedFieldDefinition = new PersistenceFieldDefinition();
        $expectedFieldDefinition->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'allowedExternalVideoSources' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
        );

        $actualFieldDefinition = new PersistenceFieldDefinition();
        $actualFieldDefinition->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'allowedExternalVideoSources' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
        );

        $this->converter->toFieldDefinition($storageFieldDefinition, $actualFieldDefinition);
        self::assertEquals($expectedFieldDefinition, $actualFieldDefinition);
    }

    public function testToFieldDefinitionWithInvalidDataText5Format()
    {
        $this->expectException(\JsonException::class);

        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 = 'String that is not in a valid json format';

        $fieldDefinition = new PersistenceFieldDefinition();
        $this->converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);
    }

    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText =
            <<< 'DATATEXT'
            {
                "id": "cloudflare_video_id",
                "source": "cloudflare"
            }
            DATATEXT;
        $storageFieldValue->sortKeyString = 'id';

        $expectedFieldValue = new FieldValue();
        $expectedFieldValue->data = [
            'id' => 'cloudflare_video_id',
            'source' => 'cloudflare',
        ];
        $expectedFieldValue->sortKey = 'id';

        $actualFieldValue = new FieldValue();
        $this->converter->toFieldValue($storageFieldValue, $actualFieldValue);

        self::assertEquals($expectedFieldValue, $actualFieldValue);
    }

    public function testToFieldValueWithDataTextNull()
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = null;
        $storageFieldValue->sortKeyString = 'id';

        $expectedFieldValue = new FieldValue();
        $expectedFieldValue->data = null;
        $expectedFieldValue->sortKey = 'id';

        $actualFieldValue = new FieldValue();
        $this->converter->toFieldValue($storageFieldValue, $actualFieldValue);

        self::assertEquals($expectedFieldValue, $actualFieldValue);
    }

    public function testToFieldValueWithInvalidDataTextFormat()
    {
        $this->expectException(\JsonException::class);

        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = 'String that is not in a valid json format';

        $fieldValue = new FieldValue();
        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
    }

    public function testToStorageValue()
    {
        $fieldValue = new FieldValue();
        $fieldValue->data = [
            'id' => 'cloudflare_video_id',
            'source' => 'cloudflare',
        ];
        $fieldValue->sortKey = 'id';

        $expectedStorageFieldValue = new StorageFieldValue();
        $expectedStorageFieldValue->dataText =
            <<< 'DATATEXT'
            {
                "id": "cloudflare_video_id",
                "source": "cloudflare"
            }
            DATATEXT;
        $expectedStorageFieldValue->sortKeyString = 'id';

        $actualStorageFieldValue = new StorageFieldValue();
        $this->converter->toStorageValue($fieldValue, $actualStorageFieldValue);

        self::assertEquals($expectedStorageFieldValue, $actualStorageFieldValue);
    }

    public function testToStorageValueWithIdNull()
    {
        $fieldValue = new FieldValue();
        $fieldValue->data = [
            'id' => null,
            'source' => 'cloudflare',
        ];
        $fieldValue->sortKey = 'id';

        $expectedStorageFieldValue = new StorageFieldValue();
        $expectedStorageFieldValue->dataText = null;
        $expectedStorageFieldValue->sortKeyString = 'id';

        $actualStorageFieldValue = new StorageFieldValue();
        $this->converter->toStorageValue($fieldValue, $actualStorageFieldValue);

        self::assertEquals($expectedStorageFieldValue, $actualStorageFieldValue);
    }

    public function testGetIndexColumn()
    {
        $expectedIndexColumn = 'sort_key_string';

        $actualIndexColumn = $this->converter->getIndexColumn();

        self::assertEquals($expectedIndexColumn, $actualIndexColumn);
    }
}
