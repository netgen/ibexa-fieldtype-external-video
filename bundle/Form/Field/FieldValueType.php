<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeExternalVideoBundle\Form\Field;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use JMS\TranslationBundle\Annotation\Desc;
use Netgen\IbexaFieldTypeExternalVideo\FieldType\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldValueType extends AbstractType
{
    private ContentService $contentService;
    private ContentTypeService $contentTypeService;
    private FieldTypeService $fieldTypeService;

    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeService $fieldTypeService
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'ibexa_fieldtype_ngexternalvideo';
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'source',
            ChoiceType::class,
            [
                'choices' => [
                    'ngexternalvideo.field_definition.video_source.' . Type::SOURCE_CLOUDFLARE => Type::SOURCE_CLOUDFLARE,
                ],
                'label' => /* @Desc("Text") */ 'ngexternalvideo.video_source',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ],
        );
        $builder->add(
            'id',
            TextType::class,
            [
                'label' => /* @Desc("Text") */ 'ngexternalvideo.video_id',
                'required' => true,
                'disabled' => false,
            ],
        );

        $builder->addModelTransformer(
            new FieldValueTransformer(
                $this->fieldTypeService->getFieldType('ngexternalvideo'),
            ),
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \Netgen\IbexaFieldTypeExternalVideo\FieldType\Value $data */
        $data = $form->getData();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'field_edit',
        ]);
    }
}
