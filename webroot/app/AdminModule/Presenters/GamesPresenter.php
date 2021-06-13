<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette;
use App\Model\GamesRepository;
use Nette\Application\UI\Form;

class GamesPresenter extends Nette\Application\UI\Presenter
{
    private GamesRepository $gamesRepository;

    private $database;

    public function __construct(Nette\Database\Context $database, GamesRepository $gamesRepository)
    {
        $this->database = $database;
        $this->gamesRepository = $gamesRepository;
    }


    public function renderDefault($genreId): void
    {
        if (!empty($genreId)) {
            $this->template->games = $this->gamesRepository->getGamesByGenreId($genreId);
        } else {
            
            $this->template->games = $this->gamesRepository->getGames();
        }
        $this->template->genres = $this->gamesRepository->getGenres();
        $this->template->headline = "Games";
    }

    

    public function renderShow(int $gameId): void
    {
      $this->template->headline = "Detail hry";
        $game = $this->gamesRepository->get($gameId);
        if (!$game) {
            $this->error('Stránka nebyla nalezena');
        }
        $this->template->game = $game;

        $genres = $this->gamesRepository->getGenre($game->id);
        $this->template->genres = $genres;
        $this->template->headline = "Games";
    }

    public function renderCreate(): void{
    bdump($this->gamesRepository->GetGenres());
    $this->template->headline = "Games"; 
    }


protected function createComponentGameForm(): Form
{
  $genres = [
        '1'=> 'hra pro vice hracu',
        '2' => 'hra pro jednoho',
        '3' => 'simulator jizdy',
        '4' => 'kockoholky',
  ];
	$form = new Form;
	  $form->addText('Name', 'Hra:')
		->setRequired();
    $form->addSelect('genre_id', 'Žánr:', $genres)
            ->setPrompt('Vyber');
    $form->addInteger('age_from', 'Minimální věk:')
		->setRequired();
    $form->addInteger('price', 'Cena v Kč:')
		->setRequired();
    $form->addText('distributor', 'Distributor:')
		->setRequired();
	  $form->addTextArea('description', 'Popis:')
		->setRequired();
    $form->addRadioList('active', 'Aktivni:',[1 => 'ano', 0 => 'ne']);
  

	$form->addSubmit('send', 'Uložit a publikovat');
	$form->onSuccess[] = [$this, 'gameFormSucceeded'];

	return $form;
}

public function gameFormSucceeded(Form $form, array $values): void
{
        if (!$this->getUser()->isLoggedIn()) {
            $this->error('Pro vytvoření, nebo editování příspěvku se musíte přihlásit.');
        }
    $gameId = $this->getParameter('gameId');
  

    if ($gameId) {
			$game = $this->database->table('game')->get($gameId);
			$game->update($values);
		} else {
			$game = $this->gamesRepository->add($values);
		}
		
	
		$this->flashMessage('Hra byla uspesne publikována', 'success');
		$this->redirect('Games:');
}

public function actionEdit(int $gameId): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Sign:in');
		}

		$game = $this->gamesRepository->get($gameId);
		if (!$game) {
			$this->error('Příspěvek nebyl nalezen');
		}
		$this['gameForm']->setDefaults($game->toArray());
    $this->template->headline = "Games";
	}
}