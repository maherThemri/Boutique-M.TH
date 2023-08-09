<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
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
            TextField::new('title', 'Titre du header'),
            TextareaField::new('content', 'Contenu de notre header'),
            TextField::new('btnTitle', 'Titre de notre bouton'),
            TextField::new('btnUrl', 'Url destination de notre bouton'),
            $illustrationField
        ];
    }
}
