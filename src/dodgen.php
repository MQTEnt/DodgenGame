<?php
namespace App;
class Dodgen
{
	static $instances = 0;
    public $instance;
	public $b=array();
	public $pos=array();
	// private $_1=array();
	// private $_2=array();
	// private $_3=array();
	// private $_4=array();
	public function __construct()
	{
		$this->b=[
			[' ',' ',' ',' '],
			['1',' ',' ',' '],
			['2',' ',' ',' '],
			[' ','3','4',' ']
		];
		$this->pos=[
			'1'=>[1,0],
			'2'=>[2,0],
			'3'=>[3,1],
			'4'=>[3,2]
		];
		// $_1=[1,0];
		// $_2=[2,0];
		// $_3=[3,1];
		// $_4=[3,2];
	}
	public function __clone()
	{
        $this->instance = ++self::$instances;
    }
    public function getGameOver()
    {
    	$cComputer=0;
    	$cPlayer=0;
    	if($this->pos['1'][1]==3)
    		$cPlayer++;
    	if($this->pos['2'][1]==3)
    		$cPlayer++;
    	if($this->pos['3'][0]==0)
    		$cComputer++;
    	if($this->pos['4'][0]==0)
    		$cComputer++;
		if($cPlayer==2 || $cComputer==2)
			return 1;
		return 0;
    }
	public function go($number, $direct)
	{
		$nBoard= clone $this;
		if($this->getGameOver()==1)
			return null;
		if($number=='1'||$number=='2')
		{
			if($direct=='up')
			{
				if($this->pos[$number][0]!=1&&$this->b[$this->pos[$number][0]-1][$this->pos[$number][1]]==' ')
				{
					$nBoard->b[$this->pos[$number][0]-1][$this->pos[$number][1]]=$number;
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]]=' ';
					$nBoard->pos[$number][0]--;
					return $nBoard;
				}
				else
					return null;
			}
			if($direct=='down')
			{
				if($this->pos[$number][0]!=3&&$this->b[$this->pos[$number][0]+1][$this->pos[$number][1]]==' ')
				{
					$nBoard->b[$this->pos[$number][0]+1][$this->pos[$number][1]]=$number;
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]]=' ';
					$nBoard->pos[$number][0]++;
					return $nBoard;
				}
				else
					return null;
			}
			if($direct=='right')
			{
				if($this->pos[$number][1]!=3&&($this->b[$this->pos[$number][0]][$this->pos[$number][1]+1]==' '
					||$this->pos[$number][1]==2))
				{
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]+1]=$number;
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]]=' ';
					$nBoard->pos[$number][1]++;
					return $nBoard;
				}
				else
					return null;
			}
		}
		if($number=='3'||$number=='4')
		{
			if($direct=='up')
			{
				if($this->pos[$number][0]!=0&&($this->b[$this->pos[$number][0]-1][$this->pos[$number][1]]==' '
					||$this->pos[$number][0]==1))
				{
					$nBoard->b[$this->pos[$number][0]-1][$this->pos[$number][1]]=$number;
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]]=' ';
					$nBoard->pos[$number][0]--;
					return $nBoard;
				}
				else
					return null;
			}
			if($direct=='right')
			{
				if($this->pos[$number][1]!=2&&$this->b[$this->pos[$number][0]][$this->pos[$number][1]+1]==' ')
				{
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]+1]=$number;
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]]=' ';
					$nBoard->pos[$number][1]++;
					return $nBoard;
				}
				else
					return null;
			}
			if($direct=='left')
			{
				if($this->pos[$number][1]!=0&&$this->b[$this->pos[$number][0]][$this->pos[$number][1]-1]==' ')
				{
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]-1]=$number;
					$nBoard->b[$this->pos[$number][0]][$this->pos[$number][1]]=' ';
					$nBoard->pos[$number][1]--;
					return $nBoard;
				}
				else
					return null;
			}
		}
	}
	public function computerGen()
	{
		$g=array();
		$direct=['up','right','left'];
		$number=['3','4'];
		for ($i=0; $i<3; $i++)
		{
			for($j=0; $j<2; $j++)
				if($this->pos[$number[$j]][0]!=0)
				{
					$nBoard=$this->go($number[$j],$direct[$i]);
					if(!is_null($nBoard))
						array_push($g, $nBoard);
				}
		}
		return $g;
	}
	public function playerGen()
	{
		$g=array();
		$direct=['up','right','down'];
		$number=['1','2'];
		for ($i=0; $i<3; $i++)
		{
			for($j=0; $j<2; $j++)
				if($this->pos[$number[$j]][1]!=3)
				{
					$nBoard=$this->go($number[$j],$direct[$i]);
					if(!is_null($nBoard))
						array_push($g, $nBoard);
				}
		}
		return $g;
	}
	public function evaluateBoard()
	{
		$tableTScore=[
			[10,25,40,100],
			[5,20,35,100],
			[0,15,30,100]
		];
		$tableEScore=[
			[100,100,100],
			[30,35,40],
			[15,20,25],
			[0,5,10]
		];
		$tScore=0;
		$eScore=0;
		// 1 vs 3
		if($this->pos['1'][1]==$this->pos['3'][1])
		{
			if($this->pos['1'][0]+1==$this->pos['3'][0])
				$tScore+=30;
			if($this->pos['1'][0]+2==$this->pos['3'][0])
				$tScore+=20;
		}
		//1 vs 4
		if($this->pos['1'][1]==$this->pos['4'][1])
		{
			if($this->pos['1'][0]+1==$this->pos['4'][0])
				$tScore+=30;
			if($this->pos['1'][0]+2==$this->pos['4'][0])
				$tScore+=20;
		}
		// 2 vs 3
		if($this->pos['2'][1]==$this->pos['3'][1])
		{
			if($this->pos['2'][0]+1==$this->pos['3'][0])
				$tScore+=30;
			if($this->pos['2'][0]+2==$this->pos['3'][0])
				$tScore+=20;
		}
		// 2 vs 4
		if($this->pos['2'][1]==$this->pos['4'][1])
		{
			if($this->pos['2'][0]+1==$this->pos['4'][0])
				$tScore+=30;
			if($this->pos['2'][0]+2==$this->pos['4'][0])
				$tScore+=20;
		}
		// 3 vs 1
		if($this->pos['3'][0]==$this->pos['1'][0])
		{
			if($this->pos['3'][1]-1==$this->pos['1'][1])
				$eScore+=30;
			if($this->pos['3'][1]-2==$this->pos['1'][1])
				$eScore+=20;
		}
		// 3 vs 2
		if($this->pos['3'][0]==$this->pos['2'][0])
		{
			if($this->pos['3'][1]-1==$this->pos['2'][1])
				$eScore+=30;
			if($this->pos['3'][1]-2==$this->pos['2'][1])
				$eScore+=20;
		}
		// 4 vs 1
		if($this->pos['4'][0]==$this->pos['1'][0])
		{
			if($this->pos['4'][1]-1==$this->pos['1'][1])
				$eScore+=30;
			if($this->pos['4'][1]-2==$this->pos['1'][1])
				$eScore+=20;
		}
		// 4 vs 2
		if($this->pos['4'][0]==$this->pos['2'][0])
		{
			if($this->pos['4'][1]-1==$this->pos['2'][1])
				$eScore+=30;
			if($this->pos['4'][1]-2==$this->pos['2'][1])
				$eScore+=20;
		}
		$tScore+=$tableTScore[$this->pos['1'][0]-1][$this->pos['1'][1]];
		$tScore+=$tableTScore[$this->pos['2'][0]-1][$this->pos['2'][1]];
		$eScore+=$tableEScore[$this->pos['3'][0]][$this->pos['3'][1]];
		$eScore+=$tableEScore[$this->pos['4'][0]][$this->pos['4'][1]];
		return $eScore-$tScore;
	}
	public function display()
	{
		echo '<table style="border: 1px solid; border-collapse: collapse;">';
		for ($i=1; $i <4; $i++)
		{ 
			echo '<tr>';
			for ($j=0; $j<3; $j++)
			{
				// $border=' ';
				// if($i==0||$j==3)
				// {
				// 	$border='color: #ff3333';
				// }
				$chess=' ';
				if($this->b[$i][$j]=='1')
				{
					$chess='<h2>&#9814;</h2>';
				}
				if($this->b[$i][$j]=='2')
				{
					$chess='<h2>&#9816;</h2>';
				}
				if($this->b[$i][$j]=='3')
				{
					$chess='<h2>&#9822;</h2>';
				}
				if($this->b[$i][$j]=='4')
				{
					$chess='<h2>&#9820;</h2>';
				}
				echo "<td style='border: 1px solid; width:70px; height:70px; text-align:center'>".$chess."</td>";
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<br>';
	}
}
