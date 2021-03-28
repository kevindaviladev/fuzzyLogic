<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase abastracta que se encarga de resolver la implicacion entre conjuntos difusos
*/
abstract class Implicacion{
    protected $nombre; //Nombre del tipo de Implicacion
    
    public function __construct($nombre){
        $this->nombre = $nombre;
    }

    /*
        funcion que retorna el nombre del implicador
    */
    public function get_nombre(){
        return $this->nombre;
    }
    
    /*
        funcion diferida que se encargara de realizar la operacion de implicacion entre conjuntos difusos 
    */
    public abstract function implicar($x, $y);

}


/* 
 clase que hereda de la clase implicacion y define el tipo de implicacion de Larsen (Producto)
*/
class Larsen extends Implicacion{

    /*
    Implicación que concreta la operación implicar(num1, num2) mediante la función:
	F(x, y) = x * y
    */
    public function implicar($x, $y){
        return $x*$y;
    }
}


/* 
 clase que hereda de la clase implicacion y define el tipo de implicacion de Mamdani (Minimo)
*/
class Mamdani extends Implicacion{

    /*
    Implicación que concreta la operación implicar(num1, num2) mediante la función:
	F(x, y) = min(x, y)
    */
    public function implicar($x, $y){
        if ($x<=$y)
            return $x;
        else 
            return $y;
    }
}

/* 
 clase que hereda de la clase implicacion y define el tipo de implicacion de Zadeh
*/
class Zadeh extends Implicacion{

    /*
    Implicación que concreta la operación implicar(num1, num2) mediante la función:
	F(x, y) = max(min(x, y), 1-x)
    */
    public function implicar($x, $y){ 
        $b = 1-$x;
        if ($x<=$y)
            $min = $x;
        else 
            $min = $y;
        if ($min>=$b)
            return $min;
        else
            return $b;
    }
}


/* 
 clase que hereda de la clase implicacion y define el tipo de implicacion de Lukasiewicz
*/
class Lukasiewicz extends Implicacion{

    /*
    Implicación que concreta la operación implicar(num1, num2) mediante la función:
	F(x, y) = min(1, 1 - x + y)
    */
    public function implicar($x, $y){
        $b = 1-$x+$y;
        if ($b<=1)
            return $b;
        else 
            return 1;
    }
}

?>