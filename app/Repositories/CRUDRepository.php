<?php

namespace App\Repositories;

use App\Interfaces\CRUDRepositoryInterface;

class CRUDRepository implements CRUDRepositoryInterface
{
    public function getAllItems($model, array $filters = [], $perPage = 50, $latest = true)
    {
        $query = $model::query();
        
        if ($latest) {
            $query->latest();
        }

        if (isset($filters['ids']) && is_array($filters['ids']) && !empty($filters['ids'])) {
            $query->whereIn('id', $filters['ids']);
        }

        if (isset($filters['not_ids']) && is_array($filters['not_ids']) && !empty($filters['not_ids'])) {
            $query->whereNotIn('id', $filters['not_ids']);
        }

        if (method_exists($model, 'scopeFilter')) {
            $query->filter($filters);
        }

        if ($perPage) {
            // retain array filters in pagination links
            return $query->paginate($perPage)->appends(request()->all());
        }

        return $query->get();
    }

    public function getItemById($model, $id)
    {
        return $model::findOrFail($id);
    }

    public function createItem($model, array $data)
    {
        return $model::create($data);
    }

    public function updateItem($model, $id, array $data)
    {
        $item = $this->getItemById($model, $id);
        $item->update($data);
        return $item;
    }

    public function deleteItem($model, $id)
    {
        $item = $this->getItemById($model, $id);
        return $item->delete();
    }

    public function toggleStatus($model, $id, $column = 'active')
    {
        $item = $this->getItemById($model, $id);
        $item->$column = !$item->$column;
        $item->save();
        return $item;
    }

    public function getTrashedItems($model, array $filters = [], $perPage = 50)
    {
        $query = $model::onlyTrashed();

        if (method_exists($model, 'scopeFilter')) {
            $query->filter($filters);
        }

        return $query->latest()->paginate($perPage)->appends(request()->all());
    }

    public function restoreItem($model, $id)
    {
        $item = $model::onlyTrashed()->findOrFail($id);
        $item->restore();
        return $item;
    }

    public function forceDeleteItem($model, $id)
    {
        $item = $model::onlyTrashed()->findOrFail($id);
        return $item->forceDelete();
    }

    public function getCount($model, array $filters = [])
    {
        $query = $model::query();
        if (method_exists($model, 'scopeFilter')) {
            $query->filter($filters);
        }
        return $query->count();
    }
}
