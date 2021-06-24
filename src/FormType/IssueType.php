<?php

namespace App\FormType;

use App\Entity\Issue;
use App\Entity\IssueClient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\IntegerType;
use \Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('text', TextType::class, [
                'required' => true,
            ])
            ->add('client', EntityType::class, [
                'class' => IssueClient::class,
                'required' => true,
                'invalid_message' => 'Client is empty or not valid.',
            ])
            ->add('in_work', CheckboxType::class, [
                'required' => true,
            ])
            ->add('created_at', DateTimeType::class, [
                'required' => false, //true
            ])
            ->add('updated_at', DateTimeType::class, [
                'required' => false, //true
            ]);

//title - заголовок, строка до 150 символов, без спецсимволов
//Text - текст проблемы, строка до 3000 символов, символы любые
//client_id - ID клиента, который подает жалобу
//in_work  - флаг взятия в работу
//created_at - дата создания
//updated_at - дата последнего обновления, при создании сущности creaed_at = updated_at
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Issue::class,
        ));
    }
}
