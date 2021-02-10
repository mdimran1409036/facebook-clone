<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\VarDumper\Cloner\Data;
use Tests\TestCase;

class RetrievePostsTest extends TestCase
{
    /** @test */

   use RefreshDatabase;
   public function a_user_can_retrieve_posts()
   {

       $this->withoutExceptionHandling(); 
       $this->actingAs($user = factory(User::class)->create(), 'api');
       $posts = factory(Post::class, 2)->create(['user_id' => $user->id]);

       $response = $this->get('/api/posts');

       $response->assertStatus(200)
            ->assertJson([

                'data' => [

                    [

                        'data' => [

                            'type'       =>     'posts',
                            'post_id'    =>     $posts->last()->id,
                            'attributes' =>     [

                                'body' => $posts->last()->body,


                            ]
                        ]

                    ],
                    [

                        'data' => [

                            'type'       =>     'posts',
                            'post_id'    =>     $posts->first()->id,
                            'attributes' =>     [

                                'body' => $posts->first()->body,


                             ]
                        ]

                    ],
                    
                ],
                'links' => [

                    'self' => url('/posts/'),
                ]
              
            ]);


   }


   /** @test */

   public function a_user_can_only_retrieve_thier_posts()
   {


       $this->actingAs($user = factory(User::class)->create(), 'api');
       $posts = factory(Post::class)->create();


       $response =  $this->get('/api/posts');
       
       $response->assertStatus(200)
        ->assertExactJson([
            'data' => [],
            'links' => [
                'self' => url('/posts'),
            ]


        ]);

   } 

}