<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private array $brutData;

    public function __construct(
        private TagRepository $tagRepository
    ) {
        $this->brutData = [];
    }

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
            ->add('tags', TagAutocompleteField::class, options: [
                'required' => false,
                'attr' => [
                    'data-controller' => 'custom-autocomplete',
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->preSubmit(...))
            ->addEventListener(FormEvents::SUBMIT, $this->submit(...));
        ;
    }

    private function preSubmit(FormEvent $event): void {
        $data = $event->getData();
        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->brutData = $data['tags'];
            $data['tags'] = array_filter($data['tags'], 'ctype_digit');
            $event->setData($data);
        }
    }

    private function submit(FormEvent $event): void {
        $form = $event->getForm();
        /** @var Post $post */
        $post = $form->getData();
        $data = $this->brutData;
        foreach ($data as $tagName) {
            if(ctype_digit($tagName)) continue;
            if($this->tagRepository->findOneBy([
                'tagName' => $tagName
            ]) !== null) continue;
            $tag = new Tag();
            $tag->setTagName($tagName);
            $post->addTag($tag);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
