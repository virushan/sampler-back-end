<?php
/**
 * Created by PhpStorm.
 * User: Virushan
 * Date: 2016-08-21
 * Time: 4:49 PM
 */

namespace App\Transformers;


use App\Models;
use League\Fractal;
use \Carbon\Carbon;

class AccountTransformer extends Fractal\TransformerAbstract
{
    protected $availableEmbeds = [
        'account'
    ];
    public function transform(Models\Account $account){
        return [

            'accountId' => $account->id,
            'balance' => number_format((float)$account->balance, 2, '.', ''),
            'lastAccountUpdate' => Carbon::parse($account->created_at)->format('M d Y h:i:s A'),
            'user' => [
                'firstName' => ucfirst($account->user['first_name']),
                'lastName' => ucfirst($account->user['last_name']),
                'email' => $account->user['email'],
                'userSince' => Carbon::parse($account->user['created_at'])->format('M d Y h:i:s A'),
            ]
        ];
    }
}