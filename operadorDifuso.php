<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase abastracta que calcula la pertenencia de un valor en un termino lingusitico
*/
abstract class OperadorDifuso{
    protected $nombre; //Nombre del operador difuso
    protected $tipo;   //tipo de operador (Interseccion o union)
    
    public function __construct($nombre){
        $this->nombre = $nombre;
    }

    /*
        funcion que retorna el nombre del operador
    */
    public function get_nombre(){
        return $this->nombre;
    }
    
    /*
        funcion que retorna el nombre del operador
    */
    public function get_tipo(){
        return $this->tipo;
    }
    
    /*
        funcion diferida que se encargara de realizar la operacion difusa entre dos valores 
    */
    public abstract function operar($x, $y);

}


/* 
 clase que hereda de la clase operador difuso y define el tipo de operador como de interseccion
*/
abstract class TNorma extends OperadorDifuso{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
        $this->tipo = "Interseccion";
    }

}

/* 
 clase que hereda de la clase TNorma e implementa la funcion operador del Minimo
*/
class minimo extends TNorma{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
    }

    public function operar($x, $y){
        if ($x<$y) {return $x;}
        else return $y;
    }
}

/* 
 clase que hereda de la clase TNorma e implementa la funcion operador del Producto
*/
class producto extends TNorma{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
    }

    public function operar($x, $y){
        return $x*$y;
    }
}

/* 
 clase que hereda de la clase TNorma e implementa la funcion operador del Producto Drastico
*/
class producto_drastico extends TNorma{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
    }

    public function operar($x, $y){
        if ($x==1) 
            return $y;
        elseif ($y==1) 
               return $x;
        else    
            return 0; 
    }
}


/* 
 clase que hereda de la clase operador difuso y define el tipo de operador como de Union
*/
abstract class TConorma extends OperadorDifuso{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
        $this->tipo = "Union";
    }
}

/* 
 clase que hereda de la clase TConorma e implementa la funcion operador del Maximo
*/
class maximo extends TConorma{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
    }

    public function operar($x, $y){
        if ($x>$y) {return $x;}
        else return $y;
    }
}

/* 
 clase que hereda de la clase TConorma e implementa la funcion operador Suma acotada
*/
class suma_acotada extends TConorma{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
    }

    public function operar($x, $y){
        if ($x+$y < 1) return $x+$y;
        else
            return 1;
    }
}

/* 
 clase que hereda de la clase TConorma e implementa la funcion operador de la Suma Drastica
*/
class suma_drastica extends TConorma{
    /*
    constructor de clase que inicializa el nombre y el tipo de operador
    */
    public function __construct($nombre){
        parent::__construct($nombre);
    }

    public function operar($x, $y){
        if ($x==0) 
            return $y;
        elseif ($y==0) 
               return $x;
        else    
            return 1; 
    }
}

?>