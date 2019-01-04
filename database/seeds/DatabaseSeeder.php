<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach (Config::get('const.todo_types') as $k => $v) {
            $todo_type = new \App\Models\TodoTypes([
                'id' => $v,
                'type' => "todo_types.{$k}",
            ]);
            $todo_type->save();
        }
    }
}
