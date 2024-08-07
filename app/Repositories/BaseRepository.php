<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all($with=[], $perPage = 15)
    {
        if (empty($with)){
            return $this->model->paginate($perPage);
        }else{
            return $this->model->with($with)->paginate($perPage);
        }
    }


    public function create(array $data)
    {
        return $this->model->create($data);
    }

    private function _build_where_clause($data, $where_clause){
        if (empty($where_clause)) {
            if (!empty($data['id'])) {
                $where_clause = ['id' => $data['id']];
            } else {
                return false;
            }
        } else {
            $where_clause = is_array($where_clause) ? $where_clause : ['id' => $where_clause];
        }
        return $where_clause;
    }

    public function updateWhere($data, $where_clause = [])
    {
        $where_clause = $this->_build_where_clause($data,$where_clause);

        if (!empty($data['id'])) {
            unset($data['id']);
        }
        $query = $this->model->where($where_clause);

        return $query->update($data);
    }

    public function delete($where_clause)
    {
        if (empty($where_clause)) {
            return false;
        } else {
            $where_clause = is_array($where_clause) ? $where_clause : ['id' => $where_clause];
        }

        if(!empty($where_clause['id'])){
            $model =  $this->model->findOrFail($where_clause['id']);
        }else{
            $model = $this->model->find($where_clause);
        }

        if ($model) {
            $model->delete();
            return true;
        }
        return false;
    }

    public function get($value='', $where_clause=[], $with=[])
    {
        if (empty($where_clause)) {
            $where_clause = ['id' => $value];
        } else {
            $where_clause = is_array($where_clause) ? $where_clause : ['id' => $where_clause];
        }
        if (empty($with)){
            $results =  $this->model->where($where_clause)->get();
        }else{
            $results =  $this->model->where($where_clause)->with($with)->get();
        }
        if ($results->isEmpty()) {
            return false;
        }
        return $results;
    }

    public function alreadyExist($where_clause)
    {
        if (empty($where_clause)) {
            return false;
        } else {
            $where_clause = is_array($where_clause) ? $where_clause : ['id' => $where_clause];
        }
        return $this->model->where($where_clause)->exists();
    }

    public function restore($id){
        return  $this->model->withTrashed()->find($id);
    }

    public function getTableName()
    {
        return $this->model->getTable();
    }

    public function search($keyword, $latitude = null, $longitude = null, $radius = 10)
    {
        $query = $this->model->newQuery();

        // Search by keyword in service name or category name
        $query->where(function($query) use ($keyword) {
            $query->where('name', 'LIKE', "%{$keyword}%")
                ->orWhereHas('category', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%");
                });
        });

        if ($latitude !== null && $longitude !== null) {
            $query->selectRaw("
            *, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius);
        }
        return $query->get();
    }

}
