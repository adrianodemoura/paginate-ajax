<?php
namespace PaginateAjax\Lib;
/**
 * Class ListErros
 * 
 * @
 */
class ListErrors {
    /**
     * 
     */
    private $parametros = [];

    /**
     * 
     */
    private $table  = null;

    /**
     * Método start
     * 
     * @param   Array   $parametros     Parâmetros do PaginateAjax.
     * @param   Table   $table          Instância Table do PaginateAjax.
     */
    public function __construct( $parametros=[], $table=null)
    {
        $this->parametros   = $parametros;
        $this->table        = $table;
    }

    /**
     * Retorna os erros encontrado nos campos da PaginateAjax.
     * 
     * @return  Array       $erros  
     */
    public function getCamposErros()
    {
        $erros = [];

        if ( !empty( $this->parametros['campos'] ) )
    	{
            $arr = explode( ',', $this->parametros['campos'] );
    		foreach( $arr as $_l => $_linhaFiltro)
    		{
                $arrLinha   = explode( '=', $_linhaFiltro );
                $arrField	= explode('.', $arrLinha[0]);
                $field 		= isset( $arrField[1]) ? $arrField[1] : $arrField[0];

                $tabela 	= isset( $arrField[1]) ? $arrField[0] : $this->parametros['tabela'];
                $field 		= isset( $arrField[1]) ? $arrField[1] : $arrField[0];

                $alias 		= $this->table->alias();
                if ( $alias !== $tabela )
                {
                    $erros[] = __("Erro ao tentar montar ordem: Tabela '$tabela' inválida !");
                    break;
                }

                $myFields 	= $this->table->getSchema()->columns();
                if ( !in_array($field, $myFields) )
                {
                    $erros[] = __("Erro ao tentar montar campos: Campo '$field' inválido !");
                }
    		}
        }

        return $erros;
    }

    /**
     * Retorna os erros encontrado nos filtros da PaginateAjax.
     * 
     * @return  Array       $erros  
     */
    public function getFiltrosErros()
    {
        $erros = [];

        if ( !empty( $this->parametros['filtros'] ) )
    	{
            $arr = explode( ',', $this->parametros['filtros'] );
    		foreach( $arr as $_l => $_linhaFiltro)
    		{
                $arrLinha   = explode( '=', $_linhaFiltro );
                $arrField	= explode('.', $arrLinha[0]);
                $field 		= isset( $arrField[1]) ? $arrField[1] : $arrField[0];

                $tabela 	= isset( $arrField[1]) ? $arrField[0] : $this->parametros['tabela'];
                $field 		= isset( $arrField[1]) ? $arrField[1] : $arrField[0];

                $alias 		= $this->table->alias();
                if ( $alias !== $tabela )
                {
                    $erros[] = __("Erro ao tentar montar ordem: Tabela '$tabela' inválida !");
                    break;
                }

                $myFields 	= $this->table->getSchema()->columns();

                if ( !in_array($field, $myFields) )
                {
                    $erros[] = __("Erro ao tentar montar filtros: Campo $field inválido !");
                }
    		}
        }

        return $erros;
    }

    /**
     * Retorna os erros encontrado na ordem da PaginateAjax.
     * 
     * @return  Array       $erros  
     */
    public function getOrdemErros()
    {
        $erros = [];

        if ( !empty( $this->parametros['ordem'] ) )
    	{
            $arr = explode( ',', $this->parametros['ordem'] );
    		foreach( $arr as $_l => $_linhaFiltro)
    		{
                $arrLinha   = explode( '=', $_linhaFiltro );
                $arrField	= explode('.', $arrLinha[0]);
                $tabela 	= isset( $arrField[1]) ? $arrField[0] : $this->parametros['tabela'];
                $field 		= isset( $arrField[1]) ? $arrField[1] : $arrField[0];

                $alias 		= $this->table->alias();
                if ( $alias !== $tabela )
                {
                    $erros[] = __("Erro ao tentar montar ordem: Tabela '$tabela' inválida !");
                    break;
                }

                $myFields 	= $this->table->getSchema()->columns();
                if ( !in_array($field, $myFields) )
                {
                    $erros[] = __("Erro ao tentar montar ordem: Campo '$field' inválido !");
                }
    		}
        }

        return $erros;
    }
}