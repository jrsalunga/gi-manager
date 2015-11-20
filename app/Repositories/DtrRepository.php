<?php namespace App\Repositories;

use App\User;
use App\Models\Dtr;

class DtrRepository
{
    /**
     * Get all of the tasks for a given user.
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
}