<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase abastracta que calcula la pertenencia de un valor en un termino lingusitico
*/
abstract class Pertenencia{
    protected $limite_izquierdo; //menor valor del intervalo donde pude ser aplicada la funcion de pertenencia
    protected $limite_derecho;   //mayor valor del intervalo donde pude ser aplicada la funcion de pertenencia
    
    public function __construct($izq, $der){
        $this->set_limits($izq, $der);
    }
    /*
        funcion para asignar los limites donde sera aplicada la funcion de pertenencia
    */
    public function set_limits($izq, $der){
        if ($izq<$der){
            $this->limite_izquierdo = $izq;
            $this->limite_derecho = $der;
        } else{
            $this->limite_izquierdo = $der;
            $this->limite_derecho = $izq;
        }
    }
    
    public function get_limite_izquierdo(){
        return $this->limite_izquierdo;
    }

    public function get_limite_derecho(){
        return $this->limite_derecho;
    }

    /*
        funcion que discretiza la funcion de pertenencia en funcion del valor de paso que se recibe como entrada y en caso de encontrar algun maximo de otros terminos lo agrega.
    */
    public abstract function discretizar($paso, $maximos);

    
    /*
        funcion que devuelve los valores de x donde la funcion de pertenencia alcanza los maximos
    */
    public abstract function maximos();
    
    
    /*
        funcion diferida que se encargara de calcular el grado de pertenencia de un valor crisp (o entrada abrupta) 
    */
    public abstract function calcular_pertenencia($x);

    
    /*
        funcion que calcula el centro de gravedad de la figura definida por la funcion de pertenencia junto con la 
        asintota definida por y (y es el valor de pertenencia de la etapa de fuzificacion)
        entrada: valor de pertenencia del termino linguistico que tiene asociada la funcion de pertenencia actual
        salida: el centro de gravedad de la figura determinada por el area de la funcion de pertenencia y el valor y
        + el area de la misma
    */
    public abstract function COG($y);
     
}


/* 
 clase que hereda de la clase pertenencia e implementa la operacion calcular pertenencia correspondiente
 a la triangular.  
*/
class PertenenciaTriangular extends Pertenencia{
    protected $modal; //valor modal que, junto a los limites izquierdo y derecho, definen el triangulo utilizado para el calculo de pertenencia
    
    /*
    constructor de clase que inicializa los limites y el modal
    */
    public function __construct($izq, $der, $modal){
        parent::__construct($izq, $der);
        $this->modal = $modal;
    }
    /*
    funcion que permite setear el valor modal
    */
    public function set_modal($modal){
        if (($modal>=$this->limite_izquierdo) and ($modal<=$this->limite_derecho)) // el modal esta entre los limites
            $this->modal = $modal;
        else{
            $this->modal = ($this->limite_derecho - $this->limite_izquierdo)/2; // si el modal no se encuentra entre los limites se asigna el valor medio arbitrariamente
        };
    }
    
    /*
        funcion que determina las intersecciones del perimetro triangular con la recta de y con valor entre 0 y 1
        el valor de entrada es el valor de y que representa la recta
        la salida son los valores de x que son la interseccion del triangulo con la recta de y
    */
    public function intersecciones_x($y){
        if ($y==1){
            return array($this->modal);
        }else{
            $x1 = $y*($this->modal - $this->limite_izquierdo) + $this->limite_izquierdo;
            $x2 = -($y*($this->limite_derecho - $this->modal)- $this->limite_derecho);
            return array($x1,$x2); 
        }
    }


    
    /*
    funcion que se encarga de calcular la pertenencia de un valor crisp utilizando el concepto de 
    pertenencia triangular
    */
    public function calcular_pertenencia($x){
        $a = $this->limite_izquierdo;
        $b = $this->limite_derecho;
        $result;
        if (($x<$a)or($x>$b)){//el valor esta fuera del triangulo
            return 0;
        }elseif ($x<$this->modal){ //el valor esta en la parte izq del triangulo
            return ($x-$a)/($this->modal-$a);
        }elseif ($x>=$this->modal){ //el valor esta en la parte derecha del triangulo
            return ($b-$x)/($b-$this->modal);
        };
    }
    
    /*
    funcion que discretiza la funcion triangular tomando como paso el valor de entrada $paso. Adiciona los maximos a la discretizacion.
    Devuelve como resultado un arreglo de pares (val crisp, valor de pertenencia del valor)
    */
    public function discretizar($paso, $maximos){
         $conj_discreto = array();
         $a=$this->limite_izquierdo / $paso;
         $b = intval ($this->limite_izquierdo/$paso);
         if ($a * $paso == $b * $paso){
            $inicio = $this->limite_izquierdo;
         }else{
            if ($b>=0){
                $inicio = ($b + 1) * $paso;
            }else{
                $inicio = $b * $paso;
            }
         }
         $i=0;
         $x = $inicio;
         while ($x <= $this->limite_derecho){
            $val = $this->calcular_pertenencia($x);
            $conj_discreto[$i] = array($x,$val);
            $i++;
            foreach ($maximos as $maximo){ //si se encuentra que alguno de los valores de x de los maximos se encuentra entre los valores de x de la discretizacion
                                           //entonces se agregan los valores de x de los maximos con su valor de pertenencia en la etiqueta linguistica.
                                           //Esto sera de utilidad a la hora de realizar la agregacion de los consecuentes de las reglas activadas. 
                if ($x<$maximo and $x+$paso >$maximo){ //si al discretizar no se coincide con el modal entonces se agrega el modal con el valor 1 de pertenencia 
                   $conj_discreto[$i]= array($maximo,$this->calcular_pertenencia($maximo));
                   $i++;
                }
            }
            $x = $x + $paso;
         }
         return $conj_discreto;
    }


    public function maximos(){
        return array($this->modal);
        
    } 


    public function COG($y){
        if (($this->modal - $this->limite_izquierdo) == ($this->limite_derecho - $this->modal)){
            //estamos en el caso de una funcion de pertenencia triangular simetrica
            //el centro de gravedad coincide con el modal
            return $this->modal;
        }else{
            //calcular intersecciones con y
            //calcular centros de gravedad de los triangulos entre el limite izq y la interseccion y la segunda interseccion y el limite derecho
            //calcular el centro de gravedad del rectangulo entre ambas intersecciones y luego aplicar la formula de suma de centros
            $xs = $this->intersecciones_x($y);
            $c1 = $this->limite_izquierdo + ($xs[0]-$this->limite_izquierdo)*2/3;
            $a1 = abs(($xs[0]-$this->limite_izquierdo)*$y/2);
            $c2 = $xs[1] + ($this->limite_derecho - $xs[1])/3;
            $a2 = abs(($this->limite_derecho-$xs[1])*$y/2);
            $c3 = $xs[0]+abs(($xs[1]-$xs[0])/2);
            $a3 = abs(($xs[1]-$xs[0])*$y);
            return array(($c1*$a1+$c2*$a2+$c3*$a3)/($a1+$a2+$a3), $a1+$a2+$a3);
        };
    
    }
}



/* 
 clase que hereda de la clase pertenencia e implementa la operacion calcular pertenencia correspondiente
 a la trapezoidal.  
*/
class PertenenciaTrapezoidal extends Pertenencia{
    protected $soporte_izq; //valor modal que, junto a los limites izquierdo y derecho y al soporte derecho, definen el trapecio utilizado para el calculo de pertenencia
    protected $soporte_der; //valor modal que, junto a los limites izquierdo y derecho y al soporte derecho, definen el trapecio utilizado para el calculo de pertenencia
    
    /*
    constructor de clase que inicializa los limites y los soportes
    */
    public function __construct($izq, $der, $soporte_izq, $soporte_der){
        parent::__construct($izq, $der);
        $this->set_soportes($soporte_izq, $soporte_der);
    }

    /*
    funcion que permite setear los soportes
    */
    public function set_soportes($soporte_izq, $soporte_der){
        if ($soporte_izq>$soporte_der){ // el modal esta entre los limites
           $this->soporte_izq = $soporte_der;
           $this->soporte_der = $soporte_izq;
        }else{
           $this->soporte_izq = $soporte_izq;
           $this->soporte_der = $soporte_der;
        }; 
    }

    
    public function intersecciones_x($y){
    }


    /*
    funcion que se encarga de calcular la pertenencia de un valor crisp utilizando el concepto de 
    pertenencia triangular
    */
    public function calcular_pertenencia($x){
        $a = $this->limite_izquierdo;
        $b = $this->limite_derecho;
        $result = 0;
        if (($x<$a)or($x>$b)){//el valor esta fuera del trapecio
            return 0;
        }elseif ($x<$this->soporte_izq){ //el valor esta en la parte izq del trapecio
            return ($x-$a)/($this->soporte_izq-$a);
        }elseif (($x>=$this->soporte_izq) and ($x<=$this->soporte_der) ){ //el valor esta entre los soportes del trapecio
            return 1;
        }else{    //el valor esta en la parte derecha del trapecio
            return ($b-$x)/($b-$this->soporte_der);
        };
    }
    
    

    public function COG($y){
    }

    /*
    funcion que discretiza la funcion triangular tomando como paso el valor de entrada $paso. Adiciona los maximos a la discretizacion.
    Devuelve como resultado un arreglo de pares (val crisp, valor de pertenencia del valor)
    */
    public function discretizar($paso, $maximos){
         $conj_discreto = array();
         $a=$this->limite_izquierdo / $paso;
         $b = intval ($this->limite_izquierdo/$paso);
         if ($a * $paso == $b * $paso){
            $inicio = $this->limite_izquierdo;
         }else{
            if ($b>=0){
                $inicio = ($b + 1) * $paso;
            }else{
                $inicio = $b * $paso;
            }
         }
         $i=0;
         $x = $inicio;
         while ($x <= $this->limite_derecho){
            $val = $this->calcular_pertenencia($x);
            $conj_discreto[$i] = array($x,$val);
            $i++;
            foreach ($maximos as $maximo){ //si se encuentra que alguno de los valores de x de los maximos se encuentra entre los valores de x de la discretizacion
                                           //entonces se agregan los valores de x de los maximos con su valor de pertenencia en la etiqueta linguistica.
                                           //Esto sera de utilidad a la hora de realizar la agregacion de los consecuentes de las reglas activadas. 
                if ($x<$maximo and $x+$paso >$maximo){ //si al discretizar no se coincide con el modal entonces se agrega el modal con el valor 1 de pertenencia 
                   $conj_discreto[$i]= array($maximo,$this->calcular_pertenencia($maximo));
                   $i++;
                }
            }
            $x = $x + $paso;
         }
         return $conj_discreto;
    }


    public function maximos(){
        return array($this->soporte_izq, $this->soporte_der);
    }

}


/* 
 clase que hereda de la clase pertenenciaTrapezoidal e implementa la operacion calcular pertenencia correspondiente
 a la L.  
*/
class PertenenciaL extends PertenenciaTrapezoidal{
    
    /*
    constructor de clase que inicializa los limites y los soportes
    */
    public function __construct($izq, $der, $soporte_der){
        parent::__construct($izq, $der, $izq, $soporte_der);
    }

    /*
    funcion que permite setear el soporte
    */
    public function set_soporte($soporte_der){
        $this->soporte_izq = $this->limite_izquierdo;
        if (($soporte_der>$this->limite_izquierdo) and ($soporte_der<=$this->limite_derecho)){ // el soporte esta entre los limites
           $this->soporte_der = $soporte_der;
        }else{
           $this->soporte_der = $this->limite_derercho;
        }; 
    }

    
    public function intersecciones_x($y){
    }


    /*
    funcion que se encarga de calcular la pertenencia de un valor crisp utilizando el concepto de 
    pertenencia L
    */
    public function calcular_pertenencia($x){
        if ($x>$this->limite_derecho){
           return 0;
        }else{
           return parent::calcular_pertenencia($x);
        }
    }
    
    

    public function COG($y){
    }

 
    public function maximos(){
        return array($this->soporte_izq, $this->soporte_der);
    }


}


/* 
 clase que hereda de la clase pertenencia e implementa la operacion calcular pertenencia correspondiente
 a la Gamma  
*/
class PertenenciaGamma extends PertenenciaTrapezoidal{
    
    /*
    constructor de clase que inicializa los limites y los soportes
    */
    public function __construct($izq, $der, $soporte_izq){
        parent::__construct($izq, $der, $soporte_izq, $der);
    }

    /*
    funcion que permite setear el soporte
    */
    public function set_soporte($soporte_izq){
        $this->soporte_der = $this->limite_derecho;
        if (($soporte_izq>=$this->limite_izquierdo) and ($soporte_izq<$this->limite_derecho)){ // el soporte esta entre los limites
           $this->soporte_izq = $soporte_izq;
        }else{
           $this->soporte_izq = $this->limite_izquierdo;
        }; 
    }

    
    public function intersecciones_x($y){
    }


    /*
    funcion que se encarga de calcular la pertenencia de un valor crisp utilizando el concepto de 
    pertenencia Gamma
    */
    public function calcular_pertenencia($x){
        if ($x<$this->limite_izquierdo){
           return 0;
        }else{
           return parent::calcular_pertenencia($x);
        }
    }
    
    

    public function COG($y){
    }

}


?>
