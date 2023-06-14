<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\Tests\Integration\Core\Repository\FieldType;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Base\Exceptions\ContentFieldValidationException;
use Ibexa\Tests\Integration\Core\Repository\FieldType\BaseIntegrationTest;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Type;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Value;

/**
 * Integration test for use field type.
 *
 * @group integration
 * @group field-type
 */
class ExternalVideoIntegrationTest extends BaseIntegrationTest
{
    public function getTypeName(): string
    {
        return 'ngexternalvideo';
    }

    public function getSettingsSchema(): array
    {
        return [
            'allowedExternalVideoSource' => [
                'type' => 'array',
                'default' => [
                    Type::SOURCE_CLOUDFLARE,
                ],
            ],
        ];
    }

    public function getValidatorSchema(): array
    {
        return [];
    }

    public function getValidFieldSettings(): array
    {
        return [
            'allowedExternalVideoSource' => [Type::SOURCE_CLOUDFLARE],
        ];
    }

    public function getValidValidatorConfiguration(): array
    {
        return [];
    }

    public function getInvalidFieldSettings(): array
    {
        return [
            'allowedExternalVideoSource' => ['invalid_video_source'],
        ];
    }

    public function getInvalidValidatorConfiguration(): array
    {
        return ['noValidator' => true];
    }

    public function getValidCreationFieldData(): Value
    {
        return new Value('2ce149c61ad6a10ad075017b4ccf8890', 'cloudflare');
    }

    public function provideInvalidCreationFieldData(): array
    {
        return [
            [
                new Value('2ce149c61ad6a10ad075017b4ccf8890', 'vimeo'),
                ContentFieldValidationException::class,
            ],
        ];
    }

    public function getFieldName(): string
    {
        return 'Users';
    }

    public function assertFieldDataLoadedCorrect(Field $field): void
    {
        self::assertInstanceOf(
            Value::class,
            $field->value,
        );

        $expectedData = [
            'id' => '2ce149c61ad6a10ad075017b4ccf8890',
            'source' => 'cloudflare',
        ];
        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value,
        );
    }

    public function getValidUpdateFieldData(): Value
    {
        return new Value('2ce149c61ad6a10ad075017b4ccf8890', 'cloudflare');
    }

    public function assertUpdatedFieldDataLoadedCorrect(Field $field): void
    {
        self::assertInstanceOf(Value::class, $field->value);

        $expectedData = [
            'id' => '2ce149c61ad6a10ad075017b4ccf8890',
            'source' => 'cloudflare',
        ];
        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value,
        );
    }

    /**
     * @dataProvider provideFieldSettings
     *
     * @param mixed $settings
     * @param mixed $expectedSettings
     */
    public function testCreateContentTypes($settings, $expectedSettings): ContentType
    {
        $contentType = $this->createContentType(
            $settings,
            $this->getValidValidatorConfiguration(),
            $this->getValidContentTypeConfiguration(),
            $this->getValidFieldConfiguration(),
        );
        self::assertNotNull($contentType->id);
        self::assertEquals($expectedSettings, $contentType->fieldDefinitions[1]->fieldSettings);

        return $contentType;
    }

    public function provideInvalidUpdateFieldData(): array
    {
        return $this->provideInvalidCreationFieldData();
    }

    public function assertCopiedFieldDataLoadedCorrectly(Field $field): void
    {
        self::assertInstanceOf(
            Value::class,
            $field->value,
        );

        $expectedData = [
            'id' => '2ce149c61ad6a10ad075017b4ccf8890',
            'source' => 'cloudflare',
        ];

        $this->assertPropertiesCorrect(
            $expectedData,
            $field->value,
        );
    }

    public function provideFieldSettings(): array
    {
        return [
            'empty_settings' => [
                [],
                [
                    'allowedExternalVideoSource' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
            'complete_settings' => [
                [
                    'allowedExternalVideoSource' => [Type::SOURCE_CLOUDFLARE],
                ],
                [
                    'allowedExternalVideoSource' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
        ];
    }

    public function provideToHashData(): array
    {
        return [
            [
                new Value('2ce149c61ad6a10ad075017b4ccf8890', 'cloudflare'),
                [
                    'id' => '2ce149c61ad6a10ad075017b4ccf8890',
                    'source' => 'cloudflare',
                ],
            ],
        ];
    }

    public function provideFromHashData(): array
    {
        return [
            [
                [
                    'id' => '2ce149c61ad6a10ad075017b4ccf8890',
                    'source' => 'cloudflare',
                ],
                new Value('2ce149c61ad6a10ad075017b4ccf8890', 'cloudflare'),
            ],
        ];
    }

    public function providerForTestIsEmptyValue(): array
    {
        return [
            [new Value()],
        ];
    }

    public function providerForTestIsNotEmptyValue(): array
    {
        return [
            [
                $this->getValidCreationFieldData(),
            ],
        ];
    }
}
