<div>
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuários') }}
        </h2>
  
      </div>
    </div>
  
  <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
    <div class="py-3 ">
      <button
        wire:click="new()"
        class="relative text-white px-3 w-auto h-10 bg-blue-600 rounded-full hover:bg-blue-700
        active:shadow-lg mouse shadow transition ease-in duration-200 focus:outline-none">
        <svg viewBox="0 0 20 20" enable-background="new 0 0 20 20" class="w-6 h-6 inline-block">
          <path fill="#FFFFFF" d="M16,10c0,0.553-0.048,1-0.601,1H11v4.399C11,15.951,10.553,16,10,16c-0.553,0-1-0.049-1-0.601V11H4.601
                                  C4.049,11,4,10.553,4,10c0-0.553,0.049-1,0.601-1H9V4.601C9,4.048,9.447,4,10,4c0.553,0,1,0.048,1,0.601V9h4.399
                                  C15.952,9,16,9.447,16,10z" />
        </svg>
        <span>New</span>
      </button>
    </div>   
  
      <div class="flex flex-col my-3">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
  
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Usuário
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Password
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      <span class="sr-only">Edit</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      <span class="sr-only">Excluir</span>
                    </th>              
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
      
                  @foreach($users as $user)        
                  <tr class="bg-emerald-200">
      
                      <td class="px-6 py-4 whitespace-nowrap">
                          <span>{{ $user->name }}</span>
                      </td>
      
                      <td class="px-6 py-4 whitespace-nowrap">
                          <span>{{ $user->email }}</span>
                      </td>     
  
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span>{{ $user->password }}</span>
                    </td>                             
                      
                      <td class="px-6 py-4 whitespace-nowrap">
                        <button wire:click="edit({{$user->id}})">Edit</button>
                      </td>  
                      <td class="px-6 py-4 whitespace-nowrap">
                        <button wire:click="delete({{$user->id}})">X</button>
                      </td>                                
                  </tr>        
                  @endforeach    
                  <!-- More people... -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> 
      
      {{-- @include('livewire.user-role-form-modal') --}}
  
    </div>
  </div>    
  
          
          
  
