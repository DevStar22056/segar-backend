<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Project;
use App\ProjectUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')
            ->with(['project_users'])
            ->paginate(2);
//            ->get();
        return response()->json(array('data' => $projects), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        dd($request);

        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        $project = Project::create([
            'name' => $validatedData['name'],
            'description' => $request->description,
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'client_id' => $request->client_id
        ]);

        if (!empty($request->selectedUsers)) {
            foreach ($request->selectedUsers as $selectedUser) {
                $project_user = new ProjectUser;
                $project_user->fill([
                    'project_id' => $project->id,
                    'client_id' => $request->client_id,
                    'user_id' => $selectedUser['value'],
                ]);
                $project_user->save();
            }
        }

        return new ProjectResource([$project->id]);
    }

    /**
     * @param Project $
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project, Request $request)
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        // superadmin id 1
        if ($auth_user->role === "1") {
            $project = DB::table('project_users')
                ->where('project_id', '=', $project->id)
                ->leftJoin('users', 'project_users.user_id', '=', 'users.id')
                ->get(['name', 'surname', 'internal_hour_rate', 'project_id', 'users.id']);
            return response()->json(array('data' => $project), 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Project $projects
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $projects)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Project $projects
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required'
        ]);

        $project->update($request->only(['name', 'description', 'client_id', 'company_name']));

        // remove old
        DB::table('project_users')->where('project_id', '=', $project->id)->delete();

        // put new
        foreach ($request->selectedUsers as $selectedUser) {
            $project_user = new ProjectUser;
            $project_user->fill([
                'project_id' => $project->id,
                'client_id' => $request->client_id,
                'user_id' => $selectedUser['value'],
            ]);
            $project_user->save();
        }

        return response()->json(array('ok' => 'ok'), 200);
    }

    /**
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, 204);
    }

}
