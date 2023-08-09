<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;
    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')
            ->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')
            ->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }
    public function updatePreparation(AdminContext $context): RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();
        $this->addFlash('notice', "<span style='color:green;'><strong>la commande " . $order->getReference() . " est bien en cours de préparation.</strong></span>");

        $routeBuilder = $this->adminUrlGenerator;

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->setAction('index')->generateUrl());
    }


    public function updateDelivery(AdminContext $context): RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();
        $this->addFlash('notice', "<span style='color:orange;'><strong>la commande " . $order->getReference() . " est bien en cours de livraison.</strong></span>");

        $routeBuilder = $this->adminUrlGenerator;

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->setAction('index')->generateUrl());
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.FullName', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                "Non payée" => 0,
                "Payée" => 1,
                "Préparation en cours" => 2,
                "Livraision en cours" => 3
            ]),
            ArrayField::new('orderDetails', 'Produit achetés')->hideOnIndex(),
        ];
    }
}
