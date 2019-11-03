<?php

namespace App\Console\Commands;

use App\Models\Arupian;
use App\Models\Group;
use App\TokenStore\TokenCache;
use Illuminate\Console\Command;

class GetAllStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:allstaff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all staff info from ads to db.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Arupian::truncate();
        $staff = TokenCache::getAllStaff();
        for ($i = 0; $i < $staff['@odata.count']/200; $i++) {
            if ($i == 0) {
                foreach ($staff["value"] as $k => $v) {
                    $fist_name = $v['KnownAs'];
                    $last_name = $v['LastName'];
                    $staff_id = $v['StaffId'];
                    $group_name = $v['AccountingCentreCode'] . " " . $v['GroupName'];
                    $off_location = $v['LocationName'];
                    $location = $v['RegionName'];
                    $email = $v['Email'];
                    if (!Group::where("name", $group_name)->exists()) {
                        $group = new Group;
                        $group->name = $group_name;
                        $group->save();
                    }
                    $group_id = Group::where("name", $group_name)->first()->id;
                    $arup = Arupian::where("email", $email);
                    if ($arup->exists()) {
                        $arup->update([
                            'staff_id' => $staff_id,
                            'off_location' => $off_location,
                            'location' => $location,
                            'group_id' => $group_id,
                        ]);
                    }
                    else {
                        $arupian = new Arupian;
                        $arupian->first_name = $fist_name;
                        $arupian->last_name = $last_name;
                        $arupian->staff_id = $staff_id;
                        $arupian->off_location = $off_location;
                        $arupian->location = $location;
                        $arupian->group_id = $group_id;
                        $arupian->email = $email;
                        $arupian->save();
                    }
                }
            }
            else {
                $staff_1 = TokenCache::getAllStaff(200*$i);
                foreach ($staff_1["value"] as $k => $v) {

                    $fist_name = $v['KnownAs'];
                    $last_name = $v['LastName'];
                    $staff_id = $v['StaffId'];
                    $group_name = $v['AccountingCentreCode'] . " " . $v['GroupName'];
                    $off_location = $v['LocationName'];
                    $location = $v['RegionName'];
                    $email = $v['Email'];
                    $group = Group::where("name", $group_name);
                    if (!$group->exists()) {
                        $group = new Group;
                        $group->name = $group_name;
                        $group->save();
                    }
                    $group_id = Group::where("name", $group_name)->first()->id;
                    $arup = Arupian::where("email", $email);
                    if ($arup->exists()) {
                        $arup->update([
                            'staff_id' => $staff_id,
                            'off_location' => $off_location,
                            'location' => $location,
                            'group_id' => $group_id,
                        ]);
                    }
                    else {
                        $arupian = new Arupian;
                        $arupian->first_name = $fist_name;
                        $arupian->last_name = $last_name;
                        $arupian->staff_id = $staff_id;
                        $arupian->off_location = $off_location;
                        $arupian->location = $location;
                        $arupian->group_id = $group_id;
                        $arupian->email = $email;
                        $arupian->save();
                    }

                }
            }

        }
    }
}