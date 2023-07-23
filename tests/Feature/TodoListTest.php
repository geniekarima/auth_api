<?php

namespace Tests\Feature;

use App\Models\TodoList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;
    public function test_store_todo_list(): void
    {
        TodoList::create(['name' => 'my list']);
        // $this->withoutExceptionHandling();


        // $response = $this->get('/');

        // $response->assertStatus(200);



        //preparation//prepare


        //Action//perform
        $response = $this->getJson(route('todo-list.store'));
//dd($response->json());

        //Assertion//predict
        $this->assertEquals(1,count($response->json()));

    }
}
