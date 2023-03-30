<?php

namespace App\Http\Controllers;

use App\Jobs\PostGithubIssue;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:send.feedback');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $title = ucfirst($request->title);
        $message = ucfirst($request->message);

        if (empty($title) || strlen($title) > 128 || empty($message) || strlen($message) > 1024) {
            return redirect()->back();
        }

        $host = $request->include_host ? $request->host : 'Not available.';
        $prev_url = url()->previous();
        $full_url = ($request->include_host && $request->include_url) ? "[{$prev_url}]({$prev_url})" : 'Not available.';

        switch ($request->category) {
            case 'bug':
                $category = 'Bug';
                break;

            case 'suggestion':
                $category = 'Enhancement';
                break;

            case 'feature':
                $category = 'Feature Request';
                break;

            default:
                $category = 'Other';
                break;
        }

        $body = "{$user->name} left some feedback. The feedback's details are available below.\n\n## User information\n**Name:** {$user->name}\n**Email:** {$user->email}\n\n## Feedback\n**Category:** {$category}\n**Host:** {$host}\n**Link:** {$full_url}\n\n**Title:**\n{$title}\n\n**Message:**\n{$message}";
        $labels = ['Feedback', $category];

        PostGithubIssue::dispatch($title, $body, $labels);

        return redirect()->back()->with('alert-info', __('app.layout_feedback_message_success'));
    }
}
