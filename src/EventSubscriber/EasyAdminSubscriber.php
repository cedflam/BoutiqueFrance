<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * @var KernelInterface
     */
    private $appKernel;

    /**
     * EasyAdminSubscriber constructor.
     * @param KernelInterface $appKernel
     */
    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * @return array|\string[][]
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setIllustration'],
            BeforeEntityUpdatedEvent::class => ['updateIllustration'],
        ];
    }

    /**
     * @param $event
     */
    public function uploadIllustration($event)
    {
        //Si l'évènement n'est pas une instance de Product alors ne fait rien (return)
        if (!($event->getEntityInstance() instanceof Product)){
            return;
        }

        //Je récupère l'entity courante
        $entity = $event->getEntityInstance();
        // Je récupère le nom temporaire grâce à la constante $_FILES
        $tmp_name = $_FILES['Product']['tmp_name']['illustration'];
        // Je crée un nom aléatoire unique
        $filename = uniqid();
        // Je récupère l'extension du fichier uploadé
        $extension = pathinfo($_FILES['Product']['name']['illustration'], PATHINFO_EXTENSION);
        // Je récupère le chemin du projet
        $projectDir = $this->appKernel->getProjectDir();
        // Je déplace le fichier vers le dossier uploads
        move_uploaded_file($tmp_name, $projectDir . '/public/uploads/' . $filename . '.' . $extension);
        // Je mets à jour le nom du fichier dans l'entity
        $entity->setIllustration($filename . '.' . $extension);

    }

    /**
     * @param BeforeEntityPersistedEvent $event
     */
    public function setIllustration(BeforeEntityPersistedEvent $event)
    {
        $this->uploadIllustration($event);
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     */
    public function updateIllustration(BeforeEntityUpdatedEvent $event)
    {
        // Si l'évènement n'est pas une instance de Product alors ne fait rien (return)
        if (!($event->getEntityInstance() instanceof Product)){
            return;
        }
        // Si le nom de l'iilustration n'est pas vide alors il s'agit bien d'une modification
        if ($_FILES['Product']['name']['illustration'] != ''){
            $this->uploadIllustration($event);
        }
    }
}