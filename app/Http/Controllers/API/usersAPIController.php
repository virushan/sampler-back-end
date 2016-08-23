<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateusersAPIRequest;
use App\Http\Requests\API\UpdateusersAPIRequest;
use App\Models\users;
use App\Repositories\usersRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use InfyOm\Generator\Utils\ResponseUtil;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Transformers\UsersTransformer;
use League\Fractal;
use League\Fractal\Manager;

/**
 * Class usersController
 * @package App\Http\Controllers\API
 */

class usersAPIController extends InfyOmBaseController
{
    /** @var  usersRepository */
    private $usersRepository;

    public function __construct(usersRepository $usersRepo, Manager $fractal)
    {
        $this->usersRepository = $usersRepo;
        $this->fractal = $fractal;
        $this->middleware('oauth', ['except' => ['store', 'signIn']]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/users",
     *      summary="Get a listing of the users.",
     *      tags={"users"},
     *      description="Get all users",
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
     *                  @SWG\Items(ref="#/definitions/users")
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
        $this->usersRepository->pushCriteria(new RequestCriteria($request));
        $this->usersRepository->pushCriteria(new LimitOffsetCriteria($request));
        $usersArray = $this->usersRepository->with('account')->all();

        $resource = new Fractal\Resource\Collection($usersArray, new UsersTransformer());
        //return $this->sendResponse($usersArray->toArray(), 'users retrieved successfully');
        return $this->sendResponse($this->fractal->createData($resource)->toArray()['data'], 'users retrieved successfully');
    }

    /**
     * @param CreateusersAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/users",
     *      summary="Store a newly created users in storage",
     *      tags={"users"},
     *      description="Store users",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="users that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/users")
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
     *                  ref="#/definitions/users"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateusersAPIRequest $request)
    {
        $input = $request->all();
        $user = $this->usersRepository->create($input);
        $resource = new Fractal\Resource\Item($user, new UsersTransformer());
        return $this->sendResponse($this->fractal->createData($resource)->toArray()['data'], 'users saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/users/{id}",
     *      summary="Display the specified users",
     *      tags={"users"},
     *      description="Get users",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of users",
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
     *                  ref="#/definitions/users"
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
        /** @var users $users */
        try{
            $user = $this->usersRepository->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            $user = [];
        }

        if (empty($user)) {
            return Response::json(ResponseUtil::makeError('users not found'), 404);
        }
        $resource = new Fractal\Resource\Item($user, new UsersTransformer());
        return $this->sendResponse($this->fractal->createData($resource)->toArray()['data'], 'users retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateusersAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/users/{id}",
     *      summary="Update the specified users in storage",
     *      tags={"users"},
     *      description="Update users",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of users",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="users that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/users")
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
     *                  ref="#/definitions/users"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateusersAPIRequest $request)
    {
        $input = $request->all();

        /** @var users $users */
        try {
            $users = $this->usersRepository->find($id);
        }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            $users = [];
        }

        if (empty($users)) {
            return Response::json(ResponseUtil::makeError('users not found'), 404);
        }

        $users = $this->usersRepository->update($input, $id);

        return $this->sendResponse($users->toArray(), 'users updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/users/{id}",
     *      summary="Remove the specified users from storage",
     *      tags={"users"},
     *      description="Delete users",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of users",
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
        /** @var users $users */
        try {
            $users = $this->usersRepository->find($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            $users = [];
        }

        if (empty($users)) {
            return Response::json(ResponseUtil::makeError('users not found'), 404);
        }

        $users->delete();

        return $this->sendResponse($id, 'users deleted successfully');
    }

    public function signIn(Request $request){
        $auth = \Authorizer::issueAccessToken();
        try{
            $user = $this->usersRepository->find($request->get('username'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            $user = [];
        }

        if (empty($user)) {
            return Response::json(ResponseUtil::makeError('users not found'), 404);
        }
        $resource = new Fractal\Resource\Item($user, new UsersTransformer());
        return $this->sendResponse(array_merge($this->fractal->createData($resource)->toArray()['data'], $auth), 'users retrieved successfully');
    }

    public function signOut(){
        \Authorizer::getChecker()->getAccessToken()->expire();
        return \Response::json(['success' => true, 'message' => 'user signed out successfully']);
    }
}
