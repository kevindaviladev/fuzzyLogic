<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase que representa las reglas de inferencia que modelan el problema. 
Las reglas de inferencia estan formadas por un antecedente que es un grupo de variables linguisticas con sus terminos linguisticos y un 
onsecuente formado por una variable linguistica con su repectivo termino linguistico 
Para poder modelar la conjuncion en el antecedente se opto por un array de pares [var fuzzy, valor linguistico]. 
Para poder modelar la disyuncion en el antecedente se opto por arreglos de arreglos.
Ejemplo: 1) if a is 1 and b is 2 and c is 3 then d is 4 =>  [[a,1],[b,2],[c,3]]
         2) if a is 1 and (b is 2 or c is 3) then d is 4 => [ [a,1] , [[b,2],[c,3]] ]
         3) if a is 1 or b is 2 or c is 3 then d is 4  =>  [ [[a,1],[b,2],[c,3]] ]

*/
class BaseConocimiento{
    protected $reglas = array(); //reglas de inferencia de la base de conocimiento
    private $cant_reglas;
    private $regla_actual;
    
    
    public function __construct(){
        $this->cant_reglas = 0;
        $this->regla_actual = 0;
    }
    /*
        funcion para asignar las reglas de inferencia de la base de conocimiento
    */
    public function set_reglas($reglas){
        foreach ($reglas as $regla){
           if ($regla instanceOf ReglaInferencia){
               $this->cant_reglas++;
               $regla->set_numero_regla($this->cant_reglas);
               $this->reglas[$this->cant_reglas] = $regla;
           }
        };
         if ($this->cant_reglas >0) 
             $this->regla_actual=1;
    }
    
    public function add_regla($regla){
        if ($regla instanceOf ReglaInferencia){
            $this->cant_reglas++;
            $this->reglas[$this->cant_reglas] = $regla;
            $regla->set_numero_regla($this->cant_reglas);
        }     
    }
     
    /*
        Situa el puntero de las reglas en la primer regla
    */ 
    public function first_regla(){
        if ($this->cant_reglas>0)
            $this->regla_actual = 1;
    }

    /*
        avanza el puntero de las reglas
    */
    public function next_regla(){
        if ($this->regla_actual <= $this->cant_reglas)
            $this->regla_actual++;
    }

   
    /*
        devuelve la regla apuntada por el puntero actual
    */
    public function get_regla(){
        if ($this->cant_reglas > 0){
            return $this->reglas[$this->regla_actual];
        }else{
            throw new BaseConocimientoException('No se pueden obtener reglas de una base de reglas vacia');

        }
    }

    public function cant_reglas(){
            return $this->cant_reglas;
    }

    public function vacia(){
        return $this->cant_reglas == 0;
    }

    public function fin(){
        return $this->regla_actual == $this->cant_reglas+1;
    }

}

class BaseConocimientoException extends Exception{
    
}


?>