<?php

namespace Loiste\MinesweeperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Loiste\MinesweeperBundle\Model\Game;


class GameController extends Controller
{
    public function startAction()
    { 
      $req = $this->getRequest();
      $pr = $req->get('prob');
      $X =  $req->get('ncol');
      $Y =  $req->get('nrow');
      
      if (!isset($pr)) {
	$pr = 0.25;
	$X = $Y = 10;
      }

      $game = new Game($X, $Y, $pr);

      $session = new Session();
      $session->start();      
      $session->set('game', $game);
      return $this->show($game);
    }

    public function makeMoveAction()
    {
        $row = $this->getRequest()->get('row'); // Retrieves the row index.
        $column = $this->getRequest()->get('column'); // Retrieves the column index.

        $session = new Session();
        $session->start();
	$game = $session->get('game');
        $game->play($column,$row);
	return $this->show($game);
        
    }

   
    private function show($game) {
      return $this->render('LoisteMinesweeperBundle:Default:index.html.twig', 
			     $game->get_par());
    }
}
