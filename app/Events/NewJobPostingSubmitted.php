<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\JobPosting;

class NewJobPostingSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobPosting;

    /**
     * Create a new event instance.
     */
    public function __construct(JobPosting $jobPosting)
    {
        $this->jobPosting = $jobPosting;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-job-posting';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'job' => [
                'id' => $this->jobPosting->id,
                'title' => $this->jobPosting->title,
                'company' => $this->jobPosting->company,
                'location' => $this->jobPosting->location,
                'employment_type' => $this->jobPosting->employment_type,
                'posted_by' => $this->jobPosting->postedBy->name,
                'created_at' => $this->jobPosting->created_at->diffForHumans(),
            ],
            'message' => 'New job posting submitted for review',
            'url' => route('admin.job-postings'),
        ];
    }
}
