<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function all($with=[]);
    public function create(array $data);
    public function updateWhere($data, $where_clause=[]);
    public function delete($where_clause);
    public function get($value, $where_clause=[],$with=[]);
    public function alreadyExist($where_clause);
    public function restore($id);

}
