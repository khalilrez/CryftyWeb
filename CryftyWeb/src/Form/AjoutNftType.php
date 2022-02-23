<?php

namespace App\Form;

use App\Entity\Crypto\Node;
use App\Entity\NFT\Category;
use App\Entity\NFT\SubCategory;
use phpDocumentor\Reflection\Types\Float_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;


class AjoutNftType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class,['label'=>'e. g. Image, Audio, Video',
                'label_attr'=>['class'=>'sign__label'
                    , 'class'=>'custom-file-label'
                    ,'for'=>'customFile'
                    ,'mapped'=>false
                    ,'required'=>true
                    ,'multiple'=>false
                ]
                ,'attr'=>['class'=>'custom-file-input','name'=>'filename','id'=>'customFile']
            ])
            ->add('title',TextType::class,['label'=>"TITLE"
                ,'label_attr'=>['class'=>'sign__label']
                ,'attr'=>['class'=>'sign__input']
                ,'constraints'=>array(new NotBlank(['message'=>'Ce champ ne doit pas etre vide'])
                , new Length(['min'=>3,'max'=>20]))
            ])
            ->add('description',TextareaType::class,['label'=>"DESCRIPTION"
                ,'label_attr'=>['class'=>'sign__label']
                ,'attr'=>['class'=>'sign__textarea','cols' => '5', 'rows' => '5']
            ])
            ->add('price',MoneyType::class,['label'=>"PRICE"
                ,'label_attr'=>['class'=>'sign__label']
                ,'attr'=>['class'=>'sign__input']
                ,'constraints'=>array(new NotNull(['message'=>'Ce champ ne doit pas être vide']))
            ])
            ->add('currency',EntityType::class,
                [
                    'required' => false,
                    'label' => 'Currency',
                    'class' => Node::class,
                    'multiple' => false,
                    'expanded' => false,
                    'choice_label' => 'NodeLabel'
                    ,'label_attr'=>['class'=>'sign__label']
                    ,'attr'=>['class'=>'sign__select']

            ])
            ->add('category',EntityType::class,[
                'required' => false,
                'label' => 'Category',
                'class' => Category::class,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => 'name'
                ,'label_attr'=>['class'=>'sign__label']
                ,'attr'=>['class'=>'sign__select']
            ])
            ->add('subcategory',EntityType::class,[
                'required' => false,
                'label' => 'SubCategory',
                'class' => SubCategory::class,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => 'name'
                ,'label_attr'=>['class'=>'sign__label']
                ,'attr'=>['class'=>'sign__select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'=>Category::class
        ]);
    }
}