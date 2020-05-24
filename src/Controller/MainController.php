<?php


namespace App\Controller;


use App\Entity\User;
use App\Service\Vk\VkOAuthProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     */
    public function home()
    {
        return $this->render('home.twig');
    }

    /**
     * @Route("/step-one", name="step_one")
     * @param VkOAuthProvider $authProvider
     * @return Response
     */
    public function addExportStepOne(VkOAuthProvider $authProvider)
    {
        return $this->render('create/step-one.twig', ['url' => $authProvider->createOAuthURL()]);
    }

    public function addExportStepTwo()
    {

    }

    /**
     * @Route("/vk-auth", name="vk_auth")
     * @param Request $request
     * @param VkOAuthProvider $authProvider
     * @param EntityManagerInterface $entityManager
     */
    public function createVkToken(Request $request, VkOAuthProvider $authProvider, EntityManagerInterface $entityManager)
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

        return $this->redirectToRoute('home');
    }
}