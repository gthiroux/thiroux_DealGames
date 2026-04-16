<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($options['is_admin']) {
            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,   
                'expanded' => true,   
                'required' => false,
                'label' => 'Rôles',
            ]);
        }
        else{
        $builder
        ->add('lastname')
        ->add('firstname')
        ->add('email')
        ->add('password', PasswordType::class,['label'=> 'Nouveau mot de passe','required' => false,
        'empty_data' => '',
        'attr' => ['placeholder' => 'Nouveau mot de passe'],])
        ;
    }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_admin'=>false,
        ]);
    }
}
