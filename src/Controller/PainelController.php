<?php
/**
 * Painel Controller
 */
namespace PaginateAjax\Controller;
use PaginateAjax\Controller\AppController;
/**
 * Mantém o cadastro para testes do componente PaginateAjax.
 */
class PainelController extends AppController
{
    /**
     * Método de inicialização

     * @return  \Cake\Http\Response|Null
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('PaginateAjax.Paginator');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|Null
     */
    public function index()
    {
        $acoes = 
        [
            'Excluir'   => $this->request->here . '/excluir/{id}',
            'Visualizar'=> $this->request->here . '/visualizar/{id}',
        ];

        $pagina = 11;

        $this->set( compact('acoes', 'pagina') );
    }

    /**
     * Exibe a tela de visualização
     */
    public function visualizar()
    {
        $idMunicipio = (int) @$this->request->getParam('pass')[0];

        $this->loadModel('Municipios');

        $this->request->data = $this->Municipios->find()
            ->where(['Municipios.id'=>$idMunicipio])
            ->toArray();
    }

    /**
     * Exibe a tela de visualização
     */
    public function excluir()
    {
        $idMunicipio = (int) @$this->request->getParam('pass')[0];

        $this->loadModel('Municipios');

        if ( !$this->Municipios->deleteAll(['Municipios.id'=>$idMunicipio]) )
        {
            $this->Flash->error( __('Não foi possível excluir o registro '.$idMunicipio) );
        } else
        {
            $this->Flash->error( __("Registro $idMunicipio, excluído com sucesso") );
        }

        return $this->redirect(['action'=>'index']);
    }

    /**
     * Retorna a lista de municípios
     *
     * @var     
     */
    public function lista()
    {
        $this->Paginator->setParams('tabela', 'Municipios');
        //$this->Paginator->setParams('campos', ['Municipios.id', 'Municipios.nome', 'Municipios.uf', 'Municipios.codi_estd', 'Municipios.desc_estd'] );
        //$this->Paginate->setParametro('tipo', 'list');
        //$this->Paginate->setParametro('pagina', 20);

        $this->Paginator->paginate();
    }

}
