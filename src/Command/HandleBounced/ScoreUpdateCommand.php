<?php

namespace App\Command\HandleBounced;

use App\Entity\HandleBounced\SuppressedClient;
use App\Repository\HandleBounced\SuppressedClientRepository;
use App\Service\HandleBounced\Appraiser;
use App\Service\Keap\InfusionsoftApi;
use DateTime;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:handle-bounced:score:update',
    description: 'Update score for users with bounced & complaint mails.',
)]
class ScoreUpdateCommand extends Command
{
    public function __construct(
        private readonly Appraiser $appraiser,
        private readonly SuppressedClientRepository $clientRepository,
        private readonly string $update_score_time_limit
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title($this->getDescription());

        $recipients = $this->clientRepository->findAllNotUpdatedFrom(new DateTime($this->update_score_time_limit));

        if (empty($recipients)) {

            $io->success('Nothing to update.');

            return Command::SUCCESS;
        }

        $io->section('Start updating:');

        array_walk($recipients, [$this, 'updateScore'], $io);

        $io->success('Done.');

        return Command::SUCCESS;
    }

    private function updateScore(SuppressedClient $client, $key, SymfonyStyle $io): void
    {
        $score = $this->appraiser->assess($client);
        $client->setScore($score);

        $this->clientRepository->save($client, true);

        $io?->text('#'.$key.' - '.$client->getEmail());
    }
}
