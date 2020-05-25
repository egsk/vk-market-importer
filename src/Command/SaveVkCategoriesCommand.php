<?php


namespace App\Command;


use App\Entity\User;
use App\Service\Vk\VkMarketCategoryProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveVkCategoriesCommand extends Command
{
    protected static $defaultName = 'app:save_vk_categories';

    /**
     * @var VkMarketCategoryProvider
     */
    protected $categoryProvider;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(VkMarketCategoryProvider $categoryProvider, EntityManagerInterface $entityManager)
    {
        $this->categoryProvider = $categoryProvider;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Загружает категории вк')
            ->addArgument('username', InputArgument::REQUIRED, 'Логин авторизованного в вк пользователя');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['login' => $username]);
        if (!$user) {
            $output->writeln("<error>Пользователь {$username} не найден.</error>");

            return false;
        }
        if (!$user->getVkAccessToken()) {
            $output->writeln("<error>Пользователь {$username} не авторизован в Вк.</error>");

            return false;
        }
        $this->categoryProvider->saveCategories($user->getVkAccessToken());
        $output->writeln("<info>Категории успешно загружены.</info>");

        return true;
    }

}