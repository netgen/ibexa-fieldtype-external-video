<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideoBundle\Form\FieldDefinition;

use Ibexa\AdminUi\FieldType\Mapper\AbstractRelationFormMapper;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use JMS\TranslationBundle\Annotation\Desc;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Type;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm->add('allowedExternalVideoSources', ChoiceType::class, [
            'choices' => [
                'ngexternalvideo.field_definition.video_source.' . Type::SOURCE_CLOUDFLARE => Type::SOURCE_CLOUDFLARE,
            ],
            'property_path' => 'fieldSettings[allowedExternalVideoSources]',
            'label' => /* @Desc("Allowed video sources") */ 'ngexternalvideo.field_definition.selection_allowed_video_sources',
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
