<?php


namespace App\Controller;


use App\Entity\ImportTarget;
use App\Repository\UploadTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    /**
     * @Route("/upload_task/{id}", methods={"GET"})
     */
    public function getUploadTaskStatus(int $id, UploadTaskRepository $repository)
    {
        $task = $repository
            ->createQueryBuilder('ut')
            ->leftJoin('ut.uploadedProducts', 'up')
            ->where('ut.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        if (is_null($task)) {
            throw new NotFoundHttpException();
        }
        $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer()], [new JsonEncoder()]);

        return new JsonResponse(
            $serializer
                ->serialize($task, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['user', 'uploadTask']]),
            200,
            [],
            true
        );
    }
}