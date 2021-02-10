<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\GamesRepository;

class GamesPresenter extends Nette\Application\UI\Presenter
{
    private GamesRepository $gamesRepository;

    private $database;

    public function __construct(Nette\Database\Context $database, GamesRepository $gamesRepository)
    {
        $this->database = $database;
        $this->gamesRepository = $gamesRepository;
    }


    public function renderDefault(): void
    {
        $this->template->games = $this->gamesRepository
            ->getGames();
        //->limit(5);
    }

    public function renderShow(int $gameId): void
    {
        $gamesRepository = $this->database->table('games')->get($gameId);
        if (!$gamesRepository) {
            $this->error('Stránka nebyla nalezena');
        }
        $this->template->game = $gamesRepository;
        $this->template->description = $gamesRepository->related('description')->order('created_at');
    }
}
