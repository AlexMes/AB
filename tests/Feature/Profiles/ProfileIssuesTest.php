<?php


namespace Tests\Feature\Profiles;

use App\Facebook\Profile;
use App\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileIssuesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itHasIssues()
    {
        $profile = factory(Profile::class)->create();

        $e = new \Exception('Whatever');

        $profile->addIssue($e);

        $this->assertDatabaseHas('issues', ['id' => 1, 'message' => 'Whatever']);

        $this->assertTrue($profile->issues->first()->is(Issue::first()), 'Issue not attached to profile');
    }

    /** @test */
    public function itKnowsAboutHavingOpenIssues()
    {
        $profile = factory(Profile::class)->create();

        $e = new \Exception('Whatever');

        $profile->addIssue($e);

        $this->assertTrue($profile->hasIssues(), 'hasIssues works incorrectly');
    }

    /** @test */
    public function itKnowsHowClearOpenIssue()
    {
        $profile = factory(Profile::class)->create();

        $e = new \Exception('Whatever');

        $profile->addIssue($e);

        $profile->clearIssue(Issue::first());

        $this->assertFalse($profile->hasIssues(), 'Reacted on cleared issue');
    }

    /** @test */
    public function itClearsMultipleIssues()
    {
        $profile = factory(Profile::class)->create();

        $e = new \Exception('Whatever');

        $profile->addIssue($e);

        $profile->clearIssue();

        $this->assertFalse($profile->hasIssues(), 'Reacted on cleared issue');
    }
}
