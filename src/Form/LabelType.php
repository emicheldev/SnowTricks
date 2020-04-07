<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;


class LabelType extends AbstractType
{
    /**
     * Configure les options d'un champ de formulaire
     *
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    protected function getOptions($label, $placeholder, $options = []) {
        return array_merge ([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }
}
