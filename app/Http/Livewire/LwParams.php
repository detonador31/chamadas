<?php

namespace App\Http\Livewire;

use App\Http\Requests\ParamItemRequest;
use App\Http\Requests\ParamRequest;
use App\Models\Param;
use App\Models\ParamItem;

class LwParams extends CrudComponent
{
    public $rules      = [];
    public $messages   = [];
    public $params     = [];
    public $paramItems = [];
    public $paramModel;
    public $paramItemModel;
    public $modalOpen     = "false";
    public $modalItemOpen = "false";
    public $formTitle     = "Lista de parâmetros";
    public $paramId;
    public $opened;

    public $type = null;    

    /**
     * Instantiate a new UserController instance.
     */
    public function mount()
    {      
        $this->carregaDados('param');
    } 

    public function render()
    {
        return view('livewire.params.lw-params');
    }

    /**
     * Instantiate a new UserController instance.
     */
    public function new()
    {   
        $this->paramModel     = new Param();
        $this->opened         = 'param';
        $this->formTitle      = "Criar novo parametro";
        $this->setModalOpen('param');

    }   
    
    /**
     * Instantiate a new UserController instance.
     */
    public function newItem($id)
    {     
        $this->paramItemModel = new ParamItem(); 
        $this->paramId = $id;        
        $this->opened = 'paramItem'; 
        $this->formTitle      = "Criar novo Item";
        $this->paramItemModel->param_id = $id;
        $this->setModalOpen('paramItem');
        $this->setModalClose('param');
    }  
    
    public function edit($id, $type)
    {
        $this->opened = $type;        
        switch($type){
            case 'param':
                $this->paramId = $id;
                $this->paramModel = Param::find($id);
                $this->formTitle = "Edição do parametro {$this->paramModel->chave}";
                $this->modalOpen = "true";
            break;
            case 'paramItem':
                $this->paramItemModel = ParamItem::find($id);
                $this->formTitle = "Edição do item  {$this->paramItemModel->conteudo}
                de parametro  {$this->paramItemModel->param->chave}";
                $this->modalItemOpen = "true";
                $this->setModalClose('param');
            break;                     
        }
    }     

    /**
     * Instantiate a new UserController instance.
     */
    public function setModalClose($type)
    {      
        switch($type) {
            case 'param':
                $this->modalOpen = "false";
            break;
            case 'paramItem':
                $this->modalItemOpen = "false";
            break;            
        }
    }      

    /**
     * Instantiate a new UserController instance.
     */
    public function setModalOpen($type)
    {      
        switch($type) {
            case 'param':
                $this->modalOpen = "true";
            break;
            case 'paramItem':
                $this->modalItemOpen = "true";
            break;            
        }
    } 
    
    // Seta regras de formulario conforme lista e form new ou edit clicados por conseguinte
    protected function rules()
    {
        $srv = null;
        switch($this->opened) {
            case 'param':
                $srv = new ParamRequest();
                $this->messages = $srv->messages();
                return $srv->rules();
            break;
            case 'paramItem':
                $srv = new ParamItemRequest();
                $this->messages = $srv->messages();
                return $srv->rules();                
            break;                     
        }
    } 
    
    public function submit($model, $type)
    {
        $this->validate();
        if(isset($model['id'])) {
            switch($type){
                case 'param':
                    $this->paramModel->update($model);
                break;
                case 'paramItem':
                    $this->paramItemModel->update($model);
                break;                        
            }
        } else {
            switch($type){
                case 'param':
                    $this->paramModel->create($model);
                    break;
                case 'paramItem':
                    $this->paramItemModel->create($model);
                break;                        
            }   
        }

        $this->carregaDados('param');      
        $this->setModalClose($type);
        $this->alert('success', 'Salvo com sucesso');  
    }     

    // Seta regras de formulario conforme lista e form new ou edit clicados por conseguinte
    protected function carregaDados($type = null)
    {
        $this->params         = Param::all();
    }  
    
    public function deleteConfirm()
    {
        switch($this->type){
            case "param":
                $result = Param::find($this->deleteId)->delete();
            break;
            case "paramItem":
                $result = ParamItem::find($this->deleteId)->delete();
            break;                     
        }
        $this->carregaDados();
        $this->modalDeleteClose();     
        $this->alert('success', 'Excluído com sucesso');
    }   
    
}
