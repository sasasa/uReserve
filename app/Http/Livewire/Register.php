<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Register extends Component
{
    use WithPagination;
    
    public int $cnt = 2;
    public User $user;
    public string $text_1;
    public string $text_2;
    public string $text_3;
    public string $text_4;
    public string $search = '';
    
    protected $listeners = [
        'refresh' => '$refresh', // 再読み込み
        'destroy' => 'destroy',
        'edit' => 'edit',
    ];

    protected function rules()
    {
        if($this->user->exists) {
            $rules = [
                'user.name' => 'required|string|max:50',
                'user.email' => ['required', 'string', 'email', 'max:50', Rule::unique('users','email')->whereNot('id', $this->user->id)],
                'user.password' => 'required|string|min:8',
                'text_1' => ['string', 'max:5', 'nullable'],
                'text_2' => ['string', 'max:5', 'nullable'],
                'text_3' => ['string', 'max:5', 'nullable'],
                'text_4' => ['string', 'max:5', 'nullable'],
            ];
        } else {
            $rules = [
                'user.name' => 'required|string|max:50',
                'user.email' => 'required|string|email|max:50|unique:users,email',
                'user.password' => 'required|string|min:8',
                'text_1' => ['string', 'max:5', 'nullable'],
                'text_2' => ['string', 'max:5', 'nullable'],
                'text_3' => ['string', 'max:5', 'nullable'],
                'text_4' => ['string', 'max:5', 'nullable'],
            ];
        }
        return $rules;
    }

    public function add()
    {
        if ($this->cnt < 5) {
            $this->cnt++;
        }
    }
    public function del()
    {
        if ($this->cnt > 1) {
            $this->cnt--;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updated($property)
    {
        $this->validateOnly($property);
    }
    
    public function register()
    {
        $this->validate();

        $this->user->fill([
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'password' => Hash::make($this->user['password'])
        ]);
        $this->user->save();

        session()->flash("message", "登録okです");
        $this->create();
        // $this->emit('refresh');
        // return to_route("livewire-test.index");// Laravel9新機能
    }

    public function edit(User $user)
    {
        // dd($user);
        $user->password = "";
        $this->user = $user;
        $this->resetValidation();

    }
    public function create()
    {
        $user = new User();
        $user->password = "";
        $this->user = $user;
        $this->text_1 = "";
        $this->text_2 = "";
        $this->text_3 = "";
        $this->text_4 = "";
        $this->search = "";
        $this->resetValidation();
    }

    public function mount() // render描画前に実行( constructorのように)
    { 
        $this->create();
    }


    public function destroy(User $user)
    {
        // dd($user);
        $user->delete();
        $this->resetPage();
    }
    
    public function render()
    {
        return view('livewire.register', [
            'users' => User::
                where('name', 'like', '%'.$this->search.'%')->
                orWhere('email', 'like', '%'.$this->search.'%')->
                paginate(1),
        ]);
    }
}
