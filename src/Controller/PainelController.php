<?php
namespace PaginateAjax\Controller;

use PaginateAjax\Controller\AppController;

/**
 * Painel Controller
 *
 *
 * @method \PaginateAjax\Model\Entity\Painel[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $optionsMunicipios = [1=>'pagina 1', 2=>'pagina 2'];

        $this->set( compact('optionsMunicipios') );
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
