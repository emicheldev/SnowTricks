<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Form\LabelType;
use App\Form\VideoType;
use App\Entity\Category;
use PhpParser\Parser\Multiple;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends LabelType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, $this->getOptions('Nom', 'Nom du trick'))
        ->add('description', TextareaType::class, $this->getOptions('Description', 'Description du trick'))
        ->add('category', EntityType::class, $this->getOptions('Catégorie', 'Catégorie du trick', [
            'class' => Category::class,
            'choice_label' => 'name'
        ]))
        ->add('mainImage', ImageType::class, $this->getOptions('Image principale', 'Image principale'))
        ->add('images', CollectionType::class, [
            'entry_type' => ImageType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
            
        ])
        ->add('videos', CollectionType::class, [
            'entry_type' => VideoType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
