<?php

namespace App\Http\Livewire;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Exception;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;


class LwUser extends CrudComponent
{
    use WithPagination;
    // public $users      = [];
    public User $userModel;
    public Role $roleModel;
    public $password_confirmation;
    public $password;
    public $selectRoles = []; 
    public $strRoles; 
    public $userId = ''; 
    
    public $queryUsers = [];
    public $searchTerm = '';
    public $searchUserEmail = '';    

    public function render()
    {
        $users = User::search($this->searchTerm)->paginate(5);
        return view('livewire.users.lw-user', ['users' => $users]);
    }

    public function mount() {
        parent::mount();        
        $this->permiComp = "users";            
        $this->roleModel = new Role();
        $this->type      = 'User';
        $this->index();
        $this->loadSelects();
        $this->model     = 'userModel';
    }

    public function index()
    {
        $this->users = parent::index();
    }
    
    public function new($type = null, $id = null)
    {
        $this->type = $type;
        switch($this->type ) {
            case 'User':
                $this->model = 'userModel';
                $this->formTitle = "Criar novo Usuário";
                $this->clearPassword();
                $this->userModel = parent::new($type);
            break;
            case 'Role':
                $this->model = 'roleModel';
                $this->formTitle = "Adicionar Papel";
                $this->roleModel = parent::new($type);
                $this->userModel = User::find($id);       
            break;            
        }
    }     
    
    public function edit($key, $type)
    {
        $this->clearPassword();
        $this->userModel = parent::edit($key, $type);
        $this->formTitle = "Editação de Usuario {$this->userModel->name}";
    } 
    
    // Seta regras de formulario conforme lista e form new ou edit clicados por conseguinte
    protected function rules()
    {
        $srv = new UserRequest();
        $this->messages = $srv->messages();
        return $srv->rules($this->userModel->id ?? null, $this->password);
    }   

   /**
     * Salva a model conforme o tipo editado ou criado
     * @author Silvio Watakabe <silvio@tcmed.com.br>
     * @version 1.0
     * @param model $model
     */       
    public function submit($model, $type)
    {
        switch($this->type) {
            case 'User':
                if($this->password) {
                    $model['password']  = Hash::make($this->password);
                }
                $isNewUser = !isset($model['id']) ? true : false ;
                $user = parent::submit($model, $type);
                if(!empty($user) and $isNewUser) {
                    $user->attachRole($this->roleModel->id);
                }             
            break;
            case 'Role':
                $this->roleModel = Role::find($model['id']);
                // adiciona a Role no Usuário
                $this->insertRole();
                $this->setFormClose();
                $this->type = 'User';
                $this->index();
            break;            
        }
    } 

   /**
     * Adiciona novo papel ao usuário
     * @author Silvio Watakabe <silvio@tcmed.com.br>
     * @version 1.0
     */       
    public function insertRole()
    {
        $error = false;
        try {
            $this->userModel->attachRole($this->roleModel);
        } catch(Exception $e) {
            $error = true;
        }
        if(!$error){
            $this->alert('success', 'Papel adicionado com sucesso!');
        } else {
            $this->alert('error', "Papel selecionado já foi adicionado!");
        }
    }      

   /**
     * Carrega Selects
     * @author Silvio Watakabe <silvio@tcmed.com.br>
     * @version 1.0
     */       
    public function loadSelects()
    {
        $srv               = new UserRoleService();
        $this->selectRoles = $srv->getRoleSelectArray();
    }   
    
    /**
     * Armazeona o id para executar exclusão caso confirmado
     *
     * @return response()
     */
    public function delete($id, $type)
    {
        $this->type         = $type;
        switch($type) {
            case 'User':
                parent::delete($id, $type);
            break;
            case 'Role':
                $this->userModel = User::find($id['pivot']['user_id']);
                $this->roleModel = Role::find($id['pivot']['role_id']);
                $this->modalDelete  = "true";
            break;        
        }        
    }     

    public function deleteConfirm()
    {
        switch($this->type) {
            case 'User':
                parent::deleteConfirm();
            break;
            case 'Role':
                $this->userModel->detachRole($this->roleModel);
                $this->type = 'User';
                $this->index();
                $this->modalDeleteClose(); 
                $this->alert('success', 'Excluído com sucesso');                
            break;        
        }
    }  
    
    public function clearPassword()
    {
        $this->password = null;
        $this->password_confirmation = null;
    }   
    
    public function selectUser($user) {
        $this->searchUserEmail = $user['email'];
        $this->queryUsers      = [];
        $this->searchTerm      = null;
    }

    public function updatedSearchTerm()
    {
        $userQuery = User::query();

        $this->queryUsers = $userQuery->where(function ($query) {
            $query->where('name', 'like', trim($this->searchTerm) . "%")
                ->orWhere('email', 'like', trim($this->searchTerm) . "%");
        })->get();
    }
  

    public function searchUserClear($list = null)
    {
        $this->queryUsers      = [];
        $this->searchTerm      = null;
        $this->searchUserEmail = null;
        if($list == 'listAll') {
            $this->index();
        }
    }    

}
