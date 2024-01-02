<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function save($data)
    {
        $model = new $this->model($data);
        $model->save();
        return $model;
    }

    public function update($id, $data)
    {
        $model = $this->model->findOrFail($id);
        $model->fill($data);
        $model->save();
        return $model;
    }

    public function delete($id)
    {
        $model = $this->model->findOrFail($id);
        $model->delete();
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param array $filter
     * @param array $sort
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate($page = 1, $perPage = 10, $filter = '', $sort = '', $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model->query();
        $filter = json_decode($filter, true) ?? [];
        $sort = json_decode($sort, true) ?? [];
        // Apply filters
        foreach ($filter as $criteria) {
            $criteria = json_decode($criteria, true);
            $field = $criteria['field'];
            $value = $criteria['value'];
            $conditionType = $criteria['condition_type'];

            switch ($conditionType) {
                case 'eq':
                    $query->where($field, '=', $value);
                    break;
                case 'not_eq':
                    $query->where($field, '<>', $value);
                    break;
                case 'lt':
                    $query->where($field, '<', $value);
                    break;
                case 'gt':
                    $query->where($field, '>', $value);
                    break;
                case 'lteq':
                    $query->where($field, '<=', $value);
                    break;
                case 'gteq':
                    $query->where($field, '>=', $value);
                    break;
                case 'in':
                    $query->whereIn($field, $value);
                    break;
                case 'matches':
                    $query->where($field, 'ILIKE', '%' . $value . '%');
                    break;
                case 'in_any':
                    $query->orWhereIn($field, $value);
                    break;
                default:
                    $query->where($field, $value);
                    break;
            }
        }

        // Apply sorting
        foreach ($sort as $field => $direction) {
            $query->orderBy($field, $direction);
        }
        return $query->paginate($perPage, $columns, 'page', $page);
    }
}
