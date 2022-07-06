<?php

namespace App\Repositories\Settings;

use App\Models\Settings\Tag;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class TagRepository.
 */
class TagRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'code',
        'name'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tag::class;
    }

    public function getCount()
    {
        $query = $this->model->newQuery();

        $result = $query
            ->whereNull('deleted_at')
            ->count();

        return $result;
    }

    public function getAll()
    {
        $sql = "SELECT T.id, T.code, T.name FROM tag T";

        $result = DB::select($sql);

        return $result;
    }

    public function getForSelect($placeholder = false)
    {
        $result = [];

        if ($placeholder) {
            $result[null] = __('app.Select Tag');
        }

        $items = $this->getAll();

        foreach ($items as $item) {
            $result[$item->id] = $item->name;
        }

        return $result;
    }
}
