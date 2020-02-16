<?php
namespace PaginateAjax\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Exception;

/**
 * Paginate component
 */
class PaginatorComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig 	= [];

    /**
     * Controlador
     *
     * @var 	\Cake\Controller
     */
    private $Controller 		= null;

    /**
     * Limite da paginação
	 *
     * @var 	Integer
     */
   	private $limit 				= 10000;

   	/**
   	 * Parâmetros da lista
   	 *
   	 * @var 	Array
   	 */
   	public $parametros 		   = [];

    /**
     * Método de inicilização do componente.
     * 
     * @param   array   $config     Configurações do componente.
     * @return  \Cake\Http\Response|Null
     */
    public function initialize( array $config=[] )
    {
    	$this->Controller  = $this->_registry->getController();
    	$this->limit       = isset( $config['limit'] ) ? $config['limit'] : 10000;
    }

    /**
     * Configura o valor de um parâmetro da Paginate.
     *
     * @param   String      $parametro      Nome do parâmetro a ser configurado.
     * @param   Mixed       $vlr            Valor do parâmetro.
     * @return  \Cake\Http\Response|Null
     */
    public function setParams($parametro='', $vlr='')
    {
        $this->parametros[$parametro] = $vlr;
    }

    /**
     * Retorna a lista
	 *
     * @param 	Array 	$data 	Dados da lista.
     * Os dados podem ser:
     * 
     * @param   Array   $parametros     Parâmetros da pesquisa:
     * - pagina: pagina da pesquisa
     * - tabela: Nome da tabela
     * - campos: Campos da lista
     * - limite: Limite da lista, não pode ultrapassar o valor do atributo limit.
     * - group: Campos para group by
     * 
     * @return  \Cake\Http\Response|Null
     */
    public function paginate()
    {
		$tabela   = $this->getTabela();

        if ( !$tabela )
        {
           $retorno = ['status'=>false, 'mensagem'=> __('tabela inválida !') ];
        } else
        {
            $retorno = $this->getLista();
        }

		$this->Controller->autoRender = false; 
        $this->Controller->RequestHandler->respondAs('json');
        $this->Controller->response->type('application/json');

        echo json_encode( $retorno );
    }

    /**
     * Retorna a lista
     *
     * @return  Array   $retorno    Retorno da lista, com status, paginação e lista.
     */
    private function getlista()
    {
        $Request  = $this->Controller->request;
        $tabela   = $this->getTabela();

        $this->parametros['tipo']   = 'all';
        $this->parametros['pagina'] = 1;
        $this->parametros['limite'] = 10;

        $arrParamsObrigatorios      = ['pagina'=>'page', 'limite'=>'limit', 'tipo'=>'type'];
        foreach( $arrParamsObrigatorios as $_tag => $_tag2 )
        {
            $this->parametros[ $_tag ] = strlen( @$Request->getQuery( $_tag ) ) ? $Request->getQuery( $_tag ) : $this->parametros[ $_tag ];
            $this->parametros[ $_tag ] = strlen( @$Request->getData( $_tag ) )  ? $Request->getData( $_tag )  : $this->parametros[ $_tag ];
        }
        if ( $this->parametros['limite'] > $this->limit) { $this->parametros['limite'] = $this->limit; }

        $this->Controller->loadModel( $tabela );

        try
        {
            $lista  = $this->Controller->$tabela->find( $this->parametros['tipo'] )
            ->select( $this->getCampos() )
            ->where( $this->getFiltros() )
            ->group( $this->getGrupos() )
            ->order( $this->getOrdem() )
            ->limit( $this->parametros['limite'] )
            ->offset( (($this->parametros['pagina']-1) * $this->parametros['limite']) )
            ->toArray();

            $paginacao              = ['pagina'=>0, 'ultima'=>0, 'faixa'=>0, 'total'=>0];
            $paginacao['pagina']    = $this->parametros['pagina'];
            $paginacao['faixa']     = $this->parametros['limite'];
            $paginacao['total']     = $this->Controller->$tabela->find()->where( $this->getFiltros() )->count('*');
            $paginacao['ultima']    = round( $paginacao['total'] /  $paginacao['faixa'] );

            return ['status'=>true, 'paginacao'=>$paginacao, 'lista'=> $lista];
        } catch (Exception $e)
        {
            $this->Controller->log( $e->getCode().' '.$e->getMessage() );
            $this->Controller->log( $this->getErros(), 'errors');
            return ['status'=>false, 'mensagem'=> __('Erro ao tentar recuperar a lista !') ];
        }
    }

    /**
     * Retorna o nome da Tabela a ser pesquisada.
     *
     * @return  String  $tabela     Nome da Table.
     */
    private function getTabela()
    {
        if ( !isset( $this->parametros['tabela'] ) )
        {
            $this->parametros['tabela'] = @$this->Controller->request->getData('tabela');
            if ( !isset( $this->parametros['tabela'] ) )
            {
                $this->parametros['tabela'] = @$this->Controller->request->getQuery('tabela');
            }
        }

        return $this->parametros['tabela'];
    }

    /**
     * Retorna os campos da lista
     *
     * @param  	String 	$campos 	Campos da lista no formato campo1,campo2,campo...
     * @return 	Array 	$fields 	Campos lista no formato array
     */
    private function getCampos()
    {
    	$fields 	= [];
    	if ( !empty( $this->parametros['campos'] ) )
    	{
            if ( is_string($this->parametros['campos']) )
            {
                $fields = explode(',', $this->parametros['campos'] );
            }
    	} else
    	{
    		$tabela = $this->parametros['tabela'];
    		$fields = [$this->Controller->$tabela->primaryKey(), $this->Controller->$tabela->displayField() ];
    	}

    	return $fields;
    }

    /**
     * Retorna os filtros da lista.
     *
     * @param 	Array 	$data|query 	Dados do filtro, repassados por getData ou getQyer, o padrão é getData.
     * @return 	Array 	$where 	Filtros da lista.
     */
    private function getFiltros( )
    {
		$where 		= [];

    	if ( !empty( $this->parametros['filtros'] ) )
    	{
    		$arrFiltros = explode( ',', $this->parametros['filtros'] );
    		foreach( $arrFiltros as $_l => $_linhaFiltro)
    		{
    			$arrLinha = explode( '=', $_linhaFiltro );
				$where[ $arrLinha[0] ] = $arrLinha[1];
    		}
    	}

    	return $where;
    }

    /**
     * Retorna os filtros da lista.
     *
     * @param 	Array 	$data|query 	Dados do grupo, repassados por getData ou getQyer, o padrão é getData.
     * @return 	Array 	$group 			Campos que vão compor o group da lista.
     */
    private function getGrupos()
    {
    	$group 		= [];

    	if ( !empty( @$this->parametros['grupos'] ) )
    	{
    		$arrGrupos = explode( ',', $this->parametros['grupos'] );
    		foreach( $arrFiltros as $_l => $_campo)
    		{
    			$group[] = $_campo;
    		}
    	}

    	return $group;
    }

    /**
     * Retorna os campos que vão compor a ordem da lista.
     *
     * @param 	Array 	$parametros 	Parâmetros da lista.
     * @return 	Array 	$order 			Campos que vão compor a ordem da lista.
     */
    private function getOrdem()
    {
    	$order = [];

    	if ( !empty( @$this->parametros['ordem'] ) )
    	{
    		$arrGrupos = explode( ',', $this->parametros['ordem'] );
    		foreach( $arrGrupos as $_l => $_campo)
    		{
    			$order[] = $_campo;
    		}
    	} else
    	{
    		$tabela = $this->parametros['tabela'];
    		$order 	= $tabela.'.'.$this->Controller->$tabela->displayField();
    	}

    	return $order;
	}

	/**
	 * Retorna os erros encontrados no parâmetros do PaginateAjax.
	 * 
	 * @return 	Array 	$erros 		Erros do PaginateAjax.
	 */
	private function getErros()
	{
		$erros 		= [];
		$tabela 	= $this->parametros['tabela'];
		$alias 		= $this->Controller->$tabela->alias();

		if ( $tabela != $alias )
		{
			$this->erros[] = __("Nome tabela $tabela inválido !");
		} else
		{
			$ListErros	= new \PaginateAjax\Lib\ListErrors( $this->parametros, $this->Controller->$tabela );

			$erros += $ListErros->getCamposErros();
			$erros += $ListErros->getFiltrosErros();
			$erros += $ListErros->getOrdemErros();
		}

		return $erros;
	}
}
