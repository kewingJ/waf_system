<?php

class metodosWaf
{
    //
    function obtenerDominioPrincipal($subdominio, $tipo_sitio) {
        if($tipo_sitio != 'dominio'){
            $domain_parts = explode(".", $subdominio);
            $num_parts = count($domain_parts);
        
            if ($num_parts <= 2) {
                // Caso de subdominio simple
                $domain = implode(".", $domain_parts);
            } else {
                // Caso de subdominio compuesto
                $domain = implode(".", array_slice($domain_parts, -($num_parts - 1)));
            }
        } else {
            $domain = $subdominio;
        }
    
        return $domain;
    }
}