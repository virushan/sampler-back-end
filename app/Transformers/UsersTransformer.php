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

class UsersTransformer extends Fractal\TransformerAbstract
{
    protected $availableEmbeds = [
        'account'
    ];
    public function transform(Models\Users $user){
        return [
          'firstName' => ucfirst($user->first_name),
          'lastName' => ucfirst($user->last_name),
          'email' => $user->email,
          'userSince' => Carbon::parse($user->created_at)->format('M d Y h:i:s A'),
           'account' => [
               'accountId' => $user->account['id'],
               'balance' => number_format((float)$user->account['balance'], 2, '.', ''),
               'lastAccountUpdate' => Carbon::parse($user->account['created_at'])->format('M d Y h:i:s A'),
           ]
        ];
    }
}