<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountAPIRequest;
use App\Http\Requests\API\UpdateAccountAPIRequest;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Transformers\AccountTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use InfyOm\Generator\Utils\ResponseUtil;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use League\Fractal;
use League\Fractal\Manager;

/**
 * Class AccountController
 * @package App\Http\Controllers\API
 */

class AccountAPIController extends InfyOmBaseController
{
    /** @var  AccountRepository */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepo, Manager $fractal)
    {
        $this->accountRepository = $accountRepo;
        $this->fractal = $fractal;
        $this->middleware('oauth');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/accounts",
     *      summary="Get a listing of the Accounts.",
     *      tags={"Account"},
     *      description="Get all Accounts",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Account")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->accountRepository->pushCriteria(new RequestCriteria($request));
        $this->accountRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accounts = $this->accountRepository->with('user')->all();

        $resource = new Fractal\Resource\Collection($accounts, new AccountTransformer());
        return $this->sendResponse($this->fractal->createData($resource)->toArray()['data'], 'Accounts retrieved successfully');
    }

    /**
     * @param CreateAccountAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/accounts",
     *      summary="Store a newly created Account in storage",
     *      tags={"Account"},
     *      description="Store Account",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Account that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Account")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Account"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    private function store(CreateAccountAPIRequest $request)
    {
        $input = $request->all();

        $accounts = $this->accountRepository->create($input);

        return $this->sendResponse($accounts->toArray(), 'Account saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/accounts/{id}",
     *      summary="Display the specified Account",
     *      tags={"Account"},
     *      description="Get Account",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Account",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Account"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Account $account */
        try{
            $account = $this->accountRepository->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            $account = [];
        }

        if (empty($account)) {
            return Response::json(ResponseUtil::makeError('Account not found'), 404);
        }

        $resource = new Fractal\Resource\Item($account, new AccountTransformer());
        return $this->sendResponse($this->fractal->createData($resource)->toArray()['data'], 'Account retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAccountAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/accounts/{id}",
     *      summary="Update the specified Account in storage",
     *      tags={"Account"},
     *      description="Update Account",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Account",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Account that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Account")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Account"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    private function update($id, UpdateAccountAPIRequest $request)
    {
        $input = $request->all();

        /** @var Account $account */
        $account = $this->accountRepository->find($id);

        if (empty($account)) {
            return Response::json(ResponseUtil::makeError('Account not found'), 404);
        }

        $account = $this->accountRepository->update($input, $id);

        return $this->sendResponse($account->toArray(), 'Account updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/accounts/{id}",
     *      summary="Remove the specified Account from storage",
     *      tags={"Account"},
     *      description="Delete Account",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Account",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    private function destroy($id)
    {
        /** @var Account $account */
        $account = $this->accountRepository->find($id);

        if (empty($account)) {
            return Response::json(ResponseUtil::makeError('Account not found'), 404);
        }

        $account->delete();

        return $this->sendResponse($id, 'Account deleted successfully');
    }

    public function sendMoney($id, Request $request){
        $input = $request->get("fundsToSend");
        $senderAccount = $this->accountRepository->findByField('user_email',\Authorizer::getResourceOwnerId());

        try{
            $receiverAccount = $this->accountRepository->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return Response::json(ResponseUtil::makeError('Invalid account'), 404);
        }

        if($senderAccount[0]->balance <= (double) $input) {
            return Response::json(ResponseUtil::makeError('Funds not available'), 404);
        }elseif($senderAccount[0]->id == $receiverAccount->id){
            return Response::json(ResponseUtil::makeError('Cannot transfer fund between same account.'), 404);
        }
        $senderAccount = $this->accountRepository->with('user')->update([ 'balance' => $senderAccount[0]->balance - $input] , $senderAccount[0]->id);
                   $this->accountRepository->update(['balance' => $input + $receiverAccount->balance] , $receiverAccount->id);


        $resource = new Fractal\Resource\Item($senderAccount, new AccountTransformer());
        //return $this->sendResponse($usersArray->toArray(), 'users retrieved successfully');
        return $this->sendResponse($this->fractal->createData($resource)->toArray()['data'], 'Account updated successfully');
        //return $this->sendResponse($senderAccount->toArray(), 'Account updated successfully');
    }
}
