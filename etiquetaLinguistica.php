<?php
  require_once 'pertenencia.php';
/**
 * @author Gaston
 * @copyright 2014
 */

/* 
clase encargada de representar las etiquetas lingusticas que caracterizan las variables de entrada y 
salida del sistema difuso. Cada etiqueta linguistica posee, ademas de su nombre y limites izquierdo y derecho,
una funcion de pertenencia que es la encargada de determinar el grado de pertenencia de un valor crisp
*/

class EtiquetaLinguistica{
   private $nombre;
   private $lim_izq;
   private $lim_der;
   public $funcion_pertenencia;
   private $pertenencia;
   private $conj_discreto; // arreglo que contiene pares valor crisp+valor de pertenencia resultado de disctretizar el conj de pertenencia asociado al termino linguistico
   private $maximos; //arreglo que contiene los maximos de la funcion de pertenencia del termino linguistico
   private $paso;
   /*
   constructor de la clase que inicializa el nombre, los limites izquierdo y derecho y la funcion de pertenencia.
   Tambien se debe pasar el valor de paso necesario para la discretizacion del conjunto difuso que representa al termino linguistico
   */
   public function __construct($nom, $izq, $der, $pert){
        $this->nombre = $nom;
        $this->lim_izq = $izq;
        $this->lim_der = $der;
        $this->set_funcion_pertenencia($pert);
        $this->maximos = $this->funcion_pertenencia->maximos(); //inicializa el arreglo maximos con los valores maximos de la funcion de pertenencia del termino
   }
   
   /*
   funcion encargada de inicializar el nombre de la variable linguistica
   */
   public function set_nombre($nom){
        $this->nombre = $nom;   
   }

   public function get_nombre(){
        return $this->nombre;   
   }

   /*
   funcion que obtiene el conjunto difuso discreto invocando a la funcion de discretizacion de la funcion de pertenencia de la etiqueta 
   */
   public function discretizar($paso, $maximos){
       $this->conj_discreto = $this->funcion_pertenencia->discretizar($paso, $maximos);
   }

   
   /*
   funcion encargada de inicializar el limite izquierdo de la variable linguistica
   */
   public function set_lim_izq($izq){
        $this->lim_izq = $izq;
   }

   /*
   funcion encargada de inicializar el limite derecho de la variable linguistica
   */ 
   public function set_lim_der($der){
        $this->lim_der = $der;
   }
   
   public function get_maximos(){
        return $this->maximos;
   }
   

   /*
   funcion encargada de inicializar el paso de cada valor discreto del universo del termino
   */   
   public function set_paso($paso){
        $this->paso = $paso;
   }


   /*
   funcion encargada de inicializar la funcion de pertenencia de la variable linguistica
   */ 
   public function set_funcion_pertenencia($pert){
        if ($pert instanceof Pertenencia){ 
            $this->funcion_pertenencia = $pert;
        } else {  // si el parametro de entrada no es un objeto Pertenencia entonces se asigna por defecto una pertenencia triangular
            $mod = ($der-$izq)/2;
            $this->funcion_pertenencia = new PertenenciaTriangular($izq, $der, $mod);    
        }
   }
    
   public function set_pertenencia($val){
       $this->pertenencia=$val;
   }    

   /*
       funcion que verifica si una etiqueta esta activa chequeando el valor de pertenencia
       salida: tru si esta activa, falso si no esta activa
   */
   public function esta_activa(){
       return $this->pertenencia <> null;
   }

   public function get_pertenencia(){
       return $this->pertenencia;
   }    

   /*
   funcion encargada de llamar a la funcion de calculo de intersecciones de la funcion de pertenencia
   */ 
    public function intersecciones_x($y){
        return $this->funcion_pertenencia->intersecciones_x($y);
    }
    

   /*
   funcion encargada de llamar a la funcion de calculo de COG de la funcion de pertenencia
   */ 
    public function COG($y){
        return $this->funcion_pertenencia->COG($y);
    }
    
    
   /*
   funcion encargada de calcular el grado de pertenencia de un determinado valor en la variable linguistica
   */ 
    public function calcular_pertenencia($x){

        $this->pertenencia = $this->funcion_pertenencia->calcular_pertenencia($x);
        return $this->pertenencia;
    }

    public function get_conj_discreto(){
        return $this->conj_discreto;
    }
   
}

class etiquetaException extends Exception{}

?>