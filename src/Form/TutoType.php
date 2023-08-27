<?php

namespace App\Form;

use App\Entity\Tuto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints as Assert;

class TutoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title')
            ->add('imageFile', FileType::class, [
                'required' => true,
                'mapped' => true,
                'data_class' => null
            ])
            ->add('Categories', ChoiceType::class, [
                'choices' => [
                    'Vidéo' => 'video',
                    'Document' => 'docs',
                    'Motion Design' => 'motion',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('description', TextareaType::class)
            ->add('file', FileType::class, [
                'required' => false,
                'mapped' => true,
                'data_class' => null,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Récupérez la catégorie sélectionnée
            $category = $data['Categories'] ?? null;

            if ($category === 'video') {
                $form->add('file', FileType::class, [
                    'constraints' => [
                        new Assert\File([
                            'mimeTypes' => ['video/mp4'],
                            'mimeTypesMessage' => 'Veuillez uploader un fichier MP4 valide.',
                        ]),
                    ],
                ]);
            } elseif ($category === 'docs') {
                $form->add('file', FileType::class, [
                    'constraints' => [
                        new Assert\File([
                            'mimeTypes' => ['application/pdf'],
                            'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide.',
                        ]),
                    ],
                ]);
            }
            // Ajoutez d'autres conditions pour d'autres catégories si nécessaire
        });
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tuto::class,
        ]);
    }
}


