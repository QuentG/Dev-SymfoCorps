<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Uploader\UploaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserAccountFormType extends AbstractType
{
    private UploaderInterface $uploader;

    public function __construct(UploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class);

        if ($options['data'] instanceof Particular) {
            $builder
                ->add('firstName', TextType::class, [
                    'label' => 'Prénom'
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'Nom de famille'
                ])
                ->add('avatar', FileType::class, [
                    'label' => 'Votre avatar',
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/svg'],
                            'mimeTypesMessage' => 'Veuillez renseigner une image au format (.jpg, .jpeg, .png ou .svg)'
                        ])
                    ]
                ])
            ;
        } elseif ($options['data'] instanceof Company) {
            $builder->add('companyName', TextType::class, [
                    'label' => "Nom de l'entreprise"
                ]);
        }

        $builder->add('submit', SubmitType::class, [
            'label' => 'Enregister'
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function onPostSubmit(FormEvent $event): void
    {
        /** @var User|Company|Particular $user */
        $user = $event->getData();
        $form = $event->getForm();

        if ($user->isParticular() && $avatar = $form->get('avatar')->getData()) {
            $user->setAvatar(
                $this->uploader->upload($avatar)
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
