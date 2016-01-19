<?php namespace App\Repositories;

use App\User;
use App\Models\Dtr;
use App\Models\Employee;
use App\Models\Department;
use App\Repositories\Repository;
use Illuminate\Http\Request;

class EmployeeRepository extends Repository
{


    public function model() {
        return 'App\Models\Employee';
    }

    /**
     * Get all the DTR of all employee of a branch on a certain date
     *
     * @param  User  $user
     * @return Collection
     */
    public function branchByDate(User $user, $date)
    {
        return Dtr::with(['employee'=>function($query){
						        	$query->select('lastname', 'firstname', 'id');
						        }])
        						->select('dtr.*')
      							->leftJoin('employee', function($join){
                      	$join->on('dtr.employeeid', '=', 'employee.id');
                    })
                    ->where('employee.branchid', '=', $user->branchid)
                    ->where('dtr.date', '=', $date)
                    ->orderBy('employee.lastname', 'ASC')
                    ->orderBy('employee.firstname', 'ASC')->get();
    }



  public function byDepartment(Request $request) {

      $department = new Department;
      $d1 = array_flatten($department->whereNotIn('code', ['KIT'])->orderBy('code', 'DESC')->get(['id'])->toArray());

      $depts = [['name'=>'Dining', 'employees'=>[], 'deptid'=>$d1],
                  ['name'=>'Kitchen', 'employees'=>[], 'deptid'=>['71B0A2D2674011E596ECDA40B3C0AA12']]];

      for($i=0; $i<= 1; $i++) { 
          $employees = Employee::with('position')
                                  ->select('lastname', 'firstname', 'positionid', 'employee.id')
                                  ->join('position', 'position.id', '=', 'employee.positionid')
                                  ->where('branchid', $request->user()->branchid)
                                  ->whereIn('deptid', $depts[$i]['deptid'])
                          //->orderBy('position.ordinal', 'ASC')
                          ->orderBy('employee.lastname', 'ASC')
                          ->orderBy('employee.firstname', 'ASC')
                          ->get();
          $depts[$i]['employees'] = $employees;

      }
       return  $depts;
  }


    


    
}