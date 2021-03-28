<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase abastracta que transforma un conjunto difuso en un valor concreto
*/
abstract class Defuzificador{
    
    
    public function __construct(){
    }
    
    /*
        funcion diferida que se encargara de calcular el valor crisp a partir del conjunto difuso generado por el motor 
        de inferencia 
    */
    public abstract function defuzificar($x);
     
}


/* 
 clase que hereda de la clase Defuzificador e implementa la operacion defuzificar correspondiente a la defuzificacion 
 de minimo de maximos  
*/
class DefuzificadorMinOfMax extends Defuzificador{
    protected $min; 
    
    /*
    funcion que permite obtener el valor de la defuzificacion
    */
    public function get_crisp(){
        return $this->min;
    }
    
    /*
    funcion que se encarga de obtener el valor crisp en funcion del conjunto difuso de entrada 
    entrada: los una variable linguistica que ha sido activada por el motor de inferencia
    salida: el valor crisp resultado de la defuzificacion. En este caso el Minimo de los Maximos
    */
    public function defuzificar($conj_discretos){
        foreach($conj_discretos as $nombre => $conj_discreto){
            $mayor = 0;
            // echo 'AQUII';
            //para cada nombre de var linguistica con su conj discreto, obtener el par (minima etiqueta_mayor, valor_discreto)
            foreach ($conj_discreto as $par_discreto){
                // echo 'Den    tro';
                if ($par_discreto[1] > $mayor){
                    
                    $etiq_mayor = $par_discreto[0];
                    $mayor = $par_discreto[1];
               }
            }
            $min_of_max[$nombre] = array($etiq_mayor, $mayor);
        };
        return $min_of_max;
    }
}


/* 
 clase que hereda de la clase Defuzificador e implementa la operacion defuzificar correspondiente a la defuzificacion 
 de maximo de maximos  
*/
class DefuzificadorMaxOfMax extends Defuzificador{
    protected $max; 
    
    /*
    funcion que permite obtener el valor de la defuzificacion
    */
    public function get_crisp(){
        return $this->max;
    }
    


    /*
    funcion que se encarga de obtener el valor crisp en funcion del conjunto difuso de entrada 
    entrada: una variable linguistica que ha sido activada por el motor de inferencia
    salida: el valor crisp resultado de la defuzificacion. En este caso el Maximo de los Maximos
    */
    public function defuzificar($conj_discretos){
        foreach($conj_discretos as $nombre => $conj_discreto){
            $mayor = 0;
            //para cada nombre de var linguistica con su conj discreto, obtener el par (minima etiqueta_mayor, valor_discreto)
            foreach ($conj_discreto as $par_discreto){
                if ($par_discreto[1] >= $mayor){
                    
                    $etiq_mayor = $par_discreto[0];
                    $mayor = $par_discreto[1];
               }
            }
            $max_of_max[$nombre] = array($etiq_mayor, $mayor);
        };
        return $max_of_max;
    }
}


/* 
 clase que hereda de la clase Defuzificador e implementa la operacion defuzificar correspondiente a la defuzificacion 
 de medio de maximos  
*/
class DefuzificadorMedOfMax extends Defuzificador{
    protected $medio; 
    
    /*
    funcion que permite obtener el valor de la defuzificacion
    */
    public function get_crisp(){
        return $this->medio;
    }
    


    /*
    funcion que se encarga de obtener el valor crisp en funcion del conjunto difuso de entrada 
    entrada: una variable linguistica que ha sido activada por el motor de inferencia
    salida: el valor crisp resultado de la defuzificacion. En este caso el Maximo de los Maximos
    */
    public function defuzificar($conj_discretos){
        foreach($conj_discretos as $nombre => $conj_discreto){
            $mayor = 0;
            $cant = 0;
            $etiq_mayor = 0;
            //para cada nombre de var linguistica con su conj discreto, obtener la media de los pares (minima etiqueta_mayor, valor_discreto)
            foreach ($conj_discreto as $par_discreto){
                if ($par_discreto[1] == $mayor){
                    $etiq_mayor = $etiq_mayor + $par_discreto[0];
                    $cant++;
               }
               else{
                    if ($par_discreto[1] > $mayor){
                        $etiq_mayor = $par_discreto[0];
                        $mayor = $par_discreto[1];
                        $cant = 1;
                    } 
               }
            }
            $med_of_max[$nombre] = array($etiq_mayor/$cant, $mayor);
        };
        return $med_of_max;
    }
}


/* 
 clase que hereda de la clase Defuzificador e implementa la operacion defuzificar correspondiente a la defuzificacion 
 del Centroide  
*/
class DefuzificadorCOG extends Defuzificador{
    protected $COG; 
    
    /*
    funcion que permite obtener el valor de la defuzificacion
    */
    public function get_crisp(){
        return $this->COG;
    }
    

    /*
    funcion que se encarga de obtener el valor crisp en funcion del conjunto difuso de entrada 
    entrada: los conjuntos discretos resultado de la implicacion identificados por el nombre de cada Variable Linguistica
    salida: un arreglo donde los subindices son los nombre de las variables linguisticas y los elementos son el valor crisp resultado de la defuzificacion del centroide
    */
    public function defuzificar($conj_discretos){
        $centroides = array();
        foreach($conj_discretos as $nombre => $conj_discreto){
            $suma_areas = 0;
            $suma_alturas = 0;
            //para cada nombre de var linguistica con su conj discreto, obtener la media de los pares (minima etiqueta_mayor, valor_discreto)
            foreach ($conj_discreto as $par_discreto){
                if ($par_discreto[1] > 0){ //Si el resultado de la implicacion en el punto es mayor que 0 entonces sumar areas
                    $suma_areas = $suma_areas + $par_discreto[0]*$par_discreto[1];

//                    $suma_areas = $suma_areas + $par_discreto[0]*$par_discreto[1];
                    $suma_alturas = $suma_alturas + $par_discreto[1];
               }
            }
            $centroides[$nombre] = array($suma_areas/$suma_alturas);
//            $centroides[$nombre] = array($suma_areas/$suma_alturas);
        }
        return $centroides;
    }

}



?>