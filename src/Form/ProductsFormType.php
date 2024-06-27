<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Marques;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Faker\Provider\ar_EG\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\Positive;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class)
            ->add('description',TextType::class)
            ->add('prix',MoneyType::class , options:['divisor'=>100 , 'constraints'=>[new Positive(message:'Le prix doit etre positif')]])
            ->add('stock',IntegerType::class)
            ->add('imageFile',VichImageType::class,[
                'required'=>false,
                'allow_delete'=>false,
                'download_uri'=>false,
                'image_uri'=>false,
                'asset_helper'=>true
            ])
            ->add('marques',EntityType::class,[
                'class'=>Marques::class,
                'choice_label'=>'name'
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'group_by'=>'parent.name', 
                'query_builder'=>function(CategoriesRepository $cr)
                {
                    return $cr->createQueryBuilder('c')
                    ->where('c.parent IS NOT NULL')
                    ->orderBy('c.name','ASC');
                }
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
