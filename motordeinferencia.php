<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase abastracta que evalua las reglas de inferencia. Recibe como entrada la base de conocimiento con las reglas de inferencia y devuelve  un conjunto de reglas activadas para ser desfuzificadas. Por ejemplo el motor de inferencia mamadni evalua los antecedentes. 
  Las reglas con antecedente mayor que cero son activadas 
  y devueltas para resolver la defuzificacion con el metodo de desfuzificacion seleccionado

  Otro posible motor de inferencia es el de Takagi_Sugeno en el cual las salidas son funcion de las variables del antecedente
*/
abstract class MotorInferencia{
    protected $base_conocimiento; //reglas de inferencia de entrada al motor de inferencia
    protected $tnorma;            //TNorma utilizada para evaluar la conjuncion en los antecedentes   
    protected $tconorma;          //TConorma utilizada para evaluar la disjuncion en los antecedentes   
    protected $implicacion;       //Tipo de implicacion utilizada en las reglas de inferencia
    protected $agregacion;        //Tipo de TConorma usada en la agregacion de los resultados de las reglas de inferencia  


    public function __construct($BaseC, $tnorma, $tconorma, $implicacion, $agregacion){
        $this->set_base_conocimiento($BaseC);
        $this->tnorma = $tnorma;
        $this->tconorma = $tconorma;
        $this->implicacion = $implicacion;
        $this->agregacion = $agregacion;
    }
    /*
        funcion para asignar los limites donde sera aplicada la funcion de pertenencia
    */
    public function set_base_conocimiento($baseC){
        if ($baseC instanceOf BaseConocimiento){
            $this->base_conocimiento = $baseC;
        } else{
            throw new baseConocimientoException('Asignacion erronea de la base de conocimiento');
        }
    }
    
    public function set_tnorma($tnorma){
        $this->tnorma = $tnorma;
    }
    
    public function get_tnorma(){
        return $this->tnorma;
    }
    
    
    public function set_tconorma($tconorma){
        $this->tconorma = $tconorma;
    }

    public function get_tconorma(){
        return $this->tconorma;
    }

    public function set_implicacion($implicacion){
        $this->implicacion = $implicacion;
    }

    public function get_implicacion(){
        return $this->implicacion;
    }
    
    
    public function set_agregacion($agregacion){
        $this->agregacion = $agregacion;
    }

    public function get_agregacion(){
        return $this->agregacion;
    }
   

    /*
        funcion diferida que se encargara realizar la inferencia de las reglas de la base de conocimiento dados los valores de entrada
        Entrada: pares de valores variable fuzzy + valor crisp 
    */
    public abstract function inferir($valores);
    
     
    /*
    funcion que unifica los resultados individuales de las reglas activadas
    */
    public abstract function agregacion($activadas);
    
    /*
    funcion encargada de obtener el resultado de la inferencia a partir de la agregacion de las reglas activadas
    */
    public abstract function resultado();
     
}


class MotorInferenciaMamdani extends MotorInferencia{
    
  
    /* funcion encargada de inferir las reglas de inferencia de la base de conocimiento.
       Recibe como entrada una lista con las variables linguisticas que seran evaluadas en el proceso de inferencia.
       La fuzzificacion debe hacerse previa a la inferencia. Por ejemplo si queremos evaluar para la variable
       linguistica temperatura un valor de 25 grados, primero debemos llamar a fuzzificar de la variable linguistica
       y luego llamar a inferir con la variable linguistica.
       La salida de la funcion es una lista que variables linguisticas(consecuentes de las reglas) con sus correspondientes valores de activacion que se 
       obtienen al hacer la agregacion de los resultados de las reglas activadas
       Tener en cuenta que todos los parametros (TNorma, TConorma, Implicacion y agregacion) deben estar inicializados para un correcto funcionamiento.  
    */
    public function inferir($variables){
        $reglas_activadas = $this->activadas($variables);
        // echo 'dentro';
        //para cada regla activada realizar la implicacion segun la funcion de implicacion asignada al motor de inferencia
        
        foreach ($reglas_activadas as $activada){   //para cada regla activada realizar la implicacion segun la funcion de implicacion y el valor de activacion resultado de la evaluacion del antecedente
            // echo 'antes';
            $activada[0]->implicar($this->implicacion, $activada[1]); 
            // echo 'despues';
        } //al terminar el bucle tenemos todas las reglas activadas con sus correspondientes conjuntos difusos discretos actualizados 
        return $this->agregacion($reglas_activadas);
    }
    
    
    /* funcion que evalua si las reglas de la base de conocimientos estan activadas en funcion de 
       las variables linguisticas con sus valores de pertenencia calculados y cargados en los terminos linguisticos
       La salida es una lista de reglas activadas con su valor de activacion. 
    */
    private function activadas($variables){
        $reglas_activadas = Array();
        $i=0;
        if (!$this->base_conocimiento->vacia()){
            $this->base_conocimiento->first_regla();
            do{
                $regla_inferencia = $this->base_conocimiento->get_regla();
                $valor_activacion = $regla_inferencia->evaluar_antecedente($variables, $this->tnorma, $this->tconorma);
                if ($valor_activacion <> 0){
                    $consecuente = $regla_inferencia->get_consecuente();
                    //$consecuente[0]->discretizar_universo();
                    $consecuente[0]->discretizar_etiquetas();
                    $reglas_activadas[$i] = array($regla_inferencia, $valor_activacion);
                    $i++;
                };
                $this->base_conocimiento->next_regla();
            }while (!$this->base_conocimiento->fin());
        }
        return $reglas_activadas;
    }
    
    
    
    


    /*
    Funcion que se encarga de realizar la agregacion de las variables linguisticas en funcion de los consecuentes de 
    las reglas activadas.
    Recibe como entrada las reglas de inferencia activadas junto con su valor de activacion del antecedente.
    La salida es una lista de variables linguisticas cuyos terminos linguisticos tienen asignado el mayor valor de pertenencia 
    determinado por los antecedentes de las reglas de inferencia
    
    */
    public function agregacion($activadas){
        $var_agregadas = array();
        
        foreach ($activadas as $activada){   //para cada regla con su correaspondiente valor del antecedente
            $var = $activada[0]->get_var_consecuente(); //obtenemos la var linguistica del consecuente
            if (isset($var_agregadas[$var->get_nombre()])){ //si ya esta en el conj de variables agregadas
                $conj_implicacion = $activada[0]->get_conj_difuso_implicacion(); //conjunto difuso discreto de la implicacion
                $primero = $conj_implicacion[0];
                $conj_variable = $var_agregadas[$var->get_nombre()]; //conjunto difuso discreto del universo completo de la variable difusa
                $i=0;
                while (abs($conj_variable[$i][0] - $primero[0])>0.00001){ //avanzamos en el universo discreto hasta que encontramos un valor de discretizacion igual al primero del conjunto de implicacion
                    $i++;
                };
                foreach ($conj_implicacion as $discreto){ // a partir del mismo valor discreto se comienzan a agregar los reslutados. Se aplica la TConorma entre el valor del conj discreto de la variable y el valor del conj discreto de la implicacion. 
                    $conj_variable[$i][1] = $this->agregacion->operar($conj_variable[$i][1], $discreto[1]);  //
                    $i++;
                }
                $var_agregadas[$var->get_nombre()]=$conj_variable;
            }
            else{ //si no est� entonces creamos el conjunto de valores discretos de la variable linguistica que seran de utilidad para realizar la agregacion
                $var_agregadas[$var->get_nombre()] = $var->discretizar_universo(); //retorna la discretizacion del universo de discurso de la variable linguistica sobre el cual se haran las agregaciones de los resultados de las reglas
                
                //recorrer el conj discreto de cada var agregada y realizar la agregacion del conj difuso discreto de la implicacion utilizando el operador de agregacion del motor de inferencia
                
                $conj_implicacion = $activada[0]->get_conj_difuso_implicacion(); //conjunto difuso discreto de la implicacion
                $primero = $conj_implicacion[0];
                $conj_variable = $var_agregadas[$var->get_nombre()]; //conjunto difuso discreto del universo completo de la variable difusa
                $i=0;
                while (abs($conj_variable[$i][0] - $primero[0])>0.00001){ //avanzamos en el universo discreto hasta que encontramos un valor de discretizacion igual al primero del conjunto de implicacion
                    $i++;             //comparamos con valor absoluto y un "error" ya que se utilizan numeros flotantes
                }
                foreach ($conj_implicacion as $discreto){ // a partir del mismo valor discreto se comienzan a agregar los reslutados. No se aplica la TConorma porque la aplicacion de una TConorma entre un valor y  un elemento neutro es igual al valor. 
                    $conj_variable[$i][1] = $discreto[1];  //
                    $i++;
                }
                $var_agregadas[$var->get_nombre()]=$conj_variable;
            }
        }
        return $var_agregadas;
    }

    public function resultado(){
        
    }

}

?>