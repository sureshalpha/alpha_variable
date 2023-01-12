<?php

namespace Kitamula\Kitchen\Traits;

use Request;
use Illuminate\Support\Facades\Schema;

trait Scopable
{
    public function scopeInTerm($query, $fromColumn = 'from_at', $toColumn = 'to_at')
    {
        $tableName = (new self)->getTable();
        $columnType = Schema::getColumnType($tableName, $toColumn);

        $query->where(function($query)use($fromColumn){
            $query->orWhere($fromColumn, '<=', \Carbon\Carbon::now());
            $query->orWhereNull($fromColumn);
        });
        $query->where(function($query)use($toColumn, $columnType){
            if($columnType == 'date') {
                $query->orWhereDate($toColumn, '>=', \Carbon\Carbon::today());
            }else{
                $query->orWhere($toColumn, '>=', \Carbon\Carbon::now());
            }
            $query->orWhereNull($toColumn);
        });
        return $query;
    }

    public function scopeWord($query, $text, $columns)
    {
        if (!empty($text)) {
            $text = '%'.addcslashes($text, '%_\\').'%';
            $query->where(function ($query) use ($text, $columns) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', $text);
                }
            });
        }
        return $query;
    }

    public function scopePublished($query)
    {
        $query->displayed();
        $query->inTerm();
        return $query;
    }

    public function scopeDisplayed($query)
    {
        $query->where('is_display', TRUE);
        return $query;
    }

    /**
    * Attributes
    */
    public function getIsPublishedAttribute()
    {
        return self::where('id',$this->id)->published()->exists();
    }

    /**
    * 指定カラムをグループにして取り出す
    *
    * @param int $count カラム数
    * @param array $columsn ['url' => 'url_*', 'text'=>'url_*_text'] 等としてグループ化したいカラムを指定。*は1〜$countまで代入される。
    * @param array|string $mainColumns ['image_*'] 等としてメインカラムを指定。メインカラムがemptyの場合はグループの取り出しがスキップされる。
    */
    public function columnGroup(int $count, array $columns, $mainColumns)
    {
        $records = new \Illuminate\Support\Collection();
        for ($i=1; $i <= $count; $i++) {
            // メインカラムとして指定したカラムのうち、1カラムでも有効なレコードがあればグループとして取得
            $isExistMainColumn = 0;

            if(is_array($mainColumns)){
                foreach ($mainColumns as $mainColumn) {
                    $column = str_replace('*', $i, $mainColumn);
                    if(!empty($this->$column)) {
                        $isExistMainColumn++;
                    }
                }
            }else{
                $column = str_replace('*', $i, $mainColumns);
                if(!empty($this->$column)) {
                    $isExistMainColumn++;
                }
            }

            if($isExistMainColumn){
                $record = new \Illuminate\Support\Collection();
                foreach ($columns as $key => $column) {
                    $column = str_replace('*', $i, $column);
                    $record->$key = $this->$column;
                }
                $records->push($record);
            }
        }

        return $records;
    }
}
