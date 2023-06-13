<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideo\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\Core\Persistence\Cache\ContentHandler;

use function array_intersect;
use function count;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function is_array;
use function is_string;
use function json_decode;
use function trim;

use const CURL_HTTP_VERSION_1_1;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_ENCODING;
use const CURLOPT_HTTP_VERSION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_URL;
use const JSON_THROW_ON_ERROR;

class Type extends FieldType
{
    public const SOURCE_CLOUDFLARE = 'cloudflare';
    protected $settingsSchema = [
        'allowedExternalVideoSource' => [
            'type' => 'array',
            'default' => [
                self::SOURCE_CLOUDFLARE,
            ],
        ],
    ];
    private ContentHandler $contentHandler;
    private string $apiUrl;
    private string $apiBearerToken;

    public function __construct(
        ContentHandler $contentHandler,
        string $apiUrl,
        string $apiBearerToken
    ) {
        $this->contentHandler = $contentHandler;
        $this->apiUrl = $apiUrl;
        $this->apiBearerToken = $apiBearerToken;
    }

    public function validateFieldSettings($fieldSettings): array
    {
        $validationErrors = [];
        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[{$name}]",
                );

                continue;
            }

            if ($name === 'allowedExternalVideoSource') {
                if (!is_array($value) || count(array_intersect($value, [self::SOURCE_CLOUDFLARE])) === 0) {
                    $validationErrors[] = new ValidationError(
                        "Setting '%setting%' value must be one of allowed sources",
                        null,
                        [
                            '%setting%' => $name,
                        ],
                        "[{$name}]",
                    );
                }
            }
        }

        return $validationErrors;
    }

    /**
     * @throws \JsonException
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue): array
    {
        $validationErrors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }
        $videoId = $fieldValue->id;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . $videoId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiBearerToken,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if (is_string($response)) {
            $decoded_response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            if (isset($decoded_response['success']) && $decoded_response['success'] === false) {
                $validationErrors[] = new ValidationError(
                    'Video with given id does not exist',
                );
            }
        } else {
            $validationErrors[] = new ValidationError(
                'Connection to Cloudflare API failed',
            );
        }

        return $validationErrors;
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ngexternalvideo';
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        /* @var Value $value */
        return $value->id;
    }

    public function getEmptyValue(): Value
    {
        return new Value();
    }

    public function isEmptyValue(SPIValue $value): bool
    {
        /* @var Value $value */
        return trim($value->id) === '';
    }

    public function fromHash($hash): Value
    {
        if ($hash !== null) {
            $id = $hash['id'];

            if (isset($id)) {
                return new Value($id, $hash['source']);
            }
        }

        return $this->getEmptyValue();
    }

    public function toHash(SPIValue $value): array
    {
        return [
            'id' => $value->id,
            'source' => $value->source,
        ];
    }

    public function isSearchable(): bool
    {
        return true;
    }

    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_string($value->id)) {
            throw new InvalidArgumentType(
                '$value->id',
                'string',
                $value->id,
            );
        }
    }

    protected function getSortInfo(BaseValue $value): string
    {
        return $this->transformationProcessor->transformByGroup((string) $value, 'lowercase');
    }
}
