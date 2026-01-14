<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'enc_password',
        'alter_email',
        'member_id',
        'epin',
        'parent_string',
        'left_count',
        'right_count',
        'sponsor_id',
        'parent_id',
        'parent_leg',
        'father_name',
        'gender',
        'dob',
        'mobile',
        'email_activation',
        'terms',
        'role',
        'is_blocked',
        'is_paid',
        'remember_token',
        'left_child_id',
        'right_child_id',
        'user_icon',
        'is_franchise',
        'kit_id',
        'profile_image',
        'wallet_pin'
    ];

    protected $appends = ['profile'];

    public static $panCardOption = [
        'pan_exist' => 'Pancard Available',
        'pan_not_exist' => 'Pancard Not Available'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function position(){
        return [
            'left' => 'Left',
            'right' => 'Right'
        ];
    }


    public function wallet(){
        return $this->belongsTo(UserWallet::class,'username','username');
    }

    public static function getWalletAmount(){
        $user = \Auth::guard('member')->user();
        if($user){
            return $user->walletIncomesByKey('totalIncome');
        }
        return 0;
    }

    public function walletIncomesByKey($key = 'totalIncome') {
        // Calculate Total Income (Credits)
        // Sum of all incomes that are NOT withdrawals
        $credits = $this->walletTransations()
                        ->whereIn('keyword', ['self_transfer_direct_income'])
                        ->sum('amount');
        
        // Calculate Withdrawals (Debits)
        // Sum of all withdrawals that are NOT rejected
        $debits = $this->walletTransations()
                       ->where('keyword', 'withdrawal')
                    //    ->where(function($q){
                    //        $q->Where('is_paid', '!=', '1');
                    //    })
                       ->sum('amount');

        $balance = $credits - $debits;

        $incomeArray = [
            'totalIncome'   => $balance,
            'directIncome'  => $this->payouts()->where('income_type', 'direct_income')->sum('amount'),
            'teamPerform'   => $this->payouts()->where('income_type', 'like', 'level%')->sum('amount'),
            'autopool'      => $this->payouts()->where('income_type', 'reward')->sum('amount'), // Assuming 'reward' is mapped to autopool/reward key
            'total'         => $credits,
            'transaction'   => 0,
            'withdrawls'    => $debits,
            'received'      => 0,
        ];

        return $incomeArray[$key] ?? 0;
    }

    public function position_rel(){
        return $this->belongsTo(Position::class,'member_id','user_id');
    }

    public function bank_details(){
        return $this->hasOne(UserBankDetail::class,'user_id','id');
    }

    public function user_address(){
        return $this->hasOne(UserAddress::class,'user_id','id');
    }

    public function profile(){
        return $this->belongsTo(UserProfile::class,'id','user_id');
    }

    public function getProfileAttribute(){
        return $this->profile()->first();
    }

    public function used_pin_rel(){
        return $this->belongsTo(Epin::class,'epin','pin_no')->with(['joining_kit_rel']);
    }

    public function profile_rel(){
        return $this->belongsTo(UserProfile::class,'id','user_id');
    }

    public function transfer_pin_rel(){
        return $this->hasMany(Epin::class,'transfer_to','id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id','member_id');
    }

    public function leftChild()
    {
        return $this->belongsTo(User::class, 'left_child_id');
    }

    public function rightChild()
    {
        return $this->belongsTo(User::class, 'right_child_id');
    }

    public function getLeftCount()
    {
        return $this->countChildren('left_child_id');
    }

    public function getRightCount()
    {
        return $this->countChildren('right_child_id');
    }

    private function countChildren($childColumn)
    {
        $count = 0;
        $childId = $this->$childColumn;

        if ($childId) {
            $child = self::find($childId);
            $count += 1 + $child->countChildren('left_child_id') + $child->countChildren('right_child_id');
        }

        return $count;
    }

    public function kyc_rel(){
        return $this->belongsTo(KycDoc::class,'id','user_id');
    }

    public function saleEntries() {
        return $this->hasMany(SaleEntry::class,'user_id','id');
    }

    public function isFirstSale() {
        return $this->saleEntries()->count();
    }

    public function joiningKit(){
        return $this->belongsTo(Epin::class,'epin','pin_no');
    }

    public function achievedRewards(){
        return $this->hasMany(RewardAchiever::class,'user_id','id');
    }

    public function latestReward(){
        return $this->achievedRewards()->latest()->first();
    }

    public function allChildMembers(){
        return $this->hasMany(self::class,'sponsor_id','member_id');
    }
    public function latestChildMember(){
        return $this->hasOne(self::class,'sponsor_id','member_id')->latest();
    }
    public function userRole(){
        return $this->belongsTo(Role::class,'role','id');
    }
    public function sponsor(){
        return $this->belongsTo(self::class,'sponsor_id','member_id');
    }
    public function regisBonus(){
        return $this->hasMany(RegisterationBonus::class,'user_id','id');
    }
    public function payouts(){
        return $this->hasMany(UserPayout::class,'user_id','id');
    }
    public function joiningKits(){
        return $this->belongsToMany(JoiningKit::class,'user_kits','user_id','kit_id');
    }

    public function latestJoiningKit(){
        return $this->hasOneThrough(JoiningKit::class, UserKit::class, 'user_id', 'id', 'id', 'kit_id')->latest();
    }
    public function getParentStringArrayAttribute(){
        return ($this->parent_string) ? explode(',', $this->parent_string) : [];
    }
    public function unPaidPayouts(){
        return $this->payouts()->whereNull('is_paid');
    }

    // Dashboard Counts
    public function getLevelIncomeAttribute(){
        $walletTransferedIncome = 0;
        $levelIncome = $this->unPaidPayouts()->where('income_type','level')->sum('amount') ?? 0;
        $walletTransferedIncome = $this->walletTransations->where('keyword', 'self_transfer_level')->sum('amount') ?? 0;
        return round($levelIncome - $walletTransferedIncome);
    }
    public function getDirectBonusIncomeAttribute(){
        $walletTransferedIncome = 0;
        $directIncome = $this->unPaidPayouts()->where('income_type','direct')->sum('amount') ?? 0;
        $walletTransferedIncome = $this->walletTransations->where('keyword', 'self_transfer_direct')->sum('amount') ?? 0;
        return round($directIncome - $walletTransferedIncome);
    }
    public function getTotalIncomeAttribute(){
        return round(($this->unPaidPayouts()->whereNull('is_requested')->sum('net_amount')));
    }
    public function getAutopoolIncomeAttribute(){
        $walletTransferedIncome = 0;
        $autopoolIncome = $this->unPaidPayouts()->where('income_type','autopool')->sum('amount') ?? 0;
        $walletTransferedIncome = $this->walletTransations->where('keyword', 'self_transfer_autopool')->sum('amount') ?? 0;
        return round($autopoolIncome - $walletTransferedIncome);
    }
    public function getBonusIncomeAttribute(){
        return $this->unPaidPayouts()->sum('pair_amount');
    }
    public function getTotalIncomeWithoutChargesAttribute(){
        return round($this->unPaidPayouts()->whereNull('is_requested')->sum('amount'));
    }

    public function getProfileImageUrlAttribute(){
        return $this->profile_image ? asset('storage/profile_images/'.$this->profile_image) : asset('images/nophoto_m.jpg');
    }   
    public function childs(){
        return $this->hasMany(self::class,'parent_id','member_id');
    }
    public function allChilds(){
        $user_id = $this->id;
        return self::whereNotNull('parent_string')->get()->map(function($item)use($user_id){
            if(in_array($user_id,$item->parent_string_array)){
                return $item;
            }else{
                return null;
            }
        })->filter();
    }
    public function allPaidChilds(){
        $user_id = $this->id;
        return self::where('is_paid',1)->whereNotNull('parent_string')->get()->map(function($item)use($user_id){
            if(in_array($user_id,$item->parent_string_array)){
                return $item;
            }else{
                return null;
            }
        })->filter();
    }
    public function autoPoolIncomes(){
        return $this->hasMany(AutopoolIncome::class,'user_id','id');
    }
    public function latestAutopoolIncomes(){
        $kit_id = $this?->latestJoiningKit?->autopool_id ?? null;
        return $this->hasMany(AutopoolIncome::class,'user_id','id')->when($kit_id,function($query) use($kit_id){
            return $query->where('autopool_id',$kit_id);
        })->orderBy('level','asc');
    }
    public function latestAutopool(){
        return $this->latestJoiningKit->autoPool ?? null; 
    }
    public function walletTransations() {
        return $this->hasMany(WalletTransaction::class, 'user_id', 'member_id')->orderBy('created_at', 'desc');
    }
    public function recievedMoney() {
        return $this->hasMany(WalletTransaction::class, 'transfered_to', 'member_id');
    }


}
