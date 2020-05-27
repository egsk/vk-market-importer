<?php


namespace App\Controller;


use App\Entity\CsvLinkDataSource;
use App\Entity\ImportTarget;
use App\Entity\User;
use App\Entity\VkMarketCategory;
use App\Form\CsvLinkDataSourceType;
use App\Form\ImportTargetType;
use App\Repository\ImportTargetRepository;
use App\Service\Vk\CsvLinkDataSource\CsvLinkDataSourceRepresentationFactory;
use App\Service\Vk\VkManager;
use App\Service\Vk\VkMarketCategoryProvider;
use App\Service\Vk\VkOAuthProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class HomeController
 * @package App\Controller
 *
 * @Route("/cp")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ImportTargetRepository $repository
     * @return Response
     */
    public function home(ImportTargetRepository $repository)
    {
        /**
         * @var ImportTarget[] $importTargets
         */
        $importTargets = $repository->findWithDataSource($this->getUser());

        return $this->render('home.twig', [
            'importTargets' => $importTargets
        ]);
    }

    /**
     * @Route("/step-one", name="step_one")
     * @param VkOAuthProvider $authProvider
     * @return Response
     */
    public function addExportStepOne(VkOAuthProvider $authProvider)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user->getVkAccessToken()) {
            return $this->redirectToRoute('step_two');
        }

        return $this->render('vk/step-one.twig', ['url' => $authProvider->createOAuthURL()]);
    }

    /**
     * @Route("/step-two", name="step_two")
     * @param Request $request
     * @param VkManager $vkManager
     * @param EntityManagerInterface $entityManager
     * @param CacheInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     */
    public function addExportStepTwo(
        Request $request,
        VkManager $vkManager,
        EntityManagerInterface $entityManager,
        CacheInterface $cache)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $groups = $cache->get(
            "vk_groups_{$this->getUser()->getUsername()}",
            function (ItemInterface $item) use ($user, $vkManager) {
                $item->expiresAfter(300);

                return $vkManager->getGroupsList($user->getVkAccessToken());
            }
        );
        $categories = $entityManager
            ->getRepository(VkMarketCategory::class)
            ->findAll();
        $importTarget = new ImportTarget();
        $form = $this->createForm(ImportTargetType::class, $importTarget, [
            'groupChoices' => $groups,
            'categoryChoices' => $categories
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var ImportTarget $importTarget ;
             */
            /**
             * @var User $user
             */
            $user = $this->getUser();
            $importTarget->setUser($user);
            $entityManager->persist($importTarget);
            $entityManager->flush();
            $this->addFlash('success', 'Группа успешно добавлена! <br> Теперь вы можете прикрепить источник данных к группе');

            return $this->redirectToRoute('home');
        }

        return $this->render('form/step-two.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/data-source/{id}/csv-link/add", requirements={"id"="\d+"}, name="add_csv_link_data_source")
     * @param ImportTarget $importTarget
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addCsvLinkDataSource(ImportTarget $importTarget, Request $request, EntityManagerInterface $entityManager)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && ($importTarget->getUser() !== $user)) {
            throw new AccessDeniedHttpException();
        }
        $dataSource = new CsvLinkDataSource();
        $dataSource->setUser($user);
        $dataSource->setImportTarget($importTarget);
        $form = $this->createForm(CsvLinkDataSourceType::class, $dataSource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dataSource);
            $entityManager->flush();

            $this->redirectToRoute('validate_csv_link_data_source', ['id' => $dataSource->getId()]);
        }

        return $this->render('form/csv-link-data-source.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/data-source/csv-link/edit/{id}", requirements={"id"="\d+"}, name="edit_csv_link_data_source")
     * @param CsvLinkDataSource $dataSource
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editCsvLinkDataSource(CsvLinkDataSource $dataSource, Request $request, EntityManagerInterface $entityManager)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && ($dataSource->getUser() !== $user)) {
            throw new AccessDeniedHttpException();
        }
        $form = $this->createForm(CsvLinkDataSourceType::class, $dataSource);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataSource->setValidated(false);
            $entityManager->flush();

            return $this->redirectToRoute('validate_csv_link_data_source', ['id' => $dataSource->getId()]);
        }

        return $this->render('form/csv-link-data-source.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/data-source/csv-link/validate/{id}", requirements={"id"="\d+"}, name="validate_csv_link_data_source")
     * @param CsvLinkDataSource $dataSource
     * @param CsvLinkDataSourceRepresentationFactory $representationFactory
     * @return Response
     */
    public function validateCsvLinkDataSource(CsvLinkDataSource $dataSource, CsvLinkDataSourceRepresentationFactory $representationFactory)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && $dataSource->getUser() !== $user) {
            throw new AccessDeniedHttpException();
        }
        try {
            $productRepresentations = $representationFactory->create($dataSource);
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Ошибка при попытке загрузки товаров из csv.
                <br>
                Пожалуйста, проверьте правильность заполнения параметров источника данных и csv файл на сервере
                <br>
                Когда-нибудь я сделаю подробный отчёт об ошибке, обещаю.
            ');

            return $this->redirectToRoute('edit_csv_link_data_source', ['id' => $dataSource->getId()]);
        }
        if (count($productRepresentations) === 0) {
            $this->addFlash('warning', 'В csv-файле не обнаружено ни одного товара.
            <br>
            Пожалуйста, загрузите товары в csv. Вы сможете вернуться к валидации позднее.
            ');

            return $this->redirectToRoute('edit_csv_link_data_source', ['id' => $dataSource->getId()]);
        }

        return $this->render('dataSource/data-source-validation.twig', [
            'productRepresentations' => array_slice($productRepresentations, 0, 4),
            'productsCount' => count($productRepresentations)
        ]);
    }

    /**
     * @Route("/vk-auth", name="vk_auth")
     * @param Request $request
     * @param VkOAuthProvider $authProvider
     * @param EntityManagerInterface $entityManager
     * @param VkMarketCategoryProvider $categoryProvider
     * @return RedirectResponse
     */
    public function createVkToken(
        Request $request,
        VkOAuthProvider $authProvider,
        EntityManagerInterface $entityManager,
        VkMarketCategoryProvider $categoryProvider
    )
    {
        $code = $request->get('code');
        if (!$code) {
            throw new BadRequestHttpException();
        }
        $accessTokenResponse = $authProvider->createAccessTokenResponse($code);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $user->setVkId($accessTokenResponse->getUserId());
        $user->setVkAccessToken($accessTokenResponse->getAccessToken());
        $entityManager->flush();
        $categoryProvider->saveCategories($user->getVkAccessToken());

        return $this->redirectToRoute('home');
    }
}