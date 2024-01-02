<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\PaginationRequest;
use App\Http\Traits\HttpResponses;
use App\Models\Team;
use App\Models\User;
use App\Services\UserService;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use HttpResponses;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function index()
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a user",
     *     tags={"Users"},
     *     summary="Create a user",
     *     @OA\RequestBody(
     *       required=true,
     *       description="The User model",
     *       @OA\JsonContent(
     *        @OA\Property(property="name",type="string",example="Test User"),
     *        @OA\Property(property="email",type="string",example="your@email.com"),
     *        @OA\Property(property="password",type="string",example="password"),
     *        @OA\Property(property="password_confirmation",type="string",example="password"),
     *       )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     security={{"sanctum":{}}},
     * )
     */
    public function create(CreateUserRequest $input)
    {
        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }
    /**
     * Create a personal team for the user.
     */
    protected function createTeam(User $user): void
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => explode(' ', $user->name, 2)[0] . "'s Team",
            'personal_team' => true,
        ]));
    }

    /**
     * @OA\Get(
     *     path="/api/users-pagination",
     *     summary="Get a list of users",
     *     description="Get a list of users with pagination",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Filter criteria",
     *         required=false,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="field", type="string"),
     *                 @OA\Property(property="value", type="string"),
     *                 @OA\Property(property="condition_type", type="string",
     *                 enum={"eq", "not_eq", "lt", "gt", "lteq", "gteq", "in", "matches", "in_any"})
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort criteria",
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             example={"name": "desc"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="page",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="perPage",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="totalPages",
     *                     type="integer"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getUser(PaginationRequest $input)
    {
        $users =  $this->userService->findUserPagination($input);
        return $this->success($users);
    }
}