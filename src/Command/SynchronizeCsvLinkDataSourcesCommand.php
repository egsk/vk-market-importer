<?php


namespace App\Command;


use App\Entity\CsvLinkDataSource;
use App\Message\UpdateDataSource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SynchronizeCsvLinkDataSourcesCommand extends Command
{
    protected static $defaultName = 'app:synchronize_csv_link_data_sources';

    protected $entityManager;
    protected $bus;

    /**
     * SynchronizeCsvLinkDataSourcesCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $bus
     */
    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dataSources = $this->entityManager
            ->getRepository(CsvLinkDataSource::class)
            ->findBy(['validated' => 1]);
        foreach ($dataSources as $dataSource) {
            $this->bus->dispatch(new UpdateDataSource($dataSource));
        }

        return 0;
    }
}