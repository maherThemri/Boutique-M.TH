<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Filesystem\Filesystem;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $illustrationField = ImageField::new('illustration')
            ->setBasePath('/uploads/') // Définissez ici le chemin public vers le répertoire des images
            ->setUploadDir('public/uploads') // Définissez ici le répertoire de téléchargement pour les images
            ->setUploadedFileNamePattern('[randomhash].[extension]'); // Optionnel : Définissez un pattern de nom de fichier unique
        if ($pageName === Crud::PAGE_EDIT) {
            $illustrationField = $illustrationField->setFormTypeOptions(['required' => false]);
        }
        return [
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
            // ImageField::new('illustration')->setBasePath('uploads/')
            //     ->setUploadDir('public/uploads'),
            $illustrationField,
            TextField::new('subtitle'),
            TextareaField::new('description'),
            BooleanField::new('isBest'),
            MoneyField::new('price')->setCurrency('EUR'),
            AssociationField::new('category')->autocomplete(false)
        ];
    }
    public function remove(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Récupérer le champ 'illustration'
        $illustration = $entityInstance->getIllustration();

        // Vérifier si une illustration est associée à l'entité
        if ($illustration) {
            // Chemin complet vers l'image à supprimer
            $imagePath = $this->getParameter('kernel.project_dir') . '/public' . $illustration;

            // Supprimer l'image du dossier d'uploads s'il existe
            if (file_exists($imagePath)) {
                $filesystem = new Filesystem();
                $filesystem->remove($imagePath);
            }
        }

        // Supprimer l'entité elle-même
        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}
