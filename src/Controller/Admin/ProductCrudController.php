<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('name')->setLabel('Nom'),
            // SlugField::new('slug')->setTargetFieldName('name')->setLabel('Slug'),
            ImageField::new('illustration')->setUploadDir('public/uploads')->setBasePath('uploads/')->setUploadedFileNamePattern('[randomhash].[extension]')->setLabel('Illustration'),
            TextField::new('subtitle')->setLabel('Sous-Titre'),
            TextareaField::new('description')->setLabel('Description'),
            BooleanField::new('isBest'),
            MoneyField::new('price')->setCurrency('EUR')->setLabel('Prix'),
            AssociationField::new('category'),

        ];
    }
}
