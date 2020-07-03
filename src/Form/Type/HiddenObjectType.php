<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectsToIdTransformer;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\DataTransformer\ObjectToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenObjectType extends AbstractType
{
    /** @var ManagerRegistry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $transformerClassName = $options['multiple'] === true ? ObjectsToIdTransformer::class : ObjectToIdTransformer::class;

        $transformer = new $transformerClassName(
            $this->registry,
            $options['class'],
            $options['property']
        );

        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setRequired(['class']);

        $resolver->setDefaults([
            'multiple'        => false,
            'data_class'      => null,
            'invalid_message' => 'The object does not exist.',
            'property'        => 'id',
        ]);

        $resolver->setAllowedTypes('invalid_message', ['null', 'string']);
        $resolver->setAllowedTypes('property', ['null', 'string']);
        $resolver->setAllowedTypes('multiple', ['boolean']);
    }

    public function getParent() : string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix() : string
    {
        return 'shapecode_hidden_object';
    }
}
