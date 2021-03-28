<?php
  require_once 'etiquetaLinguistica.php';
/**
 * @author Gaston
 * @copyright 2014
 */

/*
Clase que representa las variables difusas del sistema difuso.
Tanto las variables de entrada como la de salida seran representadas por objetos de esta clase.
Se define el nombre de la variable, el universo de la misma y las etiquetas linguisticas que la componen.
Cada etiqueta linguistica posee los limites sobre los que actua y se puede definir una funcion de pertenencia
distinta para cada etiqueta linguistica
*/
class VariableLinguistica{
    private $nombre;
    private $etiquetas_linguisticas = Array();
    private $limite_inferior;
    private $limite_superior;
    private $maximos = array(); // arreglo que contiene los valores de x donde las etiquetas linguisticas de la variable alcanzan los maximos 
    private $paso; //valor que indica el valor de paso para realizar la discretizacion de los conjuntos difusos de las etiquetas linguisticas
    /*
	constructor de clase que inicializa el nombre de la variable, los limites del Universo de la misma y recibe como parametro
        un arreglo de etiquetas linguisticas. 
    */
    public function __construct($nom, $inf, $sup, $etiquetas,$paso){
        $this->nombre = $nom;
        $this->limite_inferior = $inf;
        $this->limite_superior = $sup;
        if ($paso == 0){
            $this->paso = ($this->limite_superior -  $this->limite_inferior)/100; //si no se establece el paso en los parametros entonces se divide el intervalo en 100 partes   
        }else{
            $this->paso = $paso;
        }
        /*
           se recorre el arreglo de etiquetas lingusticas y se controla que sean instancias de la clase etiquetaLinguistica, si es asi 
	   se guarda en el arreglo de etiquetas_linguisticas con subindice correspondiente al nombre de la etiqueta linguistica. Esto sera util 
	   a la hora de evaluar las reglas de inferencia.
        */
        foreach ($etiquetas as $etiqueta){
            if ($etiqueta instanceOf etiquetaLinguistica){
		       $etiqueta->set_paso($this->paso);
               $this->etiquetas_linguisticas[$etiqueta->get_nombre()] = $etiqueta; //asigna las etiquetas de la variable linguistica 
	           $this->maximos = array_merge($this->maximos, $etiqueta->get_maximos()); //obtiene los maximos de cada etiqueta y los guarda en el arreglo de maximos
               
            }
        }
//        $this->discretizar_etiquetas(); //discretizar el conjunto difuso de cada etiqueta de la variable de salida del consecuente que sera de utilidad al implicar y agregar el resultado del proceso de inferencia 
    }

    /*
    funcion que se encarga de discretizar cada uno de los conjuntos difusos de las etiquetas linguisticas. Al llamar a cada uno de los discretizadores de las 
    etiquetas, se les pasa como parametro los maximos de las mismas para incluir los valores de x de los maximos en la discretizacion de las etiquetas.
    Esto sirve para que a la hora de hacer la agregacion se incluyan los valores de x donde las etiquetas linguisticas obtienen su punto maximo de verdad.
    */    
    public function discretizar_etiquetas(){
        foreach ($this->etiquetas_linguisticas as $etiqueta){
            $etiqueta->discretizar($this->paso, $this->maximos);
        }
    }
    
    /*
    funcion que se encarga de discretizar el universo de la variable linguistica. Incluye dentro del mismo los maximos correspondientes a las etiquetas linguisticas
    */    
    public function discretizar_universo(){
         $conj_discreto = array();

         $a=$this->limite_inferior / $this->paso;
         $b = intval ($this->limite_inferior/$this->paso);
         if ($a * $this->paso == $b * $this->paso){
            $inicio = $this->limite_inferior;
         }else{
            if ($b>=0){
                $inicio = ($b + 1) * $this->paso;
            }else{
                $inicio = $b * $this->paso;
            }
         }

         $i=0;
         $x = $inicio;
         while ($x <= $this->limite_superior){
            $conj_discreto[$i] = array($x,0);
            $i++;
            foreach ($this->maximos as $maximo){ //si se encuentra que alguno de los valores de x de los maximos se encuentra entre los valores de x de la discretizacion
                                           //entonces se agregan los valores de x de los maximos con el valor 0. Este conjunto discreto de la variable sirve para luego
                                           //Esto sera de utilidad a la hora de realizar la agregacion de los consecuentes de las reglas activadas. 
                if ($x<$maximo and $x+$this->paso >$maximo){  
                   $conj_discreto[$i]= array($maximo,0);
                   $i++;
                }
            }
            $x = $x + $this->paso;
         }
         return $conj_discreto;

    }

    public function set_nombre($nom){
	$this->nombre = $nom;
    }
    
    public function get_nombre(){
        return $this->nombre;
    }
        
    public function set_limite_inferior($lim_inf){
	$this->limite_inferior = $lim_inf;
    }

    public function set_limite_superior($lim_sup){
	$this->limite_superior = $lim_sup;
    }

    public function set_etiquetas($etiquetas){
        foreach ($etiquetas as $etiqueta){
            if ($etiqueta instanceOf etiquetaLinguistica){
		$this->etiquetas_linguisticas[$etiqueta->get_nombre()] = $etiqueta; 
	    }
        }
    } 

    public function add_etiqueta($etiqueta){
        if (!isset($this->etiquetas_linguisticas[$etiqueta->get_nombre()])){
	    $this->etiquetas_linguisticas[$etiqueta->get_nombre()]=$etiqueta;
	}
    }


    /*
        funcion que devuelve la etiqueta linguistica segun el nombre dado en la variable de entrada
        entrada: cadena que representa el nombre de la etiqueta linguistica
        salida: etiqueta linguistica
    */
    public function get_etiqueta($etiqueta){
        if (isset($this->etiquetas_linguisticas[$etiqueta])){
	    return $this->etiquetas_linguisticas[$etiqueta];
	}else throw new etiquetaException('No existe la etiqueta linguistica');
    }


// funcion que devuelve los nombres de las etiquetas linguisticas para poder acceder a las mismas mediante get_etiqueta
    public function get_name_etiquetas(){
        return (array_keys($this->etiquetas_linguisticas));
    }	
    /*
        funcion encargada de verificar si una cadena se corresponde con alguno de los terminos linguisticos que forman la 
        variable linguistica
        entrada: string
        salida: verdadero si la cadena se corresponde con algun termino linguistico, sino falso
    */
    public function pertenece_termino($valor){
	   if (isset($this->etiquetas_linguisticas[$valor])){
	       return true;
	   }else{
	       return false;
	   }
	}
    
    
    /*
       funcion encargada de cargar el valor de pertenencia en los terminos linguisticos de la variable linguistica fuzzificada 
       entrada: valor crisp
    */
    public function fuzificar($x){
        $i=0;
        if (($x < $this->limite_inferior) or ($x > $this->limite_superior)){
	       throw new VarLinguisticaException('Valor CRISP fuera de los limites del Universo de la variable');    
        }else{
            foreach ($this->etiquetas_linguisticas as $etiqueta_linguistica){
         	    $etiqueta_linguistica->calcular_pertenencia($x);
            }
        }
    }

}

class VarLinguisticaException extends Exception { }
?>