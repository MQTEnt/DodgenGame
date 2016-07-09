<?php
require 'dodgen.php';
require '../vendor/autoload.php';
use App\Dodgen;
use Tree\Node\Node;
session_start();
////////////////////////
///////////////////////
//////////////////////
//Fuction
function MaxVal(Node $u, $a, $b)
{
	if($u->isLeaf())
	{
		return $u->getValue()->evaluateBoard();
	}
	else
	{
		$g=$u->getChildren();
		foreach($g as $v)
		{
			$a=max([$a, MinVal($v, $a, $b)]);
			//Cut
			if($a>=$b)
				break;
		}
	}
	return $a;
}
function MinVal(Node $u, $a, $b)
{
	if($u->isLeaf())
	{
		return $u->getValue()->evaluateBoard();
	}
	else
	{
		$g=$u->getChildren();
		foreach($g as $v)
		{
			$b=min([$b, MaxVal($v, $a, $b)]);
			//Cut
			if($a>=$b)
				break;
		}
	}
	return $b;
}
function Alpha_beta(Node $u, Node &$v)
{
	$a=-999999;
	$b=999999;
	$g=$u->getChildren();
	foreach($g as $w)
	{
		if($a<=MinVal($w, $a, $b)&&$w->getDepth()%2!=0)
		{
			$a=MinVal($w, $a, $b);
			$v=$w;
		}
	}
}
//////////////////////////////////
/////////////////////////////////
////////////////////////////////
//main
if(!isset($_SESSION['board'])||isset($_POST['reset']))
{
	$_SESSION['board']=new Dodgen();
	echo $_SESSION['board']->display();
	//echo $_SESSION['board']->evaluateBoard();
}
if (isset($_POST['submit']))
{
	$b=$_SESSION['board']->go($_POST['number'],$_POST['direct']);
	if(is_null($b))
	{
		echo "Can't go";
		$_SESSION['board']->display();
	}
	else
	{
		$node=new Node(0);
		$node->setValue($b);
		////////////////////////
		////////////////////////
		//Just generate 2 level of the tree for the best solution
		$i=1;
		$g=$b->computerGen();
		foreach($g as $n)
		{
			$child=new Node($i++);
			$child->setValue($n);
			//$n->display();
			//echo $n->evaluateBoard();
			$node->addChild($child);
		}
		foreach ($node->getChildren() as $c1)
		{
			$g1=$c1->getValue()->playerGen();
			foreach($g1 as $n2)
			{
				$child=new Node($i++);
				$child->setValue($n2);
				//$n2->display();
				//echo $n2->evaluateBoard();
				$c1->addChild($child);
			}
		}
		$decide=new Node();
		Alpha_beta($node, $decide);
		if(is_null($decide->getValue())||$decide->getValue()->getGameOver())
			echo "Game Over";
		else
			$decide->getValue()->display();
		$_SESSION['board']=$decide->getValue();
	}
}
//session_unset($_SESSION['board']);
//session_destroy();
?>
<!-- UI -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dodgen</title>
</head>
<body>
	<form method="POST">
		<select name="number">
			<option value="" disabled selected>Chessman</option>
			<option value="1">&#9814;</option>
			<option value="2">&#9816;</option>
		</select>
		<select name="direct">
			<option value="" disabled selected>Direct</option>
			<option value="up">&uarr;</option>
			<option value="right">&rarr;</option>
			<option value="down">&darr;</option>
		</select>
		
		<input type="submit" name="submit" value="Go">
		<input type="submit" name="reset" value="Reset">
	</form>
</body>
</html>
