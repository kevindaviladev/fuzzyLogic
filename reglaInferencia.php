<?php

/**
 * @author Gaston
 * @copyright 2014
 */
/* Clase que representa las reglas de inferencia. 
   Permite calcular los participantes del antecedente devolviendo los valores de pertenencia de cada termino linguistico
   de las variables linguisticas del antecedente  y del consecuente de la regla.
Las reglas de inferencia estan formadas por un antecedente que es un grupo de variables linguisticas con sus terminos linguisticos y un 
onsecuente formado por una variable linguistica con su repectivo termino linguistico 
Para poder modelar la conjuncion en el antecedente se opto por un array de pares [var fuzzy, valor linguistico]. 
Para poder modelar la disyuncion en el antecedente se opto por arreglos de arreglos.
Ejemplo: 1) if a is 1 and b is 2 and c is 3 then d is 4 =>  [[a,1],[b,2],[c,3]]
         2) if a is 1 and ((b is 2 and e is 5) or c is 3) then d is 4 => [ [a,1] , [ [[b,2],[e,5]],[c,3] ] ]
         3) if a is 1 or b is 2 or c is 3 then d is 4  =>  [ [[a,1],[b,2],[c,3]] ]
         
   
*/
class ReglaInferencia{
    protected $numero_regla;
    protected $antecedente; //arreglo que contiene los pares variables linguisticas con sus terminos linguisticos
    protected $consecuente; // variable linguistica con su termino linguistico
    protected $tnorma; //operador que realizara la conjuncion de los componentes del antecedente
    protected $tconorma;  //operador que realizara la disyuncion de los componentes del antecedente
    protected $conj_difuso_implicacion; // arreglo que contiene el conjunto difuso resultante de aplicar al consecuente una funcion de implicacion en funcion del valor de activacion de la regla    
    
    public function __construct($num, $antecedente, $consecuente){
        $this->numero_regla = $num;
        $this->set_antecedente($antecedente);
        $this->set_consecuente($consecuente);
    }
    
    
    /*
      funcion encargada de chequear la correctitud del antecedente. Debe ser de la forma descripta en los ejemplos, un termino con su 
      valor linguistico o una lista de disyunciones o una lista de conjunciones. Pueden estar anidadas. 
    */
    public function set_antecedente($antecedente){
        if ($this->verificar_antecedente($antecedente, 0)){
            $this->antecedente = $antecedente;
        }else{
            throw new ReglaInferenciaException('Antecedente Invalido');
        }
    }
    
    public function get_antecedente(){
        return $this->antecedente;
    }
    
    /*
      Funcion encargada de inicializar el consecuente de la regla de inferencia  
      El consecuente debe estar formado por una variable linguistica y una cadena que indica el termino linguistico que se activaria en la regla
      
    */
    public function set_consecuente($consecuente){
        if (($consecuente[0] instanceof variableLinguistica) and (is_string($consecuente[1])) and ($consecuente[0]->pertenece_termino($consecuente[1]))){
            // se chequea que el consecuente este formado por una variable linguistica, una cadena y que a su vez esa cadena sea un termino linguistico 
            // valido de la variable linguistica
            $this->consecuente = $consecuente;
        }else{
            throw new ReglaInferenciaException('Consecuente Invalido');
        }
    }
    
    public function get_consecuente(){
        return $this->consecuente;
    }
    
    public function set_conorma($conorma){
        
    }

    public function set_tconorma($tconorma){
        
    }
    

    /* funcion que retorna el nombre del termino linguistico activado correspondiente al consecuente de la regla de inferencia
        Ej: si velocidad es alta ent frenar "mucho". devuelve la cadena "mucho" que representa el termino activado
    */ 
    public function get_val_consecuente(){
        return $this->consecuente[1];
    }

    /* funcion que retorna la variable linguistica correspondiente al consecuente de la regla de inferencia
    */ 
    public function get_var_consecuente(){
        return $this->consecuente[0];
    }
    
    public function set_numero_regla($num){
        $this->numero_regla = $num;
    }

    public function get_numero_regla(){
        return $this->numero_regla;
    }


    /*
    funcion encargada de evaluar el antecedente. Se aplicaran los operadores asignados para la conjuncion y la disyuncion
    
    */
    public function evaluar_antecedente($variables_linguisticas, $tnorma, $tconorma){
        $this->tnorma = $tnorma;
        $this->tconorma = $tconorma;
        return $this->evaluar($this->antecedente, $variables_linguisticas, 'AND');
    }
    
    public function evaluar($antecedentes, $var, $operador){
        
        if (is_array($antecedentes)){
            if ((is_integer($antecedentes[0])) and (is_array($antecedentes[1])) ){
                if ($antecedentes[0] == -1){  // tenemos una negacion por lo tanto el valor de la evaluacion es 1- la evaluacion del resto del antecedente
                    return 1 - $this->evaluar($antecedentes[1], $var, $operador); 
                };
            }elseif (($antecedentes[0] instanceof variableLinguistica) and (is_string($antecedentes[1]))){
                //estamos en el caso de un solo elemento en el antecedente formado por una var linguistica y una string que representa un valor linguistico de la variable
                //buscamos la var linguistica en las variables de entrada. Si la encontramos entonces retornamos el valor correspondoiente al termino linguistico
                foreach ($var as $v){
                    if ($v->get_nombre()==$antecedentes[0]->get_nombre()){
                        return $v->get_etiqueta($antecedentes[1])->get_pertenencia();
                    }
                };
                return 0;
            }else{   
                //recorremos cada elemento del antecedente para ver si son pares var linguistica y valor linguistico
                //o conjuncion de terminos o disyuncion de terminos
                if ($operador == 'AND'){
                    $result=1; //se setea el resultado con el valor del elemento neutro de la conjuncion
                    $operador_sig_nivel = 'OR';
                }else{
                    $result = 0; //se setea el resultado con el valor del elemento neutro de la disyuncion
                    $operador_sig_nivel='AND';
                };
                //$result=1;
                foreach($antecedentes as $antecedente){
                    $r = $this->evaluar($antecedente,$var,$operador_sig_nivel);
                    if ($operador == 'AND'){
                        //aplicar operador difuso T_Norma
                            $result = $this->tnorma->operar($result, $r);
                    }elseif ($operador == 'OR'){
                            //Aplicar operador difuso T_Conorma
                            $result = $this->tconorma->operar($result, $r);
                    }
                        
                };
                return $result;    
            }
        }
    }
    

    private function verificar_antecedente($antecedentes, $nivel){
        if (is_array($antecedentes)){
            if ((is_integer($antecedentes[0])) and (is_array($antecedentes[1])) ){
                if ($antecedentes[0] == -1) if ($this->verificar_antecedente($antecedentes[1],$nivel)){ 
                    return true;
                };
            }elseif (($antecedentes[0] instanceof variableLinguistica) and (is_string($antecedentes[1])) and ($antecedentes[0]->pertenece_termino($antecedentes[1]))){
                //estamos en el caso de un solo elemento en el antecedente formado por una var linguistica y una string que representa un valor linguistico de la variable
                return true;
            }elseif (count($antecedentes)==1 and $nivel>0){
                 return false; //si no  es un par var ling, valor ling entonces es una disyuncion de terminos o una conjuncion de terminos por lo tanto debe tener mas de un elemento
            }else{   
                //recorremos cada elemento del antecedente para ver si son pares var linguistica y valor linguistico
                //o conjuncion de terminos o disyuncion de terminos
                $nivel++;
                foreach($antecedentes as $antecedente){
                    if (!$this->verificar_antecedente($antecedente,$nivel)){
                        return false;
                    };
                };
                return true;    
            }
        }else{
            return false;
        }
    }
    
    /*
    funcion que se encarga de realizar los calculos de implicacion sobre el consecuente de la regla de inferencia
    Recibe como entrada la funcion de implicacion a utilizar y el valor de activacion de la regla de inferencia
    La funcion realiza la actualizacion del conjunto discreto de la regla resultado de la implicacion de la regla
    */
    public function implicar($implicacion, $val_activacion){
        $conj = $this->consecuente[0]->get_etiqueta($this->consecuente[1])->get_conj_discreto(); //recuperamos el conj difuso discreto del termino linguistico de la variable linguistica del consecuente
        $this->conj_difuso_implicacion = array();
        $i = 0;
        foreach ($conj as $valor){ //para cada valor se efectua la implicacion en funcion de la implicacion y el valor de activacion recibidos como parametros de entrada
            $result_implicacion = $implicacion->implicar($val_activacion, $valor[1]);
            $this->conj_difuso_implicacion[$i] = array($valor[0],$result_implicacion); //se actualiza el conj difuso implicado de la regla
            $i++;
        };
    }

    //funcion que devuelve el conjunto difuso resultado de la implicacion
    public function get_conj_difuso_implicacion(){
        return $this->conj_difuso_implicacion;
    }

}



class ReglaInferenciaException extends Exception{ }


?>