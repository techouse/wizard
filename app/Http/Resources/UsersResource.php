<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return ['data' => UserResource::collection($this->collection)];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function with($request)
    {
        if ($request->input('sort')) {
            $sort = explode('|', $request->input('sort'));
        } else {
            $sort = ['id', 'asc'];
        }

        return [
            'links' => ['self' => route('api.users.index')],
            'meta'  => ['sort' => ['by'        => $sort[0],
                                   'direction' => $sort[1]]],
        ];
    }
}
