<?php

/**
 * @author Gaston
 * @copyright 2014
 */

include_once 'reglaInferencia.php';
include_once 'variableLinguistica.php';
include_once 'etiquetaLinguistica.php';
include_once 'pertenencia.php';
include_once 'baseConocimiento.php';
include_once 'motordeinferencia.php';
include_once 'defuzificador.php';
include_once 'OperadorDifuso.php';
include_once 'implicacion.php';

//Definicion Temperatura

//Funciones de Pertenencia de las etiquetas linguisticas
$TempPert1 = new PertenenciaTriangular(0,15,10);
$TempPert2 = new PertenenciaTriangular(10,20,15);
$TempPert3 = new PertenenciaTriangular(18,22,20);
$TempPert4 = new PertenenciaTriangular(20,30,25);
$TempPert5 = new PertenenciaTriangular(25,35,30);

//5 etiquetas Linguisticas
$MBTemp = new EtiquetaLinguistica('muy baja',0,15,$TempPert1);
$BTemp = new EtiquetaLinguistica('baja',10,20,$TempPert2);
$NTemp = new EtiquetaLinguistica('normal',18,22,$TempPert3);
$ATemp = new EtiquetaLinguistica('alta',20,30,$TempPert4);
$MATemp = new EtiquetaLinguistica('muy alta',25,35,$TempPert5);

$Temperatura=new VariableLinguistica('temperatura',0,40,array($MBTemp,$BTemp,$NTemp,$ATemp,$MATemp),1);
// Fin definicion Variable Temperatura


//Definicion Humedad

//Funciones de Pertenencia de las etiquetas linguisticas
$HumPert1 = new PertenenciaTriangular(0,20,10);
$HumPert2 = new PertenenciaTriangular(10,40,25);
$HumPert3 = new PertenenciaTriangular(30,50,40);
$HumPert4 = new PertenenciaTriangular(40,70,55);
$HumPert5 = new PertenenciaTriangular(60,100,70);

//5 etiquetas Linguisticas
$MBHum = new EtiquetaLinguistica('muy baja',0,15,$HumPert1);
$BHum = new EtiquetaLinguistica('baja',10,20,$HumPert2);
$NHum = new EtiquetaLinguistica('normal',18,22,$HumPert3);
$AHum = new EtiquetaLinguistica('alta',20,30,$HumPert4);
$MAHum = new EtiquetaLinguistica('muy alta',25,35,$HumPert5);

$Humedad=new VariableLinguistica('humedad',0,100,array($MBHum,$BHum,$NHum,$AHum,$MAHum),1);
// Fin definicion Variable Humedad



//Definicion Variacion Temperatura

//Funciones de Pertenencia de las etiquetas linguisticas
$VarPert1 = new PertenenciaTriangular(-15,-7.5,-10);
$VarPert2 = new PertenenciaTriangular(-10,-2.5,-5);
$VarPert3 = new PertenenciaTriangular(-7.5,0,-2);
$VarPert4 = new PertenenciaTriangular(-1,1,0);
$VarPert5 = new PertenenciaTriangular(0,7.5,2.5);
$VarPert6 = new PertenenciaTriangular(2.5,10,5.5);
$VarPert7 = new PertenenciaTriangular(7.5,15,10);


//7 etiquetas Linguisticas
$BGVar = new EtiquetaLinguistica('bajada grande',-15,-7.5,$VarPert1);
$BNVar = new EtiquetaLinguistica('bajada normal',-10,-2.5,$VarPert2);
$BPVar = new EtiquetaLinguistica('bajada peque�a',-7.5,0,$VarPert3);
$MVar = new EtiquetaLinguistica('mantener',-1,1,$VarPert4);
$SPVar = new EtiquetaLinguistica('subida peque�a',0,7.5,$VarPert5);
$SNVar = new EtiquetaLinguistica('subida normal',2.5,10,$VarPert6);
$SGVar = new EtiquetaLinguistica('subida grande',7.5,15,$VarPert7);

$Variacion=new VariableLinguistica('variacion temperatura',-15,15,array($BGVar,$BNVar,$BPVar,$MVar,$SPVar,$SNVar,

$SGVar),0.2);
// Fin definicion Variable Variacion Temperatura


$BaseConocimiento = new BaseConocimiento();

try {                                                                       
   //( ( ( not( (alto,normal) and (bajo,mucho) ) or (alto,poco) ) and (bajo,mucho) )
   //si la temperatura es baja y la humedad es alta entonces la variacion es subida peque�a
   $regla1 = new ReglaInferencia(1,array( array($Temperatura, 'baja') ,array($Humedad, 'alta') ), array($Variacion, 'subida peque�a'));
   $BaseConocimiento->add_regla($regla1);
   echo 'des 1';

   //si la temperatura es baja y la humedad es muy alta entonces la variacion es subida normal
   $regla2 = new ReglaInferencia(2,array( array($Temperatura, 'baja') ,array($Humedad, 'muy alta') ), array($Variacion, 'subida normal'));
   $BaseConocimiento->add_regla($regla2);

   $regla3 = new ReglaInferencia(3,array( array($Temperatura, 'normal') ,array($Humedad, 'alta') ), array($Variacion, 'mantener'));
   $BaseConocimiento->add_regla($regla3);

   $regla4 = new ReglaInferencia(4,array( array($Temperatura, 'normal') ,array($Humedad, 'muy alta') ), array($Variacion, 'bajada peque�a'));
   $BaseConocimiento->add_regla($regla4);
}
catch(ReglaInferenciaException $e){
    echo 'error: '.$e->getMessage();    
};

$conjuncion = new minimo('Min');
$disyuncion = new maximo('Max');
$implicacion = new Mamdani('Mamdani');
$agregacion = new maximo('agregacion Max');

$motor = new MotorInferenciaMamdani($BaseConocimiento, $conjuncion, $disyuncion, $implicacion, $agregacion);
$Temperatura->fuzificar(21);
$Humedad->fuzificar(41);

$resultados = $motor->inferir(array($Temperatura, $Humedad));


$min = new DefuzificadorMinOfMax();
$max = new DefuzificadorMaxOfMax();
$med = new DefuzificadorMedOfMax();
$cog = new DefuzificadorCOG();

echo 'defuzificador Min Of Max: ';
print_r($min->defuzificar($resultados));
echo '</br>'.'</br>';

echo 'defuzificador Max Of Max: ';
print_r($max->defuzificar($resultados));
echo '</br>'.'</br>';


echo 'defuzificador Med Of Max: ';
print_r($med->defuzificar($resultados));
echo '</br>'.'</br>';

// $r = Array('variacion temperatura'=>array(array(-30, 0.1), array(-20, 0.1), array(-10, 0.1), array(0, 0.2), array(10, 

// 0.2), array(20, 0.2), array(30, 0.2), array(40, 0.5), array(50, 0.5), array(60, 0.5), array(70, 0.5) ));


echo 'defuzificador Centroide: ';
print_r($cog->defuzificar($resultados));
echo '</br>'.'</br>';

echo 'AQUIII';

   // echo 'defuzificador Min Of Max: '.$min->defuzificar($resultados).'</br>'.'</br>';
   // echo 'defuzificador Max Of Max: '.$max->defuzificar($resultado).'</br>'.'</br>';
   // echo 'defuzificador Med Of Max: '.$med->defuzificar($resultado).'</br>'.'</br>';
   // echo 'defuzificador COS: '.$cos->defuzificar($resultado).'</br>'.'</br>';


?>