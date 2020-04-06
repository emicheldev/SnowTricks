<?php

namespace App\Form;

use App\Entity\Comment;
use App\Form\LabelType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentFormType extends LabelType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', TextareaType::class, $this->getOptions('Votre commentaire', 'Contenu de votre commentaire ...'))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
