<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Credential;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $groups = Group::withCount('credentials')
            ->orderBy('level')
            ->orderBy('name')
            ->get()
            ->filter(fn($g) => $user->canAccessGroup($g, 'read'))
            ->values();

        $navigationTree = $this->getNavigationTree();

        return Inertia::render('Groups/Index', [
            'groups' => $groups,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $groups = Group::orderBy('name')->get()->filter(fn($g) => $user->canAccessGroup($g, 'read'))->values();
            
        $navigationTree = $this->getNavigationTree();
        
        return Inertia::render('Groups/Create', [
            'groups' => $groups,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'parent_id' => 'nullable|exists:groups,id',
        ]);

        Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        $user = auth()->user();
        if (!$user->canAccessGroup($group, 'read')) {
            abort(403);
        }
        
        $credentials = Credential::where('group_id', $group->id)
            ->orderBy('title')
            ->get()
            ->filter(fn($c) => auth()->user()->canAccessCredential($c, 'read'))
            ->values();

        return Inertia::render('Groups/Show', [
            'group' => $group,
            'credentials' => $credentials
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $user = auth()->user();
        if (!$user->canAccessGroup($group, 'write')) {
            abort(403);
        }
        
        $credentials = Credential::where('group_id', $group->id)
            ->orderBy('title')
            ->get()
            ->filter(fn($c) => auth()->user()->canAccessCredential($c, 'read'))
            ->values();
            
        $availableGroups = Group::where('id', '!=', $group->id)
            ->orderBy('name')
            ->get()
            ->filter(fn($g) => auth()->user()->canAccessGroup($g, 'write'))
            ->values();
            
        $navigationTree = $this->getNavigationTree();
        
        return Inertia::render('Groups/Edit', [
            'group' => $group,
            'credentials' => $credentials,
            'availableGroups' => $availableGroups,
            'navigationTree' => $navigationTree
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $user = auth()->user();
        if (!$user->canAccessGroup($group, 'write')) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? $group->color,
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $user = auth()->user();
        if (!$user->canAccessGroup($group, 'admin')) {
            abort(403);
        }
        
        // Move credentials to ungrouped (null group_id)
        Credential::where('group_id', $group->id)
            ->where('created_by', auth()->id())
            ->update(['group_id' => null]);
        
        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    /**
     * Move credentials between groups
     */
    public function move(Request $request, Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'credential_ids' => 'required|array',
            'credential_ids.*' => 'exists:credentials,id',
            'target_group_id' => 'nullable|exists:groups,id',
        ]);

        Credential::whereIn('id', $request->credential_ids)
            ->where('created_by', auth()->id())
            ->update(['group_id' => $request->target_group_id]);

        return response()->json(['message' => 'Credentials moved successfully.']);
    }

    /**
     * Export group credentials
     */
    public function export(Group $group)
    {
        if ($group->created_by !== auth()->id()) {
            abort(403);
        }
        
        $credentials = Credential::where('group_id', $group->id)
            ->where('created_by', auth()->id())
            ->get();

        $csvData = "Title,Username,Password,URL,Notes\n";
        
        foreach ($credentials as $credential) {
            $csvData .= sprintf(
                '"%s","%s","%s","%s","%s"' . "\n",
                $credential->title,
                $credential->username,
                decrypt($credential->password),
                $credential->url ?? '',
                $credential->notes ?? ''
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $group->name . '_credentials.csv"');
    }

    /**
     * Show permissions editor for a group.
     */
    public function permissions(Group $group)
    {
        $user = auth()->user();
        if (!$user->canAccessGroup($group, 'admin')) {
            abort(403);
        }

        $users = \App\Models\User::select('id','name','email')->orderBy('name')->get();
        $existing = $group->permissions()->get(['user_id','effect','level','can_export','can_share']);

        return Inertia::render('Groups/Permissions', [
            'group' => $group,
            'users' => $users,
            'permissions' => $existing,
            'navigationTree' => $this->getNavigationTree(),
        ]);
    }

    /**
     * Save permissions for a group.
     */
    public function savePermissions(Request $request, Group $group)
    {
        $user = auth()->user();
        if (!$user->canAccessGroup($group, 'admin')) {
            abort(403);
        }

        $data = $request->validate([
            'entries' => 'required|array',
            'entries.*.user_id' => 'required|exists:users,id',
            'entries.*.effect' => 'required|in:allow,deny',
            'entries.*.level' => 'required|in:read,write,admin',
            'entries.*.can_export' => 'boolean',
            'entries.*.can_share' => 'boolean',
        ]);

        foreach ($data['entries'] as $entry) {
            $group->permissions()->updateOrCreate(
                ['user_id' => $entry['user_id']],
                [
                    'effect' => $entry['effect'],
                    'level' => $entry['level'],
                    'can_export' => $entry['can_export'] ?? false,
                    'can_share' => $entry['can_share'] ?? false,
                ]
            );
        }

        return redirect()->route('groups.permissions', $group->id)->with('success', 'Permissions updated.');
    }

    /**
     * Get navigation tree for sidebar
     */
    private function getNavigationTree()
    {
        $groups = Group::where('created_by', auth()->id())
            ->orderBy('name')
            ->get();

        return $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'level' => 0,
                'credential_count' => $group->credentials()->count(),
                'children' => [],
            ];
        })->toArray();
    }
}
