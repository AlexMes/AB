<?php

namespace Tests\Feature\Comments;

use App\Facebook\Ad;
use App\Facebook\Profile;
use App\Facebook\SentComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SentCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itHasProfile()
    {
        $comment = factory(SentComment::class)->create();

        $this->assertNotNull($comment->fresh()->profile);
        $this->assertInstanceOf(Profile::class, $comment->profile);
    }

    /** @test */
    public function itHasAd()
    {
        $comment = factory(SentComment::class)->create();

        $this->assertNotNull($comment->fresh()->ad);
        $this->assertInstanceOf(Ad::class, $comment->ad);
    }
}
