<?php


namespace App\Controller;


use App\Entity\ImportTarget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/import-target/{id}", methods={"DELETE"}, name="delete_import_target")
     * @param ImportTarget $importTarget
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteImportTarget(ImportTarget $importTarget, EntityManagerInterface $entityManager)
    {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && $importTarget->getUser() !== $this->getUser()) {
            throw new AccessDeniedHttpException();
        }
        $entityManager->remove($importTarget);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}