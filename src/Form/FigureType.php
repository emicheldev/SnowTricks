<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FigureType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('mainImgFile', FileType::class, [
				'required' => false,
				'multiple' => false,
			])
			->add('pictureFiles', FileType::class, [
				'required' => false,
				'label' => 'Photos supplémentaires',
				'multiple' => true
			])
			->add('name')
			->add('categories', EntityType::class, [
				'class' => Category::class,
				'choice_label' => 'name',
				'multiple' => true,
				'required' => false,
			])
			->add('description');
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Figure::class,
			'translation_domain' => 'forms'
		]);
	}
}