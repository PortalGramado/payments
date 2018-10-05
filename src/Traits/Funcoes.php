<?php namespace PortalGramado\Payments\Traits;

/**
 * Trait Funcoes
 * @package PortalGramado\Payments\Traits
 */
trait Funcoes
{
    protected function tirarAcentos($string){
        $string = preg_replace('/[áàãâä]/ui', 'a', $string);
        $string = preg_replace('/[éèêë]/ui', 'e', $string);
        $string = preg_replace('/[íìîï]/ui', 'i', $string);
        $string = preg_replace('/[óòõôö]/ui', 'o', $string);
        $string = preg_replace('/[úùûü]/ui', 'u', $string);
        $string = preg_replace('/[ç]/ui', 'c', $string);
        return $string;
    }
}