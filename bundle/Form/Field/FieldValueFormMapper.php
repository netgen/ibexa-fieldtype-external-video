<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideoBundle\Form\Field;

use Ibexa\ContentForms\FieldType\Mapper\AbstractRelationFormMapper;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Symfony\Component\Form\FormInterface;

class FieldValueFormMapper extends AbstractRelationFormMapper
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $fieldSettings = $fieldDefinition->getFieldSettings();

        $fieldForm->add(
            $formConfig->getFormFactory()->createBuilder()
                ->create(
                    'value',
                    FieldValueType::class,
                    [
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                    ],
                )
                ->setAutoInitialize(false)
                ->getForm(),
        );
    }
}
