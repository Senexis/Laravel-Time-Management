<?php

namespace App\Http\Controllers;

use App\Project;
use App\TimeEntry;
use App\User;
use App\UserLocation;
use App\WorkType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeularController extends Controller
{
    public function TimeularWebhook(Request $request)
    {
        // Event type
        $eventType = $request->input('eventType');
        if (empty($eventType))
            abort(400, "No event type supplied.");
        if ($eventType != 'trackingStopped')
            abort(400, "Event wasn't stopped.");

        // User
        $userId = intval($request->input('userId'));
        $user = $this->GetUserByTimeularId($userId);
        if (!$user)
            abort(400, "No user found.");

        // Timeular Entry
        $data = $request->input('data');
        $entry = $data['newTimeEntry'];
        if ($entry == null)
            abort(400, "No new Timeular entry.");

        // Timeular Entry ID
        $entryId = intval($entry['id']);
        $existingEntry = TimeEntry::where('timeular_entry_id', $entryId)->first();
        if ($existingEntry != null)
            abort(400, "Time entry with that Timeular ID already exists.");

        // Project
        $activityName = $entry['activity']['name'];
        $projectId = $this->GetProjectId($activityName);
        $project = Project::where('id', $projectId)->first();
        if ($project == null)
            abort(400, "Project doesn't exist.");

        // Time
        if ($entry['duration'] == null || $entry['duration']['startedAt'] == null || $entry['duration']['stoppedAt'] == null)
            abort(400, "The time entry's duration wasn't provided.");

        $startedAt = new Carbon($entry['duration']['startedAt']);
        $stoppedAt = new Carbon($entry['duration']['stoppedAt']);

        if ($stoppedAt->lte($startedAt))
            abort(400, "Stopped is before started or is equal to it.");
        if ($stoppedAt->diffInMinutes($startedAt) < 1)
            abort(400, "Entry is shorter than a minute.");

        // Location
        $location = $user->last_location;
        if ($location == null) {
            $firstLocation = UserLocation::select('id')->where('user_id', $user->id)->first();
            $location = $firstLocation->id;
        }
        if ($location == null)
            abort(400, "User didn't have a location.");

        // Work Type
        $workTypeId = 6;
        $workType = WorkType::where('id', $workTypeId)->first();
        if ($workType == null) {
            $firstWorkType = WorkType::select('id')->first();
            $workTypeId = $firstWorkType->id;
        }
        if ($location == null)
            abort(400, "There were no work types.");

        // Notes
        $notes = $entry['note']['text'];

        // Write the time entry for the user to the database.
        $result = $user->time_entries()->create([
            'project_id' => $project->id,
            'work_type_id' => $workTypeId,
            'location_id' => $location,
            'start_time' => $startedAt,
            'end_time' => $stoppedAt,
            'notes' => $notes,
            'timeular_entry_id' => $entryId,
        ]);

        return json_encode($result);
    }

    private function GetUserByTimeularId(int $timeularUserId): ?User
    {
        return User::where('timeular_id', $timeularUserId)->first();
    }

    /**
     * Example input value:  "Organization (#1)"
     * Example output value: 1
     */
    private function GetProjectId(string $activityName): ?int
    {
        $re = '/.*\(\#([0-9]+)\)/m';
        if (!preg_match($re, $activityName, $matches)) return null;

        if (count($matches) < 2) return null;

        $match = $matches[1];

        $int_value = ctype_digit($match) ? intval($match) : null;
        if ($int_value === null) return null;

        return $int_value;
    }
}
