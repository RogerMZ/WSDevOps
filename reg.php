<?
/*
 * Dada la ecuación y = mx + b
 *
 *     n(∑(xi)(yi))-∑(xi)*∑(yi)
 * m = ------------------------
 *     n∑(xi^2) - (∑(xi))^2
 *
 *      ∑yi - (m * ∑xi)
 * b = -----------------
 *             n
 *
 * y = mx + b
 *
 */
/*
$v_x = [1, 2, 3, 4, 5];
$v_y = [5, 5, 5, 6.8, 9];
$pos = 100;
 */
$v_x = [2010, 2011, 2012, 2013, 2014, 2015, 2016];
$v_y = [166.6, 200, 430, 451, 474, 620, 651];
$pos = 2017;


echo "Regresión lineal \n";

$n = count($v_x);

for($i = 0;$i < $n;$i++)
{
  $x += $v_x[$i];
  $y += $v_y[$i];
  $xy += $v_x[$i] * $v_y[$i];
  $xx += $v_x[$i] ** 2;
}

//echo "\n".$x;
//echo "\n".$y;
//echo "\n".$xx;
//echo "\n".$xy;

$m = (($n * $xy) - ($x * $y)) / (($n * $xx) - ($x ** 2));

//echo "\n".$m;

$b = ($y - ($m * $x)) / $n;

//echo "\n".$b;

echo "\nResultado: ".(($m * $pos) + $b);

