<?php


namespace App\Command;


use App\Entity\UploadTask;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClearUploadTaskCommand extends Command
{
    protected static $defaultName = 'app:clear_upload_task';
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager
            ->getRepository(UploadTask::class);
        $tasks = $repository
            ->findBy(['status' => UploadTask::STATUS_FINISHED]);
        foreach ($tasks as $task) {
            $this->entityManager->remove($task);
        }
        $this->entityManager->flush();

        return 0;
    }
}