<?php

namespace Loiste\MinesweeperBundle\Model;

/**
 * This class represents a game model.
 */


class Game
{
   
  public function __construct($X, $Y, $pr) {
      $this->pr = $pr;
      $this->X = $X;
      $this->Y = $Y;
      $this->nCell = $X*$Y;

      $this->neigh = array(
		 array(-1, 1),
		 array(0, 1),
		 array(1, 1),
		 array(1, 0),
		 array(1, -1),
		 array(0, -1),
		 array(-1, -1),
		 array(-1, 0));
      
      $this->nVis = 0;  // number of visible cells

      $cell = array('n' => 0, 'm' => false, 'p' => 'cell', 'v' => false);
      // number, mine, picture, visible

      for ($x = 0; $x < $X; $x++)
	for ($y = 0; $y < $Y; $y++)
	  $this->grid[$x][$y] = $cell;

      $this->msg = 'Click a cell to start';
   }

   public function get_par() {
     return array('grid' => $this->grid,'pr' => $this->pr,
		  'X' => $this->X, 'Y'=> $this->Y, 'msg' => $this->msg);
   }
   

   public function play($nx, $ny) {

     if (!$this->nVis)
       $this->generate($nx, $ny);
    
     
     if ($this->grid[$nx][$ny]['m']) {
       // make all other mines visible and this mine explode
       for ($x = 0; $x < $this->X; $x++)
	 for ($y = 0; $y < $this->Y; $y++)
	   if ($this->grid[$x][$y]['m'])
	     $this->grid[$x][$y]['p'] = 'mine';
    
       $this->grid[$nx][$ny]['p'] = 'explosion';
       $this->msg = 'Sorry, you lost';
     }
     else if (!$this->grid[$nx][$ny]['v']) {
       $this->reveal($nx, $ny);
       if ($this->nVis == $this->nSafe)
	 $this->msg = 'Congratulations, you won !';
       else
	 $this->msg = '';
     }
   }

   
   private function generate($nx, $ny) {
       $nmax = $this->pr*$this->nCell;
       $nm = 0;

       while ($nm < $nmax) {
	 $x = mt_rand(0, $this->X-1);
	 $y = mt_rand(0, $this->Y-1);
	 if (($x != $nx || $y != $ny) && !$this->grid[$x][$y]['m']) {
	   $this->grid[$x][$y]['m'] = true;
	   $nm++;
	 }
       }
       
       $this->nSafe = $this->nCell - $nm; // cells without mines
   }


   private function reveal($x, $y) {
     $this->count($x,$y);
     $this->grid[$x][$y]['v'] = true;
     $this->nVis++;

     if (!$this->grid[$x][$y]['n']) {
       foreach ($this->neigh as $v) {
	 $x1 = $x + $v[0];
	 $y1 = $y + $v[1];
	 if ($this->inside($x1,$y1) && !$this->grid[$x1][$y1]['v'])
	   $this->reveal($x1, $y1);
       }
       $this->grid[$x][$y]['p'] = 'empty';
     }
     else
       $this->grid[$x][$y]['p'] = $this->grid[$x][$y]['n'];
   }

    private function count($x, $y) {
     foreach ($this->neigh as $v) {
	$x1 = $x + $v[0];
        $y1 = $y + $v[1];
	if ($this->inside($x1,$y1))
	  $this->grid[$x][$y]['n'] += $this->grid[$x1][$y1]['m'];
     }
   }


    private function inside($x,$y) {
     return $x >= 0 && $x < $this->X && $y >= 0 && $y < $this->Y;
    }
}