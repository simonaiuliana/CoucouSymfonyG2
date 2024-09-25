<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postTitle')
            ->add('postDescription')
            ->add('postDateCreated', null, [
                'widget' => 'single_text',
                'empty_data' => date('Y-m-d H:i:s'),
                'required' => false,
            ])
            ->add('postDatePublished', null, [
                'widget' => 'single_text',
            ])
            ->add('postPublished')
            ->add('sections', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'section_title',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('tagsId', ChoiceType::class, [
                'choices' => $options['tags'],
                'data' => $options['tagDatas'],
                'multiple' => true,
                'expanded' => true
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'tags' => [],
            'tagDatas' => [],
        ]);
    }
}
