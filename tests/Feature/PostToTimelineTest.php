<?php

namespace Tests\Feature;

use App\Post;
use App\User;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }
    
    /** @test */
    
    public function a_user_can_post_a_text_post()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user =  factory(User::class)->create(), 'api');
        
        $response = $this->post('/api/posts', [
            
            "body" => "Testing body",
        ]);

        $post =  Post::first();
        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing body', $post->body);

        $response->assertStatus(201)
        
            ->assertJson([
                'data'=>[
                    'type'      =>  'posts',
                    'post_id'   =>  $post->id,
                    'attributes'=>  [
                        'posted_by' => [
                              
                                'data' => [

                                    'attributes' =>[

                                        'name' => $user->name,
                                    ]

                                ]
                               
                        ],
                        'body'  => 'Testing body',
                    ]

                ],
                'links'=>[

                    'self'  =>url('/posts/'. $post->id),

                ]


            ]);
    }

    /** @test */

    public function a_user_can_post_a_text_post_with_an_image()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user =  factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user-post.jpg');

        $response = $this->post('/api/posts', [

            'body' => 'Testing body',
            'image' => $file,
            'width' => 100,
            'height' =>100,
        ]);

        Storage::disk('public')->assertExists('post-images/'.$file->hashName());
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'attributes' =>  [
                        'body'  => 'Testing body',
                        'image' => url('storage/post-images/'.$file->hashName()),
                    ]

                ],
            ]);
    }
}
