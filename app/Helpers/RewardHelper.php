<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Payout;
use App\Models\Reward;
use App\Models\UserPayout;
use App\Models\AdminCharge;
use App\Models\LevelPayout;
use App\Models\AutopoolIncome;
use App\Models\RewardAchiever;
use App\Models\RegisterationBonus;

class RewardHelper
{
    public static function giveRewards()
    {
        $rewards = self::getRewards();
        foreach($rewards as $k => $singleReward){
            $users = User::where('left_count','>=',$singleReward->pairs)
                ->where('right_count','>=',$singleReward->pairs)->with(['achievedRewards'])->get();
            foreach($users as $index => $user){
                $isRewardAlreadyGiven = $user->achievedRewards->where('pairs',$singleReward->pairs)->count();
                if($isRewardAlreadyGiven == 0){
                    self::saveRewardAchiever($user,$singleReward);
                }
            }
        }
    }

    public static function getRewards(){
        return Reward::get();
    }

    public static function saveRewardAchiever($user,$reward): RewardAchiever
    {
        $modelObject = new RewardAchiever;
        $modelObject->user_id = $user->id;
        $modelObject->reward_id = $reward->id;
        $modelObject->pairs = $reward->pairs;
        $modelObject->save();
        return $modelObject;
    }

    public static function studentRegIncentive($userId){
        $adminCharge = AdminCharge::first();
        $student = User::find($userId);
        $sponsor = $student->sponsor;
        if($sponsor != null){
            $payout = Payout::where('username',$sponsor->member_id)->get()->last();
            $pair_amount = 200;
            $tds = round((200*$adminCharge->tds_charges)/100);
            $admin_charges = round((200*$adminCharge->admin_charges)/100);
            $net_amount = round((200 - $tds) - $admin_charges);
            if($payout != null && $payout->pair_count < 60){
                $payout_count = $payout->pair_count + 1;
                $pair_amount = $payout->pair_amount + $pair_amount;
                $tds = $payout->tds + $tds;
                $admin_charges = $payout->admin_charge + $admin_charges;
                $net_amount = $payout->net_amount + $net_amount;
                $payout->update([
                    'pair_count' => $payout_count,
                    'pair_amount' => $pair_amount,
                    'tds' => 0,
                    'admin_charge' => 0,
                    'net_amount' => $pair_amount,
                ]);
                if($payout_count == 60){
                    self::registerBonus($sponsor,$sponsor->payouts->count());
                    self::registerBonus($sponsor->sponsor);
                    self::upperLevelBonus($sponsor->sponsor);
                }
            }else{
                Payout::create([
                    'username' => $sponsor->member_id,
                    'tree' => 1,
                    'pair_count' => 1,
                    'pair_amount' => $pair_amount,
                    'direct_income' => 0,
                    'tds' => 0,
                    'admin_charge' => 0,
                    'net_amount' => $pair_amount,
                ]);
            }
        }
    }
    public static function upperLevelBonus($user){
        $roles = [
            4 => 100000,  // district field officer
            3 => 20000,  // district team manager
            2 => 20000,  //  admin
        ];  // role_id => amount

        foreach ($roles as $role => $amount) {
            $count = User::where('sponsor_id',$user->sponsor_id)->where('role',$user->role)->whereHas('regisBonus',function($query)use($amount){
                $query->select(\DB::raw('SUM(bonus_amount) as total_bonus'))
                      ->groupBy('user_id')
                      ->having('total_bonus', '>=', $amount);
            })->get()->count();
            $index = 0;
            while ($count >= 5) {
                $index = $index + 1;
                self::registerBonus($user->sponsor,$index);
                $count = $count - 5;
            }
            $user = $user->sponsor;
        }
    }
    public static function registerBonus($user,$index = null){
        $adminCharge = AdminCharge::first();
        $bonus_amount = 20000;
        if($index != null){
            $bonus_amount = 20000 * $index;
        }

        $tds = round(($bonus_amount*$adminCharge->tds_charges)/100);
        $admin_charges = round(($bonus_amount*$adminCharge->admin_charges)/100);
        $net_amount = round(($bonus_amount - $tds) - $admin_charges);
        RegisterationBonus::updateOrCreate([
            'user_id' => $user->id,
        ],[
            'bonus_amount' => $bonus_amount,
            'tds' => 0,
            'admin_charges' => 0,
            'net_amount' => $bonus_amount,
        ]);
    }
    public static function levelIncome($user_id){
        $adminCharge = AdminCharge::first();
        if($user_id != null){
            $user = User::find($user_id);
            $kit = $user->latestJoiningKit ?? null;
            $user = $user->sponsor;
            $levelIncome = [
                2 => 50,
                3 => 50,
                4 => 50,
                5 => 50,
                6 => 20,
                7 => 20,
                8 => 20,
                9 => 20,
                10 => 20,
                11 => 20,
                12 => 20,
                13 => 20,
                14 => 20,
                15 => 20,
                16 => 10,
                17 => 10,
                18 => 10,
                19 => 10,
                20 => 10,
                21 => 10,
                22 => 10,
                23 => 10,
                24 => 10,
                25 => 10,
            ]; // level => income
            foreach ($levelIncome as $level => $income) {
                if($user != null){
                    if($user->sponsor != null){
                        $user = $user->sponsor;
                        $level_income = $income;
                        if($kit != null){
                            if($level >= 2 && $level <= 5){
                                $level_income = $kit->level2_5;
                            }elseif($level >= 6 && $level <= 15){
                                $level_income = $kit->level6_15;
                            }elseif ($level >= 16 && $level <= 25) {
                                $level_income = $kit->level16_25;
                            }
                        }
                        $payout = Payout::where('username',$user->member_id)->where('level',$level)->whereNull('credit_or_cut')->get()->last();
                        if($payout != null){
                            $payoutArray = $payout->toArray();
                            $total = $level_income + $payoutArray['level_income'];
                            $tds = round(($total*$adminCharge->tds_charges)/100);
                            $admin_charges = round(($total*$adminCharge->admin_charges)/100);
                            $net_amount = round(($total-$tds)-$admin_charges);
                            $payout->update([
                                'level' => $level,
                                'level_income' => $total,
                                'tds' => 0,
                                'admin_charge' => 0,
                                'net_amount' => $total,
                            ]);
                        }else{
                            $tds = round(($level_income*$adminCharge->tds_charges)/100);
                            $admin_charges = round(($level_income*$adminCharge->admin_charges)/100);
                            $net_amount = round(($level_income-$tds)-$admin_charges);
                            $payout = Payout::create([
                                'username' => $user->member_id,
                                'tree' => 1,
                                'pair_count' => 1,
                                'pair_amount' => 0,
                                'direct_income' => 0,
                                'tds' => 0,
                                'admin_charge' => 0,
                                'net_amount' => $level_income,
                                'level' => $level,
                                'level_income' => $level_income,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public static function directIncome($user_id){
        $adminCharge = AdminCharge::first();
        $user = User::with(['latestJoiningKit','sponsor'])->where('id',$user_id)->first();
        if($user != null){
            $kit = $user->latestJoiningKit ?? null;
            $user = $user->sponsor;
            $direct_amount = $kit->direct_income ?? 0;
            $amount = $direct_amount;
            $tds = round(($amount * $adminCharge->tds_charges)/100);
            $admin_charges = round(($amount * $adminCharge->admin_charges)/100);
            $net_amount = round(($amount - $tds) - $admin_charges);
            $payout = UserPayout::where('user_id',$user->id)->where('income_type','direct')->whereNull('is_requested')->whereNull('is_paid')->latest()->first();
            if($payout != null){
                $payout->update([
                    'amount' => round($payout->amount + $amount),
                    'tds' => 0,
                    'admin_charges' => 0,
                    'net_amount' => round($payout->net_amount + $amount),
                ]);
            }else{
                UserPayout::create([
                    'user_id' => $user->id,
                    'income_type' => 'direct',
                    'amount' => $amount,
                    'tds' => 0,
                    'admin_charges' => 0,
                    'net_amount' => $amount,
                ]);
            }
        }
    }
    public static function sponsorIncome($user_id){
        $adminCharge = AdminCharge::first();
        $user = User::with(['latestJoiningKit','sponsor'])->where('id',$user_id)->first();
        $amount = $user->latestJoiningKit->level2_5;
        $tds = round(($amount * $adminCharge->tds_charges)/100);
        $admin_charges = round(($amount * $adminCharge->admin_charges)/100);
        $net_amount = round(($amount - $tds) - $admin_charges);
        $sponsor = $user->sponsor;
        for ($i=0; $i < 6; $i++) { 
            if($sponsor != null){
                $payout = UserPayout::where('user_id',$sponsor->id)->where('income_type','level')->whereNull('is_requested')->whereNull('is_paid')->latest()->first();
                if($payout != null){
                    $payout->update([
                        'amount' => round($payout->amount + $amount),
                        'tds' => 0,
                        'admin_charges' => 0,
                        'net_amount' => round($payout->net_amount + $amount),
                    ]);
                }else{
                    UserPayout::create([
                        'user_id' => $sponsor->id,
                        'income_type' => 'level',
                        'amount' => $amount,
                        'tds' => 0,
                        'admin_charges' => 0,
                        'net_amount' => $amount,
                    ]);
                }
            }
            $sponsor = $sponsor->sponsor ?? null;
        }
    }
    public static function charityIncome($user_id){    
        $adminCharge = AdminCharge::first();
        $user = User::with(['latestJoiningKit','sponsor'])->where('id',$user_id)->first();
        $amount = $user?->latestJoiningKit?->bonus_amount ?? 0;
        $tds = round(($amount * $adminCharge->tds_charges)/100);
        $admin_charges = round(($amount * $adminCharge->admin_charges)/100);
        $net_amount = round(($amount - $tds) - $admin_charges);    
        $charityUsers = User::whereIn('member_id',['CH0001','CH0002'])->get();
        foreach ($charityUsers as $user) {
            $payout = UserPayout::where('user_id',$user->id)->where('income_type','charity')->whereNull('is_requested')->whereNull('is_paid')->latest()->first();
            if($payout != null){
                $payout->update([
                    'amount' => round($payout->amount + $amount),
                    'tds' => 0,
                    'admin_charges' => 0,
                    'net_amount' => round($payout->net_amount + $amount),
                ]);
            }else{
                UserPayout::create([
                    'user_id' => $user->id,
                    'income_type' => 'charity',
                    'amount' => $amount,
                    'tds' => 0,
                    'admin_charges' => 0,
                    'net_amount' => $amount,
                ]);
            }
        }
    }
    public static function autopoolIncome($user_id){
        $user = User::with(['latestJoiningKit','sponsor'])->where('id',$user_id)->first();
        $autoPool = $user->latestJoiningKit->autoPool;
        $counts = [
            1 => 'count_4',
            2 => 'count_16',
            3 => 'count_64',
            4 => 'count_256',
            5 => 'count_1024',
            6 => 'count_4096',
            7 => 'count_16384',
        ];
        $sponsor = $user->sponsor;
        $userCount = 4;
        foreach($counts as $level => $column){
            $olderAutopoolIncome = null;
            $autopoolIncome = AutopoolIncome::where('autopool_id',$autoPool->id)->where('level',$level)->where('user_id',$sponsor->id)->latest()->first();
            if($level >= 2){
                $olderAutopoolIncome = AutopoolIncome::where('autopool_id',$autoPool->id)->where('level',($level - 1))->where('user_id',$sponsor->id)->latest()->first();
            }
            if($olderAutopoolIncome != null){
                $sameKitSponsorChilds = $user->sponsor->childs->toQuery()->where('is_paid',1)->whereHas('latestJoiningKit',function($query)use($autoPool){
                    return $query->where('autopool_id',$autoPool->id);
                })->count();
                if(!in_array($user->sponsor->id,$olderAutopoolIncome->child_ids)){
                    break;
                }else if($sameKitSponsorChilds > 4){
                    break;
                }
            }
            $is_eligible = false;
            $sponsorAutopoolIds = $sponsor?->joiningKits->pluck('autopool_id')->toArray() ?? [];
            if(in_array($autoPool->id,$sponsorAutopoolIds) || $sponsor->member_id == 'Company'){
                $is_eligible = true;
            }
            if($is_eligible){
                if($autopoolIncome != null){
                    if(count($autopoolIncome->child_ids) < $userCount){
                        $currentChild_ids = $autopoolIncome->child_ids;
                        if(!in_array($user->id,$currentChild_ids)){
                            array_push($currentChild_ids,$user->id);
                            $autopoolIncome->update([
                                'income' => ($autopoolIncome->income != null)? round($autopoolIncome->income + $autoPool->$column) : $autoPool->$column,
                                'child_ids' => ($autopoolIncome->child_ids != null)? $currentChild_ids : [$user->id],
                            ]);
                        }
                    }
                }else{
                    $data = [
                        'user_id' => $sponsor->id,
                        'income' => $autoPool->$column,
                        'level' => $level,
                        'child_ids' => [$user->id],
                        'autopool_id' => $autoPool->id,
                    ];
                    AutopoolIncome::create($data);
                }
            }
            if(count($sponsorAutopoolIds) > 1 && $sponsor->latestJoiningKit->autopool_id != $autoPool->id){
                self::settlePreviousAutopool($sponsor,$autoPool->id);
            }
            $sponsor = $sponsor->sponsor;
            $userCount = $userCount*4;
            if($sponsor == null){
                break;
            }
        }
    }
    public static function settlePreviousAutopool($user,$autopool_id){
        $adminCharge = AdminCharge::first();
        $autopoolIncomes = AutopoolIncome::where('user_id',$user->id)->where('autopool_id',$autopool_id)->orderBy('level','asc')->get();
        $olderPayouts = UserPayout::where('user_id',$user->id)->where('income_type','autopool')->where(function($query){
            return $query->whereNotNull('is_requested')->orWhereNotNull('is_paid');
        })->get();
        $income = getMatchedAutoPoolIncome($autopoolIncomes);
        if($olderPayouts->count() > 0) {
            $amount = round($income - $olderPayouts->sum('amount'));
        }else {
            $amount = round($income);
        }
        $tds = round(($amount * $adminCharge->tds_charges)/100);
        $admin_charges = round(($amount * $adminCharge->admin_charges)/100);
        $net_amount = round(($amount - $tds) - $admin_charges);
        $payout = UserPayout::where('user_id',$user->id)->where('income_type','autopool')->whereNull('is_requested')->whereNull('is_paid')->latest()->first();
        if($payout != null){
            $payout->update([
                'amount' => $amount,
                'tds' => 0,
                'admin_charges' => 0,
                'net_amount' => $amount,
            ]);
        }else{
            UserPayout::create([
                'user_id' => $user->id,
                'income_type' => 'autopool',
                'amount' => $amount,
                'tds' => 0,
                'admin_charges' => 0,
                'net_amount' => $amount,
            ]);
        }
    }
}
