<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;

class Employees extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $name, $email;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.employees.view', [
            'employees' => Employee::latest()
						->orWhere('name', 'LIKE', $keyWord)
						->orWhere('email', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }
	
    private function resetInput()
    {		
		$this->name = null;
		$this->email = null;
    }

    public function store()
    {
        $this->validate([
		'name' => 'required',
		'email' => 'required',
        ]);

        Employee::create([ 
			'name' => $this-> name,
			'email' => $this-> email
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Employee Successfully created.');
    }

    public function edit($id)
    {
        $record = Employee::findOrFail($id);

        $this->selected_id = $id; 
		$this->name = $record-> name;
		$this->email = $record-> email;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'name' => 'required',
		'email' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Employee::find($this->selected_id);
            $record->update([ 
			'name' => $this-> name,
			'email' => $this-> email
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Employee Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Employee::where('id', $id);
            $record->delete();
        }
    }
}
