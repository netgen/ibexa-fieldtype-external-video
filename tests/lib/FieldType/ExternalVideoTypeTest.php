<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\Tests\Unit\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Tests\Core\FieldType\FieldTypeTest;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Type;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Value;

/**
 * @group type
 */
class ExternalVideoTypeTest extends FieldTypeTest
{
    private const DESTINATION_CONTENT_ID = 14;
    private const NON_EXISTENT_CONTENT_ID = 123;

    private $contentHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $versionInfo = new VersionInfo([
            'versionNo' => 24,
            'names' => [
                'en_GB' => 'name_en_GB',
                'de_DE' => 'Name_de_DE',
            ],
        ]);
        $currentVersionNo = 28;
        $destinationContentInfo = $this->createMock(ContentInfo::class);
        $destinationContentInfo
            ->method('__get')
            ->willReturnMap([
                ['currentVersionNo', $currentVersionNo],
                ['mainLanguageCode', 'en_GB'],
            ]);

        $this->contentHandler = $this->createMock(SPIContentHandler::class);

        $this->contentHandler
            ->method('loadContentInfo')
            ->with(
                self::logicalOr(
                    self::equalTo(self::NON_EXISTENT_CONTENT_ID),
                    self::equalTo(self::DESTINATION_CONTENT_ID),
                ),
            )
            ->willReturnCallback(static function ($contentId) use ($destinationContentInfo) {
                if ($contentId === self::DESTINATION_CONTENT_ID) {
                    return $destinationContentInfo;
                }

                throw new NotFoundException('Content', self::NON_EXISTENT_CONTENT_ID);
            });

        $this->contentHandler
            ->method('loadVersionInfo')
            ->with(self::DESTINATION_CONTENT_ID, $currentVersionNo)
            ->willReturn($versionInfo);
    }

    public function provideInvalidInputForAcceptValue(): array
    {
        return [
            [
                true,
                InvalidArgumentException::class,
            ],
        ];
    }

    public function provideValidInputForAcceptValue(): array
    {
        return [
            [
                new Value(),
                new Value(),
            ],
            [
                'cloudflare_video_id',
                new Value('cloudflare_video_id'),
            ],
        ];
    }

    public function provideInputForToHash(): array
    {
        return [
            [
                new Value('cloudflare_video_id', 'cloudflare'),
                [
                    'id' => 'cloudflare_video_id',
                    'source' => 'cloudflare',
                ],
            ],
            [
                new Value(),
                [
                    'id' => '',
                    'source' => 'cloudflare',
                ],
            ],
        ];
    }

    public function provideInputForFromHash(): array
    {
        return [
            [
                [
                    'id' => 'cloudflare_video_id',
                    'source' => 'cloudflare',
                ],
                new Value('cloudflare_video_id', 'cloudflare'),
            ],
            [
                [
                    'id' => null,
                    'source' => null,
                ],
                new Value(),
            ],
        ];
    }

    public function provideValidFieldSettings(): array
    {
        return [
            [
                [
                    'allowedExternalVideoSource' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
        ];
    }

    public function provideInvalidFieldSettings(): array
    {
        return [
            [
                // Unknown key
                [
                    'unknownKey' => 'Unknown',
                    'allowedExternalVideoSource' => [Type::SOURCE_CLOUDFLARE],
                ],
            ],
            [
                // Invalid video source
                [
                    'allowedExternalVideoSource' => ['invalid_video_source'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideDataForGetName
     */
    public function testGetName(
        SPIValue $value,
        string $expected,
        array $fieldSettings = [],
        string $languageCode = 'en_GB'
    ): void {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition|\PHPUnit\Framework\MockObject\MockObject $fieldDefinitionMock */
        $fieldDefinitionMock = $this->createMock(FieldDefinition::class);
        $fieldDefinitionMock->method('getFieldSettings')->willReturn($fieldSettings);

        $name = $this->getFieldTypeUnderTest()->getName($value, $fieldDefinitionMock, $languageCode);

        self::assertSame($expected, $name);
    }

    public function provideDataForGetName(): array
    {
        return [
            'empty_video_id' => [
                $this->getEmptyValueExpectation(), '', [], 'en_GB',
            ],
            'cloudflare_video_id' => [
                new Value('cloudflare_video_id_en'), 'cloudflare_video_id_en', [], 'en_GB',
            ],
            'cloudflare_video_id_de_DE' => [
                new Value('cloudflare_video_id_de'), 'cloudflare_video_id_de', [], 'de_DE',
            ],
        ];
    }

    public function testIsSearchable(): void
    {
        $type = $this->createFieldTypeUnderTest();

        self::assertTrue($type->isSearchable());
    }

    public function provideValidDataForValidate(): array
    {
        return [
            [[], new Value('cloudflare_video_id', 'cloudlfare')],
        ];
    }

    public function provideInvalidDataForValidate(): array
    {
        return [
            [[], new Value(), []],
        ];
    }

    protected function createFieldTypeUnderTest(): Type
    {
        $fieldType = new Type(
            $this->contentHandler,
            'apiUrl',
            'apiBearerToken',
        );
        $fieldType->setTransformationProcessor($this->getTransformationProcessorMock());

        return $fieldType;
    }

    protected function getValidatorConfigurationSchemaExpectation(): array
    {
        return [];
    }

    protected function getSettingsSchemaExpectation(): array
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

    protected function getEmptyValueExpectation(): Value
    {
        return new Value();
    }

    protected function provideFieldTypeIdentifier(): string
    {
        return 'ngexternalvideo';
    }
}
