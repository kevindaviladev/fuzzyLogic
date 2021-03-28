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
$Alimentacion1 = new PertenenciaTrapezoidal(0,2,0,0);
$Alimentacion2 = new PertenenciaTriangular(1,4,3);
$Alimentacion3 = new PertenenciaTrapezoidal(2.95,7,4,7);

$AlimentacionBajo = new EtiquetaLinguistica('bajo',0,2,$Alimentacion1);
$AlimentacionMediano = new EtiquetaLinguistica('mediano',1,4,$Alimentacion2);
$AlimentacionAlto = new EtiquetaLinguistica('alto',3,7,$Alimentacion3);

$Alimentacion = new VariableLinguistica('alimentacion',0,7,array($AlimentacionBajo,$AlimentacionMediano,$AlimentacionAlto),0.01);

$Glucosa1 = new PertenenciaTrapezoidal(0,90,0,0);
$Glucosa2 = new PertenenciaTriangular(85,126,105);
$Glucosa3 = new PertenenciaTrapezoidal(110,200,160,200);

$GlucosaNormal = new EtiquetaLinguistica('normal',0,90,$Glucosa1);
$GlucosaPreocupante = new EtiquetaLinguistica('preocupante',85,126,$Glucosa2);
$GlucosaMuyPreocupante = new EtiquetaLinguistica('muyPreocupante',110,200,$Glucosa3);

$Glucosa = new VariableLinguistica('glucosa',0,200,array($GlucosaNormal,$GlucosaPreocupante,$GlucosaMuyPreocupante),0.01);

$Genetica1 = new PertenenciaTrapezoidal(0,1,0,0);
$Genetica2 = new PertenenciaTriangular(0.95,3,2);
$Genetica3 = new PertenenciaTrapezoidal(1.95,5,3,5);

$GeneticaNinguno = new EtiquetaLinguistica('ninguno',0,1,$Genetica1);
$GeneticaLeve = new EtiquetaLinguistica('leve',1,3,$Genetica2);
$GeneticaGrave = new EtiquetaLinguistica('grave',2,5,$Genetica3);

$Genetica = new VariableLinguistica('genetica',0,5,array($GeneticaNinguno,$GeneticaLeve,$GeneticaGrave),0.01);

$ActividadFisica1 = new PertenenciaTrapezoidal(0,2,0,0);
$ActividadFisica2 = new PertenenciaTriangular(1,4,3);
$ActividadFisica3 = new PertenenciaTrapezoidal(2.95,7,4,7);

$ActividadFisicaBajo = new EtiquetaLinguistica('bajo',0,2,$ActividadFisica1);
$ActividadFisicaNormal = new EtiquetaLinguistica('normal',1,4,$ActividadFisica2);
$ActividadFisicaAlto = new EtiquetaLinguistica('alto',3,7,$ActividadFisica3);

$ActividadFisica = new VariableLinguistica('actividadFisica',0,7,array($ActividadFisicaBajo,$ActividadFisicaNormal,$ActividadFisicaAlto),0.01);

$Riesgo1 = new PertenenciaTrapezoidal(0,0.09,0.01,0.09);
$Riesgo2 = new PertenenciaTriangular(0.05,0.3,0.15);
$Riesgo3 = new PertenenciaTriangular(0.2,0.6,0.5);
$Riesgo4 = new PertenenciaTrapezoidal(0.5,1,0.65,1);

$RiesgoBajo = new EtiquetaLinguistica('bajo',0,0.09,$Riesgo1);
$RiesgoNormal = new EtiquetaLinguistica('normal',0.05,0.3,$Riesgo2);
$RiesgoAlto = new EtiquetaLinguistica('alto',0.2,0.6,$Riesgo3);
$RiesgoCritico = new EtiquetaLinguistica('critico',0.5,1,$Riesgo4);

$Riesgo = new VariableLinguistica('riesgo',0,1,array($RiesgoBajo,$RiesgoNormal,$RiesgoAlto,$RiesgoCritico),0.01);

//Base de Conocimientos
$BaseConocimiento = new BaseConocimiento();
try {                                                                       
    $regla1 = new ReglaInferencia(1,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal')); 
    $BaseConocimiento->add_regla($regla1);
    
    $regla2 = new ReglaInferencia(2,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla2); 
    
    $regla3 = new ReglaInferencia(3,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'bajo')); 
    $BaseConocimiento->add_regla($regla3);
    
    $regla4 = new ReglaInferencia(4,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal')); 
    $BaseConocimiento->add_regla($regla4);
    
    $regla5 = new ReglaInferencia(5,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla5);
    
    $regla6 = new ReglaInferencia(6,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'normal')); 
    $BaseConocimiento->add_regla($regla6);
    
    $regla7 = new ReglaInferencia(7,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto')); 
    $BaseConocimiento->add_regla($regla7);
    
    $regla8 = new ReglaInferencia(8,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla8); 
    
    $regla9 = new ReglaInferencia(9,array(array($Alimentacion, 'bajo'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla9);
    
    $regla10 = new ReglaInferencia(10,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal')); 
    $BaseConocimiento->add_regla($regla10);
    
    $regla11 = new ReglaInferencia(11,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla11);
    
    $regla12 = new ReglaInferencia(12,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'normal')); 
    $BaseConocimiento->add_regla($regla12);
    
    $regla13 = new ReglaInferencia(13,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla13);
    
    $regla14 = new ReglaInferencia(14,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'alto')); 
    $BaseConocimiento->add_regla($regla14);
    
    $regla15 = new ReglaInferencia(15,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla15);
    
    $regla16 = new ReglaInferencia(16,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla16); 
    
    $regla17 = new ReglaInferencia(17,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla17);
    
    $regla18 = new ReglaInferencia(18,array(array($Alimentacion, 'bajo'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla18);
    
    $regla19 = new ReglaInferencia(19,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla19);
    
    $regla20 = new ReglaInferencia(20,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla20);
    
    $regla21 = new ReglaInferencia(21,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla21);
    
    $regla22 = new ReglaInferencia(22,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla22);
    
    $regla23 = new ReglaInferencia(23,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla23);
    
    $regla24 = new ReglaInferencia(24,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla24);
    
    $regla25 = new ReglaInferencia(25,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla25);
    
    $regla26 = new ReglaInferencia(26,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla26);
    
    $regla27 = new ReglaInferencia(27,array(array($Alimentacion, 'bajo'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla27);
    
    $regla28 = new ReglaInferencia(28,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla28);
    
    $regla29 = new ReglaInferencia(29,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla29);
    
    $regla30 = new ReglaInferencia(30,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla30);
    
    $regla31 = new ReglaInferencia(31,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla31);
    
    $regla32 = new ReglaInferencia(32,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla32);
    
    $regla33 = new ReglaInferencia(33,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla33);
    
    $regla34 = new ReglaInferencia(34,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla34);
    
    $regla35 = new ReglaInferencia(35,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla35);
    
    $regla36 = new ReglaInferencia(36,array(array($Alimentacion, 'mediano'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla36);
    
    $regla37 = new ReglaInferencia(37,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla37);
    
    $regla38 = new ReglaInferencia(38,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla38);
    
    $regla39 = new ReglaInferencia(39,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla39);
    
    $regla40 = new ReglaInferencia(40,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla40);
    
    $regla41 = new ReglaInferencia(41,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla41);
    
    $regla42 = new ReglaInferencia(42,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla42);
    
    $regla43 = new ReglaInferencia(43,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico')); 
    $BaseConocimiento->add_regla($regla43);
    
    $regla44 = new ReglaInferencia(44,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla44);
    
    $regla45 = new ReglaInferencia(45,array(array($Alimentacion, 'mediano'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla45);
    
    $regla46 = new ReglaInferencia(46,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla46);
    
    $regla47 = new ReglaInferencia(47,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla47);
    
    $regla48 = new ReglaInferencia(48,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla48);
    
    $regla49 = new ReglaInferencia(49,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla49);
    
    $regla50 = new ReglaInferencia(50,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'alto')); 
    $BaseConocimiento->add_regla($regla50);
    
    $regla51 = new ReglaInferencia(51,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla51);
    
    $regla52 = new ReglaInferencia(52,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla52);
    
    $regla53 = new ReglaInferencia(53,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla53);
    
    $regla54 = new ReglaInferencia(54,array(array($Alimentacion, 'mediano'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla54);
    
    $regla55 = new ReglaInferencia(55,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla55);
    
    $regla56 = new ReglaInferencia(56,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla56);
    
    $regla57 = new ReglaInferencia(57,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla57);
    
    $regla58 = new ReglaInferencia(58,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla58);
    
    $regla59 = new ReglaInferencia(59,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla59);
    
    $regla60 = new ReglaInferencia(60,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'bajo'));
    $BaseConocimiento->add_regla($regla60);
    
    $regla61 = new ReglaInferencia(61,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla61);
    
    $regla62 = new ReglaInferencia(62,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla62);
    
    $regla63 = new ReglaInferencia(63,array(array($Alimentacion, 'alto'),array($Glucosa,'normal'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla63);
    
    $regla64 = new ReglaInferencia(64,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla64);
    
    $regla65 = new ReglaInferencia(65,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla65);
    
    $regla66 = new ReglaInferencia(66,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla66);
    
    $regla67 = new ReglaInferencia(67,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla67);
    
    $regla68 = new ReglaInferencia(68,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla68);
    
    $regla69 = new ReglaInferencia(69,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla69);
    
    $regla70 = new ReglaInferencia(70,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla70);
    
    $regla71 = new ReglaInferencia(71,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla71);
    
    $regla72 = new ReglaInferencia(72,array(array($Alimentacion, 'alto'),array($Glucosa,'preocupante'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla72);
    
    $regla73 = new ReglaInferencia(73,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla73);
    
    $regla74 = new ReglaInferencia(74,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla74);
    
    $regla75 = new ReglaInferencia(75,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'ninguno'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla75);
    
    $regla76 = new ReglaInferencia(76,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'bajo') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla76);
    
    $regla77 = new ReglaInferencia(77,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'normal') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla77);
    
    $regla78 = new ReglaInferencia(78,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'leve'),array($ActividadFisica,'alto') ),array($Riesgo,'normal'));
    $BaseConocimiento->add_regla($regla78);
    
    $regla79 = new ReglaInferencia(79,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'bajo') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla79);
    
    $regla80 = new ReglaInferencia(80,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'normal') ),array($Riesgo,'critico'));
    $BaseConocimiento->add_regla($regla80);
    
    $regla81 = new ReglaInferencia(81,array(array($Alimentacion, 'alto'),array($Glucosa,'muyPreocupante'),array($Genetica,'grave'),array($ActividadFisica,'alto') ),array($Riesgo,'alto'));
    $BaseConocimiento->add_regla($regla81);
}
catch(ReglaInferenciaException $e){
    echo 'error: '.$e->getMessage();    
};

$conjuncion = new minimo('Min');
$disyuncion = new maximo('Max');
$implicacion = new Mamdani('Mamdani');
$agregacion = new maximo('agregacion Max');
// print_r($Alimentacion);
$motor = new MotorInferenciaMamdani($BaseConocimiento, $conjuncion, $disyuncion, $implicacion, $agregacion);


$Alimentacion->fuzificar(1);
$Glucosa->fuzificar(90);
$Genetica->fuzificar(1);
$ActividadFisica->fuzificar(1.799);

$resultados = $motor->inferir(array($Alimentacion, $Glucosa,$Genetica,$ActividadFisica));
// print_r($motor->inferir(array($Alimentacion, $Glucosa,$Genetica,$ActividadFisica)));

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

// CORRECTO
// echo 'defuzificador Centroide: ';
// print_r($cog->defuzificar($resultados));
// $riesgoArray = $cog->defuzificar($resultados);
// $riesgo = $riesgoArray[riesgo];
// echo 'RIESGO: '.$riesgo[0];

echo 'defuzificador Centroide: ';
print_r($cog->defuzificar($resultados));
$riesgoArray = $cog->defuzificar($resultados)[riesgo];
$riesgo = $riesgoArray[0];
echo 'RIESGO: '.$riesgo;



echo '</br>'.'</br>';



//    echo 'defuzificador Min Of Max: '.$min->defuzificar($resultados).'</br>'.'</br>';
//    echo 'defuzificador Max Of Max: '.$max->defuzificar($resultado).'</br>'.'</br>';
//    echo 'defuzificador Med Of Max: '.$med->defuzificar($resultado).'</br>'.'</br>';
//    echo 'defuzificador COS: '.$cos->defuzificar($resultado).'</br>'.'</br>';


?>