<?php

namespace App\Jobs;

use GitHub;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostGithubIssue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $body;
    protected $labels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $title, string $body = null, array $labels = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->labels = $labels;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $owner = config('settings.github_repo_owner');
        $repo = config('settings.github_repo_name');

        GitHub::issues()->create($owner, $repo, [
            'title' => $this->title,
            'body' => $this->body,
            'labels' => $this->labels
        ]);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::error("A GitHub issue could not be posted. Please check your environment file and settings.php config file.\n\nMessage: {$exception->getMessage()}\n\nTitle: {$this->title}\nBody: {$this->body}\nLabels: {$this->labels}");
    }
}
