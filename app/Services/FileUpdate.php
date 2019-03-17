<?php 

namespace XigeCloud\Services;

use XigeCloud\Models\Tag;
use XigeCloud\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use XigeCloud\Models\FileLog as FileLogModel;

class FileUpdate
{
    protected $dirty;

    protected $tabID;

    protected $model;

    public function __construct(int $tabID, $number)
    {
        $this->tabID = $tabID;
        $this->model = File::cate($tabID)->find($number);
    }

    public function update(array $data)
    {
        $this->dirty = [];
        $fun = function () use ($data) {
            $this->model->fill($data);
            $this->addDirty();
            $this->model->save();
            $this->changeTags($data);
            !empty($this->dirty) && $this->recordLog();

            return $this->model;
        };
        return $this->model->getConnection()->transaction($fun);
    }

    /**
     * 更新文件标签.
     * 
     * @param  array $data
     * @return void
     */
    protected function changeTags(array $data)
    {
        if (array_has($data, 'tags')) {
            $tags = $data['tags'] ?? [];
            $relationQuery = $this->model->tags();
            $original = $relationQuery->pluck('id');
            $changed = ['attached' => [], 'detached' => []];
            if (empty($tags)) {
                $changed['detached'] = $original->all();
            } else {
                $values = collect($tags);
                $diff = $original->diff($values)->values()->all();
                $diffOri = $values->diff($original)->values()->all();
                $changed['attached'] = $diffOri;
                $changed['detached'] = $diff;
            }
            // 同步标签🐎
            $relationQuery->sync($tags);

            if (!empty(array_filter($changed))) {
                $this->dirty['tags'] = $changed;
            }
        }
    }

    protected function addDirty()
    {
        $dirty = [];
        foreach ($this->model->getDirty() as $key => $value) {
            $dirty[$key] = [
                'original' => $this->model->getOriginal($key, ''),
                'dirty' => $value,
            ];
        }
        $this->dirty = array_collapse([$this->dirty, $dirty]);
    }

    /**
     * 记录文件日志
     * 
     * @return void
     */
    public function recordLog()
    {
        $model = new FileLogModel;
        $model->user_id = getStaff();
        $model->changes = $this->trans();
        $model->operate_at = now()->toDateString();
        $model->file_number = $this->model->number;
        $model->operate_type = 'edit';
        $model->save();
    }

    /**
     * 翻译文件变更字段.
     * 
     * @return array
     */
    protected function trans(): array
    {
        $changes = [];
        $localization = trans('fields.file');
        foreach ($this->dirty as $key => $change) {
            if ($key === 'tags') {
                $key = $localization[$key];
                $value = array_filter([
                    '添加' => Tag::whereIn('id', $change['attached'])->pluck('name')->all(),
                    '删除' => Tag::whereIn('id', $change['detached'])->pluck('name')->all(),
                ]);
            } elseif (is_array($change)) {
                $key = $localization[$key];
                $value = array_values($change);
            } else {
                $key = $localization[$key];
                $value = $change;
            }
            $changes[$key] = $value;
        }
        return $changes;
    }
}