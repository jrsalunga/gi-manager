<?php namespace App\Repositories;

use App\User;
use App\Models\Manskeddtl;

class ManskeddtlRepository
{
    /**
     * Get all of the tasks for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function branchByDate(User $user, $date)
    {
        return Manskeddtl::with(['employee'=>function($query){
						        	$query->select('lastname', 'firstname', 'id');
						        }])
        						->select('manskeddtl.*')
      							->leftJoin('hr.employee', function($join){
                                  	$join->on('manskeddtl.employeeid', '=', 'employee.id');
                                })
                                ->leftJoin('manskedday', function($join){
                                    $join->on('manskeddtl.mandayid', '=', 'manskedday.id');
                                })
                                ->where('employee.branchid', '=', $user->branchid)
                                ->where('manskedday.date', '=', $date)
                                ->orderBy('employee.lastname', 'ASC')
                                ->orderBy('employee.firstname', 'ASC')->get();
      
    }
}