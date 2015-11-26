<?php namespace App\Repositories;

use App\User;
use App\Models\Timelog;
use App\Models\Employee;

class TimelogRepository
{
    /**
     * Get all the timelog for an employee on the day.
     *
     * @param  Employee $employee, Carbon $date ('Y-m-d')
     * @return Collection of timelog
     */
    public function employeeTimelogs(Employee $employee, $date)
    {
        $res = Timelog::employeeid($employee->id)
                  ->whereBetween('datetime', [
                      $date->copy()->format('Y-m-d').' 06:00:00',          // '2015-11-13 06:00:00'
                      $date->copy()->addDay()->format('Y-m-d').' 05:59:59' // '2015-11-14 05:59:59'
                    ])
                  ->orderBy('datetime', 'ASC')
                  ->orderBy('txncode', 'ASC')
                  ->get();
        return count($res)>0 ? $res:false;
    }
}