<?php

namespace App\Http\Controllers;

use App;
use App\Change;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class UserController extends Controller
{


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $auth_user = JWTAuth::parseToken()->authenticate();

        // if is super admin
        if ($auth_user->role === '1') {

            $users = User::with([
                'devices',
                'invoices',
                'files',
                'client',
                'bankAccounts',
                'changes'
            ])
//                ->latest('created_at')
                ->orderBy('ID', 'ASC')
                ->paginate(10);
//                ->get();

        } elseif ($auth_user->role === '9') {

            $users = User::with(['devices', 'invoices', 'bankAccounts'])->latest('created_at')->find($auth_user->id);

        }

        return response()->json(array('data' => $users), 200);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        // handle fields
        $user->update($request->toArray());
        app()->setLocale('en');
        return response()->json(array('data' => $user), 200);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user_auth = JWTAuth::parseToken()->authenticate();
        if ($user_auth->role === '1') {
            $user->delete();
            return response()->json(array('data' => 'success'), 200);
        }
        return response()->json(null, 404);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user, Request $request)
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        // superadmin id 1
        if ($auth_user->role === "1") {
            return response()->json(array('data' => $user), 200);
        }
        return response()->json(array('data' => false), 404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCandidates(Request $request)
    {
        $candidates = DB::connection('searger')
            ->table('candidates')
            ->orderBy('id', 'DESC')
            ->take(10)
            ->get();
        return response()->json(array('data' => $candidates));
    }

    /**
     * @param Request $request
     * @return UserResource
     */
    public function getAllContractors(Request $request)
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        $contractors = DB::table('users')
            ->where('role', '=', '9')
            ->where('is_active', '=', true)
            ->get(['id', 'name', 'surname']);
        if ($auth_user->role === "1") {
            return new UserResource($contractors);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function candidateChangeRequest(Request $request)
    {
        $this->validate($request, [
            'data' => 'required'
        ]);
        $auth_user = JWTAuth::parseToken()->authenticate();

        if ($auth_user) {
            $id = $auth_user->id;

            foreach ($request->data as $change) {

                Change::create([
                    'user_id' => $id,
                    'field_name' => $change['field_name'],
                    'old_value' => $change['old_value'],
                    'new_value' => $change['new_value']
                ]);
            }

            return response()->json(array('data' => 'success'), 201);
        }
        return response()->json(array('data' => false), 404);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function candidateChangeRequestUpdate(Request $request)
    {
        // update user
        if ($request->accepted === true) {
            // new value
            User::where('id', $request->row['user_id'])
                ->update([
                    $request->row['field_name'] => $request->row['new_value']
                ]);
        } else {
            // back to old value
            User::where('id', $request->row['user_id'])
                ->update([
                    $request->row['field_name'] => $request->row['old_value']
                ]);
        }
        // update change request
        Change::where('id', $request->id)->update($request->only(['accepted']));

        return response()->json(array('status' => true), 200);
    }

    /**
     * @param Request $request
     * @param Change $change
     * @return \Illuminate\Http\JsonResponse
     */
    public function candidateChangeRequestDelete(Request $request, Change $change)
    {
        Change::where('id', $request->id)->delete();
        return response()->json(null, 204);
    }

}
