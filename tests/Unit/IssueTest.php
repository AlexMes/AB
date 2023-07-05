<?php

namespace Tests\Unit;

use App\Facebook\Traits\HasIssues;
use App\Issue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function itClearsItSelf()
    {
        $issue = Issue::create([
            'issuable_id'   => 'whatever',
            'issuable_type' => 'whatever',
            'message'       => 'boo',
        ]);
        $issue->clear();

        $this->assertNotNull($issue->fresh()->fixed_at);
    }

    /** @test */
    public function itDoesNotCreateDuplicateIssues()
    {
        $model = new DummyModel();

        $model->addIssue(new \Exception('Some text innit'));
        $this->assertTrue($model->hasIssues());
        $this->assertCount(1, $model->issues()->get());

        $model->addIssue(new \Exception('Some text innit'));
        $this->assertCount(1, $model->issues()->get());
    }
}


class DummyModel extends Model
{
    use HasIssues;

    protected $attributes = ['id' => 1];
}
