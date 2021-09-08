<?php

namespace App\Http\Livewire;

use App\Http\Requests\MenuPageRequest;
use App\Http\Requests\MenuRequest;
use App\Http\Requests\SubMenuRequest;
use App\Models\Menu;
use App\Models\MenuPage;
use App\Models\SubMenu;
use App\Services\Helpers\ImportDataService;
use App\Services\PermissionService;

class LwMenu extends CrudComponent
{
    public $header = "Papeis de Usuários";
    public $opened = null;
    protected $rules    = [];
    protected $messages = [];
    public $formTitle;
    protected $service;
    public $iconsArray = [];
    public $modalListIconsOpen = "false";
    public $selPermissions = [];
    public $selectControllers = [];

    // Variaveis de Page
    public MenuPage $menuPageModel;
    public $menuPages = [];
    public $modalFormPageOpen = "false";
    public $modalListPageOpen = "false";
    // Variaveis de Menu
    public Menu     $menuModel;
    public $menus = [];
    public $modalFormMOpen = "false";
    public $modalListMOpen = "false"; 
    public $menuPageId;   
    // Variaveis de SubMenu
    public SubMenu  $subMenuModel;
    public $subMenus = [];
    public $modalFormSMOpen = "false";
    public $modalListSMOpen = "false";
    public $menuId; 
    

 

    /**
     * Instantiate a new UserController instance.
     */
    public function mount()
    {
        $this->loadPemissionsSelect();          
        $this->menuPageModel = new MenuPage();
        $this->menuModel     = new Menu();
        $this->subMenuModel  = new SubMenu();
        $this->loadMenuPages();
        $inpDataSrv = new ImportDataService();
        $this->iconsArray = $inpDataSrv->awesomeIconsArray(); 
    }   

    public function render()
    {
        return view('livewire.menus.lw-menu')->layout('layouts.app');
    }
    
    public function new()
    {
        $this->formTitle = "Criar nova Página de Menu";
        $this->menuPageModel = new MenuPage();
        $this->modalFormPageOpen = "true";
        $this->setListClose('MenuPage');
    }   
    
    public function newMenu($id)
    {
        $this->menuModel = new Menu();
        $this->menuModel->menu_page_id = $id;
        $page = MenuPage::find($id);
        $this->formTitle = "Criar Menu para {$page->name}";
        $this->modalFormMOpen = "true";
        $this->setListClose('Menu');
    }   
    
    public function newSubMenu($id)
    {
        $this->subMenuModel = new SubMenu();
        $this->subMenuModel->menu_id = $id;
        $menu = Menu::find($id);
        $this->formTitle = "Criar Item para {$menu->name} na página {$menu->menuPage->name}";
        $this->modalFormSMOpen = "true";
        $this->setListClose('SubMenu');
    }      

    
    public function edit($id, $type)
    {
        switch($type){
            case 'MenuPage':
                $this->menuPageModel = MenuPage::find($id);
                $this->formTitle = "Edição da Página {$this->menuPageModel->name}";
                $this->modalFormPageOpen = "true";
                $this->setListClose($type);
            break;
            case 'Menu':
                $this->menuModel = Menu::find($id);
                $this->formTitle = "Edição de Menu de {$this->menuModel->name}
                da página {$this->menuModel->menuPage->name}";
                $this->modalFormMOpen = "true";
                $this->setListClose($type);
            break;
            case 'SubMenu':
                $this->subMenuModel = SubMenu::find($id);
                $this->formTitle = "Edição de {$this->subMenuModel->name}
                do menu {$this->subMenuModel->menu->name} da página 
                {$this->subMenuModel->menu->menuPage->name}";
                $this->modalFormSMOpen = "true";
                $this->setListClose($type);                
            break;                        
        }
    } 

    public function submit($model, $type)
    {
        $this->validate();
        if(isset($model['id'])) {
            switch($type){
                case 'MenuPage':
                    $this->menuPageModel->update($model);
                break;
                case 'Menu':
                    $this->menuModel->update($model);
                break;
                case 'SubMenu':
                    $this->subMenuModel->update($model);
                break;                        
            }
        } else {
            switch($type){
                case 'MenuPage':
                    $this->menuPageModel->create($model);
                break;
                case 'Menu':
                    $this->menuModel->create($model);
                break;
                case 'SubMenu':
                    $this->subMenuModel->create($model);
                break;                        
            }            
        }

        $this->setFormClose($type);
        $this->list($type);
        $this->alert('success', 'Salvo com sucesso');  
    } 
    
    public function setFormClose($type)
    {
        switch($type){
            case 'MenuPage':              
                $this->modalFormPageOpen = "false";
                $this->list($type);
            break;
            case 'Menu':
                $this->modalFormMOpen = "false";
                $this->list($type, $this->menuPageId ?? $this->menuPageId);
            break;
            case 'SubMenu':
                $this->modalFormSMOpen = "false";
                $this->list($type);
            break;                        
        }
    }  

    public function setListClose($type)
    {
        switch($type){
            case 'MenuPage':
                $this->modalListPageOpen = "false";
            break;
            case 'Menu':
                $this->modalListMOpen = "false";
            break;
            case 'SubMenu':
                $this->modalListSMOpen = "false";
            break;  
            case 'Icons':
                $this->modalListIconsOpen = "false";
            break;                                    
        }
    }      
    
    public function loadMenuPages()
    {
        $this->menuPages = MenuPage::orderBy('order', 'ASC')->get();
    }

    public function loadMenus()
    {
        if($this->menuPageId) {
            $menuPageM = MenuPage::find($this->menuPageId);
            $this->menus = $menuPageM->menus->sortBy(
                function($menu) {
                return $menu->order;
            });           
        } 
    }    

    public function loadSubMenus()
    {
        if($this->menuId) {
            $menuM = Menu::find($this->menuId);
            $this->subMenus = $menuM->subMenus->sortBy(
                function($subMenu) {
                return $subMenu->order;
            });           
        } 
    }  
    
    public function list($type, $id = null)
    {
        $this->opened = $type;
        switch($type){
            case 'MenuPage':
                $this->loadMenuPages();                  
                $this->modalListPageOpen = "true";
                $this->formTitle         = "Lista de páginas de menu";
            break;
            case 'Menu':
                $this->menuPageId = $id ? $id : $this->menuPageId;
                $this->loadMenus();
                $this->modalListMOpen    = "true";
                $this->formTitle         = "Lista de menus da página";
            break;
            case 'SubMenu':
                $this->menuId = $id ? $id : $this->menuId;
                $this->loadSubMenus();
                $this->modalListSMOpen   = "true";
                $this->formTitle         = "Lista de SubMenus do Menu";
            break;     
            case 'Icons':
                $this->modalListIconsOpen   = "true";
                $this->formTitle            = "Lista de Icones";
            break;                                
        }
    }
    
    // Seta regras de formulario conforme lista e form new ou edit clicados por conseguinte
    protected function rules()
    {
        $srv = null;
        switch($this->opened) {
            case 'MenuPage':
                $srv = new MenuPageRequest();
                $this->messages = $srv->messages();
                return $srv->rules();
            break;
            case 'Menu':
                $srv = new MenuRequest();
                $this->messages = $srv->messages();
                return $srv->rules();                
            break;
            case 'SubMenu':
                $srv = new SubMenuRequest();
                $this->messages = $srv->messages();
                return $srv->rules();                
            break;                        
        }
    }  

    public function loadPemissionsSelect()
    {
        $permissionSrv           = new PermissionService();
        $this->selPermissions    = $permissionSrv->getPermissionSelectArray();
        $this->selectControllers = $this->param->getSelectArray('ctr_comp');
    }
    
    /**
     * Armazeona o id para executar exclusão caso confirmado
     *
     * @return response()
     */
    public function delete($key , $type = null)
    {
        $this->setListClose($type);
        parent::delete($key, $type);
    } 


    public function deleteConfirm()
    {
        $classname = "App\\Models\\".$this->type;
        $newClasse = new $classname();
        $result = $newClasse->find($this->deleteId)->delete();
        switch($this->type){
            case 'MenuPage':
                $this->loadMenuPages();
            break;
            case 'Menu':
                $this->loadMenus();
            break;
            case 'SubMenu':
                $this->loadSubMenus();
            break;                        
        }
        $this->modalDeleteClose();     
        $this->alert('success', 'Excluído com sucesso');
        $this->list($this->type);
    }   
    
    /**
     * Fecha modalDelete sem excluir
     *
     * @return response()
     */    
    public function modalDeleteClose()
    {
        parent::modalDeleteClose();
        $this->list($this->type);
    }          
     
}
