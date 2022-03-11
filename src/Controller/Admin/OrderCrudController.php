<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add('index', 'detail')
            ->remove('index', 'new')
            ->remove('index', 'edit')
            ->remove('index', 'delete');
    }





    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le : '),
            TextField::new('user.getFullName', 'Utilisateur'),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrier.name', 'Transporteur'),
            MoneyField::new('carrier.price', 'Frais de port')->setCurrency('EUR'),
            BooleanField::new('statut', 'payée'),
            ArrayField::new('orderDetails', 'Produits achetés')
        ];
    }
}
