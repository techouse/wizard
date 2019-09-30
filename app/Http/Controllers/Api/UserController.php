<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BasicPagination;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Http\Requests\BasicPagination $request
     * @return \App\Http\Resources\UsersResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(BasicPagination $request): UsersResource
    {
        $this->authorize('viewAny', User::class);

        $request->validate(['email'  => ['nullable', 'string', 'min:1', 'max:255'],
                            'name'   => ['nullable', 'string', 'min:1'],
                            'search' => ['nullable', 'string', 'min:1'],]);

        return new UsersResource(
            User
                ::when($request->input('name'), function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->input('name')}%");
                })
                ->when($request->input('email'), function ($query) use ($request) {
                    $query->where('email', 'like', "%{$request->input('email')}%");
                })
                ->when($request->input('search'), function ($query) use ($request) {
                    $query->where(function (Builder $query) use ($request) {
                        $query->where('name', 'like', "%{$request->input('search')}%")
                              ->orWhere('email', 'like', "%{$request->input('search')}%");
                    });
                })
                ->when($request->input('sort'),
                    function (Builder $query) use ($request) {
                        $query->orderBy(...explode('|', $request->input('sort', 'id|asc')));
                    },
                    function (Builder $query) {
                        $query->orderBy('id', 'asc');
                    })
                ->when($request->input('no_paginate'),
                    function (Builder $query) {
                        return $query->get();
                    },
                    function (Builder $query) use ($request) {
                        return $query->paginate($request->input('per_page', Controller::DEFAULT_ITEMS_PER_PAGE))
                                     ->appends(['sort'     => $request->input('sort', Controller::DEFAULT_SORT),
                                                'per_page' => $request->input('per_page', Controller::DEFAULT_ITEMS_PER_PAGE)]);
                    })
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request): UserResource
    {
        $this->authorize('create', User::class);

        $data = $request->validate(['name'     => ['required', 'bail', 'string', 'min:1', 'max:255'],
                                    'email'    => ['required', 'bail', 'email', 'unique:users,email'],
                                    'password' => ['required', 'bail', 'confirmed', 'min:8'],
                                    'role'     => ['nullable', 'in:' . implode(',', ['administrator', 'user'])]]);

        return new UserResource(User::create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return \App\Http\Resources\UserResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Get currently logged in user as a resource
     *
     * @return \App\Http\Resources\UserResource
     */
    public function me(): UserResource
    {
        return new UserResource(Auth::user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user): Response
    {
        $this->authorize('update', $user);

        $data = $request->validate(['name'     => ['required', 'bail', 'string', 'min:1', 'max:255'],
                                    'email'    => ['required', 'bail', 'email', Rule::unique('users')->ignore($user->id)],
                                    'password' => ['nullable', 'confirmed', 'min:8'],
                                    'role'     => ['nullable', 'in:' . implode(',', ['administrator', 'user'])]]);

        if (!Auth::user()->isAdministrator()) {
            unset($data['role']);
        }

        $user->update($data);

        return response(['message' => 'OK'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }

    /**
     * Remove multiple resources from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function bulkDestroy(Request $request): Response
    {
        $this->authorize('deleteMany', User::class);

        $request->validate(['ids'   => ['required', 'bail', 'array', 'min:1'],
                            'ids.*' => ['required', 'numeric', 'min:1', 'exists:users,id']]);

        User::whereIn('id', $request->input('ids'))
            ->delete();

        return response()->noContent();
    }
}
